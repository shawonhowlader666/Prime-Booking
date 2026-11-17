<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Booking;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * bKash Tokenized Checkout (v1.2.0-beta): grant token, create, execute, query.
 *
 * There is no "demo" success path in this class. If bKash cannot confirm a
 * payment, the payment is not confirmed.
 */
class BkashPaymentService
{
    private string $baseUrl;
    private string $appKey;
    private string $appSecret;
    private string $username;
    private string $password;
    private bool $isSandbox;
    private bool $isActive;

    public function __construct()
    {
        $gateway = null;
        try {
            $gateway = PaymentGateway::where('gateway_code', 'bkash')->first();
        } catch (\Throwable $e) {
            Log::warning('bKash gateway config unavailable: ' . $e->getMessage());
        }

        $settings = is_array($gateway?->settings) ? $gateway->settings : [];

        $dbKey    = PaymentGateway::realCredential($gateway?->api_key);
        $dbSecret = PaymentGateway::realCredential($gateway?->api_secret);
        $dbUser   = PaymentGateway::realCredential($settings['username'] ?? $gateway?->merchant_id);
        $dbPass   = PaymentGateway::realCredential($settings['password'] ?? '');
        $fromVault = $dbKey !== '' && $dbSecret !== '' && $dbUser !== '' && $dbPass !== '';

        // Vault credentials win when complete; otherwise the BKASH_* environment values are used.
        $this->isActive  = $fromVault ? (bool) $gateway->is_active : true;
        $this->isSandbox = $fromVault ? (bool) $gateway->is_sandbox : (bool) config('services.bkash.is_sandbox', true);
        $this->appKey    = $fromVault ? $dbKey : (string) config('services.bkash.app_key', '');
        $this->appSecret = $fromVault ? $dbSecret : (string) config('services.bkash.app_secret', '');
        $this->username  = $fromVault ? $dbUser : (string) config('services.bkash.username', '');
        $this->password  = $fromVault ? $dbPass : (string) config('services.bkash.password', '');

        $this->baseUrl = $this->isSandbox
            ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout'
            : 'https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized/checkout';
    }

    public function isConfigured(): bool
    {
        return $this->isActive
            && $this->appKey !== '' && $this->appSecret !== ''
            && $this->username !== '' && $this->password !== '';
    }

    /** Grant token, cached for most of its one-hour lifetime. */
    public function getToken(): ?string
    {
        if (!$this->isConfigured()) {
            return null;
        }

        $cacheKey = 'bkash_id_token_' . md5($this->appKey . ($this->isSandbox ? 's' : 'l'));
        $cached = Cache::get($cacheKey);
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        try {
            $response = Http::timeout(15)->withHeaders([
                'username' => $this->username,
                'password' => $this->password,
                'Accept'   => 'application/json',
            ])->post("{$this->baseUrl}/token/grant", [
                'app_key'    => $this->appKey,
                'app_secret' => $this->appSecret,
            ]);

            $token = $response->json('id_token');
            if ($response->successful() && is_string($token) && $token !== '') {
                Cache::put($cacheKey, $token, now()->addMinutes(50));
                return $token;
            }

            Log::error('bKash grant token failed', ['http' => $response->status(), 'message' => $response->json('statusMessage')]);
            return null;
        } catch (\Throwable $e) {
            Log::error('bKash grant token exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * @return array{status:string,paymentID?:string,bkashURL?:string,message?:string}
     */
    public function createPayment(Booking $booking, string $callbackUrl): array
    {
        $token = $this->getToken();
        if (!$token) {
            return ['status' => 'error', 'message' => 'bKash is not available right now.'];
        }

        $amount = (float) ($booking->total_price ?? $booking->total_amount ?? 0);
        if ($amount <= 0) {
            return ['status' => 'error', 'message' => 'Invalid payable amount.'];
        }

        try {
            $response = $this->authorized($token)->post("{$this->baseUrl}/create", [
                'mode'                  => '0011',
                'payerReference'        => preg_replace('/\D/', '', (string) $booking->guest_phone) ?: '01700000000',
                'callbackURL'           => $callbackUrl,
                'amount'                => number_format($amount, 2, '.', ''),
                'currency'              => 'BDT',
                'intent'                => 'sale',
                'merchantInvoiceNumber' => $booking->booking_reference,
            ]);

            if ($response->successful() && $response->json('bkashURL') && $response->json('paymentID')) {
                return [
                    'status'    => 'success',
                    'paymentID' => (string) $response->json('paymentID'),
                    'bkashURL'  => (string) $response->json('bkashURL'),
                ];
            }

            Log::error('bKash create payment failed', ['http' => $response->status(), 'message' => $response->json('statusMessage')]);
            return ['status' => 'error', 'message' => (string) ($response->json('statusMessage') ?? 'bKash payment could not be started.')];
        } catch (\Throwable $e) {
            Log::error('bKash create payment exception: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'bKash is unreachable right now.'];
        }
    }

    /**
     * Execute the payment the guest approved. If the execute call times out or
     * returns an ambiguous answer, fall back to Query so a paid customer is
     * never left unconfirmed.
     *
     * @return array{status:string,trxID?:?string,paymentID?:?string,amount?:?float,invoice?:?string,message?:string}
     */
    public function executePayment(string $paymentID): array
    {
        $token = $this->getToken();
        if (!$token || $paymentID === '') {
            return ['status' => 'failed', 'message' => 'bKash is not available right now.'];
        }

        try {
            $response = $this->authorized($token)->post("{$this->baseUrl}/execute", ['paymentID' => $paymentID]);
            $data = $response->json() ?: [];

            if ($response->successful()
                && ($data['statusCode'] ?? '') === '0000'
                && ($data['transactionStatus'] ?? '') === 'Completed') {
                return $this->completed($data);
            }
        } catch (\Throwable $e) {
            Log::warning('bKash execute exception, falling back to query: ' . $e->getMessage());
        }

        return $this->queryPayment($paymentID);
    }

    public function queryPayment(string $paymentID): array
    {
        $token = $this->getToken();
        if (!$token || $paymentID === '') {
            return ['status' => 'failed', 'message' => 'bKash is not available right now.'];
        }

        try {
            $response = $this->authorized($token)->post("{$this->baseUrl}/payment/status", ['paymentID' => $paymentID]);
            $data = $response->json() ?: [];

            if ($response->successful() && ($data['transactionStatus'] ?? '') === 'Completed') {
                return $this->completed($data);
            }

            return ['status' => 'failed', 'message' => (string) ($data['statusMessage'] ?? 'Payment was not completed.')];
        } catch (\Throwable $e) {
            Log::error('bKash query exception: ' . $e->getMessage());
            return ['status' => 'failed', 'message' => 'bKash could not confirm the payment.'];
        }
    }

    private function completed(array $data): array
    {
        return [
            'status'    => 'Completed',
            'trxID'     => $data['trxID'] ?? null,
            'paymentID' => $data['paymentID'] ?? null,
            'amount'    => isset($data['amount']) ? (float) $data['amount'] : null,
            'invoice'   => $data['merchantInvoiceNumber'] ?? ($data['merchantInvoice'] ?? null),
            'raw'       => $data,
        ];
    }

    private function authorized(string $token)
    {
        return Http::timeout(30)->withHeaders([
            'Authorization' => $token,
            'X-APP-Key'     => $this->appKey,
            'Accept'        => 'application/json',
        ]);
    }
}
