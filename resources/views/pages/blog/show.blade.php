@extends('layouts.main', ['activePage' => 'blog'])

@section('title', ($post->meta_title ?: $post->title) . ' | Prime Booking')

@section('content')
<div class="container py-4" style="max-width: 1000px;">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('blog.index') }}" class="text-decoration-none">Travel Blog</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ \Illuminate\Support\Str::limit($post->title, 40) }}</li>
        </ol>
    </nav>

    {{-- Article Header --}}
    <div class="mb-4">
        <span class="badge bg-primary text-white px-3 py-1 mb-2 fw-semibold" style="border-radius: 4px; font-size: 12px;">
            {{ $post->category }}
        </span>
        <h1 class="fw-bold mb-3 text-dark" style="font-size: 32px; line-height: 1.3;">
            {{ $post->title }}
        </h1>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-3 border-bottom text-muted" style="font-size: 13.5px;">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 13px;">
                        {{ substr($post->author_name, 0, 1) }}
                    </div>
                    <span class="fw-semibold text-dark">{{ $post->author_name }}</span>
                </div>
                <span>•</span>
                <span><i class="fa-regular fa-calendar me-1"></i> {{ $post->created_at->format('M d, Y') }}</span>
                <span>•</span>
                <span><i class="fa-regular fa-eye me-1"></i> {{ number_format($post->views_count) }} views</span>
            </div>
            
            {{-- Social Share --}}
            <div class="d-flex align-items-center gap-2">
                <span class="small fw-semibold text-muted me-1">Share:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="btn btn-sm btn-light border py-1 px-2" style="border-radius: 4px;" title="Share on Facebook">
                    <i class="fa-brands fa-facebook text-primary"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" target="_blank" class="btn btn-sm btn-light border py-1 px-2" style="border-radius: 4px;" title="Share on X">
                    <i class="fa-brands fa-x-twitter text-dark"></i>
                </a>
                <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' ' . url()->current()) }}" target="_blank" class="btn btn-sm btn-light border py-1 px-2" style="border-radius: 4px;" title="Share on WhatsApp">
                    <i class="fa-brands fa-whatsapp text-success"></i>
                </a>
                <button type="button" class="btn btn-sm btn-light border py-1 px-2" style="border-radius: 4px;" onclick="navigator.clipboard.writeText(window.location.href); alert('Link copied to clipboard!');" title="Copy Link">
                    <i class="fa-solid fa-link text-muted"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Featured Hero Image --}}
    @if($post->featured_image)
    <div class="mb-4 rounded-4 overflow-hidden shadow-sm" style="max-height: 480px;">
        <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-100 h-100 object-fit-cover" style="max-height: 480px;">
    </div>
    @endif

    {{-- Article Body --}}
    <div class="card border-0 shadow-sm p-4 p-md-5 mb-5" style="border-radius: 12px; background: #fff;">
        @if($post->excerpt)
        <div class="lead fw-medium text-dark mb-4 pb-3 border-bottom fst-italic" style="color: #334155; font-size: 17px; line-height: 1.6;">
            "{{ $post->excerpt }}"
        </div>
        @endif

        <div class="blog-article-content" style="font-size: 16px; line-height: 1.8; color: #1e293b;">
            {!! $post->content !!}
        </div>

        {{-- Author Tag Box --}}
        <div class="mt-5 p-4 rounded-3 border d-flex align-items-center gap-3" style="background: #f8fafc;">
            <div style="width: 50px; height: 50px; border-radius: 50%; background: #0284c7; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 20px; flex-shrink: 0;">
                {{ substr($post->author_name, 0, 1) }}
            </div>
            <div>
                <div class="fw-bold text-dark fs-6">{{ $post->author_name }}</div>
                <p class="text-muted mb-0 small">Specialist travel contributor for Prime Booking. Dedicated to revealing the best stays, experiences, and local secrets across Bangladesh.</p>
            </div>
        </div>
    </div>

    {{-- Related Stories --}}
    @if(isset($relatedPosts) && $relatedPosts->count() > 0)
    <div class="mb-5">
        <h4 class="fw-bold text-dark mb-3">More in {{ $post->category }}</h4>
        <div class="row g-3">
            @foreach($relatedPosts as $rel)
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-radius: 8px; background: #fff;">
                    <a href="{{ route('blog.show', $rel->slug) }}" class="d-block" style="height: 150px; overflow: hidden;">
                        <img src="{{ $rel->featured_image ?: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=400&q=80' }}" alt="{{ $rel->title }}" class="w-100 h-100 object-fit-cover">
                    </a>
                    <div class="card-body p-3">
                        <small class="text-muted d-block mb-1">{{ $rel->created_at->format('d M Y') }}</small>
                        <h6 class="fw-bold mb-2">
                            <a href="{{ route('blog.show', $rel->slug) }}" class="text-dark text-decoration-none">
                                {{ \Illuminate\Support\Str::limit($rel->title, 55) }}
                            </a>
                        </h6>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
