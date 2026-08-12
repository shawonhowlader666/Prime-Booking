{{-- Official Google One-Tap Sign-In Widget --}}
@guest
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
@endguest
