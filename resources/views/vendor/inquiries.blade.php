@extends('layouts.vendor')

@section('title', 'Guest Inquiries & Property Messenger — Vendor Portal')

@section('content')
<div class="container-fluid px-4 py-3" style="max-width: 1400px;">

    {{-- PAGE HEADER CARD --}}
    <div class="page-header-card mb-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:4px; padding:20px 24px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1" style="font-size:12px;">
                        <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Guest Inquiries</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold" style="color:#0f172a; font-size:20px; letter-spacing:-0.3px;">
                    <i class="fa-solid fa-comments text-primary me-2"></i> Guest Inquiries &amp; Property Messenger
                </h4>
                <p class="text-muted mb-0" style="font-size:12.5px;">Direct pre-booking inquiries, policy questions &amp; special requests from travelers on Prime Booking.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="font-size:13px; border-radius:4px;">
            <i class="fa-solid fa-circle-check me-1.5"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- INQUIRIES DATA TABLE --}}
    <div class="card border-0 p-0" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="border-color:#e2e8f0 !important;">
            <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;">
                <i class="fa-solid fa-inbox text-primary me-2"></i> Inquiries Inbox ({{ $inquiries->total() }} Messages)
            </h6>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:12.5px;">
                <thead class="bg-light">
                    <tr>
                        <th style="padding:12px 16px; font-weight:700; color:#475569;">GUEST &amp; CONTACT</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569;">HOTEL / PROPERTY</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569;">QUESTION / MESSAGE</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569;">STATUS</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569; text-align:right;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($inquiries as $inq)
                    @php
                        $guestWa = preg_replace('/[^0-9]/', '', $inq->phone ?? '');
                        if (str_starts_with($guestWa, '01')) { $guestWa = '88' . $guestWa; }
                    @endphp
                    <tr>
                        <td style="padding:12px 16px;">
                            <strong class="text-dark d-block" style="font-size:13px;">{{ $inq->name }}</strong>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <a href="tel:{{ $inq->phone }}" class="text-decoration-none text-muted" style="font-size:11.5px;">
                                    <i class="fa-solid fa-phone text-success me-1"></i> {{ $inq->phone }}
                                </a>
                                @if(!empty($guestWa))
                                    <a href="https://wa.me/{{ $guestWa }}?text={{ urlencode('Hello ' . $inq->name . ', replying from ' . ($inq->property?->name ?? 'Hotel Front Desk') . ' regarding your Prime Booking inquiry.') }}" target="_blank" class="badge bg-success bg-opacity-10 text-success text-decoration-none" style="font-size:10.5px; border-radius:3px;">
                                        <i class="fa-brands fa-whatsapp me-0.5"></i> WhatsApp
                                    </a>
                                @endif
                            </div>
                            @if($inq->email)
                                <small class="text-muted d-block" style="font-size:11px;">{{ $inq->email }}</small>
                            @endif
                        </td>
                        <td style="padding:12px 16px;">
                            <strong class="text-dark d-block">{{ $inq->property?->name ?? 'Direct Property' }}</strong>
                            <small class="text-muted">{{ $inq->property?->city ?? 'Bangladesh' }}</small>
                        </td>
                        <td style="padding:12px 16px; max-width:320px;">
                            <p class="mb-1 text-dark" style="font-size:12.5px; line-height:1.4;">{{ $inq->message }}</p>
                            @if($inq->reply)
                                <div class="p-2 bg-light rounded border mt-1" style="font-size:11.5px;">
                                    <strong class="text-primary d-block"><i class="fa-solid fa-reply me-1"></i> Your Reply:</strong>
                                    <span class="text-muted">{{ $inq->reply }}</span>
                                    <small class="text-muted d-block mt-0.5" style="font-size:10px;">{{ $inq->replied_at ? $inq->replied_at->format('d M Y, h:i A') : '' }}</small>
                                </div>
                            @endif
                            <small class="text-muted" style="font-size:10.5px;"><i class="fa-regular fa-clock me-1"></i> {{ $inq->created_at ? $inq->created_at->diffForHumans() : '' }}</small>
                        </td>
                        <td style="padding:12px 16px;">
                            @if($inq->status === 'answered')
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1" style="font-size:10.5px; border-radius:3px;"><i class="fa-solid fa-check me-1"></i> Answered</span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning fw-bold px-2 py-1" style="font-size:10.5px; border-radius:3px;"><i class="fa-solid fa-clock me-1"></i> Needs Reply</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px; text-align:right;">
                            <button type="button" class="btn btn-sm btn-primary fw-bold text-white d-inline-flex align-items-center gap-1" onclick="openReplyModal({{ $inq->id }}, '{{ addslashes($inq->name) }}', '{{ addslashes($inq->message) }}')" style="font-size:11.5px; height:28px; border-radius:4px; background-color:var(--primary); border:none;">
                                <i class="fa-solid fa-reply"></i> Reply
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-comments fa-2x mb-2 text-secondary opacity-50"></i>
                            <p class="mb-0">No guest inquiries received yet.</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($inquiries->hasPages())
            <div class="p-3 border-top d-flex justify-content-end" style="border-color:#e2e8f0 !important;">
                {{ $inquiries->links() }}
            </div>
        @endif
    </div>

</div>

{{-- REPLY INQUIRY MODAL --}}
<div class="modal fade" id="replyInquiryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:6px; border:1px solid #cbd5e1; box-shadow:0 12px 40px rgba(0,0,0,0.18);">
            <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px; background:#f8fafc;">
                <h6 class="modal-title fw-bold text-dark mb-0" style="font-size:15px;">
                    <i class="fa-solid fa-reply text-primary me-2"></i> Reply to Guest — <span id="replyGuestName"></span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="replyInquiryForm" method="POST">
                @csrf
                <div class="modal-body" style="padding:20px;">
                    <div class="p-2.5 bg-light rounded border mb-3" style="font-size:12px;">
                        <strong class="text-muted d-block mb-1">Guest Question:</strong>
                        <span id="replyGuestMsg" class="text-dark fw-semibold"></span>
                    </div>

                    {{-- Quick Response Templates --}}
                    <label class="form-label mb-1.5" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">Quick Response Templates</label>
                    <div class="d-flex flex-wrap gap-1.5 mb-3">
                        <button type="button" class="btn btn-sm btn-light border text-dark fw-semibold" onclick="setReplyTemplate('Yes, we are delighted to accommodate early check-in subject to room availability upon arrival.')" style="font-size:11px; border-radius:4px; padding:3px 8px;">
                            Early Check-in OK
                        </button>
                        <button type="button" class="btn btn-sm btn-light border text-dark fw-semibold" onclick="setReplyTemplate('Airport pickup transfer is available for ৳1,500 per trip. Please share your flight number and arrival time.')" style="font-size:11px; border-radius:4px; padding:3px 8px;">
                            Airport Pickup Info
                        </button>
                        <button type="button" class="btn btn-sm btn-light border text-dark fw-semibold" onclick="setReplyTemplate('Couples and families are welcome. Please present valid Government NID cards or passports during check-in.')" style="font-size:11px; border-radius:4px; padding:3px 8px;">
                            Couple / NID Policy
                        </button>
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-1" style="font-size:12px; font-weight:700; color:#1e293b;">Your Reply Message <span class="text-danger">*</span></label>
                        <textarea name="reply" id="vendorReplyTextarea" class="form-control" rows="3" required placeholder="Type your response to the guest..." style="font-size:13px; border-radius:4px;"></textarea>
                    </div>
                </div>

                <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px; background:#f8fafc;">
                    <button type="button" class="btn btn-light border text-secondary fw-bold" data-bs-dismiss="modal" style="font-size:12.5px; height:36px; border-radius:4px;">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold text-white d-inline-flex align-items-center gap-1.5" style="font-size:12.5px; height:36px; border-radius:4px; background-color:var(--primary); border:none;">
                        <i class="fa-solid fa-paper-plane"></i> Send Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openReplyModal(id, name, msg) {
    document.getElementById('replyInquiryForm').action = '/vendor/inquiries/' + id + '/reply';
    document.getElementById('replyGuestName').textContent = name;
    document.getElementById('replyGuestMsg').textContent = '"' + msg + '"';
    document.getElementById('vendorReplyTextarea').value = '';
    new bootstrap.Modal(document.getElementById('replyInquiryModal')).show();
}

function setReplyTemplate(text) {
    const el = document.getElementById('vendorReplyTextarea');
    if (el) {
        el.value = text;
        el.focus();
    }
}
</script>
@endsection
