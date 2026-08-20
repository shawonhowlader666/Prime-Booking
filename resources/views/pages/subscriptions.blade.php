@extends('layouts.main', ['activePage' => 'subscription'])

@section('title', 'Payments and Subscriptions | Prime Booking')
@section('meta_description', 'Manage saved payment methods, email subscriptions, newsletter frequencies and booking reminders.')

@section('content')
<div class="py-4" style="background-color: #f7f9fa; min-height: 88vh; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 16px;">
        <div class="row g-4">
            
            {{-- Left User Account Sidebar Navigation (Exact Preservation) --}}
            <div class="col-lg-3 col-md-4" style="max-width: 270px;">
                <x-user-sidebar activePage="subscription" />
            </div>

            {{-- Right Column: 100% 1:1 Parity with Agoda Payments and Subscriptions Screenshot --}}
            <div class="col-lg-9 col-md-8">
                
                {{-- ── SECTION 1: PAYMENT METHODS ── --}}
                <h3 class="fw-bold text-dark mb-3" style="font-size: 22px; letter-spacing: -0.3px;">Payment methods</h3>
                
                <div class="bg-white border shadow-sm p-4 mb-5" style="border-color: #e2e8f0 !important; border-radius: 12px !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-dark fw-semibold" style="font-size: 15px;">Save my credit card information</span>
                            <span class="text-muted" style="cursor: pointer; font-size: 14px;" title="We use 256-bit SSL encryption to securely tokenize your card with zero plaintext storage.">
                                <i class="fa-regular fa-circle-question"></i>
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span id="label_save_card" class="fw-bold" style="font-size: 13.5px; color: #2067e1; width: 35px; text-align: right;">YES</span>
                            <div class="form-check form-switch m-0 p-0" style="min-height: auto;">
                                <input class="form-check-input subscription-toggle" type="checkbox" role="switch" id="toggle_save_card" checked
                                       data-key="save_card" data-label="label_save_card"
                                       style="width: 44px; height: 24px; cursor: pointer; background-color: #2067e1; border-color: #2067e1;">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── SECTION 2: EMAIL SUBSCRIPTIONS ── --}}
                <h3 class="fw-bold text-dark mb-3" style="font-size: 22px; letter-spacing: -0.3px;">Email subscriptions</h3>
                
                {{-- Newsletter Frequency Radio Row (1:1 Exact Parity) --}}
                <div class="bg-white border shadow-sm p-4 mb-3" style="border-color: #e2e8f0 !important; border-radius: 12px !important;">
                    <div class="fw-bold text-dark mb-3" style="font-size: 14.5px;">Newsletter</div>
                    <div class="d-flex flex-wrap align-items-center gap-4 gap-md-5">
                        <label class="d-flex align-items-center gap-2 m-0" style="cursor: pointer; font-size: 14.5px; color: #334155;">
                            <input type="radio" name="newsletter_freq" value="daily" class="subscription-radio" style="width: 18px; height: 18px; accent-color: #2067e1;">
                            <span>Daily</span>
                        </label>

                        <label class="d-flex align-items-center gap-2 m-0" style="cursor: pointer; font-size: 14.5px; color: #334155;">
                            <input type="radio" name="newsletter_freq" value="twice_week" class="subscription-radio" style="width: 18px; height: 18px; accent-color: #2067e1;">
                            <span>Twice a week</span>
                        </label>

                        <label class="d-flex align-items-center gap-2 m-0" style="cursor: pointer; font-size: 14.5px; color: #334155;">
                            <input type="radio" name="newsletter_freq" value="weekly" class="subscription-radio" style="width: 18px; height: 18px; accent-color: #2067e1;">
                            <span>Weekly</span>
                        </label>

                        <label class="d-flex align-items-center gap-2 m-0" style="cursor: pointer; font-size: 14.5px; color: #334155;">
                            <input type="radio" name="newsletter_freq" value="never" class="subscription-radio" checked style="width: 18px; height: 18px; accent-color: #2067e1;">
                            <span class="fw-semibold">Never</span>
                        </label>
                    </div>
                </div>

                {{-- Booking Assist Reminders Switch --}}
                <div class="bg-white border shadow-sm p-4 mb-3" style="border-color: #e2e8f0 !important; border-radius: 12px !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-dark fw-semibold" style="font-size: 14.5px;">I would like to receive booking assist reminders</span>
                        <div class="d-flex align-items-center gap-3">
                            <span id="label_booking_assist" class="fw-bold" style="font-size: 13.5px; color: #64748b; width: 35px; text-align: right;">NO</span>
                            <div class="form-check form-switch m-0 p-0">
                                <input class="form-check-input subscription-toggle" type="checkbox" role="switch" id="toggle_booking_assist"
                                       data-key="booking_assist" data-label="label_booking_assist"
                                       style="width: 44px; height: 24px; cursor: pointer;">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Agoda Promotions Email Switch --}}
                <div class="bg-white border shadow-sm p-4 mb-3" style="border-color: #e2e8f0 !important; border-radius: 12px !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-dark fw-semibold" style="font-size: 14.5px;">I would like to receive emails about Prime promotions</span>
                        <div class="d-flex align-items-center gap-3">
                            <span id="label_promo_emails" class="fw-bold" style="font-size: 13.5px; color: #64748b; width: 35px; text-align: right;">NO</span>
                            <div class="form-check form-switch m-0 p-0">
                                <input class="form-check-input subscription-toggle" type="checkbox" role="switch" id="toggle_promo_emails"
                                       data-key="promo_emails" data-label="label_promo_emails"
                                       style="width: 44px; height: 24px; cursor: pointer;">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Upcoming Trip Offers Switch (1:1 Exact Parity) --}}
                <div class="bg-white border shadow-sm p-4 mb-3" style="border-color: #e2e8f0 !important; border-radius: 12px !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-dark fw-semibold" style="font-size: 14.5px;">I would like to know about information and offers related to my upcoming trip</span>
                        <div class="d-flex align-items-center gap-3">
                            <span id="label_trip_offers" class="fw-bold" style="font-size: 13.5px; color: #2067e1; width: 35px; text-align: right;">YES</span>
                            <div class="form-check form-switch m-0 p-0">
                                <input class="form-check-input subscription-toggle" type="checkbox" role="switch" id="toggle_trip_offers" checked
                                       data-key="trip_offers" data-label="label_trip_offers"
                                       style="width: 44px; height: 24px; cursor: pointer; background-color: #2067e1; border-color: #2067e1;">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

{{-- Real-Time Dynamic AJAX Subscription Switch Handlers --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Switches Handler
    document.querySelectorAll('.subscription-toggle').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const isChecked = this.checked;
            const labelEl = document.getElementById(this.dataset.label);
            const key = this.dataset.key;
            
            if (labelEl) {
                labelEl.textContent = isChecked ? 'YES' : 'NO';
                labelEl.style.color = isChecked ? '#2067e1' : '#64748b';
            }
            this.style.backgroundColor = isChecked ? '#2067e1' : '';
            this.style.borderColor = isChecked ? '#2067e1' : '';

            // Ajax Sync
            fetch("{{ route('api.user.subscription.update') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ key: key, value: isChecked ? 1 : 0 })
            }).catch(e => console.error('Subscription update failed:', e));
        });
    });

    // 2. Radio Frequency Handler
    document.querySelectorAll('.subscription-radio').forEach(function(radio) {
        radio.addEventListener('change', function() {
            fetch("{{ route('api.user.subscription.update') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ key: 'newsletter_frequency', value: this.value })
            }).catch(e => console.error('Newsletter frequency update failed:', e));
        });
    });
});
</script>
@endsection
