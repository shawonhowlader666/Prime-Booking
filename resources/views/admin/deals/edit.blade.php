@extends('layouts.admin')
@section('title', 'Edit Deal — Admin')

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('admin.deals.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i></a>
    <h1 style="font-size:19px;font-weight:700;margin:0;">Edit Deal: {{ $deal->title }}</h1>
</div>

<form action="{{ route('admin.deals.update', $deal) }}" method="POST">
    @csrf @method('PUT')
    @include('admin.deals._form', ['deal' => $deal])
    <div class="mt-3">
        <button type="submit" class="btn-stockifly-primary"><i class="fa-solid fa-save me-1"></i> Update Deal</button>
        <a href="{{ route('admin.deals.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
    </div>
</form>
@endsection
