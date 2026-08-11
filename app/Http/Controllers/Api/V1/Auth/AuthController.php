<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * API Auth Controller — Register / Login / Logout / Profile
 * All responses use the ApiResponse trait for consistent JSON envelope.
 */
class AuthController extends Controller
{
    use ApiResponse;

    // ─── Register ─────────────────────────────────────────────────────────

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'phone'    => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'role'     => 'customer',
            'status'   => 'active',
        ]);

        $token = $user->createToken('api-token', ['role:customer'])->plainTextToken;

        // Log activity
        try {
            \App\Models\ActivityLog::create([
                'user_id'     => $user->id,
                'user_name'   => $user->name,
                'action'      => 'register',
                'model_type'  => 'User',
                'model_id'    => $user->id,
                'description' => "New user registered: {$user->email}",
                'ip_address'  => $request->ip(),
            ]);
        } catch (\Exception $e) {}

        return $this->created([
            'user'  => $this->formatUser($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Registration successful! Welcome to Prime Aviation.');
    }

    // ─── Login ────────────────────────────────────────────────────────────

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid email or password.', 401);
        }

        if ($user->isBanned()) {
            return $this->error('Your account has been suspended. Contact support.', 403);
        }

        if (!$user->isActive()) {
            return $this->error('Your account is inactive. Contact support.', 403);
        }

        // Revoke old tokens (single device login)
        $user->tokens()->delete();

        // Create new token with role ability
        $token = $user->createToken('api-token', ["role:{$user->role}"])->plainTextToken;

        // Update login tracker
        $user->recordLogin($request->ip());

        return $this->success([
            'user'       => $this->formatUser($user),
            'token'      => $token,
            'token_type' => 'Bearer',
        ], 'Login successful! Welcome back, ' . $user->name);
    }

    // ─── Logout ───────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(null, 'Logged out successfully.');
    }

    // ─── Me / Profile ─────────────────────────────────────────────────────

    public function me(Request $request)
    {
        $user = $request->user()->load([
            'bookings' => fn($q) => $q->latest()->take(5)
                ->with('property:id,name,city,primary_image'),
        ]);

        return $this->success($this->formatUser($user, detailed: true));
    }

    // ─── Update Profile ───────────────────────────────────────────────────

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|string|max:100',
            'phone'    => 'sometimes|nullable|string|max:20',
            'city'     => 'sometimes|nullable|string|max:80',
            'country'  => 'sometimes|nullable|string|max:50',
            'avatar'   => 'sometimes|nullable|url',
            'password' => ['sometimes', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        $data = $request->only(['name','phone','city','country','avatar']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return $this->success($this->formatUser($user->fresh()), 'Profile updated successfully.');
    }

    // ─── Helper ───────────────────────────────────────────────────────────

    private function formatUser(User $user, bool $detailed = false): array
    {
        $base = [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'phone'      => $user->phone,
            'role'       => $user->role,
            'status'     => $user->status,
            'avatar_url' => $user->avatar_url,
            'city'       => $user->city,
            'country'    => $user->country,
            'member_since' => $user->created_at?->format('M Y'),
        ];

        if ($detailed) {
            $base['total_bookings'] = $user->total_bookings;
            $base['total_spent']    = $user->total_spent;
            $base['last_login']     = $user->last_login_at?->diffForHumans();
            $base['bookings']       = $user->bookings?->map(fn($b) => [
                'reference' => $b->booking_reference,
                'status'    => $b->effective_status,
                'property'  => $b->property?->name,
                'check_in'  => $b->check_in?->toDateString(),
                'total'     => $b->amount,
            ]);
        }

        return $base;
    }
}
