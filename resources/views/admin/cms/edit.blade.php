@extends('layouts.admin')
@section('title', 'Edit CMS Page — Admin')

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('admin.cms.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i></a>
    <h1 style="font-size:19px;font-weight:700;margin:0;">Edit CMS Page: {{ $page->title }}</h1>
</div>

@if($errors->any())
<div class="admin-alert error mb-3">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('admin.cms.update', $page) }}" method="POST">
    @csrf @method('PUT')
    <div class="stockifly-card mb-3">
        <div class="card-header-stockifly"><i class="fa-solid fa-file-lines me-1"></i> Content Details</div>
        <div class="mt-2">
            <label class="form-label-sm">Page Title <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control form-control-sm" value="{{ old('title', $page->title) }}" required>
        </div>
        <div class="mt-3">
            <label class="form-label-sm">Page Content</label>
            <textarea name="content" class="form-control form-control-sm" rows="12">{{ old('content', $page->content) }}</textarea>
        </div>
    </div>
    <div class="mt-3">
        <button type="submit" class="btn-stockifly-primary"><i class="fa-solid fa-save me-1"></i> Save Changes</button>
        <a href="{{ route('admin.cms.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
    </div>
</form>
@endsection
