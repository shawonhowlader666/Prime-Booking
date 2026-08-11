@extends('layouts.vendor')
@section('title', 'Edit Tour Package — Vendor')

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('vendor.packages.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i></a>
    <h1 style="font-size:19px;font-weight:700;margin:0;">Edit Tour Package: {{ $package->title }}</h1>
</div>

@if($errors->any())
<div class="alert alert-danger p-2 small mb-3">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="card p-3 border-0 shadow-sm rounded-3">
    <form action="{{ route('vendor.packages.update', $package->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label small fw-bold">Package Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control form-control-sm" value="{{ old('title', $package->title) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Duration / Days <span class="text-danger">*</span></label>
                <input type="text" name="days" class="form-control form-control-sm" value="{{ old('days', $package->days) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Price per Person (৳) <span class="text-danger">*</span></label>
                <input type="number" name="price" class="form-control form-control-sm" value="{{ old('price', $package->price) }}" step="0.01" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Badge Text</label>
                <input type="text" name="badge" class="form-control form-control-sm" value="{{ old('badge', $package->badge) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Image URL</label>
                <input type="url" name="image_url" class="form-control form-control-sm" value="{{ old('image_url', $package->image_url) }}">
            </div>
            <div class="col-12">
                <label class="form-label small fw-bold">Inclusions (One per line)</label>
                <textarea name="includes" class="form-control form-control-sm" rows="3">{{ old('includes', is_array($package->includes) ? implode("\n", $package->includes) : '') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label small fw-bold">Description</label>
                <textarea name="description" class="form-control form-control-sm" rows="3">{{ old('description', $package->description) }}</textarea>
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-save me-1"></i> Update Package</button>
            <a href="{{ route('vendor.packages.index') }}" class="btn btn-outline-secondary btn-sm ms-2">Cancel</a>
        </div>
    </form>
</div>
@endsection
