{{-- 
========================================================================
 Prime Booking: Floating Cashback & App QR Code Popups (1:1 Agoda Parity)
========================================================================
--}}
<style>
/* Master Container */
.prime-floating-widgets-wrap {
    position: fixed;
    bottom: 85px;
    right: 28px;
    z-index: 1040;
    display: flex;
    flex-direction: column;
    gap: 16px;
    align-items: flex-end;
    pointer-events: none;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
}

.prime-floating-widgets-wrap * {
    box-sizing: border-box;
}

.prime-floating-card {
    pointer-events: auto;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.14), 0 2px 8px rgba(0, 0, 0, 0.06);
    border: 1px solid #eef2f6;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
}

/* ========================================================================
 1. TOP CARD: PrimeCash Rewards & Cashback Popup
======================================================================== */
.prime-reward-popup {
    width: 320px;
    padding: 16px 18px 14px;
    animation: primePopIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.prime-reward-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 6px;
}

.prime-reward-logo-h {
    font-family: 'Georgia', serif;
    font-size: 22px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
    font-style: italic;
    letter-spacing: -0.5px;
}

.prime-brand-logo-pill {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
}

.prime-brand-logo-pill .brand-text {
    font-size: 11px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: 0.8px;
    text-transform: lowercase;
}

.prime-brand-logo-pill .dots-row {
    display: flex;
    gap: 3px;
}

.prime-brand-logo-pill .dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
}

.prime-reward-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}

.prime-icon-btn {
    background: transparent;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px;
    font-size: 13px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
}

.prime-icon-btn:hover {
    color: #334155;
    background: #f1f5f9;
}

.prime-reward-title {
    font-size: 21px;
    font-weight: 800;
    color: #0f172a;
    text-align: center;
    margin: 12px 0 14px;
    letter-spacing: -0.3px;
}

.prime-reward-btn {
    width: 100%;
    background: #d84315;
    color: #ffffff;
    font-weight: 700;
    font-size: 14px;
    padding: 10px 16px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 2px 6px rgba(216, 67, 21, 0.25);
}

.prime-reward-btn:hover {
    background: #bf360c;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(216, 67, 21, 0.35);
}

.prime-reward-btn.activated {
    background: #16a34a !important;
    box-shadow: 0 2px 6px rgba(22, 163, 74, 0.25) !important;
}

.prime-reward-footer-note {
    font-size: 10.5px;
    color: #64748b;
    text-align: center;
    margin-top: 10px;
    margin-bottom: 0;
    line-height: 1.4;
}

.prime-reward-footer-note a {
    color: #475569;
    text-decoration: underline;
}

/* ========================================================================
 2. BOTTOM CARD: App QR Code / Instant Savings Card (Exact Screenshot Parity)
======================================================================== */
.prime-app-popup {
    width: 290px;
    padding: 22px 20px 0;
    animation: primePopIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    position: relative;
}

/* Tooltip Downward Arrow pointing to the blue button */
.prime-app-popup::after {
    content: '';
    position: absolute;
    bottom: -8px;
    right: 28px;
    width: 16px;
    height: 16px;
    background: #ffffff;
    transform: rotate(45deg);
    border-right: 1px solid #eef2f6;
    border-bottom: 1px solid #eef2f6;
    box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.04);
}

.prime-app-title {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    text-align: center;
    line-height: 1.25;
    margin-bottom: 6px;
    letter-spacing: -0.3px;
}

.prime-app-subtitle {
    font-size: 12.5px;
    color: #475569;
    text-align: center;
    margin-bottom: 18px;
    line-height: 1.35;
}

/* Smartphone Mockup Frame (Pixel Perfect with Agoda Screenshot) */
.prime-phone-mockup {
    width: 200px;
    margin: 0 auto;
    background: #ffffff;
    border: 7px solid #9baec8;
    border-bottom: none;
    border-radius: 36px 36px 0 0;
    padding: 0 10px 0;
    position: relative;
    box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.03);
}

/* Phone Top Notch */
.prime-phone-notch {
    width: 68px;
    height: 11px;
    background: #9baec8;
    border-radius: 0 0 12px 12px;
    margin: 0 auto 8px;
}

/* Brand Logo inside phone */
.prime-phone-brand {
    text-align: center;
    margin-bottom: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
}

.prime-phone-brand .brand-name {
    font-size: 13px;
    font-weight: 800;
    color: #1e293b;
    letter-spacing: 0.5px;
    line-height: 1;
    text-transform: lowercase;
}

.prime-phone-brand .dots-row {
    display: flex;
    gap: 3.5px;
}

.prime-phone-brand .dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
}

/* QR Code Container */
.prime-phone-qr-frame {
    background: #ffffff;
    border-radius: 6px;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 4px;
}

.prime-phone-qr-frame img {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 4px;
}

/* ========================================================================
 3. BOTTOM FLOATING TOGGLE BUTTON (Blue Circle with '✕')
======================================================================== */
.prime-floating-toggle-btn {
    pointer-events: auto;
    position: fixed;
    bottom: 24px;
    right: 28px;
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: #2067e1;
    color: #ffffff;
    border: none;
    box-shadow: 0 4px 18px rgba(32, 103, 225, 0.45);
    cursor: pointer;
    z-index: 1045;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.prime-floating-toggle-btn:hover {
    transform: scale(1.08);
    background: #1752b8;
    box-shadow: 0 6px 22px rgba(32, 103, 225, 0.55);
}

/* Slide and Fade Animations */
@keyframes primePopIn {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.prime-card-closing {
    opacity: 0 !important;
    transform: translateY(20px) scale(0.95) !important;
    pointer-events: none !important;
}

/* Settings Popover Menu */
.prime-reward-settings-menu {
    position: absolute;
    top: 40px;
    right: 12px;
    width: 220px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    padding: 10px 12px;
    z-index: 1050;
    display: none;
    font-size: 12px;
    color: #334155;
}

.prime-reward-settings-menu.show {
    display: block;
    animation: primePopIn 0.2s ease forwards;
}

/* Responsive adjustments for Mobile */
@media (max-width: 768px) {
    .prime-floating-widgets-wrap {
        bottom: 74px;
        right: 14px;
    }
    .prime-reward-popup {
        width: 280px;
    }
    .prime-app-popup {
        display: none; /* Mobile visitors already on smartphone */
    }
    .prime-floating-toggle-btn {
        bottom: 74px;
        right: 14px;
        width: 40px;
        height: 40px;
        font-size: 15px;
    }
}
</style>

<!-- Floating Popups Container -->
<div class="prime-floating-widgets-wrap" id="primeFloatingWidgets">

    {{-- ================================================================
     1. TOP CARD: PrimeCash Rewards Card
    ================================================================ --}}
    <div class="prime-floating-card prime-reward-popup" id="primeRewardCard">
        
        <!-- Settings Popover Menu -->
        <div class="prime-reward-settings-menu" id="primeRewardSettingsMenu">
            <div class="fw-bold text-dark mb-1" style="font-size: 12px;">PrimeCash Rewards</div>
            <div class="d-flex align-items-center justify-content-between py-1 border-bottom">
                <span>Auto-Apply at Checkout</span>
                <span class="badge bg-success" style="font-size: 10px;">ON</span>
            </div>
            <div class="d-flex align-items-center justify-content-between py-1 border-bottom">
                <span>Cashback Rate</span>
                <span class="fw-bold text-primary">Up to 8%</span>
            </div>
            <div class="pt-2 text-center">
                <a href="{{ route('vip') }}" class="text-decoration-none text-primary fw-bold" style="font-size: 11px;">View VIP Benefits →</a>
            </div>
        </div>

        <!-- Top Bar -->
        <div class="prime-reward-topbar">
            <span class="prime-reward-logo-h" title="Cashback Rewards Engine">h</span>

            <div class="prime-brand-logo-pill">
                <div class="brand-text">prime booking</div>
                <div class="dots-row">
                    <span class="dot" style="background:#e11d48;"></span>
                    <span class="dot" style="background:#ea580c;"></span>
                    <span class="dot" style="background:#eab308;"></span>
                    <span class="dot" style="background:#16a34a;"></span>
                    <span class="dot" style="background:#2563eb;"></span>
                </div>
            </div>

            <div class="prime-reward-actions">
                <button type="button" class="prime-icon-btn" onclick="toggleRewardsSettings(event)" title="Rewards Settings">
                    <i class="fa-solid fa-gear"></i>
                </button>
                <button type="button" class="prime-icon-btn" onclick="closeRewardCard()" title="Dismiss">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- Title -->
        <div class="prime-reward-title">
            Get 1% to 8% back
        </div>

        <!-- CTA Button -->
        <button type="button" class="prime-reward-btn" id="activateRewardsBtn" onclick="activatePrimeCashRewards()">
            <i class="fa-solid fa-bolt me-1" id="rewardsBtnIcon"></i> <span id="rewardsBtnText">Activate Rewards</span>
        </button>

        <!-- Footer Note -->
        <p class="prime-reward-footer-note">
            Check offers for details. PrimeCash wallet credited instantly. <a href="{{ route('terms') }}" target="_blank">Terms</a> and <a href="{{ route('terms') }}" target="_blank">exclusions</a> apply.
        </p>
    </div>

    {{-- ================================================================
     2. BOTTOM CARD: App QR Code / Instant Savings Card (Exact Screenshot)
    ================================================================ --}}
    <div class="prime-floating-card prime-app-popup" id="primeAppCard">
        <div class="prime-app-title">
            Save 10% on your 1st app booking!
        </div>
        <div class="prime-app-subtitle">
            Just scan the QR code for instant savings
        </div>

        <!-- Smartphone Mockup Frame -->
        <div class="prime-phone-mockup">
            <div class="prime-phone-notch"></div>
            <div class="prime-phone-brand">
                <span class="brand-name">prime booking</span>
                <span class="dots-row">
                    <span class="dot" style="background:#e11d48;"></span>
                    <span class="dot" style="background:#ea580c;"></span>
                    <span class="dot" style="background:#eab308;"></span>
                    <span class="dot" style="background:#16a34a;"></span>
                    <span class="dot" style="background:#2563eb;"></span>
                </span>
            </div>
            <div class="prime-phone-qr-frame" title="Scan with your smartphone camera for instant savings">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(url('/?ref=app_qr&discount=PRIME10')) }}&color=0f172a&bgcolor=ffffff&margin=1" alt="Scan Prime Booking QR Code" loading="lazy">
            </div>
        </div>
    </div>

</div>

<!-- Bottom Floating Toggle Button (Blue Circle with '✕') -->
<button type="button" id="toggleFloatingCardsBtn" class="prime-floating-toggle-btn" onclick="toggleFloatingCards()" title="Toggle Savings Widgets">
    <i class="fa-solid fa-xmark" id="floatingToggleIcon"></i>
</button>

<script>
let rewardsActivated = false;
let isCardsVisible = true;

document.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('prime_rewards_active') === 'true') {
        setRewardsStateActivated();
    }
    if (sessionStorage.getItem('prime_widgets_collapsed') === 'true') {
        hideFloatingWidgets(false);
    }
});

function toggleRewardsSettings(e) {
    e.stopPropagation();
    const menu = document.getElementById('primeRewardSettingsMenu');
    if (menu) {
        menu.classList.toggle('show');
    }
}

document.addEventListener('click', function(e) {
    const menu = document.getElementById('primeRewardSettingsMenu');
    if (menu && !menu.contains(e.target) && !e.target.closest('.prime-icon-btn')) {
        menu.classList.remove('show');
    }
});

function activatePrimeCashRewards() {
    const btn = document.getElementById('activateRewardsBtn');
    const icon = document.getElementById('rewardsBtnIcon');
    const txt = document.getElementById('rewardsBtnText');
    
    if (!rewardsActivated) {
        rewardsActivated = true;
        localStorage.setItem('prime_rewards_active', 'true');
        
        btn.classList.add('activated');
        icon.className = 'fa-solid fa-check';
        txt.innerText = 'Rewards Activated (8% Applied)';
        
        if (typeof showSaasToast === 'function') {
            showSaasToast('🎉 PrimeCash 8% Cashback Activated for your bookings!', 'success');
        } else {
            alert('🎉 PrimeCash 8% Cashback Activated! Your savings will be applied automatically.');
        }
    }
}

function setRewardsStateActivated() {
    rewardsActivated = true;
    const btn = document.getElementById('activateRewardsBtn');
    const icon = document.getElementById('rewardsBtnIcon');
    const txt = document.getElementById('rewardsBtnText');
    if (btn && icon && txt) {
        btn.classList.add('activated');
        icon.className = 'fa-solid fa-check';
        txt.innerText = 'Rewards Activated (8% Applied)';
    }
}

function closeRewardCard() {
    const card = document.getElementById('primeRewardCard');
    if (card) {
        card.classList.add('prime-card-closing');
        setTimeout(() => card.style.display = 'none', 300);
    }
}

function toggleFloatingCards() {
    if (isCardsVisible) {
        hideFloatingWidgets(true);
    } else {
        showFloatingWidgets();
    }
}

function hideFloatingWidgets(animated = true) {
    const wrap = document.getElementById('primeFloatingWidgets');
    const icon = document.getElementById('floatingToggleIcon');
    isCardsVisible = false;
    sessionStorage.setItem('prime_widgets_collapsed', 'true');
    
    if (wrap) wrap.style.display = 'none';
    if (icon) icon.className = 'fa-solid fa-gift';
}

function showFloatingWidgets() {
    const wrap = document.getElementById('primeFloatingWidgets');
    const icon = document.getElementById('floatingToggleIcon');
    const rCard = document.getElementById('primeRewardCard');
    const aCard = document.getElementById('primeAppCard');
    isCardsVisible = true;
    sessionStorage.removeItem('prime_widgets_collapsed');
    
    if (wrap) wrap.style.display = 'flex';
    if (rCard) { rCard.style.display = 'block'; rCard.classList.remove('prime-card-closing'); }
    if (aCard) { aCard.style.display = 'block'; aCard.classList.remove('prime-card-closing'); }
    if (icon) icon.className = 'fa-solid fa-xmark';
}
</script>
