<!-- Agoda-Style Live Social Proof & High-Conversion Booking Toaster (Bottom-Left Float) -->
<style>
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

<div id="social-proof-toast">
    <div class="sp-avatar-pulse" id="spIcon">
        <i class="fa-solid fa-fire text-warning"></i>
    </div>
    <div class="sp-content">
        <div class="sp-title" id="spTitle">Shafiq from Dhaka</div>
        <div class="sp-time">
            <i class="fa-solid fa-circle-check text-emerald-400" style="color: #00b074;"></i>
            <span id="spSubtitle">Booked Sea Crown Hotel (2 mins ago)</span>
        </div>
    </div>
    <button type="button" class="sp-close" id="spCloseBtn" aria-label="Close">&times;</button>
</div>

<script>
(function() {
    const notifications = [
        { title: 'Shafiq from Dhaka', subtitle: 'Booked Sea Crown Hotel, Cox\'s Bazar', time: '2 mins ago', icon: 'fa-solid fa-fire' },
        { title: 'High Demand Alert', subtitle: '6 travelers are looking at Sylhet Resorts', time: 'Just now', icon: 'fa-solid fa-bolt' },
        { title: 'Tanvir from Chittagong', subtitle: 'Reserved Deluxe Room at Hotel The Cox Today', time: '5 mins ago', icon: 'fa-solid fa-circle-check' },
        { title: 'Secret Member Deal', subtitle: 'Member saved ৳1,800 on Saint Martin Resort', time: '8 mins ago', icon: 'fa-solid fa-tag' },
        { title: 'Anika from Rajshahi', subtitle: 'Booked Grand Sultan Tea Resort, Sreemangal', time: '12 mins ago', icon: 'fa-solid fa-hotel' }
    ];

    let currentIndex = 0;
    let toastElem, titleElem, subElem, iconElem, closeBtn;

    function initSocialProof() {
        toastElem = document.getElementById('social-proof-toast');
        if (!toastElem) return;

        titleElem = document.getElementById('spTitle');
        subElem = document.getElementById('spSubtitle');
        iconElem = document.getElementById('spIcon');
        closeBtn = document.getElementById('spCloseBtn');

        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                toastElem.classList.remove('show');
            });
        }

        // Start cycle after 4.5 seconds
        setTimeout(showNextSocialNotification, 4500);
    }

    function showNextSocialNotification() {
        if (!toastElem) return;

        const current = notifications[currentIndex];
        currentIndex = (currentIndex + 1) % notifications.length;

        titleElem.textContent = current.title;
        subElem.textContent = `${current.subtitle} (${current.time})`;
        iconElem.innerHTML = `<i class="${current.icon} text-white"></i>`;

        toastElem.classList.add('show');

        // Hide after 5 seconds
        setTimeout(() => {
            if (toastElem) toastElem.classList.remove('show');
            // Re-trigger next notification in 22 seconds
            setTimeout(showNextSocialNotification, 22000);
        }, 5000);
    }

    document.addEventListener('DOMContentLoaded', initSocialProof);
})();
</script>
