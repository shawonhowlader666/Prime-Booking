@extends('layouts.admin')
@section('title', 'Edit Tour Package — Admin')

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i></a>
    <h1 style="font-size:19px;font-weight:700;margin:0;">Edit Tour Package: {{ $package->title }}</h1>
</div>

<form action="{{ route('admin.packages.update', $package) }}" method="POST">
    @csrf @method('PUT')
    @include('admin.packages._form', ['package' => $package])
    <div class="mt-3">
        <button type="submit" class="btn-stockifly-primary"><i class="fa-solid fa-save me-1"></i> Update Package</button>
        <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
    </div>
</form>
@endsection
