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
    gap: 8px;
}

/* ─── Exact Agoda Official Bouncing Dots ─── */
.prime-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    animation: agodaOfficialBounce 1.2s infinite ease-in-out both;
    transform: translateZ(0);
    -webkit-transform: translateZ(0);
}

.prime-dot-1 { background: #232f6b; animation-delay: -0.48s; } /* Navy */
.prime-dot-2 { background: #1a56db; animation-delay: -0.40s; } /* Blue */
.prime-dot-3 { background: #00a8cc; animation-delay: -0.32s; } /* Cyan */
.prime-dot-4 { background: #059669; animation-delay: -0.24s; } /* Emerald */
.prime-dot-5 { background: #d97706; animation-delay: -0.16s; } /* Amber */
.prime-dot-6 { background: #ea580c; animation-delay: -0.08s; } /* Orange */
.prime-dot-7 { background: #dc2626; animation-delay: 0.00s; }  /* Red */

@keyframes agodaOfficialBounce {
    0%, 80%, 100% {
        transform: translateY(0) scale(0.9);
        opacity: 0.75;
    }
    40% {
        transform: translateY(-10px) scale(1.12);
        opacity: 1;
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
