@extends('layouts.main', ['activePage' => 'blog'])

@section('title', 'Travel Stories, Guides & Hotel Insights | Prime Booking')

@section('content')
{{-- Header Banner --}}
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0369a1 100%); padding: 50px 0 40px; color: #fff; position: relative; overflow: hidden; border-bottom: 3px solid #0284c7;">
    <div style="position: absolute; top: -50px; right: 10%; width: 260px; height: 260px; background: rgba(56, 189, 248, 0.15); filter: blur(50px); border-radius: 50%; pointer-events: none;"></div>

    <div class="container" style="max-width: 1200px; position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 mb-2 fw-semibold" style="border-radius: 20px; font-size: 12px;">
                    <i class="fa-solid fa-compass me-1"></i> Prime Travel Journal
                </span>
                <h1 class="fw-bold mb-2 text-white" style="font-size: 32px; letter-spacing: -0.5px;">
                    Discover Bangladesh &amp; Beyond
                </h1>
                <p class="text-white-50 mb-4" style="font-size: 15px; max-width: 580px;">
                    Curated hotel recommendations, insider packing tips, resort reviews, and local culinary adventures brought to you by our travel experts.
                </p>

                {{-- Search Bar --}}
                <form action="{{ route('blog.index') }}" method="GET" class="d-flex align-items-center gap-2" style="max-width: 500px;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-0 ps-3" style="border-radius: 8px 0 0 8px;"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-0 py-2 shadow-sm" placeholder="Search destinations, guides or hotels..." value="{{ request('q') }}" style="border-radius: 0 8px 8px 0; font-size: 14px;">
                    </div>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm" style="border-radius: 8px; white-space: nowrap;">Search</button>
                </form>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-end">
                <div class="p-3 rounded-4" style="background: rgba(255,255,255,0.08); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.15); display: inline-block; text-align: left;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="font-size: 36px;">🗺️</div>
                        <div>
                            <div class="fw-bold text-white fs-6">Insider Guides</div>
                            <small class="text-white-50">Verified hotel tips &amp; itineraries</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5" style="max-width: 1200px;">
    {{-- Categories Filter Tabs --}}
    <div class="d-flex align-items-center gap-2 overflow-auto pb-3 mb-4" style="white-space: nowrap;">
        <a href="{{ route('blog.index') }}" class="btn btn-sm {{ !request('category') ? 'btn-primary' : 'btn-outline-secondary' }}" style="border-radius: 20px; font-weight: 600; padding: 6px 18px;">
            All Stories
        </a>
        @foreach($categories as $cat)
        <a href="{{ route('blog.index', ['category' => $cat]) }}" class="btn btn-sm {{ request('category') == $cat ? 'btn-primary' : 'btn-outline-secondary' }}" style="border-radius: 20px; font-weight: 600; padding: 6px 18px;">
            {{ $cat }}
        </a>
        @endforeach
    </div>

    {{-- Featured Post Banner (if on page 1 without search) --}}
    @if(!request('q') && !request('category') && isset($featuredPosts) && $featuredPosts->count() > 0)
    @php $heroPost = $featuredPosts->first(); @endphp
    <div class="card border-0 shadow-sm overflow-hidden mb-5" style="border-radius: 12px; background: #fff;">
        <div class="row g-0 align-items-center">
            <div class="col-lg-7">
                <a href="{{ route('blog.show', $heroPost->slug) }}" class="d-block overflow-hidden" style="height: 380px;">
                    <img src="{{ $heroPost->featured_image ?: 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1000&q=80' }}" alt="{{ $heroPost->title }}" class="w-100 h-100 object-fit-cover transition-all" style="transition: transform 0.4s ease;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                </a>
            </div>
            <div class="col-lg-5 p-4 p-md-5">
                <span class="badge bg-primary text-white px-3 py-1 mb-3" style="border-radius: 4px; font-size: 11px; text-transform: uppercase;">
                    Featured Spotlight
                </span>
                <h3 class="fw-bold mb-3" style="line-height: 1.3;">
                    <a href="{{ route('blog.show', $heroPost->slug) }}" class="text-dark text-decoration-none hover-primary">
                        {{ $heroPost->title }}
                    </a>
                </h3>
                <p class="text-muted mb-4" style="line-height: 1.6; font-size: 14.5px;">
                    {{ $heroPost->excerpt ?: strip_tags(\Illuminate\Support\Str::limit($heroPost->content, 140)) }}
                </p>
                <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width: 34px; height: 34px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 13px;">
                            {{ substr($heroPost->author_name, 0, 1) }}
                        </div>
                        <div>
                            <div class="fw-bold small text-dark">{{ $heroPost->author_name }}</div>
                            <small class="text-muted" style="font-size: 11px;">{{ $heroPost->created_at->format('M d, Y') }}</small>
                        </div>
                    </div>
                    <a href="{{ route('blog.show', $heroPost->slug) }}" class="btn btn-outline-primary btn-sm fw-semibold px-3" style="border-radius: 6px;">
                        Read Guide <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Main Articles Grid --}}
    <div class="row g-4">
        @forelse($posts as $post)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-radius: 10px; background: #fff; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.08)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';">
                <a href="{{ route('blog.show', $post->slug) }}" class="d-block position-relative overflow-hidden" style="height: 220px;">
                    <img src="{{ $post->featured_image ?: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80' }}" alt="{{ $post->title }}" class="w-100 h-100 object-fit-cover">
                    <span class="badge bg-dark bg-opacity-75 text-white position-absolute top-0 start-0 m-3 px-2 py-1" style="border-radius: 4px; font-size: 11px;">
                        {{ $post->category }}
                    </span>
                </a>
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center justify-content-between mb-2 text-muted" style="font-size: 12px;">
                        <span><i class="fa-regular fa-clock me-1"></i> {{ $post->created_at->format('d M Y') }}</span>
                        <span><i class="fa-regular fa-eye me-1"></i> {{ number_format($post->views_count) }} views</span>
                    </div>
                    <h5 class="fw-bold mb-2" style="font-size: 16px; line-height: 1.4;">
                        <a href="{{ route('blog.show', $post->slug) }}" class="text-dark text-decoration-none">
                            {{ $post->title }}
                        </a>
                    </h5>
                    <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.6;">
                        {{ $post->excerpt ?: strip_tags(\Illuminate\Support\Str::limit($post->content, 90)) }}
                    </p>
                    <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                        <small class="text-muted fw-semibold">{{ $post->author_name }}</small>
                        <a href="{{ route('blog.show', $post->slug) }}" class="text-primary fw-bold text-decoration-none small">
                            Read More <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div style="font-size: 40px;" class="mb-3">🧭</div>
            <h5 class="fw-bold text-dark">No travel articles found</h5>
            <p class="text-muted small">Try searching for other destinations or check back soon for fresh updates.</p>
            <a href="{{ route('blog.index') }}" class="btn btn-primary btn-sm px-3 mt-2" style="border-radius: 6px;">Clear Filters</a>
        </div>
        @endforelse
    </div>

    @if(method_exists($posts, 'hasPages') && $posts->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $posts->links() }}
    </div>
    @endif
</div>
@endsection
