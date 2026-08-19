{{-- Agoda Official 5-Color Pristine Bouncing Dots Preloader (Pure Floating Dots, Ultra Smooth Wave) --}}
<div id="primeGlobalPreloader" class="prime-preloader-backdrop">
    <div class="prime-agoda-smooth-dots">
        <div class="agoda-dot agoda-dot-1"></div>
        <div class="agoda-dot agoda-dot-2"></div>
        <div class="agoda-dot agoda-dot-3"></div>
        <div class="agoda-dot agoda-dot-4"></div>
        <div class="agoda-dot agoda-dot-5"></div>
    </div>
</div>

<style>
/* ─── Ultra Clean Light Backdrop ─── */
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
    background: rgba(255, 255, 255, 0.94);
    opacity: 1;
    visibility: visible;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

.prime-preloader-backdrop.loaded {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
}

/* ─── Pure Floating Dots Container (No White Card, No Frame) ─── */
.prime-agoda-smooth-dots {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}

/* ─── Ultra Smooth Floating Dots ─── */
.agoda-dot {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    animation: agodaUltraSmoothWave 1.4s cubic-bezier(0.45, 0.05, 0.55, 0.95) infinite;
    will-change: transform;
}

.agoda-dot-1 { background-color: #3f51b5; animation-delay: 0.00s; } /* Agoda Indigo Blue */
.agoda-dot-2 { background-color: #03a9f4; animation-delay: 0.14s; } /* Agoda Sky Cyan */
.agoda-dot-3 { background-color: #00bfa5; animation-delay: 0.28s; } /* Agoda Emerald Green */
.agoda-dot-4 { background-color: #ff9800; animation-delay: 0.42s; } /* Agoda Warm Amber */
.agoda-dot-5 { background-color: #f44336; animation-delay: 0.56s; } /* Agoda Coral Red */

@keyframes agodaUltraSmoothWave {
    0%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-16px);
    }
    60% {
        transform: translateY(4px);
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
