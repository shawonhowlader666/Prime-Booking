{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {{-- Homepage --}}
    <url>
        <loc>https://primebooking.com.bd</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    {{-- Main Top Category Pages --}}
    <url>
        <loc>https://primebooking.com.bd/hotels</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>https://primebooking.com.bd/flights</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>https://primebooking.com.bd/transfers</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>https://primebooking.com.bd/packages</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>

    {{-- High-Priority City / Destination SEO Landing Pages (100% Exact Keyword Targets) --}}
    @if(!empty($destinations))
        @foreach($destinations as $destSlug => $destName)
        <url>
            <loc>{{ url('/hotels-in-' . $destSlug) }}</loc>
            <lastmod>{{ now()->toAtomString() }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>1.0</priority>
        </url>
        @endforeach
    @endif

    {{-- Individual Hotel Details (/hotels/{id} & /property/{slug}) --}}
    @foreach($properties as $property)
    <url>
        <loc>{{ url('/hotels/' . $property->id) }}</loc>
        <lastmod>{{ $property->updated_at ? $property->updated_at->toAtomString() : now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    @if(!empty($property->slug))
    <url>
        <loc>{{ url('/property/' . $property->slug) }}</loc>
        <lastmod>{{ $property->updated_at ? $property->updated_at->toAtomString() : now()->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endif
    @endforeach

    {{-- Tour Packages --}}
    @foreach($packages as $package)
    <url>
        <loc>{{ url('/packages/' . $package->slug) }}</loc>
        <lastmod>{{ $package->updated_at ? $package->updated_at->toAtomString() : now()->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach
</urlset>
