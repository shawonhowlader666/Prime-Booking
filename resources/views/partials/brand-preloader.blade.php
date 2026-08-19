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
.prime-brand-smooth-dots {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

/* ─── 7 Prime Logo Colors with Ultra Smooth Wave ─── */
.prime-dot {
    width: 13px;
    height: 13px;
    border-radius: 50%;
    animation: primeUltraSmoothWave 1.4s cubic-bezier(0.45, 0.05, 0.55, 0.95) infinite;
    will-change: transform;
}

.prime-dot-1 { background-color: #3b3b98; animation-delay: 0.00s; } /* Deep Navy */
.prime-dot-2 { background-color: #2b59c3; animation-delay: 0.10s; } /* Royal Blue */
.prime-dot-3 { background-color: #00b4d8; animation-delay: 0.20s; } /* Bright Cyan */
.prime-dot-4 { background-color: #10b981; animation-delay: 0.30s; } /* Emerald Green */
.prime-dot-5 { background-color: #ffb703; animation-delay: 0.40s; } /* Golden Yellow */
.prime-dot-6 { background-color: #fb8500; animation-delay: 0.50s; } /* Vibrant Orange */
.prime-dot-7 { background-color: #e63946; animation-delay: 0.60s; } /* Crimson Red */

@keyframes primeUltraSmoothWave {
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
