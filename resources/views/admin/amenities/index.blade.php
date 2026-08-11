@extends('layouts.admin')
@section('title', 'Amenities Manager — Admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h1 style="font-size:19px;font-weight:700;margin:0;">Amenities Manager</h1>
        <p style="font-size:12px;color:#8c8c8c;margin:0;">Manage features & amenities available for properties</p>
    </div>
</div>

@if(session('success'))
<div class="admin-alert success">{{ session('success') }}</div>
@endif

<div class="row g-3">
    <div class="col-lg-4">
        <div class="stockifly-card">
            <div class="card-header-stockifly mb-2"><i class="fa-solid fa-plus me-1"></i> Add Amenity</div>
            <form action="{{ route('admin.amenities.store') }}" method="POST">
                @csrf
                <div class="mb-2">
                    <label class="form-label-sm">Amenity Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Free Wi-Fi" required>
                </div>
                <div class="mb-2">
                    <label class="form-label-sm">FontAwesome Icon Class</label>
                    <input type="text" name="icon" class="form-control form-control-sm" placeholder="fa-wifi or fa-swimming-pool">
                    <small style="font-size:10px;color:#8c8c8c;">e.g. fa-wifi, fa-utensils, fa-snowflake</small>
                </div>
                <div class="mb-3">
                    <label class="form-label-sm">Category</label>
                    <select name="category" class="form-select form-select-sm">
                        <option value="general">General</option>
                        <option value="recreation">Recreation & Wellness</option>
                        <option value="dining">Dining & Food</option>
                        <option value="services">Services & Convenience</option>
                    </select>
                </div>
                <button type="submit" class="btn-stockifly-primary w-100"><i class="fa-solid fa-plus me-1"></i> Add Amenity</button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="stockifly-card p-0">
            <div class="table-responsive">
                <table class="table table-stockifly mb-0">
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($amenities as $am)
                        <tr>
                            <td><i class="fa-solid {{ $am->icon ?: 'fa-check' }} text-primary fs-6"></i></td>
                            <td><strong>{{ $am->name }}</strong></td>
                            <td><span class="badge bg-light text-dark border">{{ ucfirst($am->category) }}</span></td>
                            <td>
                                <form action="{{ route('admin.amenities.destroy', $am) }}" method="POST" onsubmit="return confirm('Delete amenity?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="padding:2px 8px;font-size:11px;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">No amenities added yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
