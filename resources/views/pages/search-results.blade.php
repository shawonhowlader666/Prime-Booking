@extends('layouts.main', ['activePage' => 'hotels'])

@php
    $dest       = trim((string) ($destination ?? request('destination', '')));
    $destLabel  = $dest !== '' ? $dest : 'Bangladesh';
    $merged     = $searchResults['merged_results'] ?? [];
    $paginator  = $searchResults['paginator'] ?? null;
    $totalCount = (int) ($searchResults['total_count'] ?? (isset($paginator) ? $paginator->total() : count($merged)));

    // Values the filter sidebar reads.
    $prices = [];
    foreach ($merged as $mItem) {
        $p = (float) (is_object($mItem) ? ($mItem->price_per_night ?? 0) : ($mItem['price_per_night'] ?? 0));
        if ($p > 0) { $prices[] = $p; }
    }
    $minFoundPrice = (float) ($priceRange['min'] ?? ($prices ? min($prices) : 0));
    $maxFoundPrice = (float) ($priceRange['max'] ?? ($prices ? max($prices) : 0));

    $checkinCarbon  = \Carbon\Carbon::parse($checkIn ?: now()->addDay());
    $checkoutCarbon = \Carbon\Carbon::parse($checkOut ?: now()->addDays(2));
    $sortBy = $sortBy ?? request('sort_by', 'featured');
    $sortOptions = [
        'featured'   => 'Best match',
        'price_low'  => 'Lowest price first',
        'price_high' => 'Highest price first',
        'rating'     => 'Top reviewed',
        'newest'     => 'Newest listings',
    ];
    if (request()->filled('lat') && request()->filled('lng')) {
        $sortOptions['distance'] = 'Distance';
    }

    $metaTitle = "Hotels in {$destLabel}: {$totalCount} " . ($totalCount === 1 ? 'property' : 'properties') . ' | Prime Booking';
    $metaDesc  = "Compare and book hotels, resorts and homes in {$destLabel} on Prime Booking. Real prices in BDT, free cancellation on many rooms.";
    $userWishlists = auth()->check()
        ? \App\Models\Wishlist::where('user_id', auth()->id())->pluck('property_id')->all()
        : [];
@endphp

@section('title', $metaTitle)
@section('meta_description', $metaDesc)
@section('canonical_url', route('search.index', array_filter(['destination' => $dest ?: null])))
@section('extra_meta')
    @if (request()->hasAny(['star_rating', 'amenities', 'min_price', 'max_price', 'sort_by', 'page', 'guest_rating', 'property_type', 'area']))
        <meta name="robots" content="noindex, follow">
    @endif
@endsection

@section('content')
<style>
    .sr-wrap { background: #f5f7fa; padding: 16px 0 48px; }
    .sr-searchbar { background: #1f2d5a; padding: 14px 0 18px; }
    .sr-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 12px; }
    .sr-head h1 { font-size: 20px; font-weight: 800; color: #1e293b; margin: 0; }
    .sr-sort { display: flex; align-items: center; gap: 8px; font-size: 14px; }
    .sr-sort select { border: 1px solid #cbd5e1; border-radius: 8px; padding: 7px 10px; font-weight: 600; background: #fff; }
    .sr-list { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 12px; }
    .sr-list > li.PropertyCardItem { background: #fff; border: 1px solid #dde3ea; border-radius: 10px; overflow: hidden; transition: box-shadow .15s ease; cursor: pointer; }
    .sr-list > li.PropertyCardItem:hover { box-shadow: 0 6px 18px rgba(15, 23, 42, .10); }
    .sr-list > li.PropertyCardItem [data-element-name="property-card-content"],
    .sr-list > li.PropertyCardItem .PropertyCard__Section--propertyInfo { padding: 12px 14px; }
    .sr-list > li.PropertyCardItem .PropertyCard__Section--pricingInfo { border-left: 1px solid #eef2f6; }
    .sr-empty { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 40px 20px; text-align: center; }
    .sr-pages { display: flex; justify-content: center; gap: 6px; margin-top: 20px; flex-wrap: wrap; }
    .sr-pages a, .sr-pages span { min-width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; color: #1e293b; font-weight: 600; text-decoration: none; padding: 0 10px; }
    .sr-pages .active { background: #2067e1; border-color: #2067e1; color: #fff; }
    .sr-pages .disabled { opacity: .45; }
    @media (max-width: 991px) { #searchPageLeftColumn { display: none; } }
</style>

<div class="sr-searchbar">
    <div class="container">
        @include('components.search.search-bar')
    </div>
</div>

<div class="sr-wrap">
    <div class="container">
        <div class="row g-3">
            <aside class="col-lg-3" id="searchPageLeftColumn" aria-label="Search filters">
                @include('components.search.filter-sidebar')
            </aside>

            <main class="col-lg-9" id="searchPageRightColumn">
                <div class="sr-head">
                    <h1 data-element-name="properties-available-text">
                        {{ $destLabel }}: {{ number_format($totalCount) }} {{ $totalCount === 1 ? 'property' : 'properties' }}
                    </h1>
                    <form method="GET" action="{{ route('search.index') }}" class="sr-sort" id="sortForm">
                        @foreach (request()->except(['sort_by', 'page']) as $k => $v)
                            @if (is_array($v))
                                @foreach ($v as $vv)
                                    <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endif
                        @endforeach
                        <label for="sortBySelect" class="text-muted">Sort by</label>
                        <select id="sortBySelect" name="sort_by" onchange="this.form.submit()">
                            @foreach ($sortOptions as $value => $label)
                                <option value="{{ $value }}" @selected($sortBy === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <noscript><button type="submit" class="btn btn-sm btn-primary">Apply</button></noscript>
                    </form>
                </div>

                @if (!empty($searchResults['radius_expanded']))
                    <div class="alert alert-info py-2 small">
                        No hotels right at this spot, so we are showing hotels within {{ (int) ($searchResults['expanded_radius_km'] ?? 45) }} km.
                    </div>
                @endif

                <ol class="sr-list" id="searchResultsContainer">
                    @forelse ($merged as $idx => $hotel)
                        @include('components.search.property-card', ['item' => $hotel, 'isFirst' => ($idx === 0), 'userWishlists' => $userWishlists])
                    @empty
                        <li class="sr-empty">
                            <h3 style="font-size: 19px; font-weight: 700; color: #1e293b;">No properties match your search in {{ $destLabel }}</h3>
                            <p class="text-muted mb-3">Try other dates, a wider budget or fewer filters.</p>
                            <a href="{{ route('search.index', array_filter(['destination' => $dest ?: null])) }}" class="btn btn-primary rounded-pill px-4">Clear filters</a>
                        </li>
                    @endforelse
                </ol>

                @if ($paginator && $paginator->lastPage() > 1)
                    @php
                        $cur  = $paginator->currentPage();
                        $last = $paginator->lastPage();
                        $from = max(1, $cur - 2);
                        $to   = min($last, $cur + 2);
                        $pageUrl = fn ($n) => request()->fullUrlWithQuery(['page' => $n]);
                    @endphp
                    <nav class="sr-pages" aria-label="Result pages">
                        @if ($cur > 1)
                            <a href="{{ $pageUrl($cur - 1) }}" rel="prev" aria-label="Previous page">&lsaquo;</a>
                        @else
                            <span class="disabled" aria-hidden="true">&lsaquo;</span>
                        @endif
                        @if ($from > 1)
                            <a href="{{ $pageUrl(1) }}">1</a>
                            @if ($from > 2)<span class="disabled">…</span>@endif
                        @endif
                        @for ($n = $from; $n <= $to; $n++)
                            @if ($n === $cur)
                                <span class="active" aria-current="page">{{ $n }}</span>
                            @else
                                <a href="{{ $pageUrl($n) }}">{{ $n }}</a>
                            @endif
                        @endfor
                        @if ($to < $last)
                            @if ($to < $last - 1)<span class="disabled">…</span>@endif
                            <a href="{{ $pageUrl($last) }}">{{ $last }}</a>
                        @endif
                        @if ($cur < $last)
                            <a href="{{ $pageUrl($cur + 1) }}" rel="next" aria-label="Next page">&rsaquo;</a>
                        @else
                            <span class="disabled" aria-hidden="true">&rsaquo;</span>
                        @endif
                    </nav>
                @endif
            </main>
        </div>
    </div>
</div>

@include('components.search.results-interactions', ['items' => $merged])
@endsection
