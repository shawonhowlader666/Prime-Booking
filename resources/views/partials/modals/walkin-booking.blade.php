<!-- Walk-in Booking Modal -->
<div class="modal fade" id="walkInModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-bell-concierge me-2"></i>New Walk-In Reservation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="walkInForm">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Guest First Name *</label>
                            <input type="text" class="form-control" name="guest_first_name" required placeholder="e.g. Tanvir">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Guest Last Name</label>
                            <input type="text" class="form-control" name="guest_last_name" placeholder="e.g. Hasan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Phone Number *</label>
                            <input type="tel" class="form-control" name="guest_phone" required placeholder="+880 1711-000000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Email Address</label>
                            <input type="email" class="form-control" name="guest_email" placeholder="guest@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">ID Type</label>
                            <select class="form-select" name="guest_id_type">
                                <option value="NID">National ID (NID)</option>
                                <option value="PASSPORT">Passport</option>
                                <option value="DRIVING_LICENSE">Driving License</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">NID / Passport Number</label>
                            <input type="text" class="form-control" name="guest_id_number" placeholder="e.g. 19902692518293847">
                        </div>

                        <div class="col-12"><hr class="my-1 text-muted opacity-25"></div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Room Category *</label>
                            <select class="form-select" name="room_type_id" id="modal_room_type_select" required>
                                @if(isset($roomTypes) && $roomTypes->count() > 0)
                                    @foreach($roomTypes as $rt)
                                        <option value="{{ $rt->id }}" data-price="{{ $rt->base_price }}">{{ $rt->name }} (৳{{ number_format($rt->base_price) }}/night)</option>
                                    @endforeach
                                @else
                                    <option value="1" data-price="5000">Deluxe King Suite (৳5,000/night)</option>
                                    <option value="2" data-price="4500">Standard Double Room (৳4,500/night)</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Specific Room Unit</label>
                            <select class="form-select" name="room_id" id="modal_room_unit_select">
                                <option value="">Auto-Assign Best Available Room</option>
                                @if(isset($rooms) && $rooms->count() > 0)
                                    @foreach($rooms as $r)
                                        <option value="{{ $r->id }}">{{ $r->room_number }} (Floor {{ $r->floor }} - {{ $r->status }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Check-In Date *</label>
                            <input type="date" class="form-control" name="check_in_date" id="modal_check_in" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Check-Out Date *</label>
                            <input type="date" class="form-control" name="check_out_date" id="modal_check_out" value="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Total Tariff (BDT) *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">৳</span>
                                <input type="number" step="0.01" class="form-control fw-bold text-primary" name="total_amount" id="modal_total_amount" value="5000" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Advance Paid (BDT)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">৳</span>
                                <input type="number" step="0.01" class="form-control" name="paid_amount" id="modal_paid_amount" value="5000">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small text-secondary">Special Requests / Notes</label>
                            <textarea class="form-control" name="special_requests" rows="2" placeholder="e.g. Airport pickup required, extra towels, non-smoking floor"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-3">
                    <button type="button" class="btn btn-outline-secondary px-4 rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4 rounded-3 fw-semibold shadow-sm" onclick="handleQuickBooking()">
                        <i class="fa-solid fa-check me-2"></i>Create Reservation & Folio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
