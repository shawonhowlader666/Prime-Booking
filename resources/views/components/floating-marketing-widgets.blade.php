{{-- 
========================================================================
 Prime Booking: Floating Marketing & App QR Code Popups (Agoda 1:1)
========================================================================
--}}
<style>
/* Master Fixed Floating Container */
#primeFloatingWidgetsWrap {
    position: fixed !important;
    top: 110px !important;
    right: 24px !important;
    z-index: 999999 !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 14px !important;
    align-items: flex-end !important;
    pointer-events: none !important;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
}

#primeFloatingWidgetsWrap * {
    box-sizing: border-box;
}

.prime-float-card {
    pointer-events: auto !important;
    background: #ffffff !important;
    border-radius: 16px !important;
    box-shadow: 0 14px 40px rgba(0, 0, 0, 0.15), 0 2px 10px rgba(0, 0, 0, 0.06) !important;
    border: 1px solid #e2e8f0 !important;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
    position: relative !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* ========================================================================
 1. TOP CARD: PrimeCash Rewards & Cashback (Get 1% to 8% back)
======================================================================== */
.prime-reward-card-box {
    width: 320px !important;
    padding: 16px 18px 14px !important;
}

.prime-reward-topbar {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding-bottom: 6px !important;
}

.prime-reward-logo-h {
    font-family: 'Georgia', serif !important;
    font-size: 24px !important;
    font-weight: 800 !important;
    color: #1e293b !important;
    line-height: 1 !important;
    font-style: italic !important;
    letter-spacing: -0.5px !important;
}

.prime-brand-logo-pill {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 2px !important;
}

.prime-brand-logo-pill .brand-text {
    font-size: 11.5px !important;
    font-weight: 800 !important;
    color: #0f172a !important;
    letter-spacing: 0.8px !important;
    text-transform: lowercase !important;
}

.prime-brand-logo-pill .dots-row {
    display: flex !important;
    gap: 3.5px !important;
}

.prime-brand-logo-pill .dot {
    width: 5px !important;
    height: 5px !important;
    border-radius: 50% !important;
}

.prime-reward-actions {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
}

.prime-icon-btn {
    background: transparent !important;
    border: none !important;
    color: #94a3b8 !important;
    cursor: pointer !important;
    padding: 4px !important;
    font-size: 14px !important;
    border-radius: 4px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.15s ease !important;
}

.prime-icon-btn:hover {
    color: #334155 !important;
    background: #f1f5f9 !important;
}

.prime-reward-title {
    font-size: 21px !important;
    font-weight: 800 !important;
    color: #0f172a !important;
    text-align: center !important;
    margin: 12px 0 14px !important;
    letter-spacing: -0.3px !important;
}

.prime-reward-btn {
    width: 100% !important;
    background: #d84315 !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    font-size: 14px !important;
    padding: 11px 16px !important;
    border-radius: 6px !important;
    border: none !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    box-shadow: 0 2px 6px rgba(216, 67, 21, 0.25) !important;
}

.prime-reward-btn:hover {
    background: #bf360c !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 10px rgba(216, 67, 21, 0.35) !important;
}

.prime-reward-btn.activated {
    background: #16a34a !important;
    box-shadow: 0 2px 6px rgba(22, 163, 74, 0.25) !important;
}

.prime-reward-footer-note {
    font-size: 10.5px !important;
    color: #64748b !important;
    text-align: center !important;
    margin-top: 10px !important;
    margin-bottom: 0 !important;
    line-height: 1.4 !important;
}

.prime-reward-footer-note a {
    color: #475569 !important;
    text-decoration: underline !important;
}

/* ========================================================================
 2. BOTTOM CARD: Save 10% on your 1st app booking! QR Code Card
======================================================================== */
.prime-app-card-box {
    width: 290px !important;
    padding: 22px 20px 0 !important;
}

/* Tooltip Downward Arrow pointing down */
.prime-app-card-box::after {
    content: '' !important;
    position: absolute !important;
    bottom: -8px !important;
    right: 28px !important;
    width: 16px !important;
    height: 16px !important;
    background: #ffffff !important;
    transform: rotate(45deg) !important;
    border-right: 1px solid #e2e8f0 !important;
    border-bottom: 1px solid #e2e8f0 !important;
    box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.04) !important;
}

.prime-app-title {
    font-size: 20px !important;
    font-weight: 800 !important;
    color: #0f172a !important;
    text-align: center !important;
    line-height: 1.25 !important;
    margin-bottom: 6px !important;
    letter-spacing: -0.3px !important;
}

.prime-app-subtitle {
    font-size: 12.5px !important;
    color: #475569 !important;
    text-align: center !important;
    margin-bottom: 16px !important;
    line-height: 1.35 !important;
}

/* Smartphone Mockup Frame (Agoda Pixel-Perfect) */
.prime-phone-mockup {
    width: 200px !important;
    margin: 0 auto !important;
    background: #ffffff !important;
    border: 7px solid #9baec8 !important;
    border-bottom: none !important;
    border-radius: 36px 36px 0 0 !important;
    padding: 0 10px 0 !important;
    position: relative !important;
    box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.03) !important;
}

/* Phone Top Notch */
.prime-phone-notch {
    width: 68px !important;
    height: 11px !important;
    background: #9baec8 !important;
    border-radius: 0 0 12px 12px !important;
    margin: 0 auto 8px !important;
}

/* Brand Logo inside phone */
.prime-phone-brand {
    text-align: center !important;
    margin-bottom: 8px !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 3px !important;
}

.prime-phone-brand .brand-name {
    font-size: 13px !important;
    font-weight: 800 !important;
    color: #1e293b !important;
    letter-spacing: 0.5px !important;
    line-height: 1 !important;
    text-transform: lowercase !important;
}

.prime-phone-brand .dots-row {
    display: flex !important;
    gap: 3.5px !important;
}

.prime-phone-brand .dot {
    width: 5px !important;
    height: 5px !important;
    border-radius: 50% !important;
}

/* QR Code Container */
.prime-phone-qr-frame {
    background: #ffffff !important;
    border-radius: 6px !important;
    padding: 4px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin-bottom: 4px !important;
}

.prime-phone-qr-frame img {
    width: 100% !important;
    height: auto !important;
    display: block !important;
    border-radius: 4px !important;
}

/* ========================================================================
 3. BOTTOM FLOATING TOGGLE BUTTON (Blue Circle with '✕')
======================================================================== */
#primeFloatingToggleBtn {
    pointer-events: auto !important;
    position: fixed !important;
    bottom: 24px !important;
    right: 28px !important;
    width: 48px !important;
    height: 48px !important;
    border-radius: 50% !important;
    background: #2067e1 !important;
    color: #ffffff !important;
    border: none !important;
    box-shadow: 0 4px 18px rgba(32, 103, 225, 0.45) !important;
    cursor: pointer !important;
    z-index: 999999 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 18px !important;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

#primeFloatingToggleBtn:hover {
    transform: scale(1.08) !important;
    background: #1752b8 !important;
    box-shadow: 0 6px 22px rgba(32, 103, 225, 0.55) !important;
}

/* Side Sticky Orange 'h' Tab */
#primeStickyRewardsTab {
    pointer-events: auto !important;
    position: fixed !important;
    right: 0 !important;
    top: 140px !important;
    background-color: #f97316 !important;
    color: #ffffff !important;
    width: 38px !important;
    height: 60px !important;
    border-radius: 8px 0 0 8px !important;
    font-family: 'Georgia', serif !important;
    font-weight: 700 !important;
    font-style: italic !important;
    font-size: 1.5rem !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-shadow: -3px 2px 10px rgba(0,0,0,0.18) !important;
    cursor: pointer !important;
    z-index: 999998 !important;
}

/* Settings Popover Menu */
.prime-reward-settings-menu {
    position: absolute !important;
    top: 40px !important;
    right: 12px !important;
    width: 220px !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important;
    padding: 10px 12px !important;
    z-index: 1000000 !important;
    display: none;
    font-size: 12px !important;
    color: #334155 !important;
}

.prime-reward-settings-menu.show {
    display: block !important;
}

/* Mobile responsive */
@media (max-width: 768px) {
    #primeFloatingWidgetsWrap {
        top: auto !important;
        bottom: 74px !important;
        right: 14px !important;
    }
    .prime-reward-card-box {
        width: 280px !important;
    }
    .prime-app-card-box {
        display: none !important;
    }
    #primeFloatingToggleBtn {
        bottom: 74px !important;
        right: 14px !important;
        width: 40px !important;
        height: 40px !important;
        font-size: 15px !important;
    }
}
</style>

<!-- Side Sticky Orange Tab -->
<div id="primeStickyRewardsTab" onclick="primeToggleFloatingCards()" title="PrimeCash Cashback Rewards">
    h
</div>

<!-- Floating Popups Container -->
<div id="primeFloatingWidgetsWrap">

    {{-- 1. TOP CARD: PrimeCash Rewards Card --}}
    <div class="prime-float-card prime-reward-card-box" id="primeRewardCard">
        
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
                <button type="button" class="prime-icon-btn" onclick="primeToggleRewardsSettings(event)" title="Rewards Settings">
                    <i class="fa-solid fa-gear"></i>
                </button>
                <button type="button" class="prime-icon-btn" onclick="primeCloseRewardCard()" title="Dismiss">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- Title -->
        <div class="prime-reward-title">
            Get 1% to 8% back
        </div>

        <!-- CTA Button -->
        <button type="button" class="prime-reward-btn" id="primeActivateRewardsBtn" onclick="primeActivateRewards()">
            <i class="fa-solid fa-bolt me-1" id="primeRewardsBtnIcon"></i> <span id="primeRewardsBtnText">Activate Rewards</span>
        </button>

        <!-- Footer Note -->
        <p class="prime-reward-footer-note">
            Check offers for details. PrimeCash wallet credited instantly. <a href="{{ route('terms') }}" target="_blank">Terms</a> and <a href="{{ route('terms') }}" target="_blank">exclusions</a> apply.
        </p>
    </div>

    {{-- 2. BOTTOM CARD: App QR Code / Instant Savings Card --}}
    <div class="prime-float-card prime-app-card-box" id="primeAppCard">
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
<button type="button" id="primeFloatingToggleBtn" onclick="primeToggleFloatingCards()" title="Toggle Savings Widgets">
    <i class="fa-solid fa-xmark" id="primeFloatingToggleIcon"></i>
</button>

<script>
let primeCardsVisible = true;

function primeToggleRewardsSettings(e) {
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

function primeActivateRewards() {
    const btn = document.getElementById('primeActivateRewardsBtn');
    const icon = document.getElementById('primeRewardsBtnIcon');
    const txt = document.getElementById('primeRewardsBtnText');
    
    if (btn && icon && txt) {
        btn.classList.add('activated');
        icon.className = 'fa-solid fa-check';
        txt.innerText = 'Rewards Activated (8% Applied)';
        
        if (typeof showSaasToast === 'function') {
            showSaasToast('🎉 PrimeCash 8% Cashback Activated!', 'success');
        }
    }
}

function primeCloseRewardCard() {
    const card = document.getElementById('primeRewardCard');
    if (card) {
        card.style.display = 'none';
    }
}

function primeToggleFloatingCards() {
    const wrap = document.getElementById('primeFloatingWidgetsWrap');
    const icon = document.getElementById('primeFloatingToggleIcon');
    const rCard = document.getElementById('primeRewardCard');
    const aCard = document.getElementById('primeAppCard');
    
    if (primeCardsVisible) {
        primeCardsVisible = false;
        if (wrap) wrap.style.display = 'none';
        if (icon) icon.className = 'fa-solid fa-gift';
    } else {
        primeCardsVisible = true;
        if (wrap) wrap.style.display = 'flex';
        if (rCard) rCard.style.display = 'block';
        if (aCard) aCard.style.display = 'block';
        if (icon) icon.className = 'fa-solid fa-xmark';
    }
}
</script>
