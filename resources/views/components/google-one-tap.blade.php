{{-- Official Google One-Tap & Custom Card Sign-In Widget (Professional Guest Popup) --}}
@guest

{{-- Official Google Identity Services SDK --}}
@if(config('services.google.client_id'))
<script src="https://accounts.google.com/gsi/client" async defer></script>
<div id="g_id_onload"
     data-client_id="{{ config('services.google.client_id') }}"
     data-context="signin"
     data-ux_mode="redirect"
     data-login_uri="{{ route('auth.social.redirect', 'google') }}"
     data-auto_prompt="true">
</div>
@endif

{{-- Custom Agoda-Style Google Card Widget --}}
<div id="googleOneTapWidget" style="position: fixed; top: 24px; right: 24px; z-index: 10000; width: 360px; max-width: 92vw; background: #1c1e21; border-radius: 16px; box-shadow: 0 16px 48px rgba(0, 0, 0, 0.45); color: #ffffff; padding: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; display: none; animation: slideDownOneTap 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;">
    
    <!-- Top Header Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <!-- Official Google 4-Color SVG Icon -->
            <svg style="width: 20px; height: 20px; flex-shrink: 0;" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span style="font-size: 13.5px; font-weight: 600; color: #f1f5f9; line-height: 1.2;">
                Sign in to {{ request()->getHost() }} with google.com
            </span>
        </div>

        <!-- Close Button ✕ -->
        <button type="button" onclick="closeGoogleOneTap()" style="background: transparent; border: none; color: #94a3b8; font-size: 18px; cursor: pointer; padding: 0 4px; line-height: 1; transition: color 0.2s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#94a3b8'">
            ✕
        </button>
    </div>

    <!-- User Profile Account Row -->
    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 18px; padding-top: 4px;">
        <div style="width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, #2067e1, #1d4ed8); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; border: 1.5px solid rgba(255,255,255,0.2);">
            G
        </div>
        <div style="overflow: hidden;">
            <h6 style="margin: 0; font-size: 14.5px; font-weight: 700; color: #ffffff; line-height: 1.2;">
                Google Account
            </h6>
            <p style="margin: 2px 0 0 0; font-size: 12.5px; color: #94a3b8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                1-Click Instant Sign In
            </p>
        </div>
    </div>

    <!-- Large Blue Action Button -->
    <a href="{{ route('auth.social.redirect', 'google') }}" style="display: flex; align-items: center; justify-content: center; text-decoration: none; width: 100%; background-color: #2067e1; color: #ffffff; border: none; border-radius: 999px; height: 44px; font-weight: 700; font-size: 14.5px; cursor: pointer; box-shadow: 0 4px 14px rgba(32,103,225,0.4); transition: all 0.2s;">
        Continue with Google
    </a>

    <!-- Footer Disclaimer Text -->
    <p style="margin: 14px 0 0 0; font-size: 11px; color: #94a3b8; line-height: 1.55;">
        To continue, google.com will share your name, email address, and profile picture with this site. See this site's <a href="{{ route('privacy') }}" style="color: #60a5fa; text-decoration: underline;">privacy policy</a> and <a href="{{ route('terms') }}" style="color: #fdba74; text-decoration: underline;">terms of service</a>.
    </p>
</div>

<style>
@keyframes slideDownOneTap {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const widget = document.getElementById('googleOneTapWidget');
        if (widget) {
            widget.style.display = 'block';
        }
    }, 600);
});

function closeGoogleOneTap() {
    const widget = document.getElementById('googleOneTapWidget');
    if (widget) {
        widget.style.display = 'none';
    }
}
</script>
@endguest
