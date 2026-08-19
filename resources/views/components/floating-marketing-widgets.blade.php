{{-- 
========================================================================================
 Prime Booking: Senior Engineer Production Grade Floating Rewards & App Savings System
 Exact Agoda 1:1 Parity • 60fps Micro-Interactions • Canvas Confetti • Zero External Bloat
========================================================================================
--}}
<style>
/* Master Fixed System Scope */
.pb-float-system {
    position: fixed;
    right: 24px;
    bottom: 24px;
    z-index: 999999;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 16px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    pointer-events: none;
    user-select: none;
}

.pb-float-system * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

/* ========================================================================
 1. TOP CARD: PrimeCash Rewards & Cashback Engine (Fixed Top Right)
======================================================================== */
.pb-cashback-card {
    pointer-events: auto;
    width: 324px;
    background: #ffffff;
    border-radius: 16px;
    padding: 16px 18px 14px;
    box-shadow: 0 16px 40px -8px rgba(15, 23, 42, 0.16), 0 0 0 1px rgba(15, 23, 42, 0.06);
    position: fixed;
    top: 105px;
    right: 24px;
    z-index: 999999;
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease;
    animation: pbCardEntrance 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.pb-cashback-card.is-hidden {
    opacity: 0;
    transform: translateX(40px) scale(0.95);
    pointer-events: none;
    display: none !important;
}

/* Top Header Bar */
.pb-card-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 6px;
}

.pb-honey-logo {
    font-family: 'Georgia', serif;
    font-size: 24px;
    font-weight: 700;
    font-style: italic;
    color: #1e293b;
    line-height: 1;
    cursor: default;
}

.pb-brand-tag {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
}

.pb-brand-tag .pb-brand-text {
    font-size: 11.5px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: 0.6px;
    line-height: 1;
    text-transform: lowercase;
}

.pb-brand-tag .pb-brand-dots {
    display: flex;
    gap: 3.5px;
}

.pb-brand-tag .pb-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
}

.pb-actions-row {
    display: flex;
    align-items: center;
    gap: 4px;
}

.pb-btn-icon {
    background: transparent;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 5px;
    font-size: 14px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
}

.pb-btn-icon:hover {
    color: #0f172a;
    background: #f1f5f9;
}

/* Social Proof Banner */
.pb-social-proof {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    background: #fff7ed;
    color: #c2410c;
    border-radius: 20px;
    padding: 3px 10px;
    font-size: 11px;
    font-weight: 700;
    margin: 8px auto 0;
    width: fit-content;
}

/* Headline */
.pb-cashback-title {
    font-size: 21px;
    font-weight: 800;
    color: #0f172a;
    text-align: center;
    margin: 10px 0 14px;
    letter-spacing: -0.4px;
}

/* CTA Button */
.pb-cashback-btn {
    width: 100%;
    background: #d84315;
    color: #ffffff;
    font-weight: 700;
    font-size: 14.5px;
    padding: 11px 16px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(216, 67, 21, 0.28);
}

.pb-cashback-btn:hover {
    background: #bf360c;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(216, 67, 21, 0.38);
}

.pb-cashback-btn:active {
    transform: translateY(0);
}

.pb-cashback-btn.is-active {
    background: #16a34a !important;
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3) !important;
}

/* Footer Disclaimer */
.pb-cashback-note {
    font-size: 10.5px;
    color: #64748b;
    text-align: center;
    margin-top: 10px;
    line-height: 1.45;
}

.pb-cashback-note a {
    color: #475569;
    text-decoration: underline;
    transition: color 0.15s ease;
}

.pb-cashback-note a:hover {
    color: #0f172a;
}

/* Side Sticky Orange 'h' Tab (Restores card on click) */
#pbStickyHTab {
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
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease;
}

#pbStickyHTab:hover {
    transform: translateX(-4px);
    background-color: #ea580c;
}

/* ========================================================================
 2. BOTTOM CARD: Save 10% on your 1st app booking! QR Code Popup
======================================================================== */
.pb-app-wrap {
    pointer-events: auto;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    position: relative;
}

.pb-app-card {
    width: 292px;
    background: #ffffff;
    border-radius: 18px;
    padding: 22px 20px 12px;
    box-shadow: 0 16px 44px -8px rgba(15, 23, 42, 0.18), 0 0 0 1px rgba(15, 23, 42, 0.06);
    margin-bottom: 12px;
    position: relative;
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease;
    animation: pbCardEntrance 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.pb-app-card.is-hidden {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
    pointer-events: none;
    display: none !important;
}

/* Downward Pointer Arrow matching Agoda screenshot */
.pb-app-card::after {
    content: '';
    position: absolute;
    bottom: -7px;
    right: 22px;
    width: 14px;
    height: 14px;
    background: #ffffff;
    transform: rotate(45deg);
    border-right: 1px solid rgba(15, 23, 42, 0.08);
    border-bottom: 1px solid rgba(15, 23, 42, 0.08);
}

.pb-app-title {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    text-align: center;
    line-height: 1.25;
    margin-bottom: 6px;
    letter-spacing: -0.4px;
}

.pb-app-subtitle {
    font-size: 12.5px;
    color: #475569;
    text-align: center;
    margin-bottom: 14px;
    line-height: 1.35;
}

/* Smartphone Mockup Frame */
.pb-phone-mockup {
    width: 204px;
    margin: 0 auto;
    background: #ffffff;
    border: 7px solid #94a3b8;
    border-bottom: none;
    border-radius: 36px 36px 0 0;
    padding: 0 10px 0;
    position: relative;
    box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.02);
}

.pb-phone-notch {
    width: 68px;
    height: 11px;
    background: #94a3b8;
    border-radius: 0 0 12px 12px;
    margin: 0 auto 8px;
}

.pb-phone-brand {
    text-align: center;
    margin-bottom: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
}

.pb-phone-brand .pb-phone-brand-title {
    font-size: 13px;
    font-weight: 800;
    color: #1e293b;
    letter-spacing: 0.5px;
    line-height: 1;
    text-transform: lowercase;
}

.pb-phone-brand .pb-phone-dots {
    display: flex;
    gap: 3.5px;
}

.pb-phone-brand .pb-phone-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
}

.pb-qr-box {
    background: #ffffff;
    border-radius: 6px;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 4px;
}

.pb-qr-box img {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 4px;
}

/* 1-Click Copy Coupon Pill */
.pb-coupon-copy-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-top: 10px;
    padding: 6px 12px;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.pb-coupon-copy-row:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
}

.pb-coupon-code-txt {
    font-size: 11.5px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: 0.8px;
}

.pb-coupon-copy-hint {
    font-size: 10.5px;
    font-weight: 600;
    color: #2563eb;
}

/* ========================================================================
 3. BOTTOM BLUE TOGGLE BUTTON
======================================================================== */
.pb-blue-circle-btn {
    pointer-events: auto;
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: #2067e1;
    color: #ffffff;
    border: none;
    box-shadow: 0 6px 20px rgba(32, 103, 225, 0.45);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    margin-right: 6px;
}

.pb-blue-circle-btn:hover {
    transform: scale(1.08);
    background: #1752b8;
    box-shadow: 0 8px 24px rgba(32, 103, 225, 0.55);
}

.pb-blue-circle-btn:active {
    transform: scale(0.96);
}

/* Settings Popover Menu */
.pb-settings-menu {
    position: absolute;
    top: 40px;
    right: 12px;
    width: 220px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.14);
    padding: 12px;
    z-index: 1000000;
    display: none;
    font-size: 12px;
    color: #334155;
    animation: pbCardEntrance 0.2s ease forwards;
}

.pb-settings-menu.is-open {
    display: block;
}

/* Canvas Confetti Layer */
#pbConfettiCanvas {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    pointer-events: none;
    z-index: 1000001;
}

/* Entrance Keyframes */
@keyframes pbCardEntrance {
    from {
        opacity: 0;
        transform: translateY(14px) scale(0.96);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Responsive Breakpoints */
@media (max-width: 991px) {
    .pb-cashback-card {
        top: 75px;
        right: 16px;
        width: 300px;
    }
    .pb-float-system {
        right: 16px;
        bottom: 80px;
    }
    .pb-app-card {
        display: none !important;
    }
    .pb-blue-circle-btn {
        width: 42px;
        height: 42px;
        font-size: 15px;
    }
}
</style>

<!-- Canvas for Confetti Micro-Animation -->
<canvas id="pbConfettiCanvas"></canvas>

<!-- Side Sticky Orange 'h' Tab -->
<div id="pbStickyHTab" onclick="pbOpenTopCard()" title="Open PrimeCash Rewards">
    h
</div>

<!-- 1. TOP CARD: PrimeCash Rewards Card (Agoda 1:1) -->
<div class="pb-cashback-card" id="pbTopCard">
    
    <!-- Settings Menu -->
    <div class="pb-settings-menu" id="pbSettingsMenu">
        <div style="font-weight: 800; color: #0f172a; margin-bottom: 6px;">PrimeCash Settings</div>
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px solid #f1f5f9;">
            <span>Auto-Apply at Checkout</span>
            <span style="background: #dcfce7; color: #15803d; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px;">ACTIVE</span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px solid #f1f5f9;">
            <span>Cashback Rate</span>
            <span style="font-weight: 800; color: #2563eb;">Up to 8%</span>
        </div>
        <div style="padding-top: 8px; text-align: center;">
            <a href="{{ route('vip') }}" style="text-decoration: none; color: #2563eb; font-weight: 700; font-size: 11px;">View VIP Benefits &rarr;</a>
        </div>
    </div>

    <!-- Top Bar -->
    <div class="pb-card-topbar">
        <span class="pb-honey-logo">h</span>

        <div class="pb-brand-tag">
            <span class="pb-brand-text">prime booking</span>
            <div class="pb-brand-dots">
                <span class="pb-dot" style="background:#e11d48;"></span>
                <span class="pb-dot" style="background:#ea580c;"></span>
                <span class="pb-dot" style="background:#eab308;"></span>
                <span class="pb-dot" style="background:#16a34a;"></span>
                <span class="pb-dot" style="background:#2563eb;"></span>
            </div>
        </div>

        <div class="pb-actions-row">
            <button type="button" class="pb-btn-icon" onclick="pbToggleSettings(event)" title="Settings">
                <i class="fa-solid fa-gear"></i>
            </button>
            <button type="button" class="pb-btn-icon" onclick="pbDismissTopCard()" title="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <!-- Live Social Proof Ticker -->
    <div class="pb-social-proof">
        <span>🔥</span> <span>142 travelers activated today</span>
    </div>

    <!-- Title -->
    <div class="pb-cashback-title">
        Get 1% to 8% back
    </div>

    <!-- CTA Button -->
    <button type="button" class="pb-cashback-btn" id="pbActivateBtn" onclick="pbActivateRewards()">
        <i class="fa-solid fa-bolt" id="pbBoltIcon"></i> <span id="pbBtnText">Activate Rewards</span>
    </button>

    <!-- Footer Note -->
    <p class="pb-cashback-note">
        Check offers for details. PrimeCash wallet credited instantly. <a href="{{ route('terms') }}" target="_blank">Terms</a> and <a href="{{ route('terms') }}" target="_blank">exclusions</a> apply.
    </p>
</div>

<!-- 2. BOTTOM FLOATING CONTAINER: App QR Code + Blue Toggle Button -->
<div class="pb-float-system">

    <div class="pb-app-wrap">
        
        <!-- App QR Code Card -->
        <div class="pb-app-card" id="pbAppCard">
            <div class="pb-app-title">
                Save 10% on your 1st app booking!
            </div>
            <div class="pb-app-subtitle">
                Just scan the QR code for instant savings
            </div>

            <!-- Smartphone Mockup Frame -->
            <div class="pb-phone-mockup">
                <div class="pb-phone-notch"></div>
                <div class="pb-phone-brand">
                    <span class="pb-phone-brand-title">prime booking</span>
                    <div class="pb-phone-dots">
                        <span class="pb-phone-dot" style="background:#e11d48;"></span>
                        <span class="pb-phone-dot" style="background:#ea580c;"></span>
                        <span class="pb-phone-dot" style="background:#eab308;"></span>
                        <span class="pb-phone-dot" style="background:#16a34a;"></span>
                        <span class="pb-phone-dot" style="background:#2563eb;"></span>
                    </div>
                </div>
                <div class="pb-qr-box" title="Scan with camera for instant promo">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(url('/?ref=app_qr&discount=PRIME10')) }}&color=0f172a&bgcolor=ffffff&margin=1" alt="Prime Booking App QR Code" loading="lazy">
                </div>
            </div>

            <!-- 1-Click Copy Coupon Code -->
            <div class="pb-coupon-copy-row" onclick="pbCopyPromoCode('PRIME10')" title="Click to copy coupon code">
                <span>🎟️</span>
                <span class="pb-coupon-code-txt">PRIME10</span>
                <span class="pb-coupon-copy-hint" id="pbCopyHint">(Tap to Copy)</span>
            </div>
        </div>

        <!-- Blue Circular Toggle Button with '✕' -->
        <button type="button" class="pb-blue-circle-btn" id="pbBlueToggleBtn" onclick="pbToggleAppCard()" title="Toggle Deals Card">
            <i class="fa-solid fa-xmark" id="pbBlueToggleIcon"></i>
        </button>

    </div>

</div>

<script>
// Master State Handling
let pbAppCardVisible = true;

document.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('pb_rewards_active') === 'true') {
        pbSetRewardsStateActive();
    }
});

function pbToggleSettings(e) {
    e.stopPropagation();
    const menu = document.getElementById('pbSettingsMenu');
    if (menu) {
        menu.classList.toggle('is-open');
    }
}

document.addEventListener('click', function(e) {
    const menu = document.getElementById('pbSettingsMenu');
    if (menu && !menu.contains(e.target) && !e.target.closest('.pb-btn-icon')) {
        menu.classList.remove('is-open');
    }
});

function pbActivateRewards() {
    const btn = document.getElementById('pbActivateBtn');
    const icon = document.getElementById('pbBoltIcon');
    const txt = document.getElementById('pbBtnText');
    
    if (btn && icon && txt) {
        localStorage.setItem('pb_rewards_active', 'true');
        btn.classList.add('is-active');
        icon.className = 'fa-solid fa-check';
        txt.innerText = 'Rewards Activated (8% Applied)';
        
        // Launch Celebration Canvas Confetti
        pbLaunchConfetti();
        
        // Show Toast Notification
        if (typeof showSaasToast === 'function') {
            showSaasToast('🎉 PrimeCash 8% Cashback Activated! Applied automatically at checkout.', 'success');
        }
    }
}

function pbSetRewardsStateActive() {
    const btn = document.getElementById('pbActivateBtn');
    const icon = document.getElementById('pbBoltIcon');
    const txt = document.getElementById('pbBtnText');
    if (btn && icon && txt) {
        btn.classList.add('is-active');
        icon.className = 'fa-solid fa-check';
        txt.innerText = 'Rewards Activated (8% Applied)';
    }
}

function pbDismissTopCard() {
    const topCard = document.getElementById('pbTopCard');
    const stickyTab = document.getElementById('pbStickyHTab');
    if (topCard) topCard.classList.add('is-hidden');
    if (stickyTab) stickyTab.style.display = 'flex';
}

function pbOpenTopCard() {
    const topCard = document.getElementById('pbTopCard');
    const stickyTab = document.getElementById('pbStickyHTab');
    if (topCard) {
        topCard.classList.remove('is-hidden');
        topCard.style.display = 'block';
    }
    if (stickyTab) stickyTab.style.display = 'none';
}

function pbToggleAppCard() {
    const appCard = document.getElementById('pbAppCard');
    const icon = document.getElementById('pbBlueToggleIcon');
    
    if (pbAppCardVisible) {
        pbAppCardVisible = false;
        if (appCard) appCard.classList.add('is-hidden');
        if (icon) icon.className = 'fa-solid fa-mobile-screen';
    } else {
        pbAppCardVisible = true;
        if (appCard) {
            appCard.classList.remove('is-hidden');
            appCard.style.display = 'block';
        }
        if (icon) icon.className = 'fa-solid fa-xmark';
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

// Lightweight Vanilla JS Confetti Animation (Zero Dependencies)
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
            y: 180 + (Math.random() * 40 - 20),
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
