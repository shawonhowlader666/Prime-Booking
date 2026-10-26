@extends('layouts.vendor')
@section('title', 'Notifications Center | Vendor Portal')

@section('content')
<div class="page-header-card mb-4" style="background:#fff; border:1px solid #e8e8e8; border-radius:4px; padding:20px;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h1 class="page-title m-0 fw-bold fs-5 text-dark">
                <i class="fa-solid fa-bell me-2 text-warning"></i> Notifications Center
            </h1>
            <p class="text-muted mb-0 small mt-1">Real-time alerts for reservations, guest reviews, payout approvals, and support messages.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if(isset($notifications) && $notifications->count() > 0)
            <form action="{{ route('vendor.notifications.read-all') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary" style="border-radius:4px;">
                    <i class="fa-solid fa-check-double me-1"></i> Mark All as Read
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

<div class="page-content-area">
    <div class="card shadow-sm border-0" style="background:#fff; border-radius:4px;">
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
            <span class="fw-bold text-dark" style="font-size:13.5px;"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i> Activity Alerts</span>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="border-radius:4px; font-size:11.5px;">
                {{ $notifications->count() }} Updates
            </span>
        </div>
        
        <div class="list-group list-group-flush">
            @forelse($notifications as $notif)
            <div class="list-group-item p-3 border-bottom d-flex align-items-start gap-3 transition-all" style="transition:background 0.15s ease;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                {{-- Icon Badge --}}
                <div style="width:40px; height:40px; border-radius:4px; background:{{ $notif['bg'] ?? 'rgba(24,144,255,0.1)' }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="{{ $notif['icon'] ?? 'fa-solid fa-bell text-primary' }}" style="font-size:16px;"></i>
                </div>

                {{-- Content --}}
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                        <span class="fw-bold text-dark" style="font-size:13px;">{{ $notif['title'] ?? 'Notification' }}</span>
                        <span class="text-muted" style="font-size:11px;">
                            <i class="fa-regular fa-clock me-1"></i>
                            @if(isset($notif['time']))
                                {{ is_string($notif['time']) ? $notif['time'] : $notif['time']->diffForHumans() }}
                            @else
                                Just now
                            @endif
                        </span>
                    </div>
                    <p class="text-muted mb-2" style="font-size:12.5px; line-height:1.5;">
                        {{ $notif['message'] ?? '' }}
                    </p>
                    @if(isset($notif['link']) && $notif['link'])
                    <a href="{{ $notif['link'] }}" class="btn btn-sm btn-light border py-1 px-2" style="font-size:11.5px; border-radius:4px;">
                        View Details <i class="fa-solid fa-arrow-right ms-1" style="font-size:10px;"></i>
                    </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="fa-regular fa-bell-slash fa-2x mb-3 text-muted d-block" style="opacity:0.4;"></i>
                <div class="fw-semibold">No recent activity notifications</div>
                <small class="text-muted">You're all caught up! New bookings and alerts will appear here automatically.</small>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
