@extends('layouts.admin')
@section('title', 'Add New CMS Page — Admin')

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('admin.cms.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i></a>
    <h1 style="font-size:19px;font-weight:700;margin:0;">Create New CMS Page</h1>
</div>

@if($errors->any())
<div class="admin-alert error mb-3">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('admin.cms.store') }}" method="POST">
    @csrf
    <div class="stockifly-card mb-3">
        <div class="card-header-stockifly"><i class="fa-solid fa-file-lines me-1"></i> Page Information</div>
        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <label class="form-label-sm">Page Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control form-control-sm" value="{{ old('title') }}" placeholder="e.g. Refund Policy" required>
            </div>
            <div class="col-md-6">
                <label class="form-label-sm">System Key / Slug <span class="text-danger">*</span></label>
                <input type="text" name="key" class="form-control form-control-sm" value="{{ old('key') }}" placeholder="e.g. refund_policy" required>
            </div>
            <div class="col-md-12">
                <label class="form-label-sm">Group / Category <span class="text-danger">*</span></label>
                <select name="group" class="form-select form-select-sm" required>
                    <option value="general">General</option>
                    <option value="legal">Legal & Policy</option>
                    <option value="partner">Partner / Vendor</option>
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label-sm">Page HTML Content</label>
                <textarea name="content" class="form-control form-control-sm" rows="10" placeholder="Write page content here...">{{ old('content') }}</textarea>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <button type="submit" class="btn-stockifly-primary"><i class="fa-solid fa-plus me-1"></i> Create Page</button>
        <a href="{{ route('admin.cms.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
    </div>
</form>
@endsection
