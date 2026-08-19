{{-- 
========================================================================================
 Prime Booking: Pixel-Perfect Agoda App QR Code & Instant Savings Floating System
 Independent Bottom-Right Floating Widget • Smooth Morphing Toggle • 60fps Micro-Interactions
========================================================================================
--}}
<style>
/* Master Bottom-Right Fixed Container */
.pb-app-float-system {
    position: fixed;
    right: 24px;
    bottom: 24px;
    z-index: 999999;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    pointer-events: none;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    user-select: none;
}

.pb-app-float-system * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

/* ========================================================================
 1. WHITE APP SAVINGS CARD (Save 10% on your 1st app booking!)
======================================================================== */
.pb-app-card-box {
    pointer-events: auto;
    width: 288px;
    background: #ffffff;
    border-radius: 18px;
    padding: 18px 18px 10px;
    box-shadow: 0 16px 44px -6px rgba(15, 23, 42, 0.16), 0 0 0 1px rgba(15, 23, 42, 0.06);
    margin-bottom: 12px;
    position: relative;
    transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.22s ease;
    animation: pbCardPopIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.pb-app-card-box.is-hidden {
    opacity: 0 !important;
    transform: translateY(24px) scale(0.95) !important;
    pointer-events: none !important;
    display: none !important;
}

/* Downward Pointer Arrow matching Agoda screenshot */
.pb-app-card-box::after {
    content: '';
    position: absolute;
    bottom: -6px;
    right: 20px;
    width: 13px;
    height: 13px;
    background: #ffffff;
    transform: rotate(45deg);
    border-right: 1px solid rgba(15, 23, 42, 0.08);
    border-bottom: 1px solid rgba(15, 23, 42, 0.08);
}

.pb-app-header-title {
    font-size: 19px;
    font-weight: 800;
    color: #0f172a;
    text-align: center;
    line-height: 1.25;
    margin-bottom: 4px;
    letter-spacing: -0.3px;
}

.pb-app-header-sub {
    font-size: 12px;
    color: #475569;
    text-align: center;
    margin-bottom: 12px;
    line-height: 1.35;
}

/* Smartphone Mockup Frame */
.pb-phone-mock-frame {
    width: 184px;
    margin: 0 auto;
    background: #ffffff;
    border: 6px solid #94a3b8;
    border-bottom: none;
    border-radius: 30px 30px 0 0;
    padding: 0 8px 0;
    position: relative;
}

.pb-phone-notch-pill {
    width: 58px;
    height: 9px;
    background: #94a3b8;
    border-radius: 0 0 10px 10px;
    margin: 0 auto 6px;
}

.pb-phone-brand-header {
    text-align: center;
    margin-bottom: 6px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
}

.pb-phone-brand-header .brand-title {
    font-size: 12px;
    font-weight: 800;
    color: #1e293b;
    letter-spacing: 0.5px;
    line-height: 1;
    text-transform: lowercase;
}

.pb-phone-brand-header .dots-row {
    display: flex;
    gap: 3px;
}

.pb-phone-brand-header .dot {
    width: 4.5px;
    height: 4.5px;
    border-radius: 50%;
}

.pb-phone-qr-wrap {
    background: #ffffff;
    border-radius: 6px;
    padding: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 2px;
}

.pb-phone-qr-wrap img {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 4px;
}

/* 1-Click Copy Promo Code Bar */
.pb-copy-coupon-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-top: 8px;
    padding: 5px 10px;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.pb-copy-coupon-bar:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
}

.pb-copy-coupon-bar .code-text {
    font-size: 11.5px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: 0.8px;
}

.pb-copy-coupon-bar .hint-text {
    font-size: 10.5px;
    font-weight: 600;
    color: #2563eb;
}

/* ========================================================================
 2. BOTTOM BLUE MORPHING BUTTON (Circle '✕' when Open ↔ Pill when Closed)
======================================================================== */
.pb-app-toggle-btn {
    pointer-events: auto;
    background: #2067e1;
    color: #ffffff;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 4px 18px rgba(32, 103, 225, 0.45);
    margin-right: 4px;
}

.pb-app-toggle-btn:hover {
    transform: scale(1.06);
    background: #1752b8;
    box-shadow: 0 6px 22px rgba(32, 103, 225, 0.55);
}

.pb-app-toggle-btn:active {
    transform: scale(0.96);
}

/* Circle State (Card is OPEN) */
.pb-app-toggle-btn.btn-circle {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    font-size: 18px;
}

/* Pill State (Card is CLOSED) */
.pb-app-toggle-btn.btn-pill {
    width: auto !important;
    height: 42px !important;
    border-radius: 24px !important;
    padding: 0 18px !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    letter-spacing: 0.2px !important;
    animation: pbCardPopIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Entrance Keyframes */
@keyframes pbCardPopIn {
    from {
        opacity: 0;
        transform: translateY(16px) scale(0.96);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Responsive adjustments for Mobile */
@media (max-width: 768px) {
    .pb-app-float-system {
        right: 14px;
        bottom: 74px;
    }
    .pb-app-card-box {
        display: none !important; /* Hide QR code popup on mobile phones */
    }
}
</style>

<!-- Independent Bottom-Right App Savings Floating System -->
<div class="pb-app-float-system" id="pbAppFloatSystem">

    <!-- 1. WHITE APP SAVINGS CARD -->
    <div class="pb-app-card-box" id="pbAppCardBox">
        <div class="pb-app-header-title">
            Save 10% on your 1st app booking!
        </div>
        <div class="pb-app-header-sub">
            Just scan the QR code for instant savings
        </div>

        <!-- Phone Frame Mockup -->
        <div class="pb-phone-mock-frame">
            <div class="pb-phone-notch-pill"></div>
            <div class="pb-phone-brand-header">
                <span class="brand-title">prime booking</span>
                <div class="dots-row">
                    <span class="dot" style="background:#e11d48;"></span>
                    <span class="dot" style="background:#ea580c;"></span>
                    <span class="dot" style="background:#eab308;"></span>
                    <span class="dot" style="background:#16a34a;"></span>
                    <span class="dot" style="background:#2563eb;"></span>
                </div>
            </div>
            <div class="pb-phone-qr-wrap" title="Scan with phone camera">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(url('/?ref=app_qr&discount=PRIME10')) }}&color=0f172a&bgcolor=ffffff&margin=1" alt="Scan QR Code" loading="lazy">
            </div>
        </div>

        <!-- 1-Click Copy Code Bar -->
        <div class="pb-copy-coupon-bar" onclick="pbCopyPromoCode('PRIME10')" title="Click to copy promo code">
            <span>🎟️</span>
            <span class="code-text">PRIME10</span>
            <span class="hint-text" id="pbCopyHint">(Tap to Copy)</span>
        </div>
    </div>

    <!-- 2. MORPHING BLUE BUTTON (Circle with '✕' ↔ Pill with 'Save more on App!') -->
    <button type="button" class="pb-app-toggle-btn btn-circle" id="pbAppToggleBtn" onclick="pbToggleAppSavingsCard()" title="Close App Savings">
        <i class="fa-solid fa-xmark" id="pbToggleIcon"></i>
    </button>

</div>

<script>
let pbCardIsOpen = true;

function pbToggleAppSavingsCard() {
    const card = document.getElementById('pbAppCardBox');
    const btn = document.getElementById('pbAppToggleBtn');
    
    if (pbCardIsOpen) {
        // CLOSE CARD -> MORPH TO PILL
        pbCardIsOpen = false;
        if (card) card.classList.add('is-hidden');
        if (btn) {
            btn.className = 'pb-app-toggle-btn btn-pill';
            btn.innerHTML = '<i class="fa-solid fa-mobile-screen"></i> <span>Save more on App!</span>';
            btn.title = 'Open App Savings';
        }
    } else {
        // OPEN CARD -> MORPH TO CIRCLE WITH X
        pbCardIsOpen = true;
        if (card) {
            card.classList.remove('is-hidden');
            card.style.display = 'block';
        }
        if (btn) {
            btn.className = 'pb-app-toggle-btn btn-circle';
            btn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            btn.title = 'Close App Savings';
        }
    }
}

function pbCopyPromoCode(code) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(code).then(() => {
            const hint = document.getElementById('pbCopyHint');
            if (hint) {
                hint.innerText = '✅ Copied!';
                hint.style.color = '#16a34a';
                setTimeout(() => {
                    hint.innerText = '(Tap to Copy)';
                    hint.style.color = '#2563eb';
                }, 2500);
            }
        });
    }
}
</script>
