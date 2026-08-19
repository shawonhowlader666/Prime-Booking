{{-- 
========================================================================
 Prime Booking: Pixel-Perfect Agoda 1:1 Floating Marketing & App Cards
========================================================================
--}}
<style>
/* Master Container */
.prime-floating-system-wrap {
    position: fixed;
    right: 24px;
    bottom: 24px;
    z-index: 999999;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 16px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    pointer-events: none;
}

.prime-floating-system-wrap * {
    box-sizing: border-box;
}

/* ========================================================================
 1. TOP CARD: PrimeCash Rewards & Cashback Popup
======================================================================== */
.prime-top-cashback-popup {
    pointer-events: auto;
    width: 320px;
    background: #ffffff;
    border-radius: 16px;
    padding: 16px 18px 14px;
    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.14), 0 2px 8px rgba(0, 0, 0, 0.06);
    border: 1px solid #eef2f6;
    position: fixed;
    top: 105px;
    right: 24px;
    z-index: 999999;
    transition: transform 0.25s ease, opacity 0.25s ease;
}

.prime-top-cashback-popup.hidden {
    opacity: 0;
    transform: translateX(40px) scale(0.95);
    pointer-events: none;
    display: none;
}

.prime-top-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 4px;
}

.prime-honey-h-logo {
    font-family: "Georgia", serif;
    font-size: 24px;
    font-weight: 700;
    font-style: italic;
    color: #1e293b;
    line-height: 1;
}

.prime-center-brand-tag {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
}

.prime-center-brand-tag .brand-title {
    font-size: 12px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: 0.5px;
    line-height: 1;
    text-transform: lowercase;
}

.prime-center-brand-tag .brand-dots {
    display: flex;
    gap: 3.5px;
}

.prime-center-brand-tag .dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
}

.prime-top-action-icons {
    display: flex;
    align-items: center;
    gap: 6px;
}

.prime-icon-btn-action {
    background: transparent;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px;
    font-size: 14px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
}

.prime-icon-btn-action:hover {
    color: #1e293b;
    background: #f1f5f9;
}

.prime-cashback-headline {
    font-size: 21px;
    font-weight: 800;
    color: #0f172a;
    text-align: center;
    margin: 12px 0 14px;
    letter-spacing: -0.3px;
}

.prime-cashback-cta-btn {
    width: 100%;
    background: #d84315;
    color: #ffffff;
    font-weight: 700;
    font-size: 14.5px;
    padding: 11px 16px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 2px 6px rgba(216, 67, 21, 0.25);
}

.prime-cashback-cta-btn:hover {
    background: #bf360c;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(216, 67, 21, 0.35);
}

.prime-cashback-cta-btn.active-state {
    background: #16a34a !important;
    box-shadow: 0 2px 6px rgba(22, 163, 74, 0.25) !important;
}

.prime-cashback-disclaimer {
    font-size: 10.5px;
    color: #64748b;
    text-align: center;
    margin-top: 10px;
    margin-bottom: 0;
    line-height: 1.4;
}

.prime-cashback-disclaimer a {
    color: #475569;
    text-decoration: underline;
}

/* Side Sticky Orange 'h' Tab (Shows only when top card is closed) */
#primeSideStickyHTab {
    pointer-events: auto;
    position: fixed;
    right: 0;
    top: 140px;
    background-color: #f97316;
    color: #ffffff;
    width: 36px;
    height: 56px;
    border-radius: 8px 0 0 8px;
    font-family: "Georgia", serif;
    font-weight: 700;
    font-style: italic;
    font-size: 1.4rem;
    display: none; /* hidden when top card is active */
    align-items: center;
    justify-content: center;
    box-shadow: -3px 2px 10px rgba(0,0,0,0.18);
    cursor: pointer;
    z-index: 999998;
    transition: transform 0.2s ease;
}

#primeSideStickyHTab:hover {
    transform: translateX(-4px);
}

/* ========================================================================
 2. BOTTOM CARD: Save 10% on your 1st app booking! QR Code Popup
======================================================================== */
.prime-bottom-app-wrapper {
    pointer-events: auto;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    position: relative;
}

.prime-app-modal-card {
    width: 290px;
    background: #ffffff;
    border-radius: 18px;
    padding: 22px 20px 0;
    box-shadow: 0 14px 40px rgba(0, 0, 0, 0.16), 0 2px 8px rgba(0, 0, 0, 0.06);
    border: 1px solid #e2e8f0;
    margin-bottom: 12px;
    position: relative;
    transition: transform 0.25s ease, opacity 0.25s ease;
}

.prime-app-modal-card.hidden {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
    pointer-events: none;
    display: none;
}

/* Triangle Pointer Arrow at bottom right pointing to the Blue Close Button */
.prime-app-modal-card::after {
    content: '';
    position: absolute;
    bottom: -7px;
    right: 22px;
    width: 14px;
    height: 14px;
    background: #ffffff;
    transform: rotate(45deg);
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
}

.prime-app-main-heading {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    text-align: center;
    line-height: 1.25;
    margin-bottom: 6px;
    letter-spacing: -0.3px;
}

.prime-app-sub-heading {
    font-size: 12.5px;
    color: #475569;
    text-align: center;
    margin-bottom: 16px;
    line-height: 1.35;
}

/* Smartphone Mockup Frame */
.prime-phone-outline-frame {
    width: 200px;
    margin: 0 auto;
    background: #ffffff;
    border: 7px solid #94a3b8;
    border-bottom: none;
    border-radius: 36px 36px 0 0;
    padding: 0 10px 0;
    position: relative;
    box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.03);
}

.prime-phone-camera-notch {
    width: 68px;
    height: 11px;
    background: #94a3b8;
    border-radius: 0 0 12px 12px;
    margin: 0 auto 8px;
}

.prime-phone-brand-title {
    text-align: center;
    margin-bottom: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
}

.prime-phone-brand-title .name {
    font-size: 13px;
    font-weight: 800;
    color: #1e293b;
    letter-spacing: 0.5px;
    line-height: 1;
    text-transform: lowercase;
}

.prime-phone-brand-title .dots {
    display: flex;
    gap: 3.5px;
}

.prime-phone-brand-title .dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
}

.prime-phone-qr-wrap {
    background: #ffffff;
    border-radius: 6px;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 2px;
}

.prime-phone-qr-wrap img {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 4px;
}

/* Blue Circular Toggle / Close Button (Exact Screenshot Parity) */
.prime-bottom-blue-circle-btn {
    pointer-events: auto;
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: #2067e1;
    color: #ffffff;
    border: none;
    box-shadow: 0 4px 18px rgba(32, 103, 225, 0.45);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    margin-right: 6px;
}

.prime-bottom-blue-circle-btn:hover {
    transform: scale(1.08);
    background: #1752b8;
    box-shadow: 0 6px 22px rgba(32, 103, 225, 0.55);
}

/* Settings Popover Dropdown */
.prime-reward-settings-dropdown {
    position: absolute;
    top: 40px;
    right: 12px;
    width: 220px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    padding: 10px 12px;
    z-index: 1000000;
    display: none;
    font-size: 12px;
    color: #334155;
}

.prime-reward-settings-dropdown.show {
    display: block;
}

/* Responsive adjustments for Mobile */
@media (max-width: 768px) {
    .prime-top-cashback-popup {
        width: calc(100vw - 32px);
        max-width: 320px;
        top: 75px;
        right: 16px;
    }
    .prime-floating-system-wrap {
        right: 14px;
        bottom: 74px;
    }
    .prime-app-modal-card {
        display: none !important; /* Hide app download on phones */
    }
}
</style>

<!-- Side Sticky Orange 'h' Tab (Reveals when top card is dismissed) -->
<div id="primeSideStickyHTab" onclick="openPrimeCashCard()" title="Open PrimeCash Rewards">
    h
</div>

<!-- TOP CARD: PrimeCash Rewards & Cashback (Fixed at top right) -->
<div class="prime-top-cashback-popup" id="primeTopCashbackCard">
    
    <!-- Settings Menu -->
    <div class="prime-reward-settings-dropdown" id="primeRewardSettingsMenu">
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
    <div class="prime-top-bar">
        <span class="prime-honey-h-logo">h</span>

        <div class="prime-center-brand-tag">
            <div class="brand-title">prime booking</div>
            <div class="brand-dots">
                <span class="dot" style="background:#e11d48;"></span>
                <span class="dot" style="background:#ea580c;"></span>
                <span class="dot" style="background:#eab308;"></span>
                <span class="dot" style="background:#16a34a;"></span>
                <span class="dot" style="background:#2563eb;"></span>
            </div>
        </div>

        <div class="prime-top-action-icons">
            <button type="button" class="prime-icon-btn-action" onclick="togglePrimeSettings(event)" title="Settings">
                <i class="fa-solid fa-gear"></i>
            </button>
            <button type="button" class="prime-icon-btn-action" onclick="dismissPrimeCashCard()" title="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <!-- Headline -->
    <div class="prime-cashback-headline">
        Get 1% to 8% back
    </div>

    <!-- CTA Button -->
    <button type="button" class="prime-cashback-cta-btn" id="primeActivateBtn" onclick="activatePrimeRewardsAction()">
        <i class="fa-solid fa-bolt me-1" id="primeBtnBoltIcon"></i> <span id="primeBtnText">Activate Rewards</span>
    </button>

    <!-- Disclaimer -->
    <p class="prime-cashback-disclaimer">
        Check offers for details. PrimeCash wallet credited instantly. <a href="{{ route('terms') }}" target="_blank">Terms</a> and <a href="{{ route('terms') }}" target="_blank">exclusions</a> apply.
    </p>
</div>

<!-- BOTTOM FLOATING CONTAINER: App QR Code + Blue Toggle Button -->
<div class="prime-floating-system-wrap">

    <div class="prime-bottom-app-wrapper">
        
        <!-- App QR Code Card -->
        <div class="prime-app-modal-card" id="primeAppQrCard">
            <div class="prime-app-main-heading">
                Save 10% on your 1st app booking!
            </div>
            <div class="prime-app-sub-heading">
                Just scan the QR code for instant savings
            </div>

            <!-- Smartphone Mockup Frame -->
            <div class="prime-phone-outline-frame">
                <div class="prime-phone-camera-notch"></div>
                <div class="prime-phone-brand-title">
                    <span class="name">prime booking</span>
                    <div class="dots">
                        <span class="dot" style="background:#e11d48;"></span>
                        <span class="dot" style="background:#ea580c;"></span>
                        <span class="dot" style="background:#eab308;"></span>
                        <span class="dot" style="background:#16a34a;"></span>
                        <span class="dot" style="background:#2563eb;"></span>
                    </div>
                </div>
                <div class="prime-phone-qr-wrap" title="Scan QR Code to Unlock Deals">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(url('/?ref=app_qr&discount=PRIME10')) }}&color=0f172a&bgcolor=ffffff&margin=1" alt="Scan Prime Booking QR Code" loading="lazy">
                </div>
            </div>
        </div>

        <!-- Blue Circular Toggle Button with '✕' -->
        <button type="button" class="prime-bottom-blue-circle-btn" id="primeBlueToggleBtn" onclick="togglePrimeAppCard()" title="Toggle App Savings">
            <i class="fa-solid fa-xmark" id="primeBlueToggleIcon"></i>
        </button>

    </div>

</div>

<script>
let appCardOpen = true;

function togglePrimeSettings(e) {
    e.stopPropagation();
    const menu = document.getElementById('primeRewardSettingsMenu');
    if (menu) {
        menu.classList.toggle('show');
    }
}

document.addEventListener('click', function(e) {
    const menu = document.getElementById('primeRewardSettingsMenu');
    if (menu && !menu.contains(e.target) && !e.target.closest('.prime-icon-btn-action')) {
        menu.classList.remove('show');
    }
});

function activatePrimeRewardsAction() {
    const btn = document.getElementById('primeActivateBtn');
    const icon = document.getElementById('primeBtnBoltIcon');
    const txt = document.getElementById('primeBtnText');
    
    if (btn && icon && txt) {
        btn.classList.add('active-state');
        icon.className = 'fa-solid fa-check';
        txt.innerText = 'Rewards Activated (8% Applied)';
        
        if (typeof showSaasToast === 'function') {
            showSaasToast('🎉 PrimeCash 8% Cashback Activated!', 'success');
        }
    }
}

function dismissPrimeCashCard() {
    const topCard = document.getElementById('primeTopCashbackCard');
    const sideTab = document.getElementById('primeSideStickyHTab');
    if (topCard) {
        topCard.classList.add('hidden');
    }
    if (sideTab) {
        sideTab.style.display = 'flex';
    }
}

function openPrimeCashCard() {
    const topCard = document.getElementById('primeTopCashbackCard');
    const sideTab = document.getElementById('primeSideStickyHTab');
    if (topCard) {
        topCard.classList.remove('hidden');
        topCard.style.display = 'block';
    }
    if (sideTab) {
        sideTab.style.display = 'none';
    }
}

function togglePrimeAppCard() {
    const appCard = document.getElementById('primeAppQrCard');
    const toggleIcon = document.getElementById('primeBlueToggleIcon');
    
    if (appCardOpen) {
        appCardOpen = false;
        if (appCard) appCard.classList.add('hidden');
        if (toggleIcon) toggleIcon.className = 'fa-solid fa-mobile-screen';
    } else {
        appCardOpen = true;
        if (appCard) {
            appCard.classList.remove('hidden');
            appCard.style.display = 'block';
        }
        if (toggleIcon) toggleIcon.className = 'fa-solid fa-xmark';
    }
}
</script>
