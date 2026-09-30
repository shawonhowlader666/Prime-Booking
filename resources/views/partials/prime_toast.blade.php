<!-- Prime Booking Master Universal Toast Notification System (Ultra Smooth Silky Physics, Bullhorn Icons, A to Z Coverage) -->
<style>
    #prime-toast-container {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 9999999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
    }

    .prime-toast {
        pointer-events: auto;
        min-width: 290px;
        max-width: 440px;
        padding: 14px 18px;
        border-radius: 8px;
        color: #ffffff !important;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 14px 36px rgba(0, 0, 0, 0.32), 0 4px 12px rgba(0, 0, 0, 0.15);
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 13.5px;
        font-weight: 600;
        line-height: 1.4;
        position: relative;
        overflow: hidden;
        animation: primeToastSlideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease, max-height 0.35s ease, margin 0.35s ease, padding 0.35s ease;
        will-change: transform, opacity;
    }

    .prime-toast:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 18px 42px rgba(0, 0, 0, 0.4);
    }

    .prime-toast.hiding {
        animation: primeToastSlideOut 0.35s cubic-bezier(0.7, 0, 0.84, 0) forwards;
    }

    /* Types & Colors matching NexusTutorBD Standard */
    .prime-toast.success {
        background-color: #00B074 !important;
    }

    .prime-toast.error {
        background-color: #E63946 !important;
    }

    .prime-toast.warning {
        background-color: #D97706 !important;
    }

    .prime-toast.info {
        background-color: #2563EB !important;
    }

    .prime-toast-icon {
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 16px;
        color: #ffffff !important;
    }

    .prime-toast-icon svg {
        width: 18px;
        height: 18px;
        fill: currentColor;
    }

    .prime-toast-body {
        flex-grow: 1;
        word-break: break-word;
        color: #ffffff !important;
        font-size: 13.5px;
        font-weight: 600;
    }

    .prime-toast-close {
        background: transparent;
        border: none;
        color: rgba(255, 255, 255, 0.85) !important;
        cursor: pointer;
        font-size: 20px;
        line-height: 1;
        padding: 0 4px;
        margin-left: 4px;
        transition: color 0.2s ease, transform 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .prime-toast-close:hover {
        color: #ffffff !important;
        transform: scale(1.25);
    }

    .prime-toast-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        background: rgba(255, 255, 255, 0.45);
        width: 100%;
        transform-origin: left;
    }

    @keyframes primeToastSlideIn {
        0% {
            transform: translateX(120%) scale(0.92);
            opacity: 0;
        }
        70% {
            transform: translateX(-4px) scale(1.01);
            opacity: 1;
        }
        100% {
            transform: translateX(0) scale(1);
            opacity: 1;
        }
    }

    @keyframes primeToastSlideOut {
        0% {
            transform: translateX(0) scale(1);
            opacity: 1;
        }
        30% {
            transform: translateX(-6px) scale(1.01);
            opacity: 1;
        }
        100% {
            transform: translateX(125%) scale(0.92);
            opacity: 0;
        }
    }

    @media (max-width: 575px) {
        #prime-toast-container {
            top: 16px;
            right: 12px;
            left: 12px;
            align-items: center;
        }
        .prime-toast {
            width: 100%;
            max-width: 100%;
        }
    }
</style>

<div id="prime-toast-container"></div>

<script>
    (function() {
        // Bullhorn SVG Icon for 0ms latency fallback
        const bullhornSvg = `<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M480 32c0-12.9-7.8-24.6-19.8-29.6s-25.7-2.2-34.9 6.9L311.9 123l-79-79c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l51.5 51.5L62.7 317.2c-15.6 15.6-24.3 36.8-24.3 58.8 0 45.9 37.3 83.2 83.2 83.2 22 0 43.2-8.8 58.8-24.3L368.2 247.1l51.5 51.5c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-79-79 113.8-113.4c9.2-9.2 11.9-22.9 6.9-34.9S480 32 480 32zM151.7 406.8c-7.5 7.5-17.7 11.7-28.3 11.7-22.1 0-40-17.9-40-40 0-10.6 4.2-20.8 11.7-28.3L282.8 162.5l56.6 56.6L151.7 406.8zM416 195.9l-27.9-27.9L440.2 116 416 195.9z"/></svg>`;

        /**
         * Global Toast Popup Notification (Smooth Right Slide with Bullhorn Icon & Hover-Pause)
         * @param {string} message - Message text
         * @param {string} type - 'success' | 'error' | 'warning' | 'info'
         * @param {number} duration - Milliseconds to stay visible (default: 3500)
         */
        window.showToast = function(message, type = 'success', duration = 3500) {
            if (!message || String(message).trim() === '') return;

            let container = document.getElementById('prime-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'prime-toast-container';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            toast.className = `prime-toast ${type}`;

            toast.innerHTML = `
                <div class="prime-toast-icon">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <div class="prime-toast-body">${message}</div>
                <button class="prime-toast-close" aria-label="Close">&times;</button>
                <div class="prime-toast-progress"></div>
            `;

            // Fallback to inline SVG if fontawesome icon isn't rendered
            const iconElem = toast.querySelector('.prime-toast-icon i');
            if (iconElem && (!window.FontAwesome && !document.querySelector('link[href*="font-awesome"]'))) {
                toast.querySelector('.prime-toast-icon').innerHTML = bullhornSvg;
            }

            container.appendChild(toast);

            const progressBar = toast.querySelector('.prime-toast-progress');
            if (progressBar && duration > 0) {
                progressBar.style.transition = `transform ${duration}ms linear`;
                requestAnimationFrame(() => {
                    progressBar.style.transform = 'scaleX(0)';
                });
            }

            let isPaused = false;
            let timerId = null;
            let remainingTime = duration;
            let startTime = Date.now();

            const removeToast = () => {
                if (toast.classList.contains('hiding')) return;
                toast.classList.add('hiding');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 350);
            };

            const startTimer = () => {
                if (duration <= 0) return;
                startTime = Date.now();
                timerId = setTimeout(removeToast, remainingTime);
            };

            const pauseTimer = () => {
                if (duration <= 0) return;
                clearTimeout(timerId);
                remainingTime -= (Date.now() - startTime);
                if (progressBar) {
                    const computedWidth = window.getComputedStyle(progressBar).transform;
                    progressBar.style.transition = 'none';
                    progressBar.style.transform = computedWidth;
                }
            };

            const resumeTimer = () => {
                if (duration <= 0 || remainingTime <= 0) return;
                startTime = Date.now();
                if (progressBar) {
                    progressBar.style.transition = `transform ${remainingTime}ms linear`;
                    progressBar.style.transform = 'scaleX(0)';
                }
                timerId = setTimeout(removeToast, remainingTime);
            };

            toast.addEventListener('mouseenter', pauseTimer);
            toast.addEventListener('mouseleave', resumeTimer);

            const closeBtn = toast.querySelector('.prime-toast-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    removeToast();
                });
            }

            startTimer();
        };

        // Universal Aliases for all roles & modules
        window.notify = window.showToast;
        window.toastSuccess = (msg, d) => window.showToast(msg, 'success', d);
        window.toastError = (msg, d) => window.showToast(msg, 'error', d);
        window.toastWarning = (msg, d) => window.showToast(msg, 'warning', d);
        window.toastInfo = (msg, d) => window.showToast(msg, 'info', d);

        // Gracefully bridge standard window.alert to smooth right-sliding bullhorn toast
        const originalAlert = window.alert;
        window.alert = function(msg) {
            if (!msg) return;
            const strMsg = String(msg);
            const lower = strMsg.toLowerCase();
            if (lower.includes('success') || lower.includes('copied') || lower.includes('সফল') || lower.includes('ধন্যবাদ') || lower.includes('গ্রহণ') || lower.includes('applied')) {
                window.showToast(strMsg, 'success', 3500);
            } else if (lower.includes('error') || lower.includes('fail') || lower.includes('denied') || lower.includes('ভুল') || lower.includes('ব্যর্থ') || lower.includes('not found') || lower.includes('invalid') || lower.includes('required')) {
                window.showToast(strMsg, 'error', 4000);
            } else {
                window.showToast(strMsg, 'warning', 3500);
            }
        };

        // Flash Session Messages Auto-Trigger on Load
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                window.showToast(@json(session('success')), 'success');
            @endif

            @if(session('error'))
                window.showToast(@json(session('error')), 'error');
            @endif

            @if(session('warning'))
                window.showToast(@json(session('warning')), 'warning');
            @endif

            @if(session('info'))
                window.showToast(@json(session('info')), 'info');
            @endif

            @if(isset($errors) && $errors->any())
                @foreach($errors->all() as $error)
                    window.showToast(@json($error), 'error');
                @endforeach
            @endif
        });
    })();
</script>
