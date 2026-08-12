{{-- Agoda 1:1 Exact Matching Skeleton Loading Screen with 7-Color Left-to-Right Bouncing Wave Loader --}}
<div id="agodaSearchLoadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(235, 239, 245, 0.98); z-index: 999999; overflow: hidden; font-family: BlinkMacSystemFont, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

    {{-- 1. Skeleton Top Header Navigation --}}
    <div style="background: #e2e8f0; padding: 12px 24px; border-bottom: 1px solid #cbd5e1; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 12px; align-items: center;">
            <div class="skeleton-pill" style="width: 140px; height: 28px; background: #cbd5e1; border-radius: 14px;"></div>
            <div class="skeleton-pill" style="width: 120px; height: 28px; background: #cbd5e1; border-radius: 14px;"></div>
            <div class="skeleton-pill" style="width: 120px; height: 28px; background: #cbd5e1; border-radius: 14px;"></div>
            <div class="skeleton-pill" style="width: 110px; height: 28px; background: #cbd5e1; border-radius: 14px;"></div>
        </div>
        <div>
            <div class="skeleton-pill" style="width: 200px; height: 28px; background: #cbd5e1; border-radius: 14px;"></div>
        </div>
    </div>

    {{-- 2. Main Skeleton Workspace Container --}}
    <div style="max-width: 1280px; margin: 24px auto 0 auto; padding: 0 16px;">
        
        {{-- Skeleton 4-Tab Pod Navigation Header --}}
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); border: 1px solid #cbd5e1; background: #f1f5f9; border-radius: 4px 4px 0 0;">
            <div style="background: #ffffff; padding: 14px; text-align: center; border-bottom: 2px solid #94a3b8; display: flex; justify-content: center; align-items: center;">
                <div style="width: 80px; height: 16px; background: #94a3b8; border-radius: 3px;"></div>
            </div>
            <div style="padding: 14px; text-align: center; border-left: 1px solid #cbd5e1; display: flex; justify-content: center; align-items: center;">
                <div style="width: 80px; height: 16px; background: #cbd5e1; border-radius: 3px;"></div>
            </div>
            <div style="padding: 14px; text-align: center; border-left: 1px solid #cbd5e1; display: flex; justify-content: center; align-items: center;">
                <div style="width: 80px; height: 16px; background: #cbd5e1; border-radius: 3px;"></div>
            </div>
            <div style="padding: 14px; text-align: center; border-left: 1px solid #cbd5e1; display: flex; justify-content: center; align-items: center;">
                <div style="width: 80px; height: 16px; background: #cbd5e1; border-radius: 3px;"></div>
            </div>
        </div>

        {{-- Skeleton Page Body Grid (Left Map + Right Hotel Cards Stack) --}}
        <div style="display: flex; gap: 20px; margin-top: 16px;">
            
            {{-- Left Map Box Skeleton --}}
            <div style="width: 250px; flex-shrink: 0;">
                <div style="width: 100%; height: 170px; background: #cad2d9; border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden; position: relative;">
                    <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                        <rect width="100%" height="100%" fill="#d8e2ea"/>
                        <path d="M 170 0 L 250 0 L 250 80 L 200 60 Z" fill="#a7d49b"/>
                        <path d="M 0 100 L 70 80 L 100 170 L 0 170 Z" fill="#93c47d"/>
                        <path d="M -10 90 Q 60 70 120 100 T 260 80" stroke="#f6c244" stroke-width="12" fill="none"/>
                        <path d="M 120 0 L 120 170" stroke="#f6c244" stroke-width="10" fill="none"/>
                        <line x1="30" y1="0" x2="30" y2="170" stroke="#ffffff" stroke-width="5"/>
                        <line x1="200" y1="0" x2="200" y2="170" stroke="#ffffff" stroke-width="5"/>
                        <line x1="0" y1="40" x2="250" y2="40" stroke="#ffffff" stroke-width="4"/>
                        <line x1="0" y1="140" x2="250" y2="140" stroke="#ffffff" stroke-width="4"/>
                        <circle cx="120" cy="100" r="8" fill="#f6c244" stroke="#ffffff" stroke-width="3"/>
                    </svg>
                </div>
            </div>

            {{-- Right Hotel Cards List Skeletons --}}
            <div style="flex-grow: 1; display: flex; flex-direction: column; gap: 16px;">
                
                {{-- Card Skeleton 1 --}}
                <div style="background: #e2e8f0; border: 1px solid #cbd5e1; border-radius: 4px; display: flex; height: 190px; overflow: hidden; position: relative;">
                    <div style="width: 240px; background: #5c6470; flex-shrink: 0;"></div>
                    <div style="flex-grow: 1; padding: 18px; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="width: 60%; height: 16px; background: #94a3b8; border-radius: 3px; margin-bottom: 12px;"></div>
                            <div style="width: 85%; height: 12px; background: #cbd5e1; border-radius: 3px; margin-bottom: 8px;"></div>
                            <div style="width: 40%; height: 12px; background: #cbd5e1; border-radius: 3px;"></div>
                        </div>
                        <div style="display: flex; justify-content: flex-end; align-items: flex-end; gap: 12px;">
                            <div>
                                <div style="width: 100px; height: 14px; background: #cbd5e1; border-radius: 3px; margin-bottom: 6px;"></div>
                                <div style="width: 140px; height: 26px; background: #94a3b8; border-radius: 3px;"></div>
                            </div>
                            <div style="width: 50px; height: 40px; background: #64748b; border-radius: 4px;"></div>
                        </div>
                    </div>
                </div>

                {{-- Card Skeleton 2 --}}
                <div style="background: #e2e8f0; border: 1px solid #cbd5e1; border-radius: 4px; display: flex; height: 190px; overflow: hidden; position: relative;">
                    <div style="width: 240px; background: #5c6470; flex-shrink: 0;"></div>
                    <div style="flex-grow: 1; padding: 18px; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="width: 70%; height: 16px; background: #94a3b8; border-radius: 3px; margin-bottom: 12px;"></div>
                            <div style="width: 90%; height: 12px; background: #cbd5e1; border-radius: 3px; margin-bottom: 8px;"></div>
                            <div style="width: 35%; height: 12px; background: #cbd5e1; border-radius: 3px; margin-bottom: 8px;"></div>
                        </div>
                        <div style="display: flex; justify-content: flex-end; align-items: flex-end; gap: 12px;">
                            <div style="width: 60px; height: 40px; background: #64748b; border-radius: 4px;"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- 3. Agoda 1:1 Centered Modal Popup with Brand Logo & 7-Color Left-to-Right Bouncing Dots --}}
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; z-index: 1000000; background: rgba(0, 0, 0, 0.12); backdrop-filter: blur(4px);">
        <div style="background: #ffffff; width: 460px; max-width: 92vw; padding: 36px 30px; border-radius: 20px; text-align: center; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.22); border: 1px solid rgba(226, 232, 240, 0.9);">
            
            {{-- Official Brand Logo --}}
            <div style="margin: 0 auto 20px auto; text-align: center;">
                <img src="{{ asset('images/logo.png') }}" alt="Prime Booking" style="height: 52px; max-width: 220px; object-fit: contain;">
            </div>

            {{-- 7-Color Left-to-Right Bouncing Wave Dots (Agoda Signature Multi-Color Dots) --}}
            <div class="agoda-7dots-loader" style="display: flex; align-items: center; justify-content: center; gap: 9px; margin-bottom: 22px;">
                <span class="dot" style="background-color: #FF385C; animation-delay: 0s;"></span>
                <span class="dot" style="background-color: #FF7E29; animation-delay: 0.12s;"></span>
                <span class="dot" style="background-color: #FFC107; animation-delay: 0.24s;"></span>
                <span class="dot" style="background-color: #22C55E; animation-delay: 0.36s;"></span>
                <span class="dot" style="background-color: #0EA5E9; animation-delay: 0.48s;"></span>
                <span class="dot" style="background-color: #2067E1; animation-delay: 0.60s;"></span>
                <span class="dot" style="background-color: #8B5CF6; animation-delay: 0.72s;"></span>
            </div>

            {{-- Heading & Subtitle --}}
            <h4 style="color: #2067e1; font-weight: 800; font-size: 20px; margin: 0 0 8px 0; letter-spacing: -0.3px;">
                Just a moment!
            </h4>
            <p style="color: #475569; font-size: 14px; margin: 0; line-height: 1.5; font-weight: 500;">
                We're finding the best hotels, resorts & stays for you...
            </p>
        </div>
    </div>

</div>

{{-- CSS for 7-Color Bouncing Dots & Skeleton Pulse --}}
<style>
    .agoda-7dots-loader .dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        display: inline-block;
        animation: agoda7ColorBounce 1.2s ease-in-out infinite;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
    }

    @keyframes agoda7ColorBounce {
        0%, 100% {
            transform: translateY(0) scale(0.9);
            opacity: 0.6;
        }
        40% {
            transform: translateY(-18px) scale(1.3);
            opacity: 1;
            box-shadow: 0 10px 18px rgba(0, 0, 0, 0.25);
        }
    }

    @keyframes agodaSkeletonPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.55; }
    }
    #agodaSearchLoadingOverlay .skeleton-pill,
    #agodaSearchLoadingOverlay [style*="background: #94a3b8"],
    #agodaSearchLoadingOverlay [style*="background: #cbd5e1"],
    #agodaSearchLoadingOverlay [style*="background: #5c6470"],
    #agodaSearchLoadingOverlay [style*="background: #64748b"] {
        animation: agodaSkeletonPulse 1.4s ease-in-out infinite;
    }
</style>

{{-- Global Javascript Function to trigger loader on search, login, reload --}}
<script>
    window.showAgodaSearchLoading = function() {
        var overlay = document.getElementById('agodaSearchLoadingOverlay');
        if (overlay) {
            overlay.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    };

    // Auto-trigger loader on all form submissions (Login, Register, Search)
    document.addEventListener('DOMContentLoaded', function() {
        var forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
            form.addEventListener('submit', function() {
                window.showAgodaSearchLoading();
            });
        });
    });

    // Hide loader automatically if user navigates back using browser history
    window.addEventListener('pageshow', function(event) {
        var overlay = document.getElementById('agodaSearchLoadingOverlay');
        if (overlay) {
            overlay.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
</script>
