{{-- 7-Color Rainbow Bouncing Dots Preloader (Agoda Style) --}}
<div id="primeGlobalPreloader" class="prime-preloader-backdrop">
    {{-- 7-Color Animated Harmonic Dots --}}
    <div class="prime-wave-container" title="Loading...">
        <div class="prime-wave-dot" style="--dot-color: #EF4444; --delay: 0.00s;"></div>
        <div class="prime-wave-dot" style="--dot-color: #F97316; --delay: 0.12s;"></div>
        <div class="prime-wave-dot" style="--dot-color: #F59E0B; --delay: 0.24s;"></div>
        <div class="prime-wave-dot" style="--dot-color: #10B981; --delay: 0.36s;"></div>
        <div class="prime-wave-dot" style="--dot-color: #06B6D4; --delay: 0.48s;"></div>
        <div class="prime-wave-dot" style="--dot-color: #2067E1; --delay: 0.60s;"></div>
        <div class="prime-wave-dot" style="--dot-color: #8B5CF6; --delay: 0.72s;"></div>
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

/* ─── 7-Color Rainbow Dots (Agoda Style Bouncing) ─── */
.prime-wave-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    height: 60px;
    padding: 20px 30px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.16);
    box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.5), 
                inset 0 1px 1px rgba(255, 255, 255, 0.1);
    border-radius: 40px;
    animation: primeCardPop 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes primeCardPop {
    from { opacity: 0; transform: scale(0.92) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.prime-wave-dot {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background-color: var(--dot-color);
    box-shadow: 0 0 12px var(--dot-color), 0 0 4px rgba(255,255,255,0.6);
    animation: primeRainbowDotWave 1.25s ease-in-out infinite alternate;
    animation-delay: var(--delay);
}

@keyframes primeRainbowDotWave {
    0%, 20% {
        transform: translateY(10px) scale(0.85);
        opacity: 0.5;
        filter: brightness(0.9);
    }
    50% {
        transform: translateY(-12px) scale(1.15);
        opacity: 1;
        filter: brightness(1.3);
        box-shadow: 0 0 20px var(--dot-color), 0 0 8px #ffffff;
    }
    80%, 100% {
        transform: translateY(10px) scale(0.85);
        opacity: 0.5;
        filter: brightness(0.9);
    }
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
    window.showPrimeLoader = function() {
        const loader = document.getElementById('primeGlobalPreloader');
        if (loader) {
            loader.style.display = 'flex';
            loader.classList.remove('loaded');
        }
    };

    window.hidePrimeLoader = hidePrimePreloader;
})();
</script>
