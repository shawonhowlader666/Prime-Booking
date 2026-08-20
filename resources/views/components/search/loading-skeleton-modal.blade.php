{{-- Agoda Signature Fast 7-Color Deep Bouncing Wave Loader (Clean Backdrop, High Amplitude Wave) --}}
<div id="agodaSearchLoadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 9999999; overflow: hidden; font-family: BlinkMacSystemFont, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #ffffff; width: 440px; max-width: 90vw; padding: 34px 28px; border-radius: 20px; text-align: center; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35); border: 1px solid rgba(226, 232, 240, 0.9); animation: agodaPopIn 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);">
            
            {{-- Official Brand Logo --}}
            <div style="margin: 0 auto 10px auto; text-align: center;">
                <img src="{{ asset('images/logo.png') }}" alt="Prime Booking" style="height: 50px; max-width: 220px; object-fit: contain;">
            </div>

            {{-- 7-Color Deep Left-to-Right Bouncing Wave Dots (High Amplitude Wave) --}}
            <div class="agoda-7dots-loader" style="display: flex; align-items: center; justify-content: center; gap: 10px; height: 70px; margin: 8px 0 10px 0;">
                <span class="dot" style="background-color: #FF385C; animation-delay: 0s;"></span>
                <span class="dot" style="background-color: #FF7E29; animation-delay: 0.09s;"></span>
                <span class="dot" style="background-color: #FFC107; animation-delay: 0.18s;"></span>
                <span class="dot" style="background-color: #22C55E; animation-delay: 0.27s;"></span>
                <span class="dot" style="background-color: #0EA5E9; animation-delay: 0.36s;"></span>
                <span class="dot" style="background-color: #2067E1; animation-delay: 0.45s;"></span>
                <span class="dot" style="background-color: #8B5CF6; animation-delay: 0.54s;"></span>
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

{{-- Deep Bouncing Wave CSS (High Amplitude: -24px to +10px) --}}
<style>
    @keyframes agodaPopIn {
        0% { transform: scale(0.92); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    .agoda-7dots-loader .dot {
        width: 13px;
        height: 13px;
        border-radius: 50%;
        display: inline-block;
        animation: agoda7ColorDeepBounce 0.85s ease-in-out infinite;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
    }

    @keyframes agoda7ColorDeepBounce {
        0%, 100% {
            transform: translateY(10px) scale(0.8);
            opacity: 0.5;
        }
        50% {
            transform: translateY(-24px) scale(1.4);
            opacity: 1;
            box-shadow: 0 14px 24px rgba(0, 0, 0, 0.3);
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
