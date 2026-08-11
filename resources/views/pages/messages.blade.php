@extends('layouts.main', ['activePage' => 'messages'])

@section('title', 'Property Messages & Inquiries | PRIME BOOKING')

@section('content')
<div class="py-4" style="background-color: #f4f6fa; min-height: 85vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        <div class="row g-4">
            
            <!-- Left White Sidebar Navigation -->
            <div class="col-lg-3 col-md-4" style="max-width: 260px;">
                <x-user-sidebar activePage="messages" />
            </div>

            <!-- Right Column: Messages Inbox -->
            <div class="col-lg-9 col-md-8">
                <div class="card border shadow-xs p-4" style="border-color: #cbd5e1 !important; border-radius: 18px !important; background-color: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 18px;">{{ __('Property Messages & Host Inquiries') }}</h5>
                        <button class="btn btn-primary btn-sm rounded-pill fw-bold px-3" data-bs-toggle="modal" data-bs-target="#newMessageModal">
                            <i class="fa-solid fa-paper-plane me-1"></i> New Inquiry
                        </button>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(isset($messages) && count($messages) > 0)
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
                        <div class="mt-3">
                            {{ $messages->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; color: #64748b; font-size: 28px;">
                                <i class="fa-regular fa-comments"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 16px;">No conversations yet</h6>
                            <p class="text-secondary small mb-4" style="max-width: 420px; margin: 0 auto;">When you send special requests or chat with hotel hosts, your message history will appear here.</p>
                            <button class="btn text-white fw-bold rounded-pill px-4 py-2" style="background-color: #2067e1; font-size: 14px;" data-bs-toggle="modal" data-bs-target="#newMessageModal">
                                {{ __('Send Message to Host') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

{{-- New Message Modal --}}
<div class="modal fade" id="newMessageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('messages.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Send Message to Property Host</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">Subject / Inquiry Title</label>
                        <input type="text" name="subject" class="form-control" placeholder="e.g. Early check-in request / Room inquiry" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">Message Details</label>
                        <textarea name="message" class="form-control" rows="4" placeholder="Write your question or request to the host..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

