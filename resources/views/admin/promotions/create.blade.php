@extends('layouts.admin')

@section('title', 'Create Promotion — Admin')

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary btn-sm">
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
    <div class="mt-3">
        <button type="submit" class="btn-stockifly-primary">
            <i class="fa-solid fa-save me-1"></i> Create Promotion
        </button>
        <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
    </div>
</form>
@endsection
