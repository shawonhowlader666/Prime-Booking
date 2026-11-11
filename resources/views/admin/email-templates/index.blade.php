@extends('layouts.admin')
@section('title', 'Email Templates | PRIME BOOKING Admin')

@section('content')
<div class="page-header-card mb-4" style="background:#fff; border:1px solid #e8e8e8; border-radius:4px; padding:20px;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fa-solid fa-envelope-open-text text-primary me-2"></i> Automated Email Templates</h4>
            <p class="text-muted mb-0 font-size-base">Manage and customize transactional emails sent to guests, vendors, and partners.</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2" style="border-radius:4px; font-size:12px;">
                <i class="fa-solid fa-shield-check me-1"></i> {{ $templates->count() }} Templates Configured
            </span>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4" style="background:#fff; border:1px solid #e8e8e8; border-radius:4px;">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.email-templates.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius:4px 0 0 4px;"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, code or subject..." value="{{ request('search') }}" style="border-radius:0 4px 4px 0;">
                </div>
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select" onchange="this.form.submit()" style="border-radius:4px;">
                    <option value="">All Categories</option>
                    <option value="booking" {{ request('category') == 'booking' ? 'selected' : '' }}>Bookings</option>
                    <option value="payout" {{ request('category') == 'payout' ? 'selected' : '' }}>Payouts</option>
                    <option value="auth" {{ request('category') == 'auth' ? 'selected' : '' }}>Authentication</option>
                    <option value="support" {{ request('category') == 'support' ? 'selected' : '' }}>Customer Support</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100" style="border-radius:4px;"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                @if(request()->anyFilled(['search', 'category']))
                    <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary" style="border-radius:4px;">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card" style="background:#fff; border:1px solid #e8e8e8; border-radius:4px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#f8fafc; border-bottom:1px solid #e2e8f0; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">
                <tr>
                    <th class="ps-3 py-3">Template Name</th>
                    <th>Category</th>
                    <th>Default Subject</th>
                    <th>Placeholders</th>
                    <th class="text-center">Status</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $tmpl)
                <tr>
                    <td class="ps-3">
                        <div class="fw-bold text-dark">{{ $tmpl->name }}</div>
                        <code class="text-muted" style="font-size:11px;">{{ $tmpl->code }}</code>
                    </td>
                    <td>
                        @switch($tmpl->category)
                            @case('booking')
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1" style="border-radius:4px;">Booking</span>
                                @break
                            @case('payout')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="border-radius:4px;">Payout</span>
                                @break
                            @case('auth')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1" style="border-radius:4px;">Auth</span>
                                @break
                            @case('support')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="border-radius:4px;">Support</span>
                                @break
                            @default
                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1" style="border-radius:4px;">{{ ucfirst($tmpl->category) }}</span>
                        @endswitch
                    </td>
                    <td>
                        <span class="text-truncate d-inline-block" style="max-width:280px;" title="{{ $tmpl->subject }}">
                            {{ $tmpl->subject }}
                        </span>
                    </td>
                    <td>
                        @if($tmpl->placeholders && is_array($tmpl->placeholders))
                            <div class="d-flex flex-wrap gap-1" style="max-width:260px;">
                                @foreach(array_slice($tmpl->placeholders, 0, 4) as $tag)
                                    <span class="badge bg-light text-secondary border px-1 py-0" style="font-size:10.5px; border-radius:3px;">{<span>{{ $tag }}</span>}</span>
                                @endforeach
                                @if(count($tmpl->placeholders) > 4)
                                    <span class="badge bg-light text-muted" style="font-size:10px;">+{{ count($tmpl->placeholders) - 4 }}</span>
                                @endif
                            </div>
                        @else
                            <span class="text-muted font-size-base">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <form action="{{ route('admin.email-templates.toggle', $tmpl->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm border-0 bg-transparent p-0" title="Click to toggle status">
                                @if($tmpl->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="border-radius:4px;"><i class="fa-solid fa-check-circle me-1"></i> Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="border-radius:4px;"><i class="fa-solid fa-pause-circle me-1"></i> Inactive</span>
                                @endif
                            </button>
                        </form>
                    </td>
                    <td class="text-end pe-3">
                        <button type="button" class="btn btn-sm btn-outline-info me-1 preview-btn" data-id="{{ $tmpl->id }}" title="Live Preview" style="border-radius:4px; padding:4px 9px;">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        <a href="{{ route('admin.email-templates.edit', $tmpl->id) }}" class="btn btn-sm btn-primary" title="Edit Template" style="border-radius:4px; padding:4px 10px;">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No email templates found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Preview Modal --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:4px;">
            <div class="modal-header py-3" style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                <h6 class="modal-title fw-bold" id="previewModalTitle"><i class="fa-solid fa-envelope me-2 text-primary"></i> Template Preview</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background:#f1f5f9;">
                <div class="mb-3 p-3 bg-white border" style="border-radius:4px;">
                    <div class="small text-muted mb-1">Rendered Subject Line:</div>
                    <div class="fw-bold text-dark" id="previewSubject"></div>
                </div>
                <div class="bg-white border p-3" style="border-radius:4px; max-height:480px; overflow-y:auto;" id="previewBodyContainer">
                    {{-- Rendered email preview loads here --}}
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius:4px;">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.preview-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        
        document.getElementById('previewSubject').innerHTML = '<span class="spinner-border spinner-border-sm text-primary"></span> Loading...';
        document.getElementById('previewBodyContainer').innerHTML = '<div class="text-center py-5"><span class="spinner-border text-primary"></span></div>';
        modal.show();

        fetch(`/admin/email-templates/${id}/preview`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('previewSubject').innerText = data.subject;
                document.getElementById('previewBodyContainer').innerHTML = data.body;
            })
            .catch(err => {
                document.getElementById('previewBodyContainer').innerHTML = '<div class="alert alert-danger">Failed to load preview.</div>';
            });
    });
});
</script>
@endpush
@endsection
