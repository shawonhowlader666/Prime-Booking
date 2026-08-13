@extends('layouts.admin')

@section('title', 'Create Promotion — Admin')

@section('content')
<div class="page-content-area" style="padding: 24px;">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:4px;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 style="font-size:19px;font-weight:700;margin:0;">Create New Promotion</h1>
            <p style="font-size:12px;color:#8c8c8c;margin:0;">Add a homepage banner or promotional card</p>
        </div>
    </div>

    <form action="{{ route('admin.promotions.store') }}" method="POST">
        @csrf
        @include('admin.promotions._form', ['promotion' => null])
        <div class="mt-4">
            <button type="submit" class="btn-stockifly-primary" style="border-radius:4px; height:38px; padding:0 20px;">
                <i class="fa-solid fa-save me-1"></i> Create Promotion
            </button>
            <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary ms-2" style="border-radius:4px; height:38px; padding:0 20px; display:inline-flex; align-items:center;">Cancel</a>
        </div>
    </form>
</div>
@endsection
