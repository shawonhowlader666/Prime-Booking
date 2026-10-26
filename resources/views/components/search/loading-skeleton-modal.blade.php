{{-- 100% Exact Agoda Full-Page Skeleton Screen with Shimmer Reflection Sweep & "Just a moment!" Mascot Dialog --}}
<style type="text/css">
    #ModalLoadingSpinner {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 9999999;
        background: #f8f9fa;
        overflow-y: auto;
        overflow-x: hidden;
        display: none;
        opacity: 0;
        transition: opacity 0.25s ease-in-out;
    }

    #ModalLoadingSpinner.is-active {
        display: block !important;
        opacity: 1 !important;
    }

    /* ─── Agoda Signature Shimmer Reflection / Light-Sweep Wave Animation ─── */
    @keyframes agodaReflectionSweep {
        0% {
            background-position: -900px 0;
        }
        100% {
            background-position: 900px 0;
        }
    }

    .agoda-shimmer-sweep {
        background-color: #e5e7eb !important;
        background-image: linear-gradient(
            105deg,
            #e5e7eb 0%,
            #eff1f4 25%,
            #ffffff 50%,
            #eff1f4 75%,
            #e5e7eb 100%
        ) !important;
        background-size: 900px 100% !important;
        background-repeat: no-repeat !important;
        animation: agodaReflectionSweep 1.6s cubic-bezier(0.4, 0, 0.2, 1) infinite !important;
    }

    /* ─── Centered Agoda Mascot Dialog ─── */
    .agoda-interstitial-dialog {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 10000005;
        width: 430px;
        max-width: calc(100vw - 32px);
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 20px 48px -8px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);
        padding: 36px 32px 32px 32px;
        text-align: center;
        box-sizing: border-box;
        animation: agodaPopModal 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes agodaPopModal {
        from {
            opacity: 0;
            transform: translate(-50%, -46%) scale(0.96);
        }
        to {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
    }

    .agoda-mascot-parachute-wrap {
        position: relative;
        width: 80px;
        height: 76px;
        margin: 0 auto 10px auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .agoda-mascot-parachute-wrap img {
        width: 72px;
        height: 72px;
        object-fit: contain;
        display: block;
        animation: agodaFloatMascot 2.4s ease-in-out infinite alternate;
    }

    @keyframes agodaFloatMascot {
        0% {
            transform: translateY(0px) rotate(-1.5deg);
        }
        100% {
            transform: translateY(-6px) rotate(1.5deg);
        }
    }

    .agoda-interstitial-title {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-size: 19px;
        font-weight: 700;
        color: #6366f1;
        margin: 0;
        letter-spacing: -0.2px;
    }

    .agoda-interstitial-desc {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-size: 13.5px;
        font-weight: 400;
        color: #475569;
        margin: 6px 0 0 0;
        line-height: 1.45;
    }

    /* Skeleton layout sizing */
    .agoda-skel-header-pill {
        height: 16px;
        border-radius: 9999px;
    }
    .agoda-skel-search-pill {
        height: 46px;
        border-radius: 9999px;
    }
</style>

<div id="ModalLoadingSpinner">
    {{-- 1. Exact Agoda Header Skeleton Bar --}}
    <div style="background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 14px 24px;">
        <div style="max-width: 1140px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 28px;">
                {{-- Agoda Logo Skeleton --}}
                <div class="agoda-shimmer-sweep" style="width: 90px; height: 28px; border-radius: 6px;"></div>
                {{-- Nav Items Skeletons --}}
                <div style="display: flex; align-items: center; gap: 18px;" class="d-none d-md-flex">
                    <div class="agoda-shimmer-sweep agoda-skel-header-pill" style="width: 88px;"></div>
                    <div class="agoda-shimmer-sweep agoda-skel-header-pill" style="width: 72px;"></div>
                    <div class="agoda-shimmer-sweep agoda-skel-header-pill" style="width: 80px;"></div>
                    <div class="agoda-shimmer-sweep agoda-skel-header-pill" style="width: 96px;"></div>
                    <div class="agoda-shimmer-sweep agoda-skel-header-pill" style="width: 78px;"></div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 14px;">
                <div class="agoda-shimmer-sweep" style="width: 58px; height: 24px; border-radius: 4px;"></div>
                <div class="agoda-shimmer-sweep" style="width: 32px; height: 32px; border-radius: 50%;"></div>
            </div>
        </div>
    </div>

    {{-- 2. Exact Agoda Subheader / Filter Pills Bar --}}
    <div style="background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 12px 24px;">
        <div style="max-width: 1140px; margin: 0 auto; display: flex; align-items: center; gap: 14px;">
            <div class="agoda-shimmer-sweep agoda-skel-search-pill" style="flex: 2; min-width: 160px;"></div>
            <div class="agoda-shimmer-sweep agoda-skel-search-pill" style="flex: 2; min-width: 160px;"></div>
            <div class="agoda-shimmer-sweep agoda-skel-search-pill" style="flex: 1.5; min-width: 140px;"></div>
            <div class="agoda-shimmer-sweep agoda-skel-search-pill" style="width: 130px; flex-shrink: 0;"></div>
        </div>
    </div>

    {{-- 3. Exact Agoda Search Results Layout Skeleton --}}
    <div style="max-width: 1140px; margin: 24px auto; padding: 0 20px;">
        <div style="display: flex; gap: 24px;">
            {{-- Left Sidebar Skeleton (Map + Filter Categories) --}}
            <div style="width: 260px; flex-shrink: 0;" class="d-none d-lg-block">
                {{-- Agoda Authentic Map Card Skeleton with bkg-map-entry.svg --}}
                <div style="height: 110px; border-radius: 10px; border: 1px solid #cbd5e1; position: relative; overflow: hidden; margin-bottom: 20px; background: #e2e8f0 url('{{ asset('images/agoda/bkg-map-entry.svg') }}') center/cover no-repeat; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    {{-- Central Location Marker Pin --}}
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 26px; height: 26px; border-radius: 50%; background: #f59e0b; border: 3px solid #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.25);"></div>
                </div>

                {{-- Filter Category Skeletons --}}
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; margin-bottom: 16px;">
                    <div class="agoda-shimmer-sweep" style="width: 60%; height: 16px; border-radius: 4px; margin-bottom: 16px;"></div>
                    <div class="agoda-shimmer-sweep" style="width: 85%; height: 12px; border-radius: 4px; margin-bottom: 10px;"></div>
                    <div class="agoda-shimmer-sweep" style="width: 70%; height: 12px; border-radius: 4px; margin-bottom: 10px;"></div>
                    <div class="agoda-shimmer-sweep" style="width: 90%; height: 12px; border-radius: 4px; margin-bottom: 10px;"></div>
                    <div class="agoda-shimmer-sweep" style="width: 65%; height: 12px; border-radius: 4px;"></div>
                </div>

                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px;">
                    <div class="agoda-shimmer-sweep" style="width: 50%; height: 16px; border-radius: 4px; margin-bottom: 16px;"></div>
                    <div class="agoda-shimmer-sweep" style="width: 100%; height: 8px; border-radius: 4px; margin-bottom: 14px;"></div>
                    <div style="display: flex; justify-content: space-between;">
                        <div class="agoda-shimmer-sweep" style="width: 35%; height: 12px; border-radius: 4px;"></div>
                        <div class="agoda-shimmer-sweep" style="width: 35%; height: 12px; border-radius: 4px;"></div>
                    </div>
                </div>
            </div>

            {{-- Right Main Hotel Cards Feed Skeleton --}}
            <div style="flex: 1; min-width: 0;">
                {{-- 4 Sort Tabs Skeleton --}}
                <div style="display: flex; gap: 10px; margin-bottom: 20px;" class="overflow-hidden">
                    <div class="agoda-shimmer-sweep" style="width: 100px; height: 38px; border-radius: 8px;"></div>
                    <div class="agoda-shimmer-sweep" style="width: 130px; height: 38px; border-radius: 8px;"></div>
                    <div class="agoda-shimmer-sweep" style="width: 120px; height: 38px; border-radius: 8px;"></div>
                    <div class="agoda-shimmer-sweep" style="width: 110px; height: 38px; border-radius: 8px;"></div>
                </div>

                {{-- 3 Realistic Agoda Hotel Card Skeletons with Shimmer Reflection Wave --}}
                @for ($i = 0; $i < 3; $i++)
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; height: 216px; margin-bottom: 18px; display: flex; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
                    {{-- Left Image Box Skeleton --}}
                    <div class="agoda-shimmer-sweep" style="width: 250px; height: 100%; flex-shrink: 0;"></div>

                    {{-- Right Content Skeleton --}}
                    <div style="flex: 1; padding: 18px 22px; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            {{-- Title & Star Rating --}}
                            <div class="agoda-shimmer-sweep" style="width: {{ $i == 0 ? '68%' : ($i == 1 ? '55%' : '62%') }}; height: 20px; border-radius: 4px; margin-bottom: 10px;"></div>
                            <div style="display: flex; gap: 5px; margin-bottom: 10px;">
                                <div class="agoda-shimmer-sweep" style="width: 12px; height: 12px; border-radius: 50%;"></div>
                                <div class="agoda-shimmer-sweep" style="width: 12px; height: 12px; border-radius: 50%;"></div>
                                <div class="agoda-shimmer-sweep" style="width: 12px; height: 12px; border-radius: 50%;"></div>
                                <div class="agoda-shimmer-sweep" style="width: 12px; height: 12px; border-radius: 50%;"></div>
                            </div>
                            {{-- Location & Landmark --}}
                            <div class="agoda-shimmer-sweep" style="width: 44%; height: 12px; border-radius: 4px; margin-bottom: 12px;"></div>
                            {{-- Promotional Badge Pill --}}
                            <div class="agoda-shimmer-sweep" style="width: 140px; height: 22px; border-radius: 9999px;"></div>
                        </div>

                        {{-- Bottom Row: Rating Badge & Price Block --}}
                        <div style="display: flex; align-items: flex-end; justify-content: space-between; border-top: 1px solid #f1f5f9; pt-2; margin-top: 8px;">
                            <div class="agoda-shimmer-sweep" style="width: 90px; height: 14px; border-radius: 4px;"></div>
                            <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                                <div class="agoda-shimmer-sweep" style="width: 60px; height: 11px; border-radius: 4px;"></div>
                                <div class="agoda-shimmer-sweep" style="width: 110px; height: 24px; border-radius: 6px;"></div>
                                <div class="agoda-shimmer-sweep" style="width: 70px; height: 10px; border-radius: 4px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- 4. Centered Interstitial Modal Dialog ("Just a moment!") --}}
    <div class="agoda-interstitial-dialog">
        <div class="agoda-mascot-parachute-wrap">
            <img src="{{ asset('images/agoda/agoji-parachute.gif') }}" alt="Agoda Mascot" />
        </div>
        <h3 class="agoda-interstitial-title">Just a moment!</h3>
        <p class="agoda-interstitial-desc">We're finding great stays for your dates and destination.</p>
    </div>
</div>

<script>
    window.showAgodaSearchLoading = function() {
        var modal = document.getElementById('ModalLoadingSpinner');
        if (modal) {
            modal.style.display = 'block';
            modal.classList.add('is-active');
            modal.style.opacity = '1';
            document.body.style.overflow = 'hidden';
        }
    };

    window.hideAgodaSearchLoading = function() {
        var modal = document.getElementById('ModalLoadingSpinner');
        if (modal) {
            modal.style.opacity = '0';
            setTimeout(function() {
                modal.classList.remove('is-active');
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }, 200);
        }
    };

    window.addEventListener('pageshow', function() {
        window.hideAgodaSearchLoading();
    });

    /* ─── 100% Identical Agoda Empty Destination Prompt Modal (Screenshot Parity) ─── */
    window.showAgodaEmptyDestinationModal = function() {
        if (typeof window.hideAgodaSearchLoading === 'function') {
            window.hideAgodaSearchLoading();
        }
        var modal = document.getElementById('AgodaEmptyDestinationModal');
        if (!modal) return;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(function() {
            modal.style.opacity = '1';
            var card = modal.querySelector('.agoda-empty-dest-card');
            if (card) card.style.transform = 'scale(1)';
        });
    };

    window.hideAgodaEmptyDestinationModal = function() {
        var modal = document.getElementById('AgodaEmptyDestinationModal');
        if (!modal) return;
        modal.style.opacity = '0';
        var card = modal.querySelector('.agoda-empty-dest-card');
        if (card) card.style.transform = 'scale(0.96)';
        setTimeout(function() {
            modal.style.display = 'none';
            document.body.style.overflow = '';
            var destInput = document.getElementById('agodaDestinationInput') || document.getElementById('mainDestInput');
            if (destInput) {
                destInput.focus();
            }
        }, 180);
    };

    document.addEventListener('click', function(e) {
        var modal = document.getElementById('AgodaEmptyDestinationModal');
        if (modal && modal.style.display === 'flex' && e.target === modal) {
            window.hideAgodaEmptyDestinationModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var modal = document.getElementById('AgodaEmptyDestinationModal');
            if (modal && modal.style.display === 'flex') {
                window.hideAgodaEmptyDestinationModal();
            }
        }
    });
</script>

{{-- Agoda Empty Destination Prompt Modal HTML (Exact Screenshot 1:1 Parity) --}}
<div id="AgodaEmptyDestinationModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.45); z-index: 9999999; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s ease;">
    <div class="agoda-empty-dest-card" style="position: relative; background: #ffffff; border-radius: 8px; box-shadow: 0 10px 32px rgba(0, 0, 0, 0.25); width: 480px; max-width: calc(100vw - 32px); padding: 40px 48px; text-align: center; box-sizing: border-box; transform: scale(0.96); transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);">
        {{-- Blue Thin Close Button (Exact Screenshot 1:1 Parity) --}}
        <button type="button" onclick="hideAgodaEmptyDestinationModal()" title="Close"
                style="position: absolute; top: 12px; right: 14px; background: transparent; border: none; color: #2067e1; cursor: pointer; padding: 4px; line-height: 1; display: flex; align-items: center; justify-content: center; outline: none; border-radius: 4px; transition: opacity 0.15s ease;"
                onmouseover="this.style.opacity='0.75'" onmouseout="this.style.opacity='1'">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.5 1.5L11.5 11.5M11.5 1.5L1.5 11.5" stroke="#2067e1" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>

        {{-- Centered Authentic Agoda Prompt Text (Exact Screenshot Text, Color, Font-Size & Spacing) --}}
        <p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; font-size: 15.5px; font-weight: 400; color: #4b5563; line-height: 1.5; margin: 0; text-align: center; letter-spacing: -0.1px;">
            Please enter the name of a country, city, airport, neighborhood, landmark, or property to proceed.
        </p>
    </div>
</div>

