@extends('layouts.admin')
@section('title', 'Write New Blog Article | PRIME BOOKING Admin')

@section('content')
<div class="page-header-card mb-4" style="background:#fff; border:1px solid #e8e8e8; border-radius:4px; padding:20px;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.blog.index') }}" class="btn btn-sm btn-outline-secondary py-1 px-2" style="border-radius:4px;"><i class="fa-solid fa-arrow-left"></i></a>
                <h4 class="mb-0 fw-bold text-dark">Write New Blog Article</h4>
            </div>
            <p class="text-muted mb-0 font-size-base">Draft and publish travel guides, hotel highlights, or destination tips.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.blog.index') }}" class="btn btn-outline-secondary" style="border-radius:4px;">Cancel</a>
            <button type="submit" form="blogCreateForm" class="btn btn-primary" style="border-radius:4px;">
                <i class="fa-solid fa-cloud-arrow-up me-1"></i> Save &amp; Publish
            </button>
        </div>
    </div>
</div>

<form id="blogCreateForm" method="POST" action="{{ route('admin.blog.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4" style="background:#fff; border:1px solid #e8e8e8; border-radius:4px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold text-dark">Article Content</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Article Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="e.g. 7 Hidden Gem Hotels in Sylhet for Weekend Escapes" required style="border-radius:4px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Short Excerpt / Summary</label>
                        <textarea name="excerpt" rows="3" class="form-control" placeholder="Brief 1-2 sentence teaser shown on cards and social shares..." style="border-radius:4px;">{{ old('excerpt') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Body HTML Content <span class="text-danger">*</span></label>
                        <textarea name="content" rows="16" class="form-control font-monospace" placeholder="<p>Write or paste your article content here...</p>" required style="border-radius:4px; font-size:13px;">{{ old('content') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- SEO Box --}}
            <div class="card" style="background:#fff; border:1px solid #e8e8e8; border-radius:4px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold text-dark"><i class="fa-solid fa-magnifying-glass me-2 text-primary"></i> Search Engine Optimization (SEO)</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title') }}" placeholder="Leave blank to use article title" style="border-radius:4px;">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small text-muted">Meta Description</label>
                        <textarea name="meta_description" rows="2" class="form-control" placeholder="Search engine snippet summary..." style="border-radius:4px;">{{ old('meta_description') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card mb-4" style="background:#fff; border:1px solid #e8e8e8; border-radius:4px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold text-dark">Publish Settings</h6>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required style="border-radius:4px;">
                            <option value="Travel Guide">Travel Guide</option>
                            <option value="Resort Guides">Resort Guides</option>
                            <option value="Tips & Deals">Tips & Deals</option>
                            <option value="Destination Spotlight">Destination Spotlight</option>
                            <option value="Food & Culture">Food & Culture</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Author Display Name</label>
                        <input type="text" name="author_name" class="form-control" value="{{ old('author_name', auth()->user()->name ?? 'Prime Editorial') }}" style="border-radius:4px;">
                    </div>

                    <div class="form-check form-switch mb-3 ps-0 d-flex align-items-center justify-content-between">
                        <label class="form-check-label fw-bold text-dark mb-0" for="is_published">Publish Immediately</label>
                        <input class="form-check-input ms-auto" type="checkbox" role="switch" id="is_published" name="is_published" value="1" checked style="cursor:pointer; width:36px; height:18px;">
                    </div>

                    <div class="form-check form-switch ps-0 d-flex align-items-center justify-content-between">
                        <label class="form-check-label fw-bold text-dark mb-0" for="is_featured">Feature on Blog Hero</label>
                        <input class="form-check-input ms-auto" type="checkbox" role="switch" id="is_featured" name="is_featured" value="1" style="cursor:pointer; width:36px; height:18px;">
                    </div>
                </div>
            </div>

            {{-- Featured Image Box --}}
            <div class="card" style="background:#fff; border:1px solid #e8e8e8; border-radius:4px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold text-dark">Cover Image</h6>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Image URL</label>
                        <input type="url" name="featured_image" id="featuredImageUrl" class="form-control" placeholder="https://..." value="{{ old('featured_image') }}" style="border-radius:4px;">
                    </div>
                    <div class="text-center my-2 text-muted small">— OR UPLOAD FILE —</div>
                    <div class="mb-3">
                        <input type="file" name="image_file" class="form-control" accept="image/*" style="border-radius:4px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
