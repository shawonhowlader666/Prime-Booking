@extends('layouts.admin')
@section('title', 'CMS Content — Admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h1 style="font-size:19px;font-weight:700;margin:0;">Website Pages CMS</h1>
        <p style="font-size:12px;color:#8c8c8c;margin:0;">Manage text and content for static website pages</p>
    </div>
</div>

@if(session('success'))
<div class="admin-alert success">{{ session('success') }}</div>
@endif

<div class="stockifly-card p-0">
    <div class="table-responsive">
        <table class="table table-stockifly mb-0">
            <thead>
                <tr>
                    <th>Page Key</th>
                    <th>Page Title</th>
                    <th>Group</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                <tr>
                    <td><code>{{ $page->key }}</code></td>
                    <td><strong>{{ $page->title }}</strong></td>
                    <td><span class="badge bg-light text-dark border">{{ ucfirst($page->group) }}</span></td>
                    <td><small>{{ $page->updated_at->format('d M Y, H:i') }}</small></td>
                    <td>
                        <a href="{{ route('admin.cms.edit', $page) }}" class="btn btn-sm btn-outline-primary" style="padding:2px 8px;font-size:11px;">
                            <i class="fa-solid fa-pen me-1"></i> Edit Content
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">No CMS pages found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
