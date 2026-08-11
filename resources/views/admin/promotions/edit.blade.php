@extends('layouts.admin')

@section('title', 'Edit Promotion — Admin')

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div>
        <h1 style="font-size:19px;font-weight:700;margin:0;">Edit Promotion</h1>
        <p style="font-size:12px;color:#8c8c8c;margin:0;">Update: <strong>{{ $promotion->title }}</strong></p>
    </div>
</div>

@if(session('success'))
<div class="admin-alert success">{{ session('success') }}</div>
@endif

<form action="{{ route('admin.promotions.update', $promotion) }}" method="POST">
    @csrf @method('PUT')
    @include('admin.promotions._form', ['promotion' => $promotion])
    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn-stockifly-primary">
            <i class="fa-solid fa-save me-1"></i> Save Changes
        </button>
        <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <form action="{{ route('admin.promotions.destroy', $promotion) }}" method="POST" class="ms-auto"
              onsubmit="return confirm('Delete this promotion permanently?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="fa-solid fa-trash me-1"></i> Delete
            </button>
        </form>
    </div>
</form>
@endsection
