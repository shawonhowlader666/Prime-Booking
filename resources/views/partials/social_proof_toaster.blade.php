<!-- Agoda-Style Live Social Proof & High-Conversion Booking Toaster (Bottom-Left Float) -->
<style>
    body.modal-open #social-proof-toast {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }
    #social-proof-toast {
        position: fixed;
        bottom: 24px;
        left: 24px;
        z-index: 999990;
        background: rgba(15, 23, 42, 0.94);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 12px;
        padding: 12px 16px;
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.35), 0 2px 8px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        gap: 12px;
        max-width: 380px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 13px;
        transform: translateY(120px) scale(0.92);
        opacity: 0;
        pointer-events: none;
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.35s ease;
    }

    @media (max-width: 768px) {
        #social-proof-toast {
            bottom: 76px;
            left: 12px;
            right: 12px;
            max-width: calc(100% - 24px);
            padding: 10px 14px;
            font-size: 12px;
        }
    }

    #social-proof-toast.show {
        transform: translateY(0) scale(1);
        opacity: 1;
        pointer-events: auto;
    }

    .sp-avatar-pulse {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #00B074, #007f5f);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 14px;
        color: #ffffff;
        position: relative;
    }

    .sp-avatar-pulse::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 2px solid #00B074;
        animation: spPulse 2s infinite ease-out;
    }

    @keyframes spPulse {
        0% { transform: scale(1); opacity: 0.8; }
        100% { transform: scale(1.6); opacity: 0; }
    }

    .sp-content {
        flex-grow: 1;
        line-height: 1.35;
    }

    .sp-title {
        font-weight: 700;
        color: #ffffff;
        font-size: 13px;
        margin-bottom: 2px;
    }

    .sp-time {
        font-size: 11px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .sp-close {
        background: transparent;
        border: none;
        color: #64748b;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
        padding: 2px;
        transition: color 0.15s ease;
    }

    .sp-close:hover {
        color: #ffffff;
    }

    @media (max-width: 575px) {
        #social-proof-toast {
            left: 12px;
            right: 12px;
            bottom: 16px;
            max-width: 100%;
        }
    }
</style>

<div id="social-proof-toast" role="status" aria-live="polite">
    <div class="sp-avatar-pulse" id="spIcon">
        <i class="fa-solid fa-circle-check text-white"></i>
    </div>
    <div class="sp-content">
        <div class="sp-title" id="spTitle"></div>
        <div class="sp-time">
            <span id="spSubtitle"></span>
        </div>
    </div>
    <button type="button" class="sp-close" id="spCloseBtn" aria-label="Close">&times;</button>
</div>

<script>
(function () {
    // Real bookings from the last 24 hours only. If there are none, nothing is shown.
    var items = [];
    var index = 0;
    var dismissed = false;
    var toast, title, sub, closeBtn;

    function ago(m) {
        if (m < 60) return m + (m === 1 ? ' min ago' : ' mins ago');
        var h = Math.round(m / 60);
        return h + (h === 1 ? ' hour ago' : ' hours ago');
    }

    function showNext() {
        if (!toast || dismissed || !items.length) return;
        var it = items[index];
        index = (index + 1) % items.length;
        title.textContent = 'A guest booked ' + it.property;
        sub.textContent = it.city + ' · ' + ago(it.minutes_ago);
        toast.classList.add('show');
        setTimeout(function () {
            toast.classList.remove('show');
            if (index !== 0) setTimeout(showNext, 22000);
        }, 5000);
    }

    document.addEventListener('DOMContentLoaded', function () {
        toast = document.getElementById('social-proof-toast');
        if (!toast) return;
        title = document.getElementById('spTitle');
        sub = document.getElementById('spSubtitle');
        closeBtn = document.getElementById('spCloseBtn');
        closeBtn.addEventListener('click', function () { dismissed = true; toast.classList.remove('show'); });

        fetch(@json(route('social-proof.recent')), { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.ok ? r.json() : { items: [] }; })
            .then(function (d) { items = (d && d.items) || []; if (items.length) setTimeout(showNext, 6000); })
            .catch(function () {});
    });
})();</script>
