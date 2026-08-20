{{-- Agoda Signature Fast 7-Color Bouncing Wave Loader (Clean Backdrop, Zero Clutter) --}}
<div id="agodaSearchLoadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 9999999; overflow: hidden; font-family: BlinkMacSystemFont, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #ffffff; width: 420px; max-width: 90vw; padding: 32px 28px; border-radius: 16px; text-align: center; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35); border: 1px solid rgba(226, 232, 240, 0.9); animation: agodaPopIn 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);">
            
            {{-- Official Brand Logo --}}
            <div style="margin: 0 auto 18px auto; text-align: center;">
                <img src="{{ asset('images/logo.png') }}" alt="Prime Booking" style="height: 48px; max-width: 200px; object-fit: contain;">
            </div>

            {{-- 7-Color Left-to-Right Fast Bouncing Wave Dots (Agoda Signature Multi-Color Dots) --}}
            <div class="agoda-7dots-loader" style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 18px;">
                <span class="dot" style="background-color: #FF385C; animation-delay: 0s;"></span>
                <span class="dot" style="background-color: #FF7E29; animation-delay: 0.08s;"></span>
                <span class="dot" style="background-color: #FFC107; animation-delay: 0.16s;"></span>
                <span class="dot" style="background-color: #22C55E; animation-delay: 0.24s;"></span>
                <span class="dot" style="background-color: #0EA5E9; animation-delay: 0.32s;"></span>
                <span class="dot" style="background-color: #2067E1; animation-delay: 0.40s;"></span>
                <span class="dot" style="background-color: #8B5CF6; animation-delay: 0.48s;"></span>
            </div>

            {{-- Heading & Subtitle --}}
            <h5 style="color: #1e293b; font-weight: 800; font-size: 18px; margin: 0 0 6px 0; letter-spacing: -0.2px;">
                Finding Best Deals...
            </h5>
            <p style="color: #64748b; font-size: 13px; margin: 0; line-height: 1.4; font-weight: 500;">
                Comparing real-time availability &amp; lowest prices for you
            </p>
        </div>
    </div>
</div>

{{-- Fast Bouncing Wave CSS (0.75s Fast Loop) --}}
<style>
    @keyframes agodaPopIn {
        0% { transform: scale(0.92); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    .agoda-7dots-loader .dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        animation: agoda7ColorFastBounce 0.75s ease-in-out infinite;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
    }

    @keyframes agoda7ColorFastBounce {
        0%, 100% {
            transform: translateY(0) scale(0.85);
            opacity: 0.55;
        }
        40% {
            transform: translateY(-15px) scale(1.25);
            opacity: 1;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.22);
        }
    }
</style>

<script>
    window.showAgodaSearchLoading = function() {
        var overlay = document.getElementById('agodaSearchLoadingOverlay');
        if (overlay) {
            overlay.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    };

    window.hideAgodaSearchLoading = function() {
        var overlay = document.getElementById('agodaSearchLoadingOverlay');
        if (overlay) {
            overlay.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    };

    // Hide loader automatically if user navigates back using browser history
    window.addEventListener('pageshow', function(event) {
        window.hideAgodaSearchLoading();
    });
</script>
