{{-- 7-Color Rainbow Wave Glassmorphism Brand Loader --}}
<div id="primeGlobalPreloader" class="prime-preloader-backdrop">
    <div class="prime-preloader-card">
        {{-- Brand Logo or Icon --}}
        <div class="prime-preloader-brand">
            <img src="{{ asset('images/logo.png') }}" alt="Prime Booking" class="prime-preloader-logo" onerror="this.style.display='none'; document.getElementById('primePreloaderFallbackTitle').style.display='block';">
            <h4 id="primePreloaderFallbackTitle" class="prime-preloader-title" style="display:none;">PRIME BOOKING</h4>
        </div>

        {{-- 7-Color Animated Harmonic Wave --}}
        <div class="prime-wave-container" title="Loading Prime Experience...">
            <div class="prime-wave-bar bar-1" style="--bar-color: #EF4444; --delay: 0.00s;"></div>
            <div class="prime-wave-bar bar-2" style="--bar-color: #F97316; --delay: 0.12s;"></div>
            <div class="prime-wave-bar bar-3" style="--bar-color: #F59E0B; --delay: 0.24s;"></div>
            <div class="prime-wave-bar bar-4" style="--bar-color: #10B981; --delay: 0.36s;"></div>
            <div class="prime-wave-bar bar-5" style="--bar-color: #06B6D4; --delay: 0.48s;"></div>
            <div class="prime-wave-bar bar-6" style="--bar-color: #2067E1; --delay: 0.60s;"></div>
            <div class="prime-wave-bar bar-7" style="--bar-color: #8B5CF6; --delay: 0.72s;"></div>
        </div>

        {{-- Flowing Rainbow Beam Line --}}
        <div class="prime-rainbow-beam"></div>

        {{-- Shimmering Text --}}
        <div class="prime-preloader-status">
            <span class="prime-status-text">One moment, preparing your experience...</span>
        </div>
    </div>
</div>

<style>
/* ─── Glassmorphism Preloader Backdrop ─── */
.prime-preloader-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: 9999999;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(10, 18, 36, 0.78);
    backdrop-filter: blur(22px) saturate(190%);
    -webkit-backdrop-filter: blur(22px) saturate(190%);
    opacity: 1;
    visibility: visible;
    transition: opacity 0.4s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.4s ease;
}

.prime-preloader-backdrop.loaded {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
}

/* ─── Frosted Glass Card ─── */
.prime-preloader-card {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.16);
    box-shadow: 0 30px 70px -10px rgba(0, 0, 0, 0.6), 
                inset 0 1px 1px rgba(255, 255, 255, 0.25),
                0 0 35px rgba(32, 103, 225, 0.18);
    border-radius: 24px;
    padding: 34px 42px;
    text-align: center;
    width: 90%;
    max-width: 380px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 18px;
    transform: scale(1);
    animation: primeCardPop 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes primeCardPop {
    from { opacity: 0; transform: scale(0.92) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

/* ─── Brand Logo ─── */
.prime-preloader-brand {
    display: flex;
    align-items: center;
    justify-content: center;
    max-height: 48px;
}

.prime-preloader-logo {
    max-height: 44px;
    max-width: 200px;
    object-fit: contain;
    filter: drop-shadow(0 4px 12px rgba(0,0,0,0.3));
}

.prime-preloader-title {
    color: #ffffff;
    font-size: 18px;
    font-weight: 800;
    letter-spacing: 1.5px;
    margin: 0;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    text-shadow: 0 2px 10px rgba(0,0,0,0.5);
}

/* ─── 7-Color Rainbow Wave (Left to Right Harmonic Motion) ─── */
.prime-wave-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    height: 46px;
    padding: 0 10px;
}

.prime-wave-bar {
    width: 6.5px;
    height: 14px;
    border-radius: 20px;
    background-color: var(--bar-color);
    box-shadow: 0 0 14px var(--bar-color), 0 0 4px rgba(255,255,255,0.8);
    animation: primeRainbowWave 1.25s ease-in-out infinite alternate;
    animation-delay: var(--delay);
    transform-origin: center center;
}

@keyframes primeRainbowWave {
    0% {
        height: 12px;
        transform: scaleY(0.7);
        opacity: 0.45;
        filter: brightness(0.9);
    }
    50% {
        height: 42px;
        transform: scaleY(1.35);
        opacity: 1;
        filter: brightness(1.35);
        box-shadow: 0 0 22px var(--bar-color), 0 0 8px #ffffff;
    }
    100% {
        height: 14px;
        transform: scaleY(0.75);
        opacity: 0.55;
        filter: brightness(0.95);
    }
}

/* ─── Flowing 7-Color Rainbow Gradient Beam ─── */
.prime-rainbow-beam {
    width: 100%;
    height: 3px;
    border-radius: 10px;
    background: linear-gradient(90deg, 
        #EF4444 0%, 
        #F97316 16.6%, 
        #F59E0B 33.3%, 
        #10B981 50%, 
        #06B6D4 66.6%, 
        #2067E1 83.3%, 
        #8B5CF6 100%
    );
    background-size: 200% 100%;
    animation: primeBeamFlow 2s linear infinite;
    box-shadow: 0 0 12px rgba(32, 103, 225, 0.4);
}

@keyframes primeBeamFlow {
    0% { background-position: 0% 50%; }
    100% { background-position: 200% 50%; }
}

/* ─── Status Subtext ─── */
.prime-preloader-status {
    margin-top: -4px;
}

.prime-status-text {
    font-size: 12px;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.75);
    letter-spacing: 0.4px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    animation: primeStatusPulse 2s ease-in-out infinite;
}

@keyframes primeStatusPulse {
    0%, 100% { opacity: 0.7; }
    50% { opacity: 1; color: #ffffff; }
}
</style>

<script>
// Auto dismiss preloader when document is ready
(function() {
    function hidePrimePreloader() {
        const loader = document.getElementById('primeGlobalPreloader');
        if (loader && !loader.classList.contains('loaded')) {
            loader.classList.add('loaded');
            setTimeout(function() {
                loader.style.display = 'none';
            }, 450);
        }
    }

    if (document.readyState === 'complete') {
        setTimeout(hidePrimePreloader, 350);
    } else {
        window.addEventListener('load', function() {
            setTimeout(hidePrimePreloader, 350);
        });
    }

    // Safety fallback: auto-hide after 2.5s maximum so page is never blocked
    setTimeout(hidePrimePreloader, 2500);

    // Global helper to show loader programmatically (e.g. during form submit)
    window.showPrimeLoader = function(customText) {
        const loader = document.getElementById('primeGlobalPreloader');
        if (loader) {
            if (customText) {
                const textEl = loader.querySelector('.prime-status-text');
                if (textEl) textEl.textContent = customText;
            }
            loader.style.display = 'flex';
            loader.classList.remove('loaded');
        }
    };

    window.hidePrimeLoader = hidePrimePreloader;
})();
</script>
