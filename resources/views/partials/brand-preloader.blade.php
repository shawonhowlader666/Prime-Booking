{{-- Agoda Official 5-Color Pristine Bouncing Dots Preloader --}}
<div id="primeGlobalPreloader" class="prime-preloader-backdrop">
    <div class="prime-agoda-loader-card">
        <div class="agoda-dot agoda-dot-1"></div>
        <div class="agoda-dot agoda-dot-2"></div>
        <div class="agoda-dot agoda-dot-3"></div>
        <div class="agoda-dot agoda-dot-4"></div>
        <div class="agoda-dot agoda-dot-5"></div>
    </div>
</div>

<style>
/* ─── Pristine Agoda Preloader Backdrop ─── */
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
    background: rgba(255, 255, 255, 0.88);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    opacity: 1;
    visibility: visible;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

.prime-preloader-backdrop.loaded {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
}

/* ─── Clean Crisp White Card ─── */
.prime-agoda-loader-card {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 22px;
    background: #ffffff;
    border-radius: 9999px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04);
    border: 1px solid #f1f5f9;
}

/* ─── Agoda 5 Brand Color Dots ─── */
.agoda-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    animation: agodaBounce 1.2s infinite ease-in-out both;
}

.agoda-dot-1 { background-color: #3855B3; animation-delay: -0.32s; } /* Agoda Deep Blue */
.agoda-dot-2 { background-color: #55BFE5; animation-delay: -0.24s; } /* Agoda Sky Cyan */
.agoda-dot-3 { background-color: #48BB78; animation-delay: -0.16s; } /* Agoda Vibrant Green */
.agoda-dot-4 { background-color: #F6AD55; animation-delay: -0.08s; } /* Agoda Warm Yellow */
.agoda-dot-5 { background-color: #E53E3E; animation-delay: 0s; }     /* Agoda Crimson Red */

@keyframes agodaBounce {
    0%, 80%, 100% {
        transform: translateY(0) scale(0.85);
        opacity: 0.65;
    }
    40% {
        transform: translateY(-8px) scale(1.15);
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
