@extends('layouts.main', ['activePage' => 'profile'])

@section('title', 'User Profile | Prime Booking')

@section('content')
@php
    $u = $user ?? auth()->user();
    $nameParts = explode(' ', $u?->name ?? 'Guest User');
    $firstName = $nameParts[0] ?? '';
    $lastName  = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
@endphp

<div class="py-4" style="background-color: #f4f6fa; min-height: 85vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        <div class="row g-4">
            
            <!-- Left White Sidebar Navigation (1:1 Exact Match of Agoda Live) -->
            <div class="col-lg-3 col-md-4" style="max-width: 260px;">
                <x-user-sidebar activePage="profile" />
            </div>

            <!-- Right Column: User Profile Settings Area (Agoda 1:1 Exact Spec) -->
            <div class="col-lg-9 col-md-8">
                
                <!-- Page Title -->
                <div class="mb-4">
                    <h2 class="fw-bold mb-1" style="color: #0f172a; font-size: 26px;">{{ __('User Profile') }}</h2>
                    <p class="text-secondary mb-0" style="font-size: 14px;">{{ __('Manage your personal details, contact information, and travel preferences.') }}</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success rounded-3 mb-4" style="font-size: 13.5px;">
                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    </div>
                @endif

                {{-- Profile Overview Card --}}
                <div class="card border shadow-xs p-4 mb-4" style="border-color: #cbd5e1 !important; border-radius: 18px !important; background-color: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="position-relative">
                                <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; background-color: #2067e1; font-size: 26px; box-shadow: 0 4px 12px rgba(32, 103, 225, 0.3);">
                                    {{ strtoupper(substr($u?->name ?? 'G', 0, 1)) }}
                                </div>
                                <button type="button" class="btn btn-dark btn-sm rounded-circle position-absolute bottom-0 end-0 p-0 d-flex align-items-center justify-content-center" style="width: 22px; height: 22px; font-size: 10px;" title="Change Photo">
                                    <i class="fa-solid fa-camera"></i>
                                </button>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark" style="font-size: 18px;">{{ $u?->name ?? 'Guest Traveler' }}</h5>
                                <p class="text-secondary mb-1" style="font-size: 13.5px;">{{ $u?->email ?? 'Not signed in' }}</p>
                                <span class="badge px-2.5 py-1 fw-bold" style="background-color: #1e293b; color: #d98662; font-size: 11px; border-radius: 6px;">
                                    ★ VIP Bronze Member
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Personal Information Details Form Card --}}
                <div class="card border shadow-xs p-4 mb-4" style="border-color: #cbd5e1 !important; border-radius: 18px !important; background-color: #ffffff;">
                    <h5 class="fw-bold mb-4 text-dark" style="font-size: 17px;">{{ __('Personal Details') }}</h5>
                    
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 13px;">{{ __('First Name') }}</label>
                                <input type="text" name="first_name" class="form-control rounded-3 py-2" value="{{ old('first_name', $firstName) }}" style="font-size: 14px; border-color: #cbd5e1;" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 13px;">{{ __('Last Name') }}</label>
                                <input type="text" name="last_name" class="form-control rounded-3 py-2" value="{{ old('last_name', $lastName) }}" style="font-size: 14px; border-color: #cbd5e1;">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 13px;">{{ __('Email Address') }}</label>
                                <div class="input-group">
                                    <input type="email" class="form-control rounded-start-3 py-2" value="{{ $u?->email }}" style="font-size: 14px; border-color: #cbd5e1;" readonly>
                                    <span class="input-group-text bg-success-subtle text-success fw-bold px-3" style="font-size: 12px; border-color: #cbd5e1;">
                                        <i class="fa-solid fa-circle-check me-1"></i> Verified
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 13px;">{{ __('Phone Number') }}</label>
                                <input type="text" name="phone" class="form-control rounded-3 py-2" value="{{ old('phone', $u?->phone) }}" placeholder="+880 1700000000" style="font-size: 14px; border-color: #cbd5e1;">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 13px;">{{ __('Date of Birth') }}</label>
                                <input type="date" name="dob" class="form-control rounded-3 py-2" value="{{ old('dob', $u?->dob) }}" style="font-size: 14px; border-color: #cbd5e1;">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 13px;">{{ __('Gender') }}</label>
                                <select name="gender" class="form-select rounded-3 py-2" style="font-size: 14px; border-color: #cbd5e1;">
                                    <option value="Male" {{ old('gender', $u?->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $u?->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender', $u?->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 13px;">{{ __('Country / Region') }}</label>
                                <select name="country" class="form-select rounded-3 py-2" style="font-size: 14px; border-color: #cbd5e1;">
                                    <option value="BD" {{ old('country', $u?->country ?? 'BD') === 'BD' ? 'selected' : '' }}>Bangladesh 🇧🇩</option>
                                    <option value="IN" {{ old('country', $u?->country) === 'IN' ? 'selected' : '' }}>India 🇮🇳</option>
                                    <option value="AE" {{ old('country', $u?->country) === 'AE' ? 'selected' : '' }}>United Arab Emirates 🇦🇪</option>
                                    <option value="US" {{ old('country', $u?->country) === 'US' ? 'selected' : '' }}>United States 🇺🇸</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4 border-gray-200">

                        <h5 class="fw-bold mb-3 text-dark" style="font-size: 16px;"><i class="fa-solid fa-passport text-primary me-2"></i> {{ __('Passport Details') }}</h5>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 13px;">{{ __('Passport Number') }}</label>
                                <input type="text" name="passport_number" class="form-control rounded-3 py-2" value="{{ old('passport_number', $u?->passport_number) }}" placeholder="e.g. A08745129" style="font-size: 14px; border-color: #cbd5e1;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 13px;">{{ __('Expiration Date') }}</label>
                                <input type="date" name="passport_expiry" class="form-control rounded-3 py-2" value="{{ old('passport_expiry', $u?->passport_expiry) }}" style="font-size: 14px; border-color: #cbd5e1;">
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn text-white fw-bold px-4 py-2 rounded-pill" style="background-color: #2067e1; font-size: 14px; box-shadow: 0 4px 12px rgba(32, 103, 225, 0.25);">
                                {{ __('Save Changes') }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Linked Social Accounts & OAuth Security Card (1:1 Agoda Spec) --}}
                <div class="card border shadow-xs p-4 mb-4" style="border-color: #cbd5e1 !important; border-radius: 18px !important; background-color: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark" style="font-size: 17px;">{{ __('Linked Social Accounts') }}</h5>
                            <p class="text-secondary mb-0" style="font-size: 13px;">{{ __('Manage connected accounts for fast 1-click sign-in.') }}</p>
                        </div>
                        <span class="badge bg-success-subtle text-success fw-bold px-3 py-1.5" style="font-size: 12px; border-radius: 6px;">
                            <i class="fa-solid fa-shield-halved me-1"></i> {{ __('OAuth Protected') }}
                        </span>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        {{-- Google Link Row --}}
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 border" style="border-color: #e2e8f0; background: #f8fafc;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-white rounded-circle p-2 shadow-xs d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <svg width="20" height="20" viewBox="0 0 24 24">
                                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 14px;">Google</div>
                                    <div class="text-secondary" style="font-size: 12px;">{{ $u?->email ?? 'Connected' }}</div>
                                </div>
                            </div>
                            <span class="badge bg-success text-white fw-bold px-3 py-1.5" style="border-radius: 20px; font-size: 11.5px;">
                                <i class="fa-solid fa-circle-check me-1"></i> Connected
                            </span>
                        </div>

                        {{-- Facebook Link Row --}}
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 border" style="border-color: #e2e8f0; background: #ffffff;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #1877f2; font-size: 18px;">
                                    <i class="fa-brands fa-facebook-f"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 14px;">Facebook</div>
                                    <div class="text-secondary" style="font-size: 12px;">Fast 1-click login</div>
                                </div>
                            </div>
                            <a href="{{ route('auth.social.redirect', 'facebook') }}" class="btn btn-outline-primary rounded-pill px-3 py-1 fw-bold" style="font-size: 12px; border-color: #1877f2; color: #1877f2;">
                                Connect
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
