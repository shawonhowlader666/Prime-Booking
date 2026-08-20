@extends('layouts.main', ['activePage' => 'messages'])

@section('title', 'Property Messages | Prime Booking')
@section('meta_description', 'Chat directly with your hotel hosts and view property inquiry conversations on Prime Booking.')

@section('content')
<style>
/* Agoda 1:1 Property Messages Styling */
.messages-page-wrapper {
    background-color: #f7f9fa;
    min-height: 88vh;
    padding: 36px 0 70px 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
.messages-container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 16px;
}
/* Agoda 3D Container Card */
.messages-main-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    min-height: 520px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 24px;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.messages-main-card:hover {
    box-shadow: 0 12px 28px -6px rgba(15, 23, 42, 0.08), 0 4px 10px -2px rgba(15, 23, 42, 0.04);
}
</style>

<div class="messages-page-wrapper">
    <div class="messages-container">
        <div class="row g-4">
            
            {{-- Left User Account Sidebar Navigation (Exact Preservation) --}}
            <div class="col-lg-3 col-md-4" style="max-width: 270px;">
                <x-user-sidebar activePage="messages" />
            </div>

            {{-- Right Column: 100% 1:1 Parity with Agoda Property Messages Screenshot --}}
            <div class="col-lg-9 col-md-8">
                
                @if(isset($messages) && count($messages) > 0)
                    {{-- Active Messages Inbox --}}
                    <div class="bg-white border p-4 shadow-sm" style="border-color: #e2e8f0 !important; border-radius: 16px !important;">
                        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                            <div>
                                <h4 class="fw-bold text-dark mb-1" style="font-size: 20px;">{{ __('Property messages') }}</h4>
                                <p class="text-muted mb-0" style="font-size: 13.5px;">Direct inquiries and conversations with hotel properties.</p>
                            </div>
                            <button class="btn btn-outline-primary btn-sm rounded-pill fw-bold px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#newMessageModal" style="font-size: 12.5px;">
                                <i class="fa-solid fa-paper-plane me-1"></i> New Message
                            </button>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            @foreach($messages as $msg)
                                <div class="p-3 border rounded-3 bg-light position-relative">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-primary-subtle text-primary fw-bold" style="font-size: 12px;">
                                                {{ $msg->subject ?: 'General Inquiry' }}
                                            </span>
                                            @if($msg->property)
                                                <small class="fw-bold text-dark"><i class="fa-solid fa-hotel me-1 text-primary"></i>{{ $msg->property->name }}</small>
                                            @endif
                                        </div>
                                        <small class="text-secondary">{{ $msg->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="text-dark mb-1" style="font-size: 14px; white-space: pre-line;">{{ $msg->message }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $messages->links() }}
                        </div>
                    </div>

                @else
                    {{-- ── 1:1 AGODA AUTHENTIC EMPTY INBOX STATE (Exact Match with Screenshot) ── --}}
                    <div class="messages-main-card">
                        <div class="text-center" style="max-width: 480px; margin: 0 auto;">
                            
                            {{-- Agoda Authentic Blue Speech Bubble with Overlapping Dots Bubble Vector Art --}}
                            <div class="mb-4 d-inline-block">
                                <svg viewBox="0 0 160 140" style="width: 140px; height: 120px; overflow: visible;">
                                    {{-- Main Royal Blue Chat Bubble --}}
                                    <g>
                                        <rect x="25" y="10" width="95" height="70" rx="10" fill="#2067e1" />
                                        {{-- Pointer Tail at Bottom-Left --}}
                                        <path d="M 40 80 L 32 98 L 56 80 Z" fill="#2067e1" />
                                        
                                        {{-- 3 White Chat Lines inside Blue Bubble --}}
                                        <rect x="40" y="26" width="65" height="5" rx="2.5" fill="#ffffff" />
                                        <rect x="40" y="38" width="65" height="5" rx="2.5" fill="#ffffff" />
                                        <rect x="40" y="50" width="42" height="5" rx="2.5" fill="#ffffff" />
                                    </g>

                                    {{-- Overlapping Light Blue Circular Chat Bubble (Bottom-Right) --}}
                                    <g transform="translate(68, 48)">
                                        {{-- White Outline / Gap Ring --}}
                                        <circle cx="32" cy="32" r="30" fill="#ffffff" />
                                        {{-- Light Blue Bubble Body --}}
                                        <circle cx="32" cy="32" r="26" fill="#dbeafe" />
                                        {{-- Small Tail --}}
                                        <path d="M 48 52 L 56 64 L 38 56 Z" fill="#dbeafe" />
                                        
                                        {{-- 3 Blue Typing Dots inside Light Blue Bubble --}}
                                        <circle cx="20" cy="32" r="3.5" fill="#2067e1" />
                                        <circle cx="32" cy="32" r="3.5" fill="#2067e1" />
                                        <circle cx="44" cy="32" r="3.5" fill="#2067e1" />
                                    </g>
                                </svg>
                            </div>

                            {{-- Exact Agoda Heading --}}
                            <h3 class="fw-bold mb-2" style="font-size: 20px; color: #2d2d2d; letter-spacing: -0.2px;">
                                You have no conversations
                            </h3>

                            {{-- Exact Agoda Subheading --}}
                            <p class="text-secondary mb-4" style="font-size: 14.5px; color: #737373 !important; line-height: 1.5;">
                                Start one by writing your first message below.
                            </p>

                            {{-- Exact Agoda Outlined Pill Button --}}
                            <div>
                                <a href="{{ route('booking.history') }}" class="btn text-secondary fw-semibold bg-white shadow-xs"
                                   style="border: 1px solid #cbd5e1; border-radius: 28px; padding: 9px 36px; font-size: 14.5px; color: #334155 !important; transition: all 0.15s ease;"
                                   onmouseover="this.style.borderColor='#94a3b8'; this.style.backgroundColor='#f8fafc'; this.style.color='#0f172a';"
                                   onmouseout="this.style.borderColor='#cbd5e1'; this.style.backgroundColor='#ffffff'; this.style.color='#334155';">
                                    Go to my bookings
                                </a>
                            </div>

                        </div>
                    </div>
                @endif

            </div>

        </div>
    </div>
</div>

{{-- New Message Modal --}}
<div class="modal fade" id="newMessageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('messages.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark">Send Message to Property Host</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold text-dark small">Subject / Inquiry Title</label>
                        <input type="text" name="subject" class="form-control rounded-3" placeholder="e.g. Early check-in request / Room inquiry" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold text-dark small">Message Details</label>
                        <textarea name="message" class="form-control rounded-3" rows="4" placeholder="Write your question or request to the host..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold rounded-pill px-4">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
