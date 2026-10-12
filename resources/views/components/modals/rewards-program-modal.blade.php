{{-- ── 1:1 EXACT AGODA "Select your rewards program" MODAL (Bangladeshi & Prime Booking Localized) ── --}}
<div class="modal fade rewards-program-modal-backdrop" id="selectRewardsProgramModal" tabindex="-1" aria-labelledby="selectRewardsProgramModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 790px;">
        <div class="modal-content border-0 rewards-modal-content position-relative">
            
            {{-- Agoda Top-Right Close Button (Large crisp ✕) --}}
            <button type="button" class="rewards-modal-close-btn" data-bs-dismiss="modal" aria-label="Close" id="closeRewardsModalBtn">
                &times;
            </button>

            {{-- ════════ STEP 1: BROWSE & SEARCH REWARDS PROGRAMS ════════ --}}
            <div id="rewardsStepBrowse" class="rewards-step-panel active">
                <div class="rewards-modal-header">
                    <h3 class="rewards-modal-title" id="selectRewardsProgramModalLabel">
                        Select your rewards program
                    </h3>
                    <p class="rewards-modal-subtitle">
                        Link your rewards program to earn miles/points when making bookings on Prime Booking.
                    </p>
                    
                    {{-- 1:1 Agoda Signature Search Input with Blue Focus Border --}}
                    <div class="rewards-search-box-wrap">
                        <input type="text" 
                               id="rewardsProgramSearchInput" 
                               class="rewards-search-input" 
                               placeholder="Search a reward program" 
                               autocomplete="off" 
                               spellcheck="false">
                        <i class="fa-solid fa-magnifying-glass rewards-search-icon"></i>
                        <button type="button" id="clearRewardsSearchBtn" class="rewards-clear-search-btn" style="display: none;">&times;</button>
                    </div>

                    {{-- Quick Filter Pills --}}
                    <div class="rewards-filter-pills mt-2 d-flex flex-wrap gap-2">
                        <button type="button" class="rewards-pill-btn active" data-filter="all">All (30)</button>
                        <button type="button" class="rewards-pill-btn" data-filter="airline">Airlines</button>
                        <button type="button" class="rewards-pill-btn" data-filter="mfs">MFS & Mobile</button>
                        <button type="button" class="rewards-pill-btn" data-filter="bank">Banks & Cards</button>
                        <button type="button" class="rewards-pill-btn" data-filter="telecom">Telco Clubs</button>
                    </div>
                </div>

                {{-- Scrollable 3-Column Card Grid (Matches Screenshot 1:1) --}}
                <div class="rewards-cards-container" id="rewardsCardsContainer">
                    <div class="rewards-cards-grid" id="rewardsCardsGrid">

                        {{-- ── 1. PRIME BOOKING REWARDS ── --}}
                        <div class="rewards-card-item" 
                             data-code="prime_rewards" 
                             data-name="Prime Rewards Club" 
                             data-category="prime" 
                             data-rate="1 Pt = ৳10 Discount" 
                             data-placeholder="Enter your Prime Booking registered phone / email"
                             data-type="Official Club">
                            <div class="rewards-card-logo bg-gradient-prime">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="#F59E0B" stroke="#D97706" stroke-width="1.2"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Prime Rewards Club</span>
                                <span class="rewards-card-sub text-amber-600">Prime Exclusive (1 Pt = ৳10)</span>
                            </div>
                        </div>

                        {{-- ── 2. BIMAN BANGLADESH AIRLINES ── --}}
                        <div class="rewards-card-item" 
                             data-code="biman_loyalty" 
                             data-name="Biman Loyalty Club" 
                             data-category="airline" 
                             data-rate="Earn 400 Miles / stay" 
                             data-placeholder="Enter your 10-digit Biman Frequent Flyer Number (e.g. BG-892410)"
                             data-type="National Carrier">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <circle cx="16" cy="16" r="14" fill="#006A4E"/>
                                    <circle cx="14" cy="16" r="7" fill="#F42A41"/>
                                    <path d="M10 18L18 10L22 14L14 22L10 18Z" fill="#FFFFFF"/>
                                    <path d="M18 10L26 8L24 16L18 10Z" fill="#FFD700"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Biman Loyalty Club</span>
                                <span class="rewards-card-sub">Biman Bangladesh</span>
                            </div>
                        </div>

                        {{-- ── 3. US-BANGLA AIRLINES ── --}}
                        <div class="rewards-card-item" 
                             data-code="us_bangla" 
                             data-name="US-Bangla Sky Star" 
                             data-category="airline" 
                             data-rate="Earn 350 Sky Stars / stay" 
                             data-placeholder="Enter your Sky Star Membership ID (e.g. BS-908234)"
                             data-type="Airline Loyalty">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <circle cx="16" cy="16" r="14" fill="#0D47A1"/>
                                    <path d="M16 6L19 13L26 14L21 19L22 26L16 22L10 26L11 19L6 14L13 13L16 6Z" fill="#E53935"/>
                                    <circle cx="16" cy="16" r="4" fill="#FFFFFF"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">US-Bangla Sky Star</span>
                                <span class="rewards-card-sub">US-Bangla Airlines</span>
                            </div>
                        </div>

                        {{-- ── 4. NOVOAIR SMILES ── --}}
                        <div class="rewards-card-item" 
                             data-code="novoair_smiles" 
                             data-name="Novoair Smiles" 
                             data-category="airline" 
                             data-rate="Earn 300 Smiles / stay" 
                             data-placeholder="Enter your Smiles Member ID (e.g. VQ-552190)"
                             data-type="Airline Loyalty">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <circle cx="16" cy="16" r="14" fill="#0284C7"/>
                                    <path d="M8 18C12 24 20 24 24 18" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round"/>
                                    <circle cx="11" cy="13" r="2" fill="#FFFFFF"/>
                                    <circle cx="21" cy="13" r="2" fill="#FFFFFF"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Novoair Smiles</span>
                                <span class="rewards-card-sub">Novoair</span>
                            </div>
                        </div>

                        {{-- ── 5. BKASH REWARDS ── --}}
                        <div class="rewards-card-item" 
                             data-code="bkash_rewards" 
                             data-name="bKash Rewards" 
                             data-category="mfs" 
                             data-rate="Earn 500 bKash Points / stay" 
                             data-placeholder="Enter your 11-digit bKash Account Number (01XXXXXXXXX)"
                             data-type="Mobile Financial Services">
                            <div class="rewards-card-logo" style="background: #e2136e15;">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <path d="M16 2L28 10L20 20L26 30L12 26L6 14L16 2Z" fill="#E2136E"/>
                                    <path d="M12 26L16 16L6 14L12 26Z" fill="#C2185B"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">bKash Rewards</span>
                                <span class="rewards-card-sub text-pink-600">bKash Loyalty Points</span>
                            </div>
                        </div>

                        {{-- ── 6. NAGAD REWARDS ── --}}
                        <div class="rewards-card-item" 
                             data-code="nagad_points" 
                             data-name="Nagad Points" 
                             data-category="mfs" 
                             data-rate="Earn 450 Nagad Points / stay" 
                             data-placeholder="Enter your 11-digit Nagad Mobile Number (01XXXXXXXXX)"
                             data-type="Mobile Financial Services">
                            <div class="rewards-card-logo" style="background: #f7941d15;">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <circle cx="16" cy="16" r="14" fill="#F7941D"/>
                                    <path d="M16 6C10 6 8 12 8 16C8 22 12 26 16 26C20 26 24 22 24 16C24 10 20 6 16 6Z" fill="#ED1C24"/>
                                    <path d="M16 10C13 10 12 13 12 16C12 19 14 22 16 22C18 22 20 19 20 16C20 12 18 10 16 10Z" fill="#FFFFFF"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Nagad Points</span>
                                <span class="rewards-card-sub text-orange-600">Nagad Rewards</span>
                            </div>
                        </div>

                        {{-- ── 7. CITY BANK CITYMILES / AMEX ── --}}
                        <div class="rewards-card-item" 
                             data-code="city_miles" 
                             data-name="City Miles" 
                             data-category="bank" 
                             data-rate="Earn 400 CityMiles / stay" 
                             data-placeholder="Enter your City Bank Amex Card Number or Customer ID"
                             data-type="Bank Loyalty">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <rect width="32" height="32" rx="4" fill="#002663"/>
                                    <path d="M7 16C7 11 11 7 16 7C21 7 25 11 25 16" stroke="#00A3E0" stroke-width="2.5"/>
                                    <text x="16" y="21" font-size="8" font-family="Arial, sans-serif" font-weight="bold" fill="#FFFFFF" text-anchor="middle">CITY</text>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Citi Miles</span>
                                <span class="rewards-card-sub">City Bank / Amex BD</span>
                            </div>
                        </div>

                        {{-- ── 8. STANDARD CHARTERED 360° REWARDS ── --}}
                        <div class="rewards-card-item" 
                             data-code="scb_360" 
                             data-name="SCB 360° Rewards" 
                             data-category="bank" 
                             data-rate="Earn 500 SCB Points / stay" 
                             data-placeholder="Enter your Standard Chartered 16-digit Card No or Client ID"
                             data-type="Bank Loyalty">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <path d="M16 4C9.37 4 4 9.37 4 16C4 22.63 9.37 28 16 28C22.63 28 28 22.63 28 16" stroke="#007A3D" stroke-width="3" stroke-linecap="round"/>
                                    <path d="M16 9C12.13 9 9 12.13 9 16C9 19.87 12.13 23 16 23" stroke="#0099FF" stroke-width="3" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">SCB 360° Rewards</span>
                                <span class="rewards-card-sub">Standard Chartered BD</span>
                            </div>
                        </div>

                        {{-- ── 9. EBL SKYCOINS ── --}}
                        <div class="rewards-card-item" 
                             data-code="ebl_skycoins" 
                             data-name="EBL Skycoins" 
                             data-category="bank" 
                             data-rate="Earn 350 Skycoins / stay" 
                             data-placeholder="Enter your Eastern Bank Card Number or Customer ID"
                             data-type="Bank Loyalty">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <circle cx="16" cy="16" r="14" fill="#003366"/>
                                    <circle cx="16" cy="16" r="10" stroke="#FFCC00" stroke-width="2"/>
                                    <path d="M12 16L16 11L20 16L16 21L12 16Z" fill="#FFCC00"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">EBL Skycoins</span>
                                <span class="rewards-card-sub">Eastern Bank Ltd</span>
                            </div>
                        </div>

                        {{-- ── 10. BRAC BANK ASTHA & TARA ── --}}
                        <div class="rewards-card-item" 
                             data-code="brac_bank" 
                             data-name="BRAC Bank Rewards" 
                             data-category="bank" 
                             data-rate="Earn 300 Points / stay" 
                             data-placeholder="Enter your BRAC Bank Card or Astha App User ID"
                             data-type="Bank Loyalty">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <rect width="32" height="32" rx="4" fill="#002D62"/>
                                    <path d="M6 22L16 6L26 22H6Z" fill="#FFC72C"/>
                                    <circle cx="16" cy="17" r="4" fill="#002D62"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">BRAC Bank Rewards</span>
                                <span class="rewards-card-sub">Astha & Tara Club</span>
                            </div>
                        </div>

                        {{-- ── 11. GRAMEENPHONE GP STARS ── --}}
                        <div class="rewards-card-item" 
                             data-code="gp_star" 
                             data-name="GP Stars" 
                             data-category="telecom" 
                             data-rate="Earn 500 Star Points / stay" 
                             data-placeholder="Enter your GP Star Mobile Number (017XXXXXXXX / 013XXXXXXXX)"
                             data-type="Telecom Privilege Club">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <circle cx="16" cy="16" r="14" fill="#0077C8"/>
                                    <path d="M16 6L18.5 13L25.5 13.5L20 18L22 25L16 21L10 25L12 18L6.5 13.5L13.5 13L16 6Z" fill="#FFD700"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">GP Stars</span>
                                <span class="rewards-card-sub text-blue-600">Grameenphone</span>
                            </div>
                        </div>

                        {{-- ── 12. ROBI DHONNOBAD / ELITE ── --}}
                        <div class="rewards-card-item" 
                             data-code="robi_elite" 
                             data-name="Robi Dhonnobad" 
                             data-category="telecom" 
                             data-rate="Earn 400 Elite Points / stay" 
                             data-placeholder="Enter your Robi Mobile Number (018XXXXXXXX)"
                             data-type="Telecom Privilege Club">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <rect width="32" height="32" rx="4" fill="#ED1C24"/>
                                    <path d="M16 7L24 16L16 25L8 16L16 7Z" fill="#FFFFFF"/>
                                    <circle cx="16" cy="16" r="4" fill="#ED1C24"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Robi Dhonnobad</span>
                                <span class="rewards-card-sub text-danger">Robi Elite Club</span>
                            </div>
                        </div>

                        {{-- ── 13. BANGLALINK ORANGE CLUB ── --}}
                        <div class="rewards-card-item" 
                             data-code="bl_orange" 
                             data-name="Orange Club" 
                             data-category="telecom" 
                             data-rate="Earn 400 Orange Points / stay" 
                             data-placeholder="Enter your Banglalink Mobile Number (019XXXXXXXX / 014XXXXXXXX)"
                             data-type="Telecom Privilege Club">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <circle cx="16" cy="16" r="14" fill="#FF6600"/>
                                    <circle cx="16" cy="16" r="8" fill="#FFFFFF"/>
                                    <circle cx="16" cy="16" r="4" fill="#FF6600"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Orange Club</span>
                                <span class="rewards-card-sub text-orange-600">Banglalink</span>
                            </div>
                        </div>

                        {{-- ── 14. EMIRATES SKYWARDS ── --}}
                        <div class="rewards-card-item" 
                             data-code="emirates_skywards" 
                             data-name="Emirates Skywards" 
                             data-category="airline" 
                             data-rate="Earn 500 Skywards Miles / stay" 
                             data-placeholder="Enter your Emirates Skywards Number (e.g. EK-10928374)"
                             data-type="International Airline">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <rect width="32" height="32" rx="4" fill="#D71921"/>
                                    <text x="16" y="21" font-size="9" font-family="Georgia, serif" font-weight="bold" fill="#FFFFFF" text-anchor="middle">EK</text>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Emirates Skywards</span>
                                <span class="rewards-card-sub">Emirates Airline</span>
                            </div>
                        </div>

                        {{-- ── 15. QATAR AIRWAYS PRIVILEGE CLUB ── --}}
                        <div class="rewards-card-item" 
                             data-code="qatar_privilege" 
                             data-name="Qatar Privilege Club" 
                             data-category="airline" 
                             data-rate="Earn 500 Avios / stay" 
                             data-placeholder="Enter your Privilege Club Membership No (e.g. QR-8892147)"
                             data-type="International Airline">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <circle cx="16" cy="16" r="14" fill="#5C0632"/>
                                    <path d="M12 18L16 10L20 18L16 15L12 18Z" fill="#FFFFFF"/>
                                    <circle cx="16" cy="22" r="2" fill="#FFFFFF"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Qatar Privilege Club</span>
                                <span class="rewards-card-sub">Qatar Airways (Avios)</span>
                            </div>
                        </div>

                        {{-- ── 16. SINGAPORE AIRLINES KRISFLYER ── --}}
                        <div class="rewards-card-item" 
                             data-code="singapore_krisflyer" 
                             data-name="Singapore Airlines" 
                             data-category="airline" 
                             data-rate="Earn 500 KrisFlyer Miles / stay" 
                             data-placeholder="Enter your KrisFlyer Membership Number (e.g. SQ-9912034)"
                             data-type="International Airline">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <circle cx="16" cy="16" r="14" fill="#00205B"/>
                                    <path d="M8 18L18 8L24 14L14 24L8 18Z" fill="#C59B27"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Singapore Airlines</span>
                                <span class="rewards-card-sub">KrisFlyer Miles</span>
                            </div>
                        </div>

                        {{-- ── 17. SAUDIA ALFURSAN ── --}}
                        <div class="rewards-card-item" 
                             data-code="saudia_alfursan" 
                             data-name="Saudia Alfursan" 
                             data-category="airline" 
                             data-rate="Earn 450 Miles / stay" 
                             data-placeholder="Enter your Alfursan Membership ID (e.g. SV-771239)"
                             data-type="International Airline">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <circle cx="16" cy="16" r="14" fill="#006A4E"/>
                                    <path d="M16 8V24M11 14L21 14M12 18L20 18" stroke="#D4AF37" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Saudia Alfursan</span>
                                <span class="rewards-card-sub">Saudia Airlines</span>
                            </div>
                        </div>

                        {{-- ── 18. TURKISH AIRLINES MILES&SMILES ── --}}
                        <div class="rewards-card-item" 
                             data-code="turkish_miles" 
                             data-name="Turkish Miles&Smiles" 
                             data-category="airline" 
                             data-rate="Earn 400 Miles / stay" 
                             data-placeholder="Enter your Miles&Smiles TK Number (e.g. TK-441209)"
                             data-type="International Airline">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <circle cx="16" cy="16" r="14" fill="#C8102E"/>
                                    <circle cx="16" cy="16" r="10" fill="#FFFFFF"/>
                                    <circle cx="16" cy="16" r="8" fill="#C8102E"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Turkish Miles&Smiles</span>
                                <span class="rewards-card-sub">Turkish Airlines</span>
                            </div>
                        </div>

                        {{-- ── 19. AIR CHINA (As in Agoda Screenshot) ── --}}
                        <div class="rewards-card-item" 
                             data-code="air_china" 
                             data-name="Air China" 
                             data-category="airline" 
                             data-rate="Earn 350 PhoenixMiles / stay" 
                             data-placeholder="Enter your PhoenixMiles Card No (e.g. CA-330129)"
                             data-type="International Airline">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <circle cx="16" cy="16" r="14" fill="#FFFFFF" stroke="#E60012" stroke-width="1.5"/>
                                    <path d="M8 20C12 12 18 10 24 12C20 16 16 18 10 22Z" fill="#E60012"/>
                                    <circle cx="21" cy="11" r="2" fill="#E60012"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Air China</span>
                                <span class="rewards-card-sub">PhoenixMiles</span>
                            </div>
                        </div>

                        {{-- ── 20. KOREAN AIR SKYPASS (As in Agoda Screenshot) ── --}}
                        <div class="rewards-card-item" 
                             data-code="korean_air" 
                             data-name="Korean Air SKYPASS" 
                             data-category="airline" 
                             data-rate="Earn 400 SKYPASS Miles / stay" 
                             data-placeholder="Enter your SKYPASS Membership ID (e.g. KE-659012)"
                             data-type="International Airline">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <circle cx="16" cy="16" r="14" fill="#00356B"/>
                                    <circle cx="16" cy="16" r="8" fill="#ED1B2D"/>
                                    <path d="M16 8C20 8 24 12 16 16C8 20 12 24 16 24" stroke="#FFFFFF" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Korean Air SKYPASS</span>
                                <span class="rewards-card-sub">Korean Air</span>
                            </div>
                        </div>

                        {{-- ── 21. D POINT (As in Agoda Screenshot) ── --}}
                        <div class="rewards-card-item" 
                             data-code="d_point" 
                             data-name="d Point" 
                             data-category="mfs" 
                             data-rate="Earn 300 d Points / stay" 
                             data-placeholder="Enter your d Point Club Account ID"
                             data-type="Global Loyalty">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <rect width="32" height="32" rx="4" fill="#FFFFFF" stroke="#E60012" stroke-width="1.5"/>
                                    <text x="16" y="22" font-size="18" font-family="'Segoe UI', Roboto, sans-serif" font-weight="900" fill="#E60012" text-anchor="middle">d</text>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">d Point</span>
                                <span class="rewards-card-sub">d Point Club</span>
                            </div>
                        </div>

                        {{-- ── 22. AIR ARABIA AIRREWARDS ── --}}
                        <div class="rewards-card-item" 
                             data-code="air_arabia" 
                             data-name="AirRewards" 
                             data-category="airline" 
                             data-rate="Earn 300 AirRewards / stay" 
                             data-placeholder="Enter your AirRewards ID (e.g. G9-902341)"
                             data-type="International Airline">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <circle cx="16" cy="16" r="14" fill="#EE2737"/>
                                    <path d="M8 18L16 10L24 18" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Air Arabia</span>
                                <span class="rewards-card-sub">AirRewards</span>
                            </div>
                        </div>

                        {{-- ── 23. MALAYSIA AIRLINES ENRICH ── --}}
                        <div class="rewards-card-item" 
                             data-code="malaysia_enrich" 
                             data-name="Malaysia Enrich" 
                             data-category="airline" 
                             data-rate="Earn 400 Enrich Points / stay" 
                             data-placeholder="Enter your Enrich Membership Number (e.g. MH-128490)"
                             data-type="International Airline">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <circle cx="16" cy="16" r="14" fill="#002D62"/>
                                    <path d="M16 6L24 16L16 26L8 16L16 6Z" fill="#CC0000"/>
                                    <path d="M12 16L16 11L20 16L16 21L12 16Z" fill="#FFFFFF"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Malaysia Enrich</span>
                                <span class="rewards-card-sub">Malaysia Airlines</span>
                            </div>
                        </div>

                        {{-- ── 24. THAI AIRWAYS ROYAL ORCHID PLUS ── --}}
                        <div class="rewards-card-item" 
                             data-code="thai_royal_orchid" 
                             data-name="Royal Orchid Plus" 
                             data-category="airline" 
                             data-rate="Earn 400 Miles / stay" 
                             data-placeholder="Enter your Royal Orchid Plus ID (e.g. TG-778901)"
                             data-type="International Airline">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <circle cx="16" cy="16" r="14" fill="#582C83"/>
                                    <path d="M16 8L20 16L16 24L12 16L16 8Z" fill="#D4AF37"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Thai Royal Orchid</span>
                                <span class="rewards-card-sub">Thai Airways</span>
                            </div>
                        </div>

                        {{-- ── 25. AIR INDIA FLYING RETURNS / MAHARAJA ── --}}
                        <div class="rewards-card-item" 
                             data-code="air_india" 
                             data-name="Air India Maharaja" 
                             data-category="airline" 
                             data-rate="Earn 350 Flying Points / stay" 
                             data-placeholder="Enter your Maharaja / Flying Returns ID (e.g. AI-992014)"
                             data-type="International Airline">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <circle cx="16" cy="16" r="14" fill="#ED1C24"/>
                                    <circle cx="16" cy="16" r="8" fill="#F7941D"/>
                                    <circle cx="16" cy="16" r="4" fill="#FFFFFF"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Air India</span>
                                <span class="rewards-card-sub">Maharaja Club</span>
                            </div>
                        </div>

                        {{-- ── 26. UPAY CLUB ── --}}
                        <div class="rewards-card-item" 
                             data-code="upay_club" 
                             data-name="Upay Club" 
                             data-category="mfs" 
                             data-rate="Earn 300 Upay Points / stay" 
                             data-placeholder="Enter your Upay Registered Mobile Number (01XXXXXXXXX)"
                             data-type="Mobile Financial Services">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <rect width="32" height="32" rx="4" fill="#004B87"/>
                                    <circle cx="16" cy="16" r="8" fill="#FFC72C"/>
                                    <path d="M12 16L16 20L20 12" stroke="#004B87" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Upay Club</span>
                                <span class="rewards-card-sub">UCB Mobile Banking</span>
                            </div>
                        </div>

                        {{-- ── 27. PRIME BANK CLUB POINTS ── --}}
                        <div class="rewards-card-item" 
                             data-code="prime_bank" 
                             data-name="Prime Bank Rewards" 
                             data-category="bank" 
                             data-rate="Earn 350 Club Points / stay" 
                             data-placeholder="Enter your Prime Bank Card or Account Number"
                             data-type="Bank Loyalty">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <circle cx="16" cy="16" r="14" fill="#1C3F94"/>
                                    <path d="M16 6L24 16H8L16 6Z" fill="#D32F2F"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Prime Bank Club</span>
                                <span class="rewards-card-sub">Prime Bank BD</span>
                            </div>
                        </div>

                        {{-- ── 28. MUTUAL TRUST BANK (MTB) DELIGHT ── --}}
                        <div class="rewards-card-item" 
                             data-code="mtb_delight" 
                             data-name="MTB Delight" 
                             data-category="bank" 
                             data-rate="Earn 300 Delight Points / stay" 
                             data-placeholder="Enter your MTB Card or Client ID"
                             data-type="Bank Loyalty">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <rect width="32" height="32" rx="4" fill="#D2232A"/>
                                    <circle cx="16" cy="16" r="8" fill="#FFFFFF"/>
                                    <text x="16" y="20" font-size="10" font-family="Arial, sans-serif" font-weight="bold" fill="#D2232A" text-anchor="middle">M</text>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">MTB Delight</span>
                                <span class="rewards-card-sub">Mutual Trust Bank</span>
                            </div>
                        </div>

                        {{-- ── 29. DHAKA BANK EXCELLENCE ── --}}
                        <div class="rewards-card-item" 
                             data-code="dhaka_bank" 
                             data-name="Dhaka Bank" 
                             data-category="bank" 
                             data-rate="Earn 300 Points / stay" 
                             data-placeholder="Enter your Dhaka Bank Card or Customer ID"
                             data-type="Bank Loyalty">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <rect width="32" height="32" rx="4" fill="#0A3A60"/>
                                    <path d="M8 8H24V24H8V8Z" stroke="#E31B23" stroke-width="2"/>
                                    <text x="16" y="20" font-size="9" font-family="Arial, sans-serif" font-weight="bold" fill="#FFFFFF" text-anchor="middle">DBL</text>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">Dhaka Bank</span>
                                <span class="rewards-card-sub">Excellence Club</span>
                            </div>
                        </div>

                        {{-- ── 30. DUTCH-BANGLA NEXUSPAY ── --}}
                        <div class="rewards-card-item" 
                             data-code="dbbl_nexus" 
                             data-name="NexusPay Points" 
                             data-category="bank" 
                             data-rate="Earn 350 Points / stay" 
                             data-placeholder="Enter your NexusPay Mobile or Card Number"
                             data-type="Bank Loyalty">
                            <div class="rewards-card-logo">
                                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                    <circle cx="16" cy="16" r="14" fill="#006837"/>
                                    <circle cx="16" cy="16" r="8" fill="#F15A24"/>
                                    <circle cx="16" cy="16" r="4" fill="#FFFFFF"/>
                                </svg>
                            </div>
                            <div class="rewards-card-meta">
                                <span class="rewards-card-name">NexusPay Points</span>
                                <span class="rewards-card-sub">DBBL Nexus</span>
                            </div>
                        </div>

                    </div>

                    {{-- Empty State (When search yields no results) --}}
                    <div id="rewardsNoResults" class="rewards-empty-search text-center py-5" style="display: none;">
                        <i class="fa-solid fa-plane-slash text-muted mb-2" style="font-size: 32px;"></i>
                        <h6 class="fw-bold text-dark mb-1">No reward programs found</h6>
                        <p class="text-secondary small mb-0">Try searching with a different bank, airline or club name</p>
                    </div>
                </div>
            </div>

            {{-- ════════ STEP 2: LINK MEMBERSHIP FORM (SLIDES IN WHEN CARD IS CLICKED) ════════ --}}
            <div id="rewardsStepLink" class="rewards-step-panel" style="display: none;">
                <div class="rewards-modal-header pb-2">
                    <button type="button" class="rewards-back-btn" id="rewardsBackToBrowseBtn">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back to programs
                    </button>
                    <h3 class="rewards-modal-title mt-2">
                        Link your rewards program
                    </h3>
                    <p class="rewards-modal-subtitle">
                        Enter your membership ID to start earning points/miles with every hotel booking.
                    </p>
                </div>

                {{-- Selected Program Highlight Banner --}}
                <div class="selected-program-preview-box mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="selected-program-logo" id="selectedProgramLogoBox">
                            {{-- Injected dynamically --}}
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold text-dark mb-0" id="selectedProgramNameText">Program Name</h5>
                            <span class="badge bg-primary-subtle text-primary fw-medium" id="selectedProgramCategoryBadge">Partner</span>
                            <span class="text-success ms-2 small fw-semibold" id="selectedProgramRateText">Earn 400 Miles / stay</span>
                        </div>
                    </div>
                </div>

                {{-- Actual Linking Form --}}
                <form id="pointsmaxLinkForm" action="{{ route('pointsmax.link') }}" method="POST">
                    @csrf
                    <input type="hidden" name="program" id="linkProgramInput">
                    <input type="hidden" name="code" id="linkCodeInput">
                    <input type="hidden" name="category" id="linkCategoryInput">

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small" id="membershipIdLabel">
                            Membership / Account Number: <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="membership_id" 
                               id="linkMembershipIdInput" 
                               class="form-control rewards-input py-2" 
                               placeholder="Enter your membership number" 
                               required 
                               autocomplete="off">
                        <div class="form-text text-muted small" id="membershipHelpText">
                            Enter the account number associated with your frequent flyer or loyalty account.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">
                            Full Name on Account (Optional):
                        </label>
                        <input type="text" 
                               name="account_name" 
                               id="linkAccountNameInput" 
                               class="form-control rewards-input py-2" 
                               value="{{ auth()->user()?->name ?? '' }}"
                               placeholder="e.g. MD SHAWON">
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="is_primary" value="1" id="linkIsPrimaryCheck" checked>
                        <label class="form-check-label text-dark fw-medium small" for="linkIsPrimaryCheck">
                            Set as primary PointsMAX rewards program for Prime Booking
                        </label>
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <button type="button" class="btn btn-outline-danger px-3 py-2 fw-semibold" id="unlinkProgramBtn" style="display: none; font-size: 13px;">
                            <i class="fa-regular fa-trash-can me-1"></i> Unlink Program
                        </button>
                        <div class="d-flex align-items-center gap-2 ms-auto">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2 fw-semibold" id="cancelLinkBtn">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold d-flex align-items-center gap-2" id="submitLinkBtn" style="background-color: #2067e1; border: none;">
                                <span class="spinner-border spinner-border-sm" id="linkSpinner" style="display: none;" role="status"></span>
                                <span id="submitLinkBtnText">Link Program</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ════════ STEP 3: SUCCESS CONFIRMATION ════════ --}}
            <div id="rewardsStepSuccess" class="rewards-step-panel text-center py-4" style="display: none;">
                <div class="success-icon-circle mx-auto mb-3">
                    <i class="fa-solid fa-check text-white"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">Program Linked Successfully!</h4>
                <p class="text-secondary small mb-4" id="successProgramMessage">
                    You're all set to earn points/miles on your next hotel reservation on Prime Booking.
                </p>
                <button type="button" class="btn btn-primary px-4 py-2 fw-semibold" data-bs-dismiss="modal" style="background-color: #2067e1; border: none;">
                    Done
                </button>
            </div>

        </div>
    </div>
</div>

<style>
/* ── 1:1 EXACT AGODA REWARDS MODAL STYLING ── */
.rewards-program-modal-backdrop .modal-backdrop {
    background-color: rgba(15, 23, 42, 0.75);
    backdrop-filter: blur(4px);
}
.rewards-modal-content {
    background: #ffffff;
    border-radius: 12px;
    padding: 28px 32px 30px 32px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #1e293b;
    position: relative;
}

/* Agoda White Close Cross (Positioned at top right) */
.rewards-modal-close-btn {
    position: absolute;
    top: 18px;
    right: 22px;
    background: transparent;
    border: none;
    font-size: 30px;
    font-weight: 300;
    line-height: 1;
    color: #94a3b8;
    cursor: pointer;
    transition: color 0.15s ease, transform 0.15s ease;
    z-index: 10;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.rewards-modal-close-btn:hover {
    color: #0f172a;
    transform: scale(1.1);
}

/* Header */
.rewards-modal-title {
    font-size: 22px;
    font-weight: 700;
    color: #1e293b;
    letter-spacing: -0.3px;
    margin-bottom: 4px;
}
.rewards-modal-subtitle {
    font-size: 13.5px;
    color: #475569;
    margin-bottom: 16px;
    line-height: 1.45;
}

/* 1:1 Agoda Signature Blue Search Box */
.rewards-search-box-wrap {
    position: relative;
    width: 100%;
    margin-bottom: 8px;
}
.rewards-search-input {
    width: 100%;
    height: 42px;
    background: #ffffff;
    border: 1.5px solid #2067e1;
    border-radius: 6px;
    padding: 8px 38px 8px 14px;
    font-size: 14px;
    color: #1e293b;
    outline: none;
    transition: all 0.2s ease;
    box-shadow: 0 0 0 3px rgba(32, 103, 225, 0.1);
}
.rewards-search-input:focus {
    border-color: #1d4ed8;
    box-shadow: 0 0 0 4px rgba(32, 103, 225, 0.2);
}
.rewards-search-input::placeholder {
    color: #94a3b8;
    font-weight: 400;
}
.rewards-search-icon {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #2067e1;
    font-size: 14px;
    pointer-events: none;
}
.rewards-clear-search-btn {
    position: absolute;
    right: 32px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: #94a3b8;
    font-size: 18px;
    cursor: pointer;
    line-height: 1;
}

/* Filter Pills */
.rewards-pill-btn {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 12px;
    font-weight: 500;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s ease;
}
.rewards-pill-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.rewards-pill-btn.active {
    background: #2067e1;
    border-color: #2067e1;
    color: #ffffff;
}

/* Cards Container & 3-Column Grid */
.rewards-cards-container {
    max-height: 380px;
    overflow-y: auto;
    overflow-x: hidden;
    padding-right: 4px;
    margin-top: 14px;
}
/* Agoda Scrollbar */
.rewards-cards-container::-webkit-scrollbar {
    width: 6px;
}
.rewards-cards-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}
.rewards-cards-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.rewards-cards-container::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.rewards-cards-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}
@media (max-width: 768px) {
    .rewards-cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 480px) {
    .rewards-cards-grid {
        grid-template-columns: 1fr;
    }
}

/* Individual Card (1:1 Agoda Screenshot Fidelity) */
.rewards-card-item {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    height: 70px;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    position: relative;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    user-select: none;
}
.rewards-card-item:hover {
    border-color: #2067e1;
    background: #f0f7ff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(32, 103, 225, 0.1);
}
.rewards-card-item.is-linked {
    border-color: #10b981;
    background: #f0fdf4;
}
.rewards-card-item.is-linked::after {
    content: "✓ Linked";
    position: absolute;
    top: 6px;
    right: 8px;
    font-size: 10px;
    font-weight: 700;
    color: #059669;
}

.rewards-card-logo {
    width: 38px;
    height: 38px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    border: 1px solid #eef2f6;
    flex-shrink: 0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.bg-gradient-prime {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-color: #334155;
}

.rewards-card-meta {
    flex-grow: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
}
.rewards-card-name {
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
    line-height: 1.25;
    white-space: normal;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.rewards-card-sub {
    font-size: 10.5px;
    color: #64748b;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Step 2 Preview Banner */
.selected-program-preview-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 14px 16px;
}
.selected-program-logo {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}
.rewards-back-btn {
    background: none;
    border: none;
    padding: 0;
    color: #2067e1;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}
.rewards-back-btn:hover {
    text-decoration: underline;
}

.rewards-input {
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 14px;
}
.rewards-input:focus {
    border-color: #2067e1;
    box-shadow: 0 0 0 3px rgba(32, 103, 225, 0.15);
}

/* Step 3 Success Circle */
.success-icon-circle {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #10b981;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('selectRewardsProgramModal');
    if (!modalEl) return;

    const stepBrowse = document.getElementById('rewardsStepBrowse');
    const stepLink = document.getElementById('rewardsStepLink');
    const stepSuccess = document.getElementById('rewardsStepSuccess');
    
    const searchInput = document.getElementById('rewardsProgramSearchInput');
    const clearSearchBtn = document.getElementById('clearRewardsSearchBtn');
    const cardsGrid = document.getElementById('rewardsCardsGrid');
    const cardItems = document.querySelectorAll('.rewards-card-item');
    const noResults = document.getElementById('rewardsNoResults');
    const pillBtns = document.querySelectorAll('.rewards-pill-btn');

    const backBtn = document.getElementById('rewardsBackToBrowseBtn');
    const cancelBtn = document.getElementById('cancelLinkBtn');
    const linkForm = document.getElementById('pointsmaxLinkForm');
    const submitBtn = document.getElementById('submitLinkBtn');
    const spinner = document.getElementById('linkSpinner');

    let activeFilter = 'all';

    // ── 1. Real-time Search Filter ──
    function filterCards() {
        const query = searchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        if (query.length > 0) {
            clearSearchBtn.style.display = 'block';
        } else {
            clearSearchBtn.style.display = 'none';
        }

        cardItems.forEach(card => {
            const name = (card.getAttribute('data-name') || '').toLowerCase();
            const category = card.getAttribute('data-category') || '';
            const sub = (card.querySelector('.rewards-card-sub')?.textContent || '').toLowerCase();

            const matchesCategory = (activeFilter === 'all') || (category === activeFilter);
            const matchesQuery = (query === '') || name.includes(query) || sub.includes(query);

            if (matchesCategory && matchesQuery) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }

    searchInput?.addEventListener('input', filterCards);

    clearSearchBtn?.addEventListener('click', function() {
        searchInput.value = '';
        searchInput.focus();
        filterCards();
    });

    // ── 2. Category Pill Filter ──
    pillBtns.forEach(pill => {
        pill.addEventListener('click', function() {
            pillBtns.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            activeFilter = this.getAttribute('data-filter') || 'all';
            filterCards();
        });
    });

    // ── 3. Dynamic Linked Programs State Management ──
    let userLinkedPrograms = [];

    async function fetchUserPrograms() {
        try {
            const res = await fetch("{{ route('pointsmax.programs') }}", {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.status === 'success' || data.success) {
                userLinkedPrograms = data.programs || [];
                updateCardsLinkedState();
            }
        } catch (e) {
            console.warn('Could not fetch linked programs:', e);
        }
    }

    function updateCardsLinkedState() {
        cardItems.forEach(card => {
            const name = card.getAttribute('data-name') || '';
            const linked = userLinkedPrograms.find(p => (p.program || '').toLowerCase() === name.toLowerCase());
            if (linked) {
                card.classList.add('is-linked');
                card.dataset.linkedId = linked.id || '';
                card.dataset.linkedMemberId = linked.membership_id || '';
                card.dataset.linkedAccountName = linked.account_name || '';
                card.dataset.linkedIsPrimary = linked.is_primary ? '1' : '0';
            } else {
                card.classList.remove('is-linked');
                delete card.dataset.linkedId;
                delete card.dataset.linkedMemberId;
                delete card.dataset.linkedAccountName;
                delete card.dataset.linkedIsPrimary;
            }
        });
    }

    modalEl.addEventListener('show.bs.modal', function () {
        fetchUserPrograms();
    });

    // ── 4. Click Card -> Transition to Step 2 (Link / Update Form) ──
    cardItems.forEach(card => {
        card.addEventListener('click', function() {
            const name = this.getAttribute('data-name') || 'Rewards Program';
            const code = this.getAttribute('data-code') || '';
            const category = this.getAttribute('data-category') || '';
            const rate = this.getAttribute('data-rate') || 'Earn Miles / stay';
            const placeholder = this.getAttribute('data-placeholder') || 'Enter your membership ID';
            const logoSvg = this.querySelector('.rewards-card-logo')?.innerHTML || '';

            const isAlreadyLinked = this.classList.contains('is-linked');
            const linkedId = this.dataset.linkedId || '';
            const linkedMemberId = this.dataset.linkedMemberId || '';
            const linkedAccountName = this.dataset.linkedAccountName || '';
            const linkedIsPrimary = this.dataset.linkedIsPrimary === '1';

            // Populate Step 2
            document.getElementById('linkProgramInput').value = name;
            document.getElementById('linkCodeInput').value = code;
            document.getElementById('linkCategoryInput').value = category;
            document.getElementById('selectedProgramNameText').textContent = name;
            document.getElementById('selectedProgramCategoryBadge').textContent = category.toUpperCase();
            document.getElementById('selectedProgramRateText').textContent = rate;
            document.getElementById('selectedProgramLogoBox').innerHTML = logoSvg;
            
            const idInput = document.getElementById('linkMembershipIdInput');
            idInput.placeholder = placeholder;
            idInput.value = isAlreadyLinked ? linkedMemberId : '';

            const nameInput = document.getElementById('linkAccountNameInput');
            if (isAlreadyLinked && linkedAccountName) {
                nameInput.value = linkedAccountName;
            }

            const primaryCheck = document.getElementById('linkIsPrimaryCheck');
            primaryCheck.checked = isAlreadyLinked ? linkedIsPrimary : true;

            const unlinkBtn = document.getElementById('unlinkProgramBtn');
            const submitText = document.getElementById('submitLinkBtnText');

            if (isAlreadyLinked && linkedId) {
                unlinkBtn.style.display = 'inline-flex';
                unlinkBtn.dataset.unlinkId = linkedId;
                unlinkBtn.dataset.unlinkProgram = name;
                submitText.textContent = 'Update Program';
            } else {
                unlinkBtn.style.display = 'none';
                submitText.textContent = 'Link Program';
            }

            // Switch views
            stepBrowse.style.display = 'none';
            stepSuccess.style.display = 'none';
            stepLink.style.display = 'block';

            setTimeout(() => idInput.focus(), 150);
        });
    });

    // ── 5. Back / Cancel to Step 1 ──
    function returnToBrowse() {
        stepLink.style.display = 'none';
        stepSuccess.style.display = 'none';
        stepBrowse.style.display = 'block';
    }
    backBtn?.addEventListener('click', returnToBrowse);
    cancelBtn?.addEventListener('click', returnToBrowse);

    // Modal reset on close
    modalEl.addEventListener('hidden.bs.modal', function () {
        returnToBrowse();
        if (searchInput) {
            searchInput.value = '';
            filterCards();
        }
    });

    // ── 6. Unlink Program Handler ──
    const unlinkBtn = document.getElementById('unlinkProgramBtn');
    unlinkBtn?.addEventListener('click', async function() {
        const id = this.dataset.unlinkId;
        const progName = this.dataset.unlinkProgram || 'this program';
        if (!id) return;

        if (!confirm(`Are you sure you want to unlink ${progName}?`)) {
            return;
        }

        try {
            unlinkBtn.disabled = true;
            const res = await fetch(`{{ url('/pointsmax/unlink') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (data.success) {
                userLinkedPrograms = userLinkedPrograms.filter(p => p.id !== id);
                updateCardsLinkedState();
                returnToBrowse();
                if (window.location.pathname.includes('pointsmax')) {
                    window.location.reload();
                }
            } else {
                alert(data.message || 'Could not unlink program.');
            }
        } catch(err) {
            console.error('Unlink error:', err);
        } finally {
            unlinkBtn.disabled = false;
        }
    });

    // ── 7. AJAX Form Submission to Real Backend (Functional) ──
    linkForm?.addEventListener('submit', async function(e) {
        e.preventDefault();

        submitBtn.disabled = true;
        spinner.style.display = 'inline-block';

        const formData = new FormData(linkForm);

        try {
            const response = await fetch(linkForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Show Step 3 Success
                document.getElementById('successProgramMessage').textContent = 
                    `${formData.get('program')} has been linked successfully. Start earning with your bookings!`;
                
                stepLink.style.display = 'none';
                stepBrowse.style.display = 'none';
                stepSuccess.style.display = 'block';

                // Refresh linked programs cache
                fetchUserPrograms();

                // If on pointsmax page, reload to show active card
                setTimeout(() => {
                    if (window.location.pathname.includes('pointsmax')) {
                        window.location.reload();
                    }
                }, 1000);
            } else {
                alert(data.message || 'Could not link rewards program. Please try again.');
            }
        } catch (err) {
            console.error('Error linking pointsmax program:', err);
            linkForm.submit();
        } finally {
            submitBtn.disabled = false;
            spinner.style.display = 'none';
        }
    });
});
</script>
