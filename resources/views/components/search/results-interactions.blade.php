{{--
    Behaviour for the search results page:
    - photo carousel arrows on each hotel card
    - wishlist heart
    - map of the hotels on this page ("See area" and each card's "View on map")
--}}
@php
    $mapHotels = collect($items ?? [])->map(function ($h) {
        $get = fn ($k) => is_object($h) ? ($h->{$k} ?? null) : ($h[$k] ?? null);
        $lat = (float) $get('latitude');
        $lng = (float) $get('longitude');
        if (!$lat || !$lng) {
            return null;
        }
        $slug = $get('slug');
        return [
            'id'    => (int) $get('id'),
            'name'  => (string) $get('name'),
            'lat'   => $lat,
            'lng'   => $lng,
            'price' => \App\Services\CurrencyService::format((float) $get('price_per_night')),
            'url'   => $slug ? route('hotel.show', ['slug' => $slug] + request()->only(['check_in', 'check_out', 'guests', 'rooms'])) : route('hotels.show', ['id' => $get('id')]),
        ];
    })->filter()->values();
@endphp

<div id="resultsMapModal" role="dialog" aria-modal="true" aria-label="Hotels on map" hidden
    style="position: fixed; inset: 0; z-index: 1000000; background: rgba(15,23,42,.55); padding: 4vh 16px;">
    <div style="max-width: 1100px; margin: 0 auto;">
        <div style="background:#fff; border-radius: 12px; overflow: hidden;">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-bottom:1px solid #e5e7eb;">
                <h5 style="font-size: 16px; font-weight: 700; margin: 0;">Hotels on the map</h5>
                <button type="button" id="resultsMapClose" aria-label="Close" style="border:0;background:none;font-size:26px;line-height:1;cursor:pointer;">&times;</button>
            </div>
            <div>
                <div id="resultsMap" style="height: 70vh; min-height: 360px; background: #eef2f7;"></div>
                <div id="resultsMapEmpty" class="p-4 text-center text-muted" style="display: none;">
                    These hotels have no map location yet.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var HOTELS = @json($mapHotels);
    var map = null, markers = {};

    function loadLeaflet(cb) {
        if (window.L) return cb();
        var css = document.createElement('link');
        css.rel = 'stylesheet';
        css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        document.head.appendChild(css);
        var js = document.createElement('script');
        js.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        js.onload = cb;
        document.head.appendChild(js);
    }

    function showModal() {
        var el = document.getElementById('resultsMapModal');
        el.hidden = false;
        document.body.style.overflow = 'hidden';
    }
    function hideModal() {
        document.getElementById('resultsMapModal').hidden = true;
        document.body.style.overflow = '';
    }
    document.addEventListener('click', function (e) {
        if (e.target.id === 'resultsMapClose' || e.target.id === 'resultsMapModal') hideModal();
    });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') hideModal(); });

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function render(focusId) {
        var empty = document.getElementById('resultsMapEmpty');
        var box = document.getElementById('resultsMap');
        if (!HOTELS.length) {
            box.style.display = 'none';
            empty.style.display = '';
            return;
        }
        loadLeaflet(function () {
            if (!map) {
                map = L.map('resultsMap');
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                HOTELS.forEach(function (h) {
                    markers[h.id] = L.marker([h.lat, h.lng]).addTo(map).bindPopup(
                        '<strong>' + escapeHtml(h.name) + '</strong><br>' + escapeHtml(h.price) +
                        ' / night<br><a href="' + h.url + '">View hotel</a>'
                    );
                });
            }
            setTimeout(function () {
                map.invalidateSize();
                var target = HOTELS.find(function (h) { return h.id === focusId; });
                if (target) {
                    map.setView([target.lat, target.lng], 15);
                    markers[target.id].openPopup();
                } else {
                    map.fitBounds(L.latLngBounds(HOTELS.map(function (h) { return [h.lat, h.lng]; })), { padding: [30, 30], maxZoom: 14 });
                }
            }, 250);
        });
    }

    window.openAgodaMapModal = function () { showModal(); render(null); };
    window.openHotelOnMap = function (id) { showModal(); render(Number(id)); };

    // Card photo carousel
    window.changeAgodaSlide = function (id, dir) {
        var slides = document.querySelectorAll('.prop-slide-' + id);
        if (!slides.length) return;
        var list = slides[0].parentElement;
        var width = list.clientWidth || 1;
        var current = Math.round(list.scrollLeft / width);
        var next = Math.max(0, Math.min(slides.length - 1, current + dir));
        list.scrollTo({ left: next * width, behavior: 'smooth' });
        var prev = document.getElementById('prev_arrow_' + id);
        var nxt = document.getElementById('next_arrow_' + id);
        if (prev) prev.classList.toggle('is-disabled', next === 0);
        if (nxt) nxt.classList.toggle('is-disabled', next === slides.length - 1);
        var counter = list.closest('[role="region"]') && list.closest('[role="region"]').parentElement.querySelector('[data-slide-counter]');
        if (counter) counter.textContent = (next + 1) + '/' + slides.length;
    };

    // Wishlist heart
    window.toggleAgodaWishlist = function (id, btn) {
        var token = { content: @json(csrf_token()) };
        fetch(@json(route('wishlist.toggle')), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token ? token.content : '' },
            body: JSON.stringify({ property_id: id })
        }).then(function (r) {
            return r.json().then(function (d) { return { status: r.status, data: d }; });
        }).then(function (res) {
            if (res.status === 401) {
                window.location.href = (res.data && res.data.redirect) || @json(route('login'));
                return;
            }
            var saved = !!(res.data && res.data.is_wishlisted);
            var rect = btn.querySelector('rect');
            if (rect) rect.setAttribute('fill', saved ? '#e12d46' : '#252c38');
            btn.setAttribute('aria-pressed', saved ? 'true' : 'false');
            btn.setAttribute('aria-label', saved ? 'Remove from saved' : 'Save to trip');
        }).catch(function () {});
    };
})();
</script>

{{-- Mobile: the filter column is hidden on small screens; this button opens it as a full-screen panel. --}}
<style>
    #mobileFilterBtn { display: none; }
    @media (max-width: 991px) {
        #mobileFilterBtn {
            display: inline-flex; align-items: center; gap: 8px; position: fixed; left: 50%; bottom: 84px;
            transform: translateX(-50%); z-index: 999980; background: #2067e1; color: #fff; border: 0;
            border-radius: 999px; padding: 10px 20px; font-weight: 700; font-size: 14px; box-shadow: 0 6px 18px rgba(0,0,0,.25);
        }
        body.mobile-filters-open #searchPageLeftColumn {
            display: block !important; position: fixed !important; inset: 0 !important; z-index: 999990 !important;
            background: #fff !important; overflow-y: auto !important; padding: 56px 16px 24px !important; width: 100% !important; max-width: 100% !important;
        }
        body.mobile-filters-open #mobileFilterClose { display: block; }
        body.mobile-filters-open { overflow: hidden; }
    }
    #mobileFilterClose { display: none; position: fixed; top: 10px; right: 14px; z-index: 999995; border: 0; background: #f1f5f9; border-radius: 999px; width: 38px; height: 38px; font-size: 22px; line-height: 1; }
</style>
<button type="button" id="mobileFilterBtn" aria-controls="searchPageLeftColumn" aria-expanded="false">
    <i class="fa-solid fa-sliders"></i> Filters
</button>
<button type="button" id="mobileFilterClose" aria-label="Close filters">&times;</button>
<script>
(function () {
    function set(open) {
        document.body.classList.toggle('mobile-filters-open', open);
        var b = document.getElementById('mobileFilterBtn');
        if (b) b.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    document.addEventListener('click', function (e) {
        if (e.target.closest('#mobileFilterBtn')) set(true);
        if (e.target.closest('#mobileFilterClose')) set(false);
    });
})();
</script>