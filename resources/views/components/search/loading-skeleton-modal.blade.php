{{-- Agoda Signature Fast 7-Color Ultra-Smooth Rolling Wave Dots (Pure Dots, High Amplitude Wave) --}}
<div id="agodaSearchLoadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 9999999; overflow: hidden; font-family: 'Barlow', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #ffffff; width: 400px; max-width: 90vw; padding: 36px 28px; border-radius: 24px; text-align: center; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35); border: 1px solid rgba(226, 232, 240, 0.9); animation: agodaPopIn 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);">
            
            {{-- 7-Color Ultra-Smooth High Amplitude Rolling Wave Dots --}}
            <div class="agoda-7dots-loader" style="display: flex; align-items: center; justify-content: center; gap: 11px; height: 80px; margin: 10px 0 16px 0;">
                <span class="dot dot-1" style="background-color: #FF385C; animation-delay: 0s;"></span>
                <span class="dot dot-2" style="background-color: #FF7E29; animation-delay: 0.09s;"></span>
                <span class="dot dot-3" style="background-color: #FFC107; animation-delay: 0.18s;"></span>
                <span class="dot dot-4" style="background-color: #22C55E; animation-delay: 0.27s;"></span>
                <span class="dot dot-5" style="background-color: #0EA5E9; animation-delay: 0.36s;"></span>
                <span class="dot dot-6" style="background-color: #2067E1; animation-delay: 0.45s;"></span>
                <span class="dot dot-7" style="background-color: #8B5CF6; animation-delay: 0.54s;"></span>
            </div>

            {{-- Heading & Subtitle --}}
            <h5 style="color: #0f172a; font-weight: 800; font-size: 19px; margin: 0 0 6px 0; letter-spacing: -0.2px;">
                Finding Best Deals...
            </h5>
            <p style="color: #64748b; font-size: 13.5px; margin: 0; line-height: 1.5; font-weight: 500;">
                Comparing real-time availability &amp; lowest prices for you
            </p>
        </div>
    </div>
</div>

{{-- High Amplitude Continuous Rolling Wave CSS (-30px to +12px) --}}
<style>
    @keyframes agodaPopIn {
        0% { transform: scale(0.90); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    .agoda-7dots-loader .dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        display: inline-block;
        animation: agoda7ColorDeepWave 0.95s cubic-bezier(0.45, 0.05, 0.55, 0.95) infinite;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
        transform: translateZ(0);
        will-change: transform, opacity, box-shadow;
    }

    @keyframes agoda7ColorDeepWave {
        0%, 100% {
            transform: translateY(12px) scale(0.75);
            opacity: 0.4;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        50% {
            transform: translateY(-30px) scale(1.45);
            opacity: 1;
            box-shadow: 0 16px 28px rgba(0, 0, 0, 0.28);
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
