@extends('layouts.main')

@section('title', 'Sign in - Google Accounts')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center py-5" style="background-color: #1e1e1e; font-family: 'Roboto', -apple-system, BlinkMacSystemFont, sans-serif;">
    <div class="card border-0 shadow-lg text-white" style="width: 100%; max-width: 480px; background-color: #2b2b2b; border-radius: 28px; padding: 36px 32px;">
        
        {{-- Google Logo Header --}}
        <div class="text-center mb-4">
            <svg width="40" height="40" viewBox="0 0 24 24" class="mb-3">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <h4 class="fw-normal mb-1" style="font-size: 24px; color: #e8eaed;">Choose an account</h4>
            <p class="text-secondary mb-0" style="font-size: 14px; color: #9aa0a6 !important;">to continue to <strong class="text-white">Prime Booking</strong></p>
        </div>

        {{-- Notice Banner --}}
        <div class="p-3 mb-4 rounded-3" style="background-color: rgba(66, 133, 244, 0.12); border: 1px solid rgba(66, 133, 244, 0.3);">
            <div class="d-flex align-items-center gap-2 mb-1 text-primary fw-medium" style="font-size: 13px;">
                <i class="fa-solid fa-circle-info"></i> Google OAuth Local Preview
            </div>
            <p class="mb-0 text-secondary" style="font-size: 12px; color: #bdc1c6 !important; line-height: 1.4;">
                To enable live redirect to <code>accounts.google.com</code>, set <code>GOOGLE_CLIENT_ID</code> in <code>.env</code>. Below you can select any account to test authentication!
            </p>
        </div>

        {{-- Account Chooser List --}}
        <div class="d-flex flex-column gap-2 mb-4">
            
            {{-- Account 1: Shawon --}}
            <form action="{{ route('auth.social.demo-select') }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="email" value="shawonhawlader1044@gmail.com">
                <input type="hidden" name="name" value="Shawon">
                <input type="hidden" name="provider" value="{{ $provider }}">
                <button type="submit" class="btn text-start w-100 p-3 d-flex align-items-center gap-3 shadow-none text-white border-0" 
                        style="background-color: #35363a; border-radius: 12px; transition: background 0.2s;"
                        onmouseover="this.style.backgroundColor='#3c4043'" onmouseout="this.style.backgroundColor='#35363a'">
                    <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=120&q=80" 
                         class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                    <div class="overflow-hidden flex-grow-1">
                        <div class="fw-medium text-truncate" style="font-size: 14px; color: #e8eaed;">Shawon</div>
                        <div class="text-secondary text-truncate" style="font-size: 12px; color: #9aa0a6 !important;">shawonhawlader1044@gmail.com</div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-secondary" style="font-size: 12px;"></i>
                </button>
            </form>

            {{-- Account 2: Guest Google --}}
            <form action="{{ route('auth.social.demo-select') }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="email" value="guest.google@primeaviation.com">
                <input type="hidden" name="name" value="Google Verified User">
                <input type="hidden" name="provider" value="{{ $provider }}">
                <button type="submit" class="btn text-start w-100 p-3 d-flex align-items-center gap-3 shadow-none text-white border-0" 
                        style="background-color: #35363a; border-radius: 12px; transition: background 0.2s;"
                        onmouseover="this.style.backgroundColor='#3c4043'" onmouseout="this.style.backgroundColor='#35363a'">
                    <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center" 
                         style="width: 40px; height: 40px; background-color: #ea4335; font-size: 16px;">
                        G
                    </div>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="fw-medium text-truncate" style="font-size: 14px; color: #e8eaed;">Google Verified User</div>
                        <div class="text-secondary text-truncate" style="font-size: 12px; color: #9aa0a6 !important;">guest.google@primeaviation.com</div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-secondary" style="font-size: 12px;"></i>
                </button>
            </form>

        </div>

        {{-- Custom Gmail Sign In Form --}}
        <div class="pt-3 border-top border-secondary border-opacity-25">
            <label class="form-label text-secondary mb-2" style="font-size: 13px; color: #9aa0a6 !important;">Use another Gmail account:</label>
            <form action="{{ route('auth.social.demo-select') }}" method="POST">
                @csrf
                <input type="hidden" name="provider" value="{{ $provider }}">
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control text-white border-0 shadow-none px-3" 
                           placeholder="enter.any.gmail@gmail.com" required
                           style="background-color: #35363a; border-radius: 8px 0 0 8px; font-size: 14px; height: 44px;">
                    <button type="submit" class="btn btn-primary fw-medium px-4" 
                            style="border-radius: 0 8px 8px 0; background-color: #8ab4f8; color: #202124; border: none; height: 44px; font-size: 14px;">
                        Sign In <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('home') }}" class="text-decoration-none" style="color: #8ab4f8; font-size: 13px;">
                <i class="fa-solid fa-arrow-left me-1"></i> Return to Prime Booking
            </a>
        </div>

    </div>
</div>
@endsection
