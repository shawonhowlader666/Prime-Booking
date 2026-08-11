@extends('layouts.admin')
@section('title', 'Guest Reviews & Moderation | Prime Aviation Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Quality</span>
        <span class="sep">-</span><strong style="color:#333;">Reviews &amp; Moderation</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title">Guest Reviews &amp; Rating Moderation</h1>
        <button class="btn-export-csv" onclick="alert('Exporting Reviews CSV...')">
            <i class="fa-solid fa-file-csv"></i> Export CSV
        </button>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Reviews Table --}}
    <div class="data-table-card">
        <div class="data-table-card-header">
            <h6>All Guest Testimonials &amp; Ratings</h6>
            <span class="live-feed-badge">Moderation Feed</span>
        </div>

        <div style="overflow-x:auto;">
            <table class="table-stockifly" style="width:100%;">
                <thead>
                    <tr>
                        <th>Guest Name</th>
                        <th>Property Name</th>
                        <th>Rating Stars</th>
                        <th>Review Comment</th>
                        <th>Submitted Date</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($reviews as $r)
                    <tr>
                        <td>
                            <strong style="font-size:13px; color:#1e293b;">{{ $r->guest_name }}</strong>
                        </td>
                        <td>
                            <span style="font-size:12.5px; color:#334155;">{{ optional($r->property)->name ?? $r->property_name ?? 'Property' }}</span>
                        </td>
                        <td>
                            <span style="color:#ff9f43; font-size:12px;">{{ str_repeat('★', $r->rating ?? 5) }}</span>
                        </td>
                        <td style="font-size:12px; color:#595959; max-width:280px; white-space:normal;">
                            "{{ $r->comment }}"
                        </td>
                        <td style="font-size:11.5px; color:#8c8c8c;">
                            {{ $r->created_at ? (is_string($r->created_at) ? $r->created_at : $r->created_at->format('M d, Y')) : 'N/A' }}
                        </td>
                        <td>
                            <span class="badge-status {{ strtolower($r->status) == 'approved' ? 'confirmed' : 'pending' }}">
                                {{ ucfirst($r->status ?? 'Approved') }}
                            </span>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <form action="{{ route('admin.reviews.toggle', $r->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-table-action primary">
                                    {{ strtolower($r->status) == 'approved' ? 'Unapprove' : 'Approve' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.reviews.destroy', $r->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this review permanently?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-table-action danger" style="margin-left:4px;">Delete <i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:32px; color:#8c8c8c;">
                            No guest reviews recorded yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
