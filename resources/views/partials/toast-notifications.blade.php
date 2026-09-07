<!-- Ultra-Smooth Top-Right Toast Notification System -->
<style>
    #pms-toast-container {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 999999;
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-width: 380px;
        width: calc(100% - 48px);
        pointer-events: none;
    }

    .pms-toast {
        pointer-events: auto;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px 18px;
        background: #ffffff;
        color: #1e293b;
        border-radius: 14px;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(0, 0, 0, 0.04);
        position: relative;
        overflow: hidden;
        transform: translateX(120%);
        opacity: 0;
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.4s ease;
    }

    [data-bs-theme="dark"] .pms-toast {
        background: #1e2230;
        color: #f1f5f9;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.08);
    }

    .pms-toast.show {
        transform: translateX(0);
        opacity: 1;
    }

    .pms-toast.hide {
        transform: translateX(120%);
        opacity: 0;
    }

    .pms-toast-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .pms-toast-success .pms-toast-icon {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }

    .pms-toast-error .pms-toast-icon {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }

    .pms-toast-warning .pms-toast-icon {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
    }

    .pms-toast-info .pms-toast-icon {
        background: rgba(99, 102, 241, 0.15);
        color: #6366f1;
    }

    .pms-toast-content {
        flex: 1;
        min-width: 0;
    }

    .pms-toast-title {
        font-weight: 700;
        font-size: 0.92rem;
        margin-bottom: 2px;
        line-height: 1.3;
    }

    .pms-toast-message {
        font-size: 0.84rem;
        color: #64748b;
        line-height: 1.4;
        word-break: break-word;
    }

    [data-bs-theme="dark"] .pms-toast-message {
        color: #94a3b8;
    }

    .pms-toast-close {
        background: none;
        border: none;
        padding: 0;
        color: #94a3b8;
        cursor: pointer;
        font-size: 14px;
        line-height: 1;
        transition: color 0.15s ease;
        margin-top: 2px;
    }

    .pms-toast-close:hover {
        color: #334155;
    }

    [data-bs-theme="dark"] .pms-toast-close:hover {
        color: #f1f5f9;
    }

    .pms-toast-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        width: 100%;
        background: rgba(0, 0, 0, 0.06);
    }

    .pms-toast-progress-bar {
        height: 100%;
        width: 100%;
        transform-origin: left;
        animation: pmsToastProgress 4s linear forwards;
    }

    .pms-toast-success .pms-toast-progress-bar { background: #10b981; }
    .pms-toast-error .pms-toast-progress-bar { background: #ef4444; }
    .pms-toast-warning .pms-toast-progress-bar { background: #f59e0b; }
    .pms-toast-info .pms-toast-progress-bar { background: #6366f1; }

    @keyframes pmsToastProgress {
        from { transform: scaleX(1); }
        to { transform: scaleX(0); }
    }
</style>

<div id="pms-toast-container"></div>

<script>
    window.toast = {
        show: function(type, message, title, duration = 4000) {
            const container = document.getElementById('pms-toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `pms-toast pms-toast-${type}`;

            let iconHtml = '<i class="fa-solid fa-check"></i>';
            let defaultTitle = 'Success';

            if (type === 'error') {
                iconHtml = '<i class="fa-solid fa-triangle-exclamation"></i>';
                defaultTitle = 'Error Notice';
            } else if (type === 'warning') {
                iconHtml = '<i class="fa-solid fa-circle-exclamation"></i>';
                defaultTitle = 'Attention';
            } else if (type === 'info') {
                iconHtml = '<i class="fa-solid fa-circle-info"></i>';
                defaultTitle = 'Information';
            }

            toast.innerHTML = `
                <div class="pms-toast-icon">${iconHtml}</div>
                <div class="pms-toast-content">
                    <div class="pms-toast-title">${title || defaultTitle}</div>
                    <div class="pms-toast-message">${message}</div>
                </div>
                <button type="button" class="pms-toast-close" onclick="this.closest('.pms-toast').remove()">&times;</button>
                <div class="pms-toast-progress">
                    <div class="pms-toast-progress-bar" style="animation-duration: ${duration}ms;"></div>
                </div>
            `;

            container.appendChild(toast);

            // Trigger smooth slide in
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);

            // Auto slide out after duration
            setTimeout(() => {
                toast.classList.remove('show');
                toast.classList.add('hide');
                setTimeout(() => {
                    toast.remove();
                }, 400);
            }, duration);
        },

        success: function(msg, title = 'Success') {
            this.show('success', msg, title);
        },

        error: function(msg, title = 'Error') {
            this.show('error', msg, title, 5000);
        },

        warning: function(msg, title = 'Warning') {
            this.show('warning', msg, title);
        },

        info: function(msg, title = 'Notice') {
            this.show('info', msg, title);
        }
    };

    // Auto-fire Laravel Flash Sessions on page load
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            toast.success("{{ addslashes(session('success')) }}");
        @endif

        @if(session('error'))
            toast.error("{{ addslashes(session('error')) }}");
        @endif

        @if(session('warning'))
            toast.warning("{{ addslashes(session('warning')) }}");
        @endif

        @if(session('status'))
            toast.info("{{ addslashes(session('status')) }}");
        @endif

        @if($errors->any())
            @foreach($errors->all() as $err)
                toast.error("{{ addslashes($err) }}");
            @endforeach
        @endif
    });
</script>
