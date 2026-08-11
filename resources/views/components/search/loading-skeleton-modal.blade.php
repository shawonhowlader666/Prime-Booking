{{-- Agoda 1:1 Exact Matching Skeleton Loading Screen & "Just a moment!" Modal Overlay --}}
<div id="agodaSearchLoadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(230, 233, 238, 0.98); z-index: 999999; overflow: hidden; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

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
            
            {{-- Left Map Box Skeleton (Matching Screenshot Map Texture & Pins) --}}
            <div style="width: 250px; flex-shrink: 0;">
                <div style="width: 100%; height: 170px; background: #cad2d9; border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden; position: relative;">
                    <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                        <rect width="100%" height="100%" fill="#d8e2ea"/>
                        {{-- Green parks --}}
                        <path d="M 170 0 L 250 0 L 250 80 L 200 60 Z" fill="#a7d49b"/>
                        <path d="M 0 100 L 70 80 L 100 170 L 0 170 Z" fill="#93c47d"/>
                        {{-- Yellow main road network --}}
                        <path d="M -10 90 Q 60 70 120 100 T 260 80" stroke="#f6c244" stroke-width="12" fill="none"/>
                        <path d="M 120 0 L 120 170" stroke="#f6c244" stroke-width="10" fill="none"/>
                        {{-- Secondary white streets --}}
                        <line x1="30" y1="0" x2="30" y2="170" stroke="#ffffff" stroke-width="5"/>
                        <line x1="200" y1="0" x2="200" y2="170" stroke="#ffffff" stroke-width="5"/>
                        <line x1="0" y1="40" x2="250" y2="40" stroke="#ffffff" stroke-width="4"/>
                        <line x1="0" y1="140" x2="250" y2="140" stroke="#ffffff" stroke-width="4"/>
                        {{-- Location Pin Ring --}}
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
                            <div style="width: 25%; height: 12px; background: #cbd5e1; border-radius: 3px;"></div>
                        </div>
                        <div style="display: flex; justify-content: flex-end; align-items: flex-end; gap: 12px;">
                            <div style="width: 60px; height: 40px; background: #64748b; border-radius: 4px;"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- 3. Agoda 1:1 Centered Modal Dialog Popup --}}
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; z-index: 1000000; background: rgba(0, 0, 0, 0.08);">
        <div style="background: #ffffff; width: 440px; max-width: 90vw; padding: 36px 28px; border-radius: 12px; text-align: center; box-shadow: 0 16px 36px rgba(0, 0, 0, 0.18); border: 1px solid rgba(226, 232, 240, 0.8);">
            
            {{-- Parachute Mascot Vector Graphic (Exact Agoda Character) --}}
            <div style="margin: 0 auto 16px auto; width: 80px; height: 75px; position: relative;">
                <svg width="80" height="75" viewBox="0 0 80 75" fill="none" xmlns="http://www.w3.org/2000/svg">
                    {{-- Parachute Canopy (Blue / Cyan Stripes) --}}
                    <path d="M 18 30 C 18 10, 62 10, 62 30 Z" fill="#28A745"/>
                    <path d="M 22 28 C 22 12, 38 12, 40 28 Z" fill="#007BFF"/>
                    <path d="M 40 28 C 42 12, 58 12, 58 28 Z" fill="#17A2B8"/>
                    
                    {{-- Parachute Strings --}}
                    <line x1="22" y1="28" x2="38" y2="44" stroke="#6C757D" stroke-width="1.2"/>
                    <line x1="32" y1="28" x2="39" y2="44" stroke="#6C757D" stroke-width="1.2"/>
                    <line x1="48" y1="28" x2="41" y2="44" stroke="#6C757D" stroke-width="1.2"/>
                    <line x1="58" y1="28" x2="42" y2="44" stroke="#6C757D" stroke-width="1.2"/>
                    
                    {{-- Cute Yellow Character --}}
                    <circle cx="40" cy="48" r="13" fill="#FFC107"/>
                    {{-- Eyes & Smile --}}
                    <circle cx="36" cy="46" r="1.5" fill="#212529"/>
                    <circle cx="44" cy="46" r="1.5" fill="#212529"/>
                    <path d="M 37 51 Q 40 54 43 51" stroke="#212529" stroke-width="1.2" fill="none"/>
                    
                    {{-- Arms / Hands --}}
                    <path d="M 28 47 Q 24 43 22 47" stroke="#FFC107" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                    <path d="M 52 47 Q 56 43 58 47" stroke="#FFC107" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                    
                    {{-- Floating Little Clouds / Dots --}}
                    <ellipse cx="14" cy="18" rx="6" ry="3" fill="#E2E8F0"/>
                    <ellipse cx="68" cy="22" rx="5" ry="2.5" fill="#E2E8F0"/>
                    <circle cx="10" cy="38" r="2" fill="#E2E8F0"/>
                    <circle cx="72" cy="42" r="2" fill="#E2E8F0"/>
                </svg>
            </div>

            {{-- Heading & Subtitle --}}
            <h3 style="color: #6f42c1; font-weight: 700; font-size: 21px; margin: 0 0 8px 0; font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.2px;">
                Just a moment!
            </h3>
            <p style="color: #212529; font-size: 14px; margin: 0; line-height: 1.45; font-weight: 400;">
                We're finding great stays for your dates and destination.
            </p>
        </div>
    </div>

</div>

{{-- Pulse / Shimmer Keyframe CSS --}}
<style>
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

{{-- Global Javascript Function to trigger loader --}}
<script>
    window.showAgodaSearchLoading = function() {
        var overlay = document.getElementById('agodaSearchLoadingOverlay');
        if (overlay) {
            overlay.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    };

    // Hide loader automatically if user navigates back using browser history
    window.addEventListener('pageshow', function(event) {
        var overlay = document.getElementById('agodaSearchLoadingOverlay');
        if (overlay) {
            overlay.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
</script>
