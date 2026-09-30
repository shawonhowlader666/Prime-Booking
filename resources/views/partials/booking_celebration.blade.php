<!-- Prime Booking Master Celebration Engine (Floating 3D Balloons + Confetti Canvas + Audio Chime) -->
<style>
    #celebration-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        pointer-events: none;
        z-index: 999999;
        overflow: hidden;
    }

    .balloon {
        position: absolute;
        bottom: -150px;
        width: 60px;
        height: 75px;
        border-radius: 50% 50% 50% 50% / 40% 40% 60% 60%;
        box-shadow: inset -8px -8px 16px rgba(0, 0, 0, 0.25), 0 10px 24px rgba(0, 0, 0, 0.2);
        animation: floatUpBalloon 5.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .balloon::before {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 50%;
        transform: translateX(-50%);
        width: 8px;
        height: 6px;
        background: inherit;
        border-radius: 2px 2px 4px 4px;
    }

    .balloon::after {
        content: '';
        position: absolute;
        bottom: -55px;
        left: 50%;
        width: 1.5px;
        height: 50px;
        background: rgba(255, 255, 255, 0.6);
        transform: translateX(-50%);
    }

    .balloon-shine {
        position: absolute;
        top: 14px;
        left: 12px;
        width: 14px;
        height: 22px;
        background: rgba(255, 255, 255, 0.55);
        border-radius: 50%;
        transform: rotate(-35deg);
    }

    @keyframes floatUpBalloon {
        0% {
            bottom: -150px;
            transform: translateX(0) rotate(0deg) scale(0.85);
            opacity: 0.95;
        }
        50% {
            transform: translateX(25px) rotate(8deg) scale(1);
        }
        75% {
            transform: translateX(-20px) rotate(-6deg) scale(1.05);
        }
        100% {
            bottom: 110vh;
            transform: translateX(35px) rotate(12deg) scale(1.1);
            opacity: 0;
        }
    }

    /* Confetti Canvas */
    #celebration-canvas {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        pointer-events: none;
        z-index: 999998;
    }
</style>

<div id="celebration-overlay"></div>
<canvas id="celebration-canvas"></canvas>

<script>
(function() {
    // 1. Synthesized Web Audio Fanfare Chime (Zero External Files, 100% Reliable)
    function playCelebrationFanfare() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();

            const notes = [
                { freq: 523.25, time: 0.0,  dur: 0.25 }, // C5
                { freq: 659.25, time: 0.2,  dur: 0.25 }, // E5
                { freq: 783.99, time: 0.4,  dur: 0.35 }, // G5
                { freq: 1046.5, time: 0.65, dur: 0.85 }  // C6 (High Grand Finale)
            ];

            notes.forEach(n => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(n.freq, ctx.currentTime + n.time);
                gain.gain.setValueAtTime(0.25, ctx.currentTime + n.time);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + n.time + n.dur);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(ctx.currentTime + n.time);
                osc.stop(ctx.currentTime + n.time + n.dur);
            });
        } catch(e) {
            console.log('Audio chime init:', e);
        }
    }

    // 2. Flying Colorful 3D Balloons Spawner
    function spawnBalloons(count = 18) {
        const overlay = document.getElementById('celebration-overlay');
        if (!overlay) return;

        const colors = [
            'linear-gradient(135deg, #ff4b4b, #d90429)', // Crimson
            'linear-gradient(135deg, #00b074, #007f5f)', // Emerald
            'linear-gradient(135deg, #3a86ff, #004e92)', // Royal Blue
            'linear-gradient(135deg, #ffb703, #fb8500)', // Radiant Gold
            'linear-gradient(135deg, #8338ec, #5a189a)', // Purple
            'linear-gradient(135deg, #06d6a0, #118ab2)', // Turquoise
            'linear-gradient(135deg, #ff006e, #8338ec)'  // Magenta Pink
        ];

        for (let i = 0; i < count; i++) {
            setTimeout(() => {
                const b = document.createElement('div');
                b.className = 'balloon';
                b.style.background = colors[i % colors.length];
                b.style.left = (Math.random() * 85 + 5) + 'vw';
                b.style.animationDuration = (Math.random() * 2 + 4) + 's';
                b.style.transform = `scale(${Math.random() * 0.4 + 0.8})`;

                const shine = document.createElement('div');
                shine.className = 'balloon-shine';
                b.appendChild(shine);

                overlay.appendChild(b);

                setTimeout(() => {
                    if (b.parentNode) b.parentNode.removeChild(b);
                }, 6000);
            }, i * 140);
        }
    }

    // 3. Multi-Colored Particle Confetti Canvas Burst
    function launchConfetti(particleCount = 120) {
        const canvas = document.getElementById('celebration-canvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const particles = [];
        const colors = ['#00B074', '#FF4B4B', '#3A86FF', '#FFB703', '#8338EC', '#06D6A0', '#FF006E'];

        for (let i = 0; i < particleCount; i++) {
            particles.push({
                x: canvas.width / 2,
                y: canvas.height * 0.45,
                r: Math.random() * 6 + 4,
                color: colors[Math.floor(Math.random() * colors.length)],
                vx: (Math.random() - 0.5) * 18,
                vy: (Math.random() - 0.5) * 18 - 6,
                rotation: Math.random() * 360,
                dr: (Math.random() - 0.5) * 10,
                opacity: 1,
                decay: Math.random() * 0.015 + 0.008
            });
        }

        let animationFrame;
        function render() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            let active = false;

            particles.forEach(p => {
                p.x += p.vx;
                p.y += p.vy;
                p.vy += 0.35; // Gravity
                p.vx *= 0.98;
                p.rotation += p.dr;
                p.opacity -= p.decay;

                if (p.opacity > 0) {
                    active = true;
                    ctx.save();
                    ctx.translate(p.x, p.y);
                    ctx.rotate(p.rotation * Math.PI / 180);
                    ctx.globalAlpha = Math.max(0, p.opacity);
                    ctx.fillStyle = p.color;
                    ctx.fillRect(-p.r / 2, -p.r / 2, p.r, p.r * 1.5);
                    ctx.restore();
                }
            });

            if (active) {
                animationFrame = requestAnimationFrame(render);
            } else {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                cancelAnimationFrame(animationFrame);
            }
        }

        render();
    }

    // 4. Master Global Trigger
    window.celebrateBooking = function() {
        playCelebrationFanfare();
        spawnBalloons(22);
        launchConfetti(150);

        if (window.showToast) {
            window.showToast('🎉 Booking Confirmed! +Rewards Added to Wallet!', 'success', 5000);
        }
    };

    // Auto trigger on booking confirmation page load
    document.addEventListener('DOMContentLoaded', function() {
        window.celebrateBooking();
    });
})();
</script>
