@extends('layouts.admin')
@section('title', 'Blog Articles & Stories | PRIME BOOKING Admin')

@section('content')
<div class="page-header-card mb-4" style="background:#fff; border:1px solid #e8e8e8; border-radius:4px; padding:20px;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fa-solid fa-newspaper text-primary me-2"></i> Travel Articles &amp; Blog CMS</h4>
            <p class="text-muted mb-0 font-size-base">Create and manage travel inspiration guides, tips, and destination highlights for guests.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('blog.index') }}" target="_blank" class="btn btn-outline-secondary" style="border-radius:4px;">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Live Blog
            </a>
            <a href="{{ route('admin.blog.create') }}" class="btn btn-primary" style="border-radius:4px;">
                <i class="fa-solid fa-plus me-1"></i> New Article
            </a>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4" style="background:#fff; border:1px solid #e8e8e8; border-radius:4px;">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.blog.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius:4px 0 0 4px;"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by title, excerpt or category..." value="{{ request('search') }}" style="border-radius:0 4px 4px 0;">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()" style="border-radius:4px;">
                    <option value="">All Statuses</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="category" class="form-select" onchange="this.form.submit()" style="border-radius:4px;">
                    <option value="">All Categories</option>
                    <option value="Travel Guide" {{ request('category') == 'Travel Guide' ? 'selected' : '' }}>Travel Guide</option>
                    <option value="Resort Guides" {{ request('category') == 'Resort Guides' ? 'selected' : '' }}>Resort Guides</option>
                    <option value="Tips & Deals" {{ request('category') == 'Tips & Deals' ? 'selected' : '' }}>Tips & Deals</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100" style="border-radius:4px;">Filter</button>
                @if(request()->anyFilled(['search', 'status', 'category']))
                    <a href="{{ route('admin.blog.index') }}" class="btn btn-outline-secondary" style="border-radius:4px;">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Articles Table --}}
<div class="card" style="background:#fff; border:1px solid #e8e8e8; border-radius:4px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:13px;">
            <thead style="background:#f8fafc; border-bottom:1px solid #e2e8f0; font-size:11.5px; text-transform:uppercase; color:#64748b;">
                <tr>
                    <th class="ps-3 py-3" style="width:70px;">Cover</th>
                    <th>Article Details</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th class="text-center">Views</th>
                    <th class="text-center">Featured</th>
                    <th class="text-center">Status</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                <tr>
                    <td class="ps-3">
                        <img src="{{ $post->featured_image ?: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=200&q=80' }}" alt="cover" style="width:52px; height:40px; object-fit:cover; border-radius:4px; border:1px solid #e2e8f0;">
                    </td>
                    <td>
                        <div class="fw-bold text-dark mb-1">{{ $post->title }}</div>
                        <div class="text-muted small text-truncate" style="max-width:320px;">
                            {{ $post->excerpt ?: strip_tags(\Illuminate\Support\Str::limit($post->content, 80)) }}
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border px-2 py-1" style="border-radius:4px;">{{ $post->category }}</span>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $post->author_name }}</div>
                        <small class="text-muted">{{ $post->created_at->format('d M Y') }}</small>
                    </td>
                    <td class="text-center font-monospace fw-semibold text-muted">
                        {{ number_format($post->views_count) }}
                    </td>
                    <td class="text-center">
                        <form action="{{ route('admin.blog.feature', $post->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm border-0 bg-transparent p-0" title="Toggle Featured">
                                @if($post->is_featured)
                                    <i class="fa-solid fa-star text-warning fs-5"></i>
                                @else
                                    <i class="fa-regular fa-star text-muted fs-5"></i>
                                @endif
                            </button>
                        </form>
                    </td>
                    <td class="text-center">
                        <form action="{{ route('admin.blog.publish', $post->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm border-0 bg-transparent p-0" title="Toggle Publish">
                                @if($post->is_published)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="border-radius:4px;"><i class="fa-solid fa-check me-1"></i> Live</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1" style="border-radius:4px;"><i class="fa-solid fa-eye-slash me-1"></i> Draft</span>
                                @endif
                            </button>
                        </form>
                    </td>
                    <td class="text-end pe-3">
                        <div class="d-flex align-items-center justify-content-end gap-1">
                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="btn btn-sm btn-light border py-1 px-2" style="border-radius:4px;" title="View Live">
                                <i class="fa-solid fa-arrow-up-right-from-square text-muted"></i>
                            </a>
                            <a href="{{ route('admin.blog.edit', $post->id) }}" class="btn btn-sm btn-primary py-1 px-2" style="border-radius:4px;" title="Edit Article">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('admin.blog.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this article?');" class="d-inline m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" style="border-radius:4px;" title="Delete">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">No blog articles found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($posts->hasPages())
    <div class="card-footer bg-white border-top p-3 d-flex justify-content-end">
        {{ $posts->links() }}
    </div>
    @endif
</div>
@endsection
