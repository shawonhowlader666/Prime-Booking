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
                        <div class="dropdown action-gear-dropdown d-inline-block">
                            <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                <i class="fa-solid fa-gear"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                <li>
                                    <a class="dropdown-item py-1.5 px-3" href="{{ route('admin.cms.edit', $page) }}">
                                        <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Page Content
                                    </a>
                                </li>
                            </ul>
                        </div>
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
