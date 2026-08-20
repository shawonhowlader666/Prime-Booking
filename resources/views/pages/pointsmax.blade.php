@extends('layouts.main', ['activePage' => 'pointsmax'])

@section('title', 'Manage PointsMAX Programs | Prime Booking')
@section('meta_description', 'Manage PointsMAX programs. Link your existing frequent flyer miles and loyalty programs to earn miles on every hotel booking.')

@section('content')
<style>
/* Agoda 1:1 PointsMAX Styling */
.pointsmax-page-wrapper {
    background-color: #f7f9fa;
    min-height: 88vh;
    padding: 36px 0 70px 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
.pointsmax-container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 16px;
}
/* 1:1 Link your miles program Card (+ icon) with Deep Bottom 3D Elevation */
.pointsmax-link-card {
    background: #ffffff;
    border: 1px solid #eef2f6;
    border-radius: 4px;
    width: 270px;
    height: 190px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.03);
    transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    text-decoration: none;
    transform: translateZ(0);
}
.pointsmax-link-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 35px -8px rgba(15, 23, 42, 0.22), 0 8px 16px -4px rgba(15, 23, 42, 0.12);
    border-color: #cbd5e1;
}

/* Active Linked Program Card */
.pointsmax-active-card {
    background: #ffffff;
    border: 1px solid #eef2f6;
    border-radius: 4px;
    width: 270px;
    height: 190px;
    padding: 22px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.03);
    transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    transform: translateZ(0);
}
.pointsmax-active-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 35px -8px rgba(15, 23, 42, 0.22), 0 8px 16px -4px rgba(15, 23, 42, 0.12);
    border-color: #cbd5e1;
}
</style>

<div class="pointsmax-page-wrapper">
    <div class="pointsmax-container">
        <div class="row g-4">
            
            {{-- Left User Account Sidebar Navigation (Exact Preservation) --}}
            <div class="col-lg-3 col-md-4" style="max-width: 270px;">
                <x-user-sidebar activePage="pointsmax" />
            </div>

            {{-- Right Column: 100% 1:1 Parity with Agoda PointsMAX Screenshot --}}
            <div class="col-lg-9 col-md-8">
                
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                {{-- Exact Agoda Heading --}}
                <h1 class="fw-bold text-dark mb-3" style="font-size: 26px; letter-spacing: -0.4px;">
                    Manage PointsMAX programs
                </h1>

                {{-- Exact Agoda Subtitle & Description --}}
                <p class="text-secondary mb-4" style="font-size: 14.5px; color: #475569 !important; line-height: 1.6; max-width: 820px;">
                    <strong class="text-dark">Manage PointsMAX programs</strong> PointsMAX lets you earn miles on your existing miles program with every booking! The programs linked to your account are listed below. Just click to add.
                </p>

                {{-- Exact Subheading --}}
                <h4 class="fw-bold text-dark mb-3" style="font-size: 18px; letter-spacing: -0.2px;">
                    Start earning miles today!
                </h4>

                {{-- Cards Container --}}
                <div class="d-flex flex-wrap align-items-center gap-4">
                    
                    {{-- Display Any Linked Programs --}}
                    @if(isset($linkedPrograms) && count($linkedPrograms) > 0)
                        @foreach($linkedPrograms as $prog)
                        <div class="pointsmax-active-card">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-primary-subtle text-primary fw-bold" style="font-size: 11px;">
                                        {{ ($prog['is_primary'] ?? false) ? 'Primary' : 'Linked' }}
                                    </span>
                                    <form action="{{ route('pointsmax.unlink', $prog['id'] ?? 0) }}" method="POST" onsubmit="return confirm('Unlink this loyalty program?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0 text-decoration-none" title="Remove" style="font-size: 14px;">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 text-truncate" style="font-size: 14.5px;" title="{{ $prog['program'] ?? '' }}">
                                    {{ $prog['program'] ?? 'Loyalty Program' }}
                                </h6>
                                <p class="text-muted small mb-0 font-monospace">
                                    ID: {{ $prog['membership_id'] ?? '' }}
                                </p>
                            </div>
                            <div class="text-end">
                                <small class="text-secondary" style="font-size: 11px;">Linked on {{ $prog['linked_at'] ?? 'Recent' }}</small>
                            </div>
                        </div>
                        @endforeach
                    @endif

                    {{-- Exact 1:1 Agoda Link Card: "+ Link your miles program" --}}
                    <div class="pointsmax-link-card" data-bs-toggle="modal" data-bs-target="#linkMilesModal">
                        <div class="text-primary mb-2" style="font-size: 32px; font-weight: 300; line-height: 1;">
                            +
                        </div>
                        <div class="text-primary fw-semibold" style="font-size: 14.5px;">
                            Link your miles program
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

{{-- ── 1:1 EXACT AGODA LINK MILES PROGRAM MODAL (Matches Screenshot 2 & 3) ── --}}
<div class="modal fade" id="linkMilesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 8px;">
            <form action="{{ route('pointsmax.link') }}" method="POST">
                @csrf
                
                {{-- Modal Header with Agoda 'x' close button --}}
                <div class="modal-header border-0 pb-0 pt-4 px-4 position-relative">
                    <h4 class="modal-title fw-bold text-dark mb-0" style="font-size: 20px; letter-spacing: -0.2px;">
                        Link your miles program
                    </h4>
                    <button type="button" class="btn-close shadow-none position-absolute" data-bs-dismiss="modal" aria-label="Close"
                            style="top: 20px; right: 20px; font-size: 12px;"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body px-4 pt-3 pb-4">
                    <p class="text-secondary mb-4" style="font-size: 13.5px; color: #475569 !important; line-height: 1.5;">
                        Select a miles program from the drop-down list and then enter your membership ID. Then click "Save" and you can start earning miles on your next booking.
                    </p>

                    <div class="row g-3 mb-4">
                        {{-- Program Dropdown Column --}}
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark" style="font-size: 13px;">Program:</label>
                            <select name="program" class="form-select form-select-sm py-2" required
                                    style="border-color: #cbd5e1; border-radius: 4px; font-size: 13.5px; color: #334155;">
                                <option value="" disabled selected>Please select</option>
                                <option value="Air China">Air China</option>
                                <option value="Citi Miles">Citi Miles</option>
                                <option value="d Point">d Point</option>
                                <option value="Korean Air SKYPASS">Korean Air SKYPASS</option>
                                <option value="Singapore Airlines KrisFlyer">Singapore Airlines KrisFlyer</option>
                                <option value="UOB Prvi Miles ID">UOB Prvi Miles ID</option>
                                <option value="UOB Prvi Miles MY">UOB Prvi Miles MY</option>
                                <option value="UOB Prvi Miles TH">UOB Prvi Miles TH</option>
                                <option value="Biman Bangladesh Biman Club">Biman Bangladesh Biman Club</option>
                                <option value="Emirates Skywards">Emirates Skywards</option>
                                <option value="Qatar Airways Privilege Club">Qatar Airways Privilege Club</option>
                            </select>
                        </div>

                        {{-- Membership ID Column --}}
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark" style="font-size: 13px;">Membership ID:</label>
                            <input type="text" name="membership_id" class="form-control form-control-sm py-2" placeholder="" required
                                   style="border-color: #cbd5e1; border-radius: 4px; font-size: 13.5px;">
                        </div>
                    </div>

                    {{-- Set as primary Checkbox --}}
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="is_primary" value="1" id="setAsPrimaryCheck" checked
                               style="width: 17px; height: 17px; accent-color: #2067e1; margin-top: 2px;">
                        <label class="form-check-label text-dark fw-medium ms-1" for="setAsPrimaryCheck" style="font-size: 13.5px;">
                            Set as primary PointsMAX program
                        </label>
                    </div>

                    {{-- Action Buttons: Cancel and Save (Exact Agoda Button Styling) --}}
                    <div class="d-flex align-items-center justify-content-end gap-2 pt-2">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2 fw-semibold" data-bs-dismiss="modal"
                                style="border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px; color: #334155; min-width: 95px;">
                            Cancel
                        </button>
                        <button type="submit" class="btn text-white px-4 py-2 fw-semibold shadow-xs"
                                style="background-color: #2067e1; border-radius: 4px; font-size: 14px; min-width: 95px; border: none;">
                            Save
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
