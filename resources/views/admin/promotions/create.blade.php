@extends('layouts.admin')
@section('title', 'Create Promotion Banner | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.promotions.index') }}">Marketing</a>
        <span class="sep">-</span><strong style="color:#333;">Create Promotion</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:8px;">
        <h1 class="page-title m-0">Create New Promotion Banner</h1>
        <a href="{{ route('admin.promotions.index') }}" class="btn-table-action" style="font-size:13px; height:36px; padding:0 16px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;">
            <i class="fa-solid fa-arrow-left"></i> <span>Back to Promotions</span>
        </a>
    </div>
</div>

{{-- PAGE CONTENT AREA --}}
<div class="page-content-area">
    <form action="{{ route('admin.promotions.store') }}" method="POST">
        @csrf
        @include('admin.promotions._form', ['promotion' => null])
        
        <div class="d-flex align-items-center justify-content-end gap-2.5 pt-4 border-top mt-4" style="border-color:#e2e8f0 !important;">
            <a href="{{ route('admin.promotions.index') }}" class="btn btn-light text-secondary border fw-bold d-inline-flex align-items-center gap-2" style="border-radius:4px; font-size:13px; height:38px; padding:0 20px;">
                <span>Cancel</span>
            </a>
            <button type="submit" class="btn btn-primary text-white fw-bold d-inline-flex align-items-center gap-2" style="background-color:var(--primary); border-radius:4px; font-size:13px; height:38px; padding:0 24px; border:none;">
                <i class="fa-solid fa-rocket"></i> <span>Publish Promotion Live</span>
            </button>
        </div>
    </form>
</div>
@endsection
