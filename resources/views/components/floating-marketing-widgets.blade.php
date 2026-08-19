{{-- 
========================================================================================
 Prime Booking: Pixel-Perfect Non-Overlapping Agoda 1:1 Floating Marketing System
 Unified Vertical Flex Dock • Zero Overlap • 60fps Micro-Interactions • Canvas Confetti
========================================================================================
--}}
<style>
/* Master Floating Dock (Unified Vertical Stack - Prevents ANY Overlapping) */
.pb-unified-dock {
    position: fixed;
    top: 85px;
    right: 24px;
    z-index: 999999;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 14px;
    pointer-events: none;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    user-select: none;
}

.pb-unified-dock * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

/* ========================================================================
 1. TOP CARD: PrimeCash Cashback Rewards Card (Get 1% to 8% back)
======================================================================== */
.pb-card-top-rewards {
    pointer-events: auto;
    width: 310px;
    background: #ffffff;
    border-radius: 16px;
    padding: 14px 16px 12px;
    box-shadow: 0 12px 36px -4px rgba(15, 23, 42, 0.14), 0 0 0 1px rgba(15, 23, 42, 0.06);
    position: relative;
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s ease;
    animation: pbFadeSlide 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.pb-card-top-rewards.is-hidden {
    opacity: 0;
    transform: translateX(40px) scale(0.95);
    pointer-events: none;
    display: none !important;
}

/* Top Card Header */
.pb-top-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 4px;
}

.pb-honey-h {
    font-family: 'Georgia', serif;
    font-size: 23px;
    font-weight: 700;
    font-style: italic;
    color: #1e293b;
    line-height: 1;
}

.pb-center-brand {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
}

.pb-center-brand .pb-brand-txt {
    font-size: 11.5px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: 0.5px;
    line-height: 1;
    text-transform: lowercase;
}

.pb-center-brand .pb-dots {
    display: flex;
    gap: 3.5px;
}

.pb-center-brand .pb-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
}

.pb-header-icons {
    display: flex;
    align-items: center;
    gap: 3px;
}

.pb-icon-btn {
    background: transparent;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px;
    font-size: 13px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
}

.pb-icon-btn:hover {
    color: #0f172a;
    background: #f1f5f9;
}

/* Live Social Proof Badge */
.pb-social-pill {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    background: #fff7ed;
    color: #c2410c;
    border-radius: 20px;
    padding: 2.5px 9px;
    font-size: 10.5px;
    font-weight: 700;
    margin: 6px auto 0;
    width: fit-content;
}

/* Headline */
.pb-top-title {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    text-align: center;
    margin: 8px 0 12px;
    letter-spacing: -0.3px;
}

/* Orange CTA Button */
.pb-top-btn {
    width: 100%;
    background: #d84315;
    color: #ffffff;
    font-weight: 700;
    font-size: 14px;
    padding: 10px 16px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 3px 10px rgba(216, 67, 21, 0.25);
}

.pb-top-btn:hover {
    background: #bf360c;
    transform: translateY(-1px);
    box-shadow: 0 5px 14px rgba(216, 67, 21, 0.35);
}

.pb-top-btn.is-active {
    background: #16a34a !important;
    box-shadow: 0 3px 10px rgba(22, 163, 74, 0.3) !important;
}

/* Footer note */
.pb-top-note {
    font-size: 10px;
    color: #64748b;
    text-align: center;
    margin-top: 8px;
    line-height: 1.4;
}

.pb-top-note a {
    color: #475569;
    text-decoration: underline;
}

/* Side Sticky Orange 'h' Tab */
#pbStickySideTab {
    pointer-events: auto;
    position: fixed;
    right: 0;
    top: 140px;
    background-color: #f97316;
    color: #ffffff;
    width: 36px;
    height: 56px;
    border-radius: 8px 0 0 8px;
    font-family: 'Georgia', serif;
    font-weight: 700;
    font-style: italic;
    font-size: 1.45rem;
    display: none;
    align-items: center;
    justify-content: center;
    box-shadow: -3px 4px 14px rgba(249, 115, 22, 0.35);
    cursor: pointer;
    z-index: 999998;
    transition: transform 0.2s ease;
}

#pbStickySideTab:hover {
    transform: translateX(-4px);
}

/* ========================================================================
 2. BOTTOM CARD: Save 10% on your 1st app booking! QR Code Popup
======================================================================== */
.pb-card-bottom-app {
    pointer-events: auto;
    width: 290px;
    background: #ffffff;
    border-radius: 18px;
    padding: 18px 18px 10px;
    box-shadow: 0 14px 40px -6px rgba(15, 23, 42, 0.16), 0 0 0 1px rgba(15, 23, 42, 0.06);
    position: relative;
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s ease;
    animation: pbFadeSlide 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.pb-card-bottom-app.is-hidden {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
    pointer-events: none;
    display: none !important;
}

/* Downward Pointer Arrow matching Agoda screenshot */
.pb-card-bottom-app::after {
    content: '';
    position: absolute;
    bottom: -6px;
    right: 20px;
    width: 12px;
    height: 12px;
    background: #ffffff;
    transform: rotate(45deg);
    border-right: 1px solid rgba(15, 23, 42, 0.08);
    border-bottom: 1px solid rgba(15, 23, 42, 0.08);
}

.pb-app-title-txt {
    font-size: 19px;
    font-weight: 800;
    color: #0f172a;
    text-align: center;
    line-height: 1.25;
    margin-bottom: 4px;
    letter-spacing: -0.3px;
}

.pb-app-sub-txt {
    font-size: 12px;
    color: #475569;
    text-align: center;
    margin-bottom: 12px;
    line-height: 1.3;
}

/* Phone Mockup Frame */
.pb-phone-frame {
    width: 196px;
    margin: 0 auto;
    background: #ffffff;
    border: 6px solid #94a3b8;
    border-bottom: none;
    border-radius: 32px 32px 0 0;
    padding: 0 10px 0;
    position: relative;
}

.pb-phone-notch-bar {
    width: 62px;
    height: 9px;
    background: #94a3b8;
    border-radius: 0 0 10px 10px;
    margin: 0 auto 6px;
}

.pb-phone-brand-tag {
    text-align: center;
    margin-bottom: 6px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
}

.pb-phone-brand-tag .name {
    font-size: 12px;
    font-weight: 800;
    color: #1e293b;
    letter-spacing: 0.5px;
    line-height: 1;
    text-transform: lowercase;
}

.pb-phone-brand-tag .dots {
    display: flex;
    gap: 3px;
}

.pb-phone-brand-tag .dot {
    width: 4.5px;
    height: 4.5px;
    border-radius: 50%;
}

.pb-qr-container {
    background: #ffffff;
    border-radius: 6px;
    padding: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 2px;
}

.pb-qr-container img {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 4px;
}

/* 1-Click Coupon Code Pill */
.pb-coupon-bar {
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

.pb-coupon-bar:hover {
    background: #f1f5f9;
}

.pb-coupon-bar .code {
    font-size: 11px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: 0.8px;
}

.pb-coupon-bar .hint {
    font-size: 10px;
    font-weight: 600;
    color: #2563eb;
}

/* ========================================================================
 3. BOTTOM BLUE TOGGLE BUTTON (Circle with '✕')
======================================================================== */
.pb-bottom-toggle-btn {
    pointer-events: auto;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #2067e1;
    color: #ffffff;
    border: none;
    box-shadow: 0 4px 16px rgba(32, 103, 225, 0.45);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    margin-right: 4px;
}

.pb-bottom-toggle-btn:hover {
    transform: scale(1.08);
    background: #1752b8;
    box-shadow: 0 6px 20px rgba(32, 103, 225, 0.55);
}

.pb-bottom-toggle-btn:active {
    transform: scale(0.96);
}

/* Canvas Confetti */
#pbConfettiCanvas {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    pointer-events: none;
    z-index: 1000001;
}

/* Entrance Animation */
@keyframes pbFadeSlide {
    from {
        opacity: 0;
        transform: translateY(12px) scale(0.97);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Responsive adjustments */
@media (max-width: 991px) {
    .pb-unified-dock {
        top: auto;
        bottom: 74px;
        right: 14px;
    }
    .pb-card-bottom-app {
        display: none !important;
    }
    .pb-card-top-rewards {
        width: 290px;
    }
}
</style>

<!-- Canvas for Confetti -->
<canvas id="pbConfettiCanvas"></canvas>

<!-- Side Sticky Orange 'h' Tab -->
<div id="pbStickySideTab" onclick="pbRestoreTopCard()" title="Open PrimeCash Rewards">
    h
</div>

<!-- Unified Master Dock (Contains Both Cards with Flex Flow - Zero Overlap) -->
<div class="pb-unified-dock" id="pbUnifiedDock">

    {{-- 1. TOP CARD: PrimeCash Rewards --}}
    <div class="pb-card-top-rewards" id="pbTopRewardsCard">
        <div class="pb-top-header">
            <span class="pb-honey-h">h</span>

            <div class="pb-center-brand">
                <span class="pb-brand-txt">prime booking</span>
                <div class="pb-dots">
                    <span class="pb-dot" style="background:#e11d48;"></span>
                    <span class="pb-dot" style="background:#ea580c;"></span>
                    <span class="pb-dot" style="background:#eab308;"></span>
                    <span class="pb-dot" style="background:#16a34a;"></span>
                    <span class="pb-dot" style="background:#2563eb;"></span>
                </div>
            </div>

            <div class="pb-header-icons">
                <button type="button" class="pb-icon-btn" onclick="pbDismissTopCard()" title="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <div class="pb-social-pill">
            <span>🔥</span> <span>142 travelers activated today</span>
        </div>

        <div class="pb-top-title">
            Get 1% to 8% back
        </div>

        <button type="button" class="pb-top-btn" id="pbActivateRewardsBtn" onclick="pbActivateRewardsAction()">
            <i class="fa-solid fa-bolt" id="pbBoltIcon"></i> <span id="pbBtnTxt">Activate Rewards</span>
        </button>

        <p class="pb-top-note">
            Check offers for details. PrimeCash wallet credited instantly. <a href="{{ route('terms') }}" target="_blank">Terms</a> apply.
        </p>
    </div>

    {{-- 2. BOTTOM CARD: App QR Code --}}
    <div class="pb-card-bottom-app" id="pbBottomAppCard">
        <div class="pb-app-title-txt">
            Save 10% on your 1st app booking!
        </div>
        <div class="pb-app-sub-txt">
            Just scan the QR code for instant savings
        </div>

        <!-- Phone Frame Mockup -->
        <div class="pb-phone-frame">
            <div class="pb-phone-notch-bar"></div>
            <div class="pb-phone-brand-tag">
                <span class="name">prime booking</span>
                <div class="dots">
                    <span class="dot" style="background:#e11d48;"></span>
                    <span class="dot" style="background:#ea580c;"></span>
                    <span class="dot" style="background:#eab308;"></span>
                    <span class="dot" style="background:#16a34a;"></span>
                    <span class="dot" style="background:#2563eb;"></span>
                </div>
            </div>
            <div class="pb-qr-container" title="Scan with phone camera">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(url('/?ref=app_qr&discount=PRIME10')) }}&color=0f172a&bgcolor=ffffff&margin=1" alt="Scan QR Code" loading="lazy">
            </div>
        </div>

        <!-- 1-Click Copy Code Bar -->
        <div class="pb-coupon-bar" onclick="pbCopyCoupon('PRIME10')" title="Click to copy promo code">
            <span>🎟️</span>
            <span class="code">PRIME10</span>
            <span class="hint" id="pbCopyHint">(Tap to Copy)</span>
        </div>
    </div>

    {{-- 3. CIRCULAR BLUE TOGGLE BUTTON (Directly Below Bottom Card) --}}
    <button type="button" class="pb-bottom-toggle-btn" id="pbCircleToggleBtn" onclick="pbToggleBottomCard()" title="Toggle Deals Card">
        <i class="fa-solid fa-xmark" id="pbToggleIcon"></i>
    </button>

</div>

<script>
let pbBottomCardVisible = true;

document.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('pb_rewards_active') === 'true') {
        pbSetRewardsStateActive();
    }
});

function pbActivateRewardsAction() {
    const btn = document.getElementById('pbActivateRewardsBtn');
    const icon = document.getElementById('pbBoltIcon');
    const txt = document.getElementById('pbBtnTxt');
    
    if (btn && icon && txt) {
        localStorage.setItem('pb_rewards_active', 'true');
        btn.classList.add('is-active');
        icon.className = 'fa-solid fa-check';
        txt.innerText = 'Rewards Activated (8% Applied)';
        
        // Server session handshake
        fetch('/api/activate-rewards', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        }).catch(e => {});

        // Launch Confetti Animation
        pbLaunchConfetti();
        
        if (typeof showSaasToast === 'function') {
            showSaasToast('🎉 PrimeCash 8% Cashback Activated! Applied automatically at checkout.', 'success');
        }
    }
}

function pbSetRewardsStateActive() {
    const btn = document.getElementById('pbActivateRewardsBtn');
    const icon = document.getElementById('pbBoltIcon');
    const txt = document.getElementById('pbBtnTxt');
    if (btn && icon && txt) {
        btn.classList.add('is-active');
        icon.className = 'fa-solid fa-check';
        txt.innerText = 'Rewards Activated (8% Applied)';
    }
}

function pbDismissTopCard() {
    const topCard = document.getElementById('pbTopRewardsCard');
    const stickyTab = document.getElementById('pbStickySideTab');
    if (topCard) topCard.classList.add('is-hidden');
    if (stickyTab) stickyTab.style.display = 'flex';
}

function pbRestoreTopCard() {
    const topCard = document.getElementById('pbTopRewardsCard');
    const stickyTab = document.getElementById('pbStickySideTab');
    if (topCard) {
        topCard.classList.remove('is-hidden');
        topCard.style.display = 'block';
    }
    if (stickyTab) stickyTab.style.display = 'none';
}

function pbToggleBottomCard() {
    const appCard = document.getElementById('pbBottomAppCard');
    const icon = document.getElementById('pbToggleIcon');
    
    if (pbBottomCardVisible) {
        pbBottomCardVisible = false;
        if (appCard) appCard.classList.add('is-hidden');
        if (icon) icon.className = 'fa-solid fa-mobile-screen';
    } else {
        pbBottomCardVisible = true;
        if (appCard) {
            appCard.classList.remove('is-hidden');
            appCard.style.display = 'block';
        }
        if (icon) icon.className = 'fa-solid fa-xmark';
    }
}

function pbCopyCoupon(code) {
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

// Zero-dependency Confetti Engine
function pbLaunchConfetti() {
    const canvas = document.getElementById('pbConfettiCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    
    const pieces = [];
    const colors = ['#e11d48', '#ea580c', '#eab308', '#16a34a', '#2563eb', '#8b5cf6'];
    
    for (let i = 0; i < 75; i++) {
        pieces.push({
            x: window.innerWidth - 180 + (Math.random() * 120 - 60),
            y: 160 + (Math.random() * 40 - 20),
            w: Math.random() * 8 + 4,
            h: Math.random() * 6 + 4,
            color: colors[Math.floor(Math.random() * colors.length)],
            vx: (Math.random() - 0.5) * 8 - 2,
            vy: Math.random() * -7 - 3,
            gravity: 0.28,
            rotation: Math.random() * 360,
            vRot: Math.random() * 10 - 5,
            opacity: 1
        });
    }
    
    let frame = 0;
    function render() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        pieces.forEach(p => {
            p.x += p.vx;
            p.y += p.vy;
            p.vy += p.gravity;
            p.rotation += p.vRot;
            p.opacity -= 0.012;
            
            ctx.save();
            ctx.translate(p.x, p.y);
            ctx.rotate((p.rotation * Math.PI) / 180);
            ctx.fillStyle = p.color;
            ctx.globalAlpha = Math.max(0, p.opacity);
            ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
            ctx.restore();
        });
        
        frame++;
        if (frame < 80) {
            requestAnimationFrame(render);
        } else {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
    }
    requestAnimationFrame(render);
}
</script>
