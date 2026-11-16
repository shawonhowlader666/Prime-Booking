<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Booking;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * SSLCommerz hosted checkout.
 *
 * Credentials come from the admin "Payment Gateways" vault (encrypted at rest)
 * or from SSLCZ_* environment variables. There are no credentials in source.
 */
class SSLCommerzPaymentService
{
    private string $storeId;
    private string $storePassword;
    private bool $isSandbox;
    private bool $isActive;
    private string $baseUrl;

    public function __construct()
    {
        $gateway = null;
        try {
            $gateway = PaymentGateway::where('gateway_code', 'sslcommerz')->first();
        } catch (\Throwable $e) {
            Log::warning('SSLCommerz gateway config unavailable: ' . $e->getMessage());
        }

        $this->isActive      = $gateway ? (bool) $gateway->is_active : true;
        $this->isSandbox     = $gateway ? (bool) $gateway->is_sandbox : (bool) config('services.sslcommerz.is_sandbox', true);
        $this->storeId       = (string) (!empty($gateway?->merchant_id) ? $gateway->merchant_id : config('services.sslcommerz.store_id', ''));
        $this->storePassword = (string) (!empty($gateway?->api_secret) ? $gateway->api_secret : config('services.sslcommerz.store_password', ''));

        $this->baseUrl = $this->isSandbox
            ? 'https://sandbox.sslcommerz.com'
            : 'https://securepay.sslcommerz.com';
    }

    public function isConfigured(): bool
    {
        return $this->isActive && $this->storeId !== '' && $this->storePassword !== '';
    }

    /**
     * SSLCommerz wants a unique tran_id per attempt, and guests may retry a failed
     * payment, so each attempt gets its own suffix on top of the booking reference.
     */
    public static function makeTranId(Booking $booking): string
    {
        return $booking->booking_reference . '_' . Str::upper(Str::random(6));
    }

    public static function referenceFromTranId(string $tranId): string
    {
        return Str::before($tranId, '_');
    }

    /**
     * Open a hosted payment session.
     *
     * @return array{status:string,redirectUrl?:string,message?:string}
     */
    public function initiatePayment(Booking $booking, string $successUrl, string $failUrl, string $cancelUrl): array
    {
        if (!$this->isConfigured()) {
            return ['status' => 'unconfigured', 'message' => 'SSLCommerz is not configured.'];
        }

        $amount = (float) ($booking->total_price ?? $booking->total_amount ?? 0);
        if ($amount <= 0) {
            return ['status' => 'error', 'message' => 'Invalid payable amount.'];
        }

        $postData = [
            'store_id'         => $this->storeId,
            'store_passwd'     => $this->storePassword,
            'total_amount'     => number_format($amount, 2, '.', ''),
            'currency'         => 'BDT',
            'tran_id'          => self::makeTranId($booking),
            'success_url'      => $successUrl,
            'fail_url'         => $failUrl,
            'cancel_url'       => $cancelUrl,
            'ipn_url'          => route('payment.ssl.ipn'),
            'value_a'          => $booking->booking_reference,
            'cus_name'         => $booking->guest_name ?: 'Guest',
            'cus_email'        => $booking->guest_email ?: 'guest@example.com',
            'cus_add1'         => $booking->property?->city ?? 'Dhaka',
            'cus_city'         => $booking->property?->city ?? 'Dhaka',
            'cus_country'      => 'Bangladesh',
            'cus_phone'        => $booking->guest_phone ?: '01700000000',
            'shipping_method'  => 'NO',
            'num_of_item'      => 1,
            'product_name'     => Str::limit('Hotel Booking - ' . ($booking->property?->name ?? 'Prime Booking'), 250, ''),
            'product_category' => 'Travel',
            'product_profile'  => 'travel-vertical',
            'hotel_name'       => Str::limit((string) ($booking->property?->name ?? 'Hotel'), 100, ''),
            'length_of_stay'   => max(1, (int) $booking->nights) . ' nights',
            'check_in_time'    => optional($booking->check_in)->format('Y-m-d') ?: '',
            'hotel_city'       => $booking->property?->city ?? 'Dhaka',
        ];

        try {
            $response = Http::asForm()->timeout(15)->post("{$this->baseUrl}/gwprocess/v4/api.php", $postData);
            $data = $response->json() ?: [];

            if ($response->successful()
                && in_array(strtoupper((string) ($data['status'] ?? '')), ['SUCCESS', 'SUCCESSFUL'], true)
                && !empty($data['GatewayPageURL'])) {
                return ['status' => 'success', 'redirectUrl' => $data['GatewayPageURL']];
            }

            Log::error('SSLCommerz session init failed', ['http' => $response->status(), 'reason' => $data['failedreason'] ?? null]);
            return ['status' => 'error', 'message' => (string) ($data['failedreason'] ?? 'Could not start the payment session.')];
        } catch (\Throwable $e) {
            Log::error('SSLCommerz session init exception: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Payment gateway is unreachable right now.'];
        }
    }

    /**
     * Ask SSLCommerz whether a val_id is a real, successful payment.
     *
     * @return array|null The validation payload (status, tran_id, amount, currency_type, bank_tran_id ...) or null.
     */
    public function fetchValidation(string $valId): ?array
    {
        if ($valId === '' || !$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::timeout(15)->get("{$this->baseUrl}/validator/api/validationserverAPI.php", [
                'val_id'       => $valId,
                'store_id'     => $this->storeId,
                'store_passwd' => $this->storePassword,
                'format'       => 'json',
            ]);

            $data = $response->json();
            if ($response->successful() && is_array($data)
                && in_array($data['status'] ?? '', ['VALID', 'VALIDATED'], true)) {
                return $data;
            }

            return null;
        } catch (\Throwable $e) {
            Log::error('SSLCommerz validation exception: ' . $e->getMessage());
            return null;
        }
    }

    /** Backwards-compatible boolean check: valid payment for (roughly) the expected BDT amount. */
    public function validatePayment(string $valId, float $amount, string $currency = 'BDT'): bool
    {
        $data = $this->fetchValidation($valId);
        if (!$data) {
            return false;
        }

        if ($amount > 0 && abs((float) ($data['amount'] ?? 0) - $amount) > 1.0) {
            Log::error("SSLCommerz amount mismatch: expected {$amount}, received " . ($data['amount'] ?? 'n/a'));
            return false;
        }

        return true;
    }

    /**
     * Verify the verify_sign hash SSLCommerz attaches to its POST callbacks,
     * proving the request body was produced with our store password.
     */
    public function verifyCallbackHash(array $post): bool
    {
        if ($this->storePassword === '' || empty($post['verify_sign']) || empty($post['verify_key'])) {
            return false;
        }

        $signed = [];
        foreach (explode(',', (string) $post['verify_key']) as $key) {
            if ($key !== '' && array_key_exists($key, $post)) {
                $signed[$key] = (string) $post[$key];
            }
        }
        $signed['store_passwd'] = md5($this->storePassword);
        ksort($signed);

        $pairs = [];
        foreach ($signed as $key => $value) {
            $pairs[] = $key . '=' . $value;
        }

        return hash_equals(md5(implode('&', $pairs)), (string) $post['verify_sign']);
    }
}
