@extends('layouts.admin')
@section('title', 'Add Special Deal — Admin')

@section('content')
<div class="page-content-area" style="padding: 24px;">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.deals.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:4px;"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 style="font-size:19px; font-weight:700; margin:0; color:#0f172a;">Add New Special Deal</h1>
    </div>

    <form action="{{ route('admin.deals.store') }}" method="POST">
        @csrf
        @include('admin.deals._form', ['deal' => null])
        <div class="mt-4">
            <button type="submit" class="btn-stockifly-primary" style="border-radius:4px; height:38px; padding:0 20px;">
                <i class="fa-solid fa-save me-1.5"></i> Create Deal
            </button>
            <a href="{{ route('admin.deals.index') }}" class="btn btn-outline-secondary ms-2" style="border-radius:4px; height:38px; padding:0 20px; display:inline-flex; align-items:center;">Cancel</a>
        </div>
    </form>
</div>
@endsection
