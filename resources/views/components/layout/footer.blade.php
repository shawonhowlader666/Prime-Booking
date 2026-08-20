<!-- ============================================================ -->
<!-- Agoda.com Exact Footer — Light Blue-Grey Background          -->
<!-- 5 Columns: Help | Company | Destinations | Partner | App     -->
<!-- ============================================================ -->
<footer style="background-color: #ebeff5; margin-top: 40px;">

    <!-- Main Footer Top: Brand Logo & 5 Columns -->
    <div class="container" style="padding-top: 36px; padding-bottom: 36px;">
        <div class="mb-4 pb-2 border-bottom border-light">
            <a href="{{ route('home') }}" style="text-decoration: none;">
                <x-logo height="42" />
            </a>
        </div>
        <div class="row g-4">

            <!-- Column 1: Help -->
            <div class="col-6 col-md-4 col-lg-2">
                <h6 style="font-weight: 700; font-size: 14px; color: #1b2631; margin-bottom: 14px;">{{ __('Help') }}</h6>
                <ul class="list-unstyled" style="display: flex; flex-direction: column; gap: 10px;">
                    <li><a href="{{ route('contact') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">Help center</a></li>
                    <li><a href="{{ route('contact') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">FAQs</a></li>
                    <li><a href="{{ route('privacy') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">{{ __('Privacy policy') }}</a></li>
                    <li><a href="{{ route('privacy') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">Cookie policy</a></li>
                    <li><a href="{{ route('terms') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">{{ __('Terms of use') }}</a></li>
                    <li><a href="{{ route('terms') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">Customer Rights &amp; Policy</a></li>
                    <li><a href="{{ route('contact') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">Content guidelines &amp; reporting</a></li>
                    <li><a href="{{ route('about') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">Compliance &amp; Safety</a></li>
                </ul>
            </div>

            <!-- Column 2: Company -->
            <div class="col-6 col-md-4 col-lg-2">
                <h6 style="font-weight: 700; font-size: 14px; color: #1b2631; margin-bottom: 14px;">{{ __('Company') }}</h6>
                <ul class="list-unstyled" style="display: flex; flex-direction: column; gap: 10px;">
                    <li><a href="{{ route('about') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">{{ __('About us') }}</a></li>
                    <li><a href="{{ route('about') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">Careers</a></li>
                    <li><a href="{{ route('about') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">Press &amp; Media</a></li>
                    <li><a href="{{ route('packages') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">Featured Travel Guides</a></li>
                    <li><a href="{{ route('home') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">Prime Rewards &amp; Coins</a></li>
                </ul>
            </div>

            <!-- Column 3: Destinations -->
            <div class="col-6 col-md-4 col-lg-2">
                <h6 style="font-weight: 700; font-size: 14px; color: #1b2631; margin-bottom: 14px;">{{ __('Destinations') }}</h6>
                <ul class="list-unstyled" style="display: flex; flex-direction: column; gap: 10px;">
                    <li><a href="{{ route('search.index') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">Countries/Territories</a></li>
                    <li><a href="{{ route('services') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">All Flight Routes</a></li>
                    <li><a href="{{ route('search.index') }}?destination=Cox%27s+Bazar" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">Cox's Bazar Hotels</a></li>
                    <li><a href="{{ route('search.index') }}?destination=Dhaka" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">Dhaka Hotels</a></li>
                    <li><a href="{{ route('search.index') }}?destination=Sylhet" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">Sylhet Hotels</a></li>
                </ul>
            </div>

            <!-- Column 4: Partner with us -->
            <div class="col-6 col-md-4 col-lg-3">
                <h6 style="font-weight: 700; font-size: 14px; color: #1b2631; margin-bottom: 14px;">{{ __('Partner with us') }}</h6>
                <ul class="list-unstyled" style="display: flex; flex-direction: column; gap: 10px;">
                    <li><a href="{{ route('contact') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">Prime Hotel Partner Portal</a></li>
                    <li><a href="{{ route('contact') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">Partner Hub</a></li>
                    <li><a href="{{ route('contact') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">Advertise on Prime Booking</a></li>
                    <li><a href="{{ route('contact') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">Affiliates &amp; Agents</a></li>
                    <li><a href="{{ route('contact') }}" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">Prime Booking API Documentation</a></li>
                </ul>
            </div>

            <!-- Column 5: Get the app -->
            <div class="col-6 col-md-4 col-lg-3">
                <h6 style="font-weight: 700; font-size: 14px; color: #1b2631; margin-bottom: 14px;">{{ __('Get the app') }}</h6>
                <ul class="list-unstyled" style="display: flex; flex-direction: column; gap: 10px;">
                    <li><a href="#" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">iOS app</a></li>
                    <li><a href="#" style="text-decoration: none; font-size: 13px; color: #2067e1; font-weight: 400;">Android app</a></li>
                </ul>

                <!-- App Store Buttons (Agoda style) -->
                <div style="margin-top: 16px; display: flex; flex-direction: column; gap: 8px;">
                    <a href="#" style="display: flex; align-items: center; gap: 8px; background: #1b2631; color: #fff; text-decoration: none; padding: 8px 14px; border-radius: 8px; width: fit-content;">
                        <i class="fa-brands fa-apple" style="font-size: 20px;"></i>
                        <div style="line-height: 1.2;">
                            <div style="font-size: 9px; opacity: 0.8;">Download on the</div>
                            <div style="font-size: 13px; font-weight: 700;">App Store</div>
                        </div>
                    </a>
                    <a href="#" style="display: flex; align-items: center; gap: 8px; background: #1b2631; color: #fff; text-decoration: none; padding: 8px 14px; border-radius: 8px; width: fit-content;">
                        <i class="fa-brands fa-google-play" style="font-size: 18px; color: #4ade80;"></i>
                        <div style="line-height: 1.2;">
                            <div style="font-size: 9px; opacity: 0.8;">Get it on</div>
                            <div style="font-size: 13px; font-weight: 700;">Google Play</div>
                        </div>
                    </a>
                </div>

                <!-- Contact Info -->
                <div style="margin-top: 20px;">
                    <div style="font-size: 12px; color: #475569; margin-bottom: 6px;">
                        <i class="fa-solid fa-phone" style="color: #2067e1; margin-right: 6px;"></i>
                        <strong>{{ \App\Models\SiteSetting::get('support_phone', '+880 1700 000000') }}</strong>
                    </div>
                    <div style="font-size: 12px; color: #475569;">
                        <i class="fa-solid fa-envelope" style="color: #2067e1; margin-right: 6px;"></i>
                        {{ \App\Models\SiteSetting::get('support_email', 'support@primeaviation.com') }}
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Divider -->
    <div style="border-top: 1px solid #d1d9e6;"></div>

    <!-- Agoda Dark Lower Mega Footer Section -->
    <div style="background-color: #1e2430; color: #ffffff;">

        <!-- Destination Cities Mega Footer Row -->
        <div class="container" style="padding-top: 32px; padding-bottom: 32px;">
            <div class="row g-4">

                <!-- Destination Cities -->
                <div class="col-6 col-md-4 col-lg-2">
                    <h6 style="font-weight: 700; font-size: 13px; color: #ffffff; margin-bottom: 12px; letter-spacing: 0.2px;">Destination Cities</h6>
                    <ul class="list-unstyled" style="display: flex; flex-direction: column; gap: 8px;">
                        <li><a href="{{ route('search.index') }}?destination=Cox%27s+Bazar" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Cox's Bazar Hotels</a></li>
                        <li><a href="{{ route('search.index') }}?destination=Dhaka" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Dhaka Hotels</a></li>
                        <li><a href="{{ route('search.index') }}?destination=Chittagong" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Chittagong Hotels</a></li>
                        <li><a href="{{ route('search.index') }}?destination=Sylhet" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Sylhet Hotels</a></li>
                        <li><a href="{{ route('search.index') }}?destination=Khulna" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Khulna Hotels</a></li>
                        <li><a href="{{ route('search.index') }}?destination=Rajshahi" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Rajshahi Hotels</a></li>
                    </ul>
                </div>

                <!-- Countries & Territories -->
                <div class="col-6 col-md-4 col-lg-2">
                    <h6 style="font-weight: 700; font-size: 13px; color: #ffffff; margin-bottom: 12px; letter-spacing: 0.2px;">Countries &amp; Territories</h6>
                    <ul class="list-unstyled" style="display: flex; flex-direction: column; gap: 8px;">
                        <li><a href="{{ route('search.index') }}?destination=Bangladesh" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Bangladesh</a></li>
                        <li><a href="{{ route('search.index') }}?destination=India" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">India</a></li>
                        <li><a href="{{ route('search.index') }}?destination=Thailand" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Thailand</a></li>
                        <li><a href="{{ route('search.index') }}?destination=UAE" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">UAE</a></li>
                        <li><a href="{{ route('search.index') }}?destination=Malaysia" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Malaysia</a></li>
                        <li><a href="{{ route('search.index') }}?destination=Singapore" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Singapore</a></li>
                    </ul>
                </div>

                <!-- Europe -->
                <div class="col-6 col-md-4 col-lg-2">
                    <h6 style="font-weight: 700; font-size: 13px; color: #ffffff; margin-bottom: 12px; letter-spacing: 0.2px;">Europe</h6>
                    <ul class="list-unstyled" style="display: flex; flex-direction: column; gap: 8px;">
                        <li><a href="{{ route('search.index') }}?destination=London" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">London Hotels</a></li>
                        <li><a href="{{ route('search.index') }}?destination=Paris" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Paris Hotels</a></li>
                        <li><a href="{{ route('search.index') }}?destination=Rome" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Rome Hotels</a></li>
                        <li><a href="{{ route('search.index') }}?destination=Amsterdam" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Amsterdam Hotels</a></li>
                        <li><a href="{{ route('search.index') }}?destination=Barcelona" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Barcelona Hotels</a></li>
                    </ul>
                </div>

                <!-- Destination Types -->
                <div class="col-6 col-md-4 col-lg-2">
                    <h6 style="font-weight: 700; font-size: 13px; color: #ffffff; margin-bottom: 12px; letter-spacing: 0.2px;">Destination Types</h6>
                    <ul class="list-unstyled" style="display: flex; flex-direction: column; gap: 8px;">
                        <li><a href="{{ route('search.index') }}" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Beach Resorts</a></li>
                        <li><a href="{{ route('search.index') }}" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Luxury Hotels</a></li>
                        <li><a href="{{ route('search.index') }}" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Budget Hotels</a></li>
                        <li><a href="{{ route('search.index') }}" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Airport Hotels</a></li>
                        <li><a href="{{ route('search.index') }}" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Eco Resorts</a></li>
                    </ul>
                </div>

                <!-- Prime Booking Homes -->
                <div class="col-6 col-md-4 col-lg-2">
                    <h6 style="font-weight: 700; font-size: 13px; color: #ffffff; margin-bottom: 12px; letter-spacing: 0.2px;">Prime Booking Homes</h6>
                    <ul class="list-unstyled" style="display: flex; flex-direction: column; gap: 8px;">
                        <li><a href="{{ route('search.index') }}" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Apartments</a></li>
                        <li><a href="{{ route('search.index') }}" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Villas</a></li>
                        <li><a href="{{ route('search.index') }}" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Guesthouses</a></li>
                        <li><a href="{{ route('search.index') }}" style="text-decoration: none; font-size: 12px; color: #cbd5e1; transition: color 0.2s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#cbd5e1'">Host your property</a></li>
                    </ul>
                </div>

                <!-- Payments -->
                <div class="col-6 col-md-4 col-lg-2">
                    <h6 style="font-weight: 700; font-size: 13px; color: #ffffff; margin-bottom: 12px; letter-spacing: 0.2px;">Payments Accepted</h6>
                    <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 14px; align-items: center;">
                        {{-- bKash --}}
                        <span style="background: #e2136e; color: #fff; font-weight: 900; font-size: 11px; padding: 4px 8px; border-radius: 6px; box-shadow: 0 2px 6px rgba(226,19,110,0.3); font-family: 'Plus Jakarta Sans', sans-serif;">bKash</span>
                        {{-- Nagad --}}
                        <span style="background: linear-gradient(135deg, #f7941d 0%, #ed1c24 100%); color: #fff; font-weight: 900; font-size: 11px; padding: 4px 8px; border-radius: 6px; box-shadow: 0 2px 6px rgba(247,148,29,0.3); font-family: 'Plus Jakarta Sans', sans-serif;">Nagad</span>
                        {{-- Visa Card --}}
                        <span style="background: #ffffff; padding: 2px 6px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; height: 25px;">
                            <i class="fa-brands fa-cc-visa text-primary" style="font-size: 18px;"></i>
                        </span>
                        {{-- Mastercard --}}
                        <span style="background: #ffffff; padding: 2px 6px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; height: 25px;">
                            <i class="fa-brands fa-cc-mastercard text-danger" style="font-size: 18px;"></i>
                        </span>
                        {{-- Amex --}}
                        <span style="background: #ffffff; padding: 2px 6px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; height: 25px;">
                            <i class="fa-brands fa-cc-amex text-info" style="font-size: 18px;"></i>
                        </span>
                        {{-- SSLCommerz --}}
                        <span style="background: #006eb4; color: #fff; font-weight: 900; font-size: 10px; padding: 4px 8px; border-radius: 6px; font-family: 'Plus Jakarta Sans', sans-serif;">SSL</span>
                    </div>
                    <div style="font-size: 11px; color: #94a3b8; line-height: 1.7;">
                        <i class="fa-solid fa-shield-halved" style="color: #4ade80; margin-right: 5px;"></i>SSL 256-Bit Encrypted<br>
                        <i class="fa-solid fa-headset" style="color: #60a5fa; margin-right: 5px;"></i>24/7 Customer Support
                    </div>
                </div>

            </div>
        </div>

        <!-- Bottom Copyright Bar — Dark Charcoal -->
        <div style="border-top: 1px solid #334155; background-color: #141824;">
            <div class="container" style="padding-top: 16px; padding-bottom: 16px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 8px;">
                <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                    &copy; 2006 – {{ date('Y') }} Prime Booking Ltd. All rights reserved.
                    &nbsp;·&nbsp; Trade License: 19/515 &nbsp;·&nbsp; Khulna, Bangladesh
                </p>
                <div style="margin: 0; font-size: 11.5px; color: #94a3b8; font-weight: 500;">
                    Engineered &amp; Maintained by 
                    <a href="tel:+8801606352642" class="fw-semibold text-decoration-none" style="color: #60a5fa; transition: color 0.2s;" title="Contact Developer: +8801606352642">
                        Shawon Howlader <span style="font-weight: 400; font-size: 11px; color: #cbd5e1;">(Software Engineer)</span>
                    </a>
                </div>
            </div>
        </div>

    </div>

</footer>
