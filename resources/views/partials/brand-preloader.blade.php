{{-- Agoda Official 5-Color Pristine Bouncing Dots Preloader --}}
<div id="primeGlobalPreloader" class="prime-preloader-backdrop">
    <div class="prime-agoda-capsule-card">
        <div class="agoda-dot agoda-dot-1"></div>
        <div class="agoda-dot agoda-dot-2"></div>
        <div class="agoda-dot agoda-dot-3"></div>
        <div class="agoda-dot agoda-dot-4"></div>
        <div class="agoda-dot agoda-dot-5"></div>
    </div>
</div>

<style>
/* ─── Agoda Crystal Clean Light Backdrop ─── */
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
    background: rgba(255, 255, 255, 0.90);
    opacity: 1;
    visibility: visible;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

.prime-preloader-backdrop.loaded {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
}

/* ─── 100% Identical Agoda White Capsule Pill Card ─── */
.prime-agoda-capsule-card {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 16px 26px;
    background: #ffffff;
    border-radius: 9999px;
    box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(0, 0, 0, 0.04);
}

/* ─── Agoda 5 Brand Color Dots (Exact Colors from User Screenshot) ─── */
.agoda-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    animation: agodaWaveBounce 1.2s infinite ease-in-out both;
}

.agoda-dot-1 { background-color: #536dfe; animation-delay: -0.32s; } /* Agoda Indigo Blue */
.agoda-dot-2 { background-color: #4fc3f7; animation-delay: -0.24s; } /* Agoda Sky Cyan */
.agoda-dot-3 { background-color: #26a69a; animation-delay: -0.16s; } /* Agoda Teal Green */
.agoda-dot-4 { background-color: #ffa726; animation-delay: -0.08s; } /* Agoda Warm Amber */
.agoda-dot-5 { background-color: #ef5350; animation-delay: 0s; }     /* Agoda Soft Crimson */

@keyframes agodaWaveBounce {
    0%, 80%, 100% {
        transform: translateY(0) scale(0.9);
        opacity: 0.85;
    }
    40% {
        transform: translateY(-9px) scale(1.1);
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
