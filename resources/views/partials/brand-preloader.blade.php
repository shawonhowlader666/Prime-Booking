{{-- Prime Booking 7-Color Rainbow Bouncing Dots Preloader (Pure Floating Dots, Ultra Smooth Wave) --}}
<div id="primeGlobalPreloader" class="prime-preloader-backdrop">
    <div class="prime-brand-smooth-dots">
        <div class="prime-dot prime-dot-1"></div>
        <div class="prime-dot prime-dot-2"></div>
        <div class="prime-dot prime-dot-3"></div>
        <div class="prime-dot prime-dot-4"></div>
        <div class="prime-dot prime-dot-5"></div>
        <div class="prime-dot prime-dot-6"></div>
        <div class="prime-dot prime-dot-7"></div>
    </div>
</div>

<style>
/* ─── 100% Solid Pure White Backdrop (Zero Transparency, Zero Blur) ─── */
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
    background: #ffffff !important;
    opacity: 1;
    visibility: visible;
    transition: opacity 0.25s ease, visibility 0.25s ease;
}

.prime-preloader-backdrop.loaded {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
}

/* ─── Pure Floating Dots Container ─── */
.prime-brand-smooth-dots {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    padding: 20px;
}

/* ─── Razor Sharp World-Class Wave Dots ─── */
.prime-dot {
    width: 11px;
    height: 11px;
    border-radius: 50%;
    animation: primeProWave 1.2s cubic-bezier(0.445, 0.05, 0.55, 0.95) infinite both;
    transform: translateZ(0);
    -webkit-transform: translateZ(0);
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

.prime-dot-1 { background: #232f6b; animation-delay: -0.60s; } /* Navy */
.prime-dot-2 { background: #1a56db; animation-delay: -0.50s; } /* Blue */
.prime-dot-3 { background: #00a8cc; animation-delay: -0.40s; } /* Cyan */
.prime-dot-4 { background: #059669; animation-delay: -0.30s; } /* Emerald */
.prime-dot-5 { background: #d97706; animation-delay: -0.20s; } /* Amber */
.prime-dot-6 { background: #ea580c; animation-delay: -0.10s; } /* Orange */
.prime-dot-7 { background: #dc2626; animation-delay: 0.00s; }  /* Red */

@keyframes primeProWave {
    0%, 100% {
        transform: translateY(0) scale(0.92);
        opacity: 0.85;
    }
    40% {
        transform: translateY(-12px) scale(1.1);
        opacity: 1;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
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
