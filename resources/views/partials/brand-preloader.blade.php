{{-- Agoda Official 5-Color Pristine Bouncing Dots Preloader (Direct Floating Dots, No Blurry Card) --}}
<div id="primeGlobalPreloader" class="prime-preloader-backdrop">
    <div class="prime-agoda-dots-wrapper">
        <div class="agoda-dot agoda-dot-1"></div>
        <div class="agoda-dot agoda-dot-2"></div>
        <div class="agoda-dot agoda-dot-3"></div>
        <div class="agoda-dot agoda-dot-4"></div>
        <div class="agoda-dot agoda-dot-5"></div>
    </div>
</div>

<style>
/* ─── Pure Crystal Clean Preloader Backdrop (No Blur/Fog) ─── */
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
    background: rgba(255, 255, 255, 0.92);
    opacity: 1;
    visibility: visible;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

.prime-preloader-backdrop.loaded {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
}

/* ─── Pure Floating Dots Wrapper ─── */
.prime-agoda-dots-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

/* ─── Ultra Solid & Sharp Agoda 5 Brand Color Dots (100% Solid, No Opacity Loss) ─── */
.agoda-dot {
    width: 13px;
    height: 13px;
    border-radius: 50%;
    animation: agodaBounce 1.1s infinite ease-in-out both;
    opacity: 1 !important;
}

.agoda-dot-1 { background-color: #2b59c3; animation-delay: -0.32s; } /* Agoda Royal Blue */
.agoda-dot-2 { background-color: #00bcd4; animation-delay: -0.24s; } /* Agoda Cyan */
.agoda-dot-3 { background-color: #10b981; animation-delay: -0.16s; } /* Agoda Green */
.agoda-dot-4 { background-color: #f59e0b; animation-delay: -0.08s; } /* Agoda Amber Yellow */
.agoda-dot-5 { background-color: #ef4444; animation-delay: 0s; }     /* Agoda Red */

@keyframes agodaBounce {
    0%, 80%, 100% {
        transform: translateY(0) scale(0.9);
    }
    40% {
        transform: translateY(-14px) scale(1.15);
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
