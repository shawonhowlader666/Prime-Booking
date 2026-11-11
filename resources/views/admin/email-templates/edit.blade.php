@extends('layouts.admin')
@section('title', 'Edit Email Template | PRIME BOOKING Admin')

@section('content')
<div class="page-header-card mb-4" style="background:#fff; border:1px solid #e8e8e8; border-radius:4px; padding:20px;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.email-templates.index') }}" class="btn btn-sm btn-outline-secondary py-1 px-2" style="border-radius:4px;"><i class="fa-solid fa-arrow-left"></i></a>
                <h4 class="mb-0 fw-bold text-dark">Edit Email Template</h4>
            </div>
            <p class="text-muted mb-0 font-size-base">Customize subject, copy, and layout for <span class="badge bg-light text-dark border">{{ $template->code }}</span></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary" style="border-radius:4px;">Cancel</a>
            <button type="submit" form="templateEditForm" class="btn btn-primary" style="border-radius:4px;">
                <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
            </button>
        </div>
    </div>
</div>

<form id="templateEditForm" method="POST" action="{{ route('admin.email-templates.update', $template->id) }}">
    @csrf
    @method('PUT')

    <div class="row g-4">
        {{-- Left Form Panel --}}
        <div class="col-lg-8">
            <div class="card mb-4" style="background:#fff; border:1px solid #e8e8e8; border-radius:4px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold text-dark"><i class="fa-solid fa-pen-nib me-2 text-primary"></i> Template Details</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Template Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $template->name) }}" required style="border-radius:4px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Unique Code Identifier</label>
                            <input type="text" class="form-control bg-light" value="{{ $template->code }}" readonly style="border-radius:4px;">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Email Subject Line <span class="text-danger">*</span></label>
                            <input type="text" id="subjectInput" name="subject" class="form-control" value="{{ old('subject', $template->subject) }}" required style="border-radius:4px;">
                            <small class="text-muted">Placeholders can also be used in the subject line (e.g. <code>#{booking_ref}</code>)</small>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label fw-bold small text-muted mb-0">Email HTML Body <span class="text-danger">*</span></label>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnPreviewLive" style="border-radius:4px; font-size:11px;">
                                    <i class="fa-solid fa-play me-1"></i> Update Live Preview
                                </button>
                            </div>
                            <textarea id="bodyHtmlInput" name="body_html" rows="18" class="form-control font-monospace" style="font-size:12.5px; border-radius:4px; tab-size:4;" required>{{ old('body_html', $template->body_html) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Panel: Placeholders & Live Preview --}}
        <div class="col-lg-4">
            {{-- Placeholders Pill Box --}}
            <div class="card mb-4" style="background:#fff; border:1px solid #e8e8e8; border-radius:4px;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0 fw-bold text-dark"><i class="fa-solid fa-code me-2 text-primary"></i> Available Variables</h6>
                    <small class="text-muted" style="font-size:11px;">Click to copy</small>
                </div>
                <div class="card-body p-3">
                    <p class="small text-muted mb-3">Click on any tag below to insert it at your cursor in the HTML body or copy it to clipboard:</p>
                    <div class="d-flex flex-wrap gap-2">
                        @if($template->placeholders && is_array($template->placeholders))
                            @foreach($template->placeholders as $ph)
                                <button type="button" class="btn btn-sm btn-light border placeholder-pill text-primary fw-bold" data-tag="&#123;{{ $ph }}&#125;" style="font-size:12px; border-radius:4px;">
                                    + {<span>{{ $ph }}</span>}
                                </button>
                            @endforeach
                        @else
                            <span class="text-muted small">No specific placeholders defined.</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Status toggle --}}
            <div class="card mb-4" style="background:#fff; border:1px solid #e8e8e8; border-radius:4px;">
                <div class="card-body p-3">
                    <div class="form-check form-switch d-flex align-items-center justify-content-between ps-0">
                        <label class="form-check-label fw-bold text-dark mb-0" for="is_active">Template Active</label>
                        <input class="form-check-input ms-auto" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ $template->is_active ? 'checked' : '' }} style="cursor:pointer; width:38px; height:20px;">
                    </div>
                    <small class="text-muted d-block mt-2">When inactive, system will pause sending this automated email.</small>
                </div>
            </div>

            {{-- Live Render Box --}}
            <div class="card" style="background:#fff; border:1px solid #e8e8e8; border-radius:4px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold text-dark"><i class="fa-solid fa-eye me-2 text-primary"></i> Live Output Sample</h6>
                </div>
                <div class="card-body p-3" style="background:#f8fafc; max-height:400px; overflow-y:auto;" id="livePreviewContainer">
                    <div class="text-center text-muted small py-4">Click "Update Live Preview" above to test your changes.</div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
// Click-to-insert placeholder into textarea
document.querySelectorAll('.placeholder-pill').forEach(pill => {
    pill.addEventListener('click', function() {
        const tag = this.getAttribute('data-tag');
        const textarea = document.getElementById('bodyHtmlInput');
        
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;

        textarea.value = text.substring(0, start) + tag + text.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + tag.length;
        textarea.focus();

        // Notification feedback
        const orig = this.innerHTML;
        this.innerHTML = '<i class="fa-solid fa-check text-success"></i> Added';
        setTimeout(() => { this.innerHTML = orig; }, 800);
    });
});

// Live preview render
document.getElementById('btnPreviewLive').addEventListener('click', function() {
    let rawHtml = document.getElementById('bodyHtmlInput').value;
    
    // Replace placeholders with dummy data for local preview
    const sample = {
        'guest_name': 'John Doe',
        'hotel_name': 'Grand Palace Hotel Dhaka',
        'room_type': 'Deluxe King Suite',
        'checkin_date': '12 Oct 2026',
        'checkout_date': '15 Oct 2026',
        'total_amount': '14,500',
        'booking_ref': 'PB-789012',
        'cancellation_reason': 'Schedule change',
        'refund_amount': '12,500',
        'vendor_name': 'Rahim Chowdhury',
        'payout_amount': '45,000',
        'payout_ref': 'PAY-4091',
        'payment_method': 'Bank Transfer (BRAC Bank)',
        'account_details': 'A/C: **** 1098',
        'transaction_id': 'TXN98721360',
        'user_name': 'John Doe',
        'reset_url': '#',
        'ticket_ref': 'TKT-2026-0042',
        'subject': 'Room check-in extension',
        'reply_message': 'Your request has been approved by the hotel.',
    };

    for (let k in sample) {
        rawHtml = rawHtml.replaceAll('{' + k + '}', sample[k]);
    }

    document.getElementById('livePreviewContainer').innerHTML = rawHtml;
});
</script>
@endpush
@endsection
