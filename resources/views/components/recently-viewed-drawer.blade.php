{{-- Agoda Recently Viewed Properties Floating Dock & Offcanvas Drawer --}}
<div id="recentlyViewedDock" class="position-fixed bottom-0 end-0 m-3 d-none" style="z-index: 1040;">
    <button type="button" class="btn btn-dark rounded-pill px-3.5 py-2 shadow-lg fw-bold d-flex align-items-center gap-2" data-bs-toggle="offcanvas" data-bs-target="#recentlyViewedOffcanvas" style="background: #1e293b; border: 1px solid #475569; font-size: 13px;">
        <i class="fa-solid fa-clock-rotate-left text-primary"></i>
        <span>Recently Viewed</span>
        <span class="badge bg-primary rounded-pill" id="recentBadgeCount">0</span>
    </button>
</div>

{{-- Offcanvas Drawer --}}
<div class="offcanvas offcanvas-end rounded-start-4 border-0 shadow-lg" tabindex="-1" id="recentlyViewedOffcanvas" aria-labelledby="recentlyViewedLabel" style="width: 360px;">
    <div class="offcanvas-header border-bottom py-3 px-4 bg-light">
        <div class="d-flex align-items-center gap-2">
            <span class="rounded-circle d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 32px; height: 32px;">
                <i class="fa-solid fa-clock-rotate-left fs-6"></i>
            </span>
            <h5 class="offcanvas-title fw-bold text-dark mb-0" id="recentlyViewedLabel" style="font-size: 16px;">Recently Viewed</h5>
        </div>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3 overflow-y-auto">
        <div class="d-flex justify-content-between align-items-center mb-2 px-1">
            <small class="text-secondary fw-semibold">Your recent searches &amp; stays</small>
            <button type="button" class="btn btn-link p-0 text-danger small text-decoration-none fw-bold" onclick="clearRecentProperties();" style="font-size: 11.5px;">
                Clear history
            </button>
        </div>
        <div class="d-flex flex-column gap-2.5" id="recentItemsContainer">
            {{-- Injected via JS from localStorage --}}
        </div>
    </div>
</div>

<script>
    const RECENT_KEY = 'prime_recent_properties';

    function getRecentProperties() {
        try {
            return JSON.parse(localStorage.getItem(RECENT_KEY)) || [];
        } catch (e) {
            return [];
        }
    }

    function renderRecentProperties() {
        const list = getRecentProperties();
        const dock = document.getElementById('recentlyViewedDock');
        const badge = document.getElementById('recentBadgeCount');
        const container = document.getElementById('recentItemsContainer');

        if (!dock || !container) return;

        if (list.length === 0) {
            dock.classList.add('d-none');
            container.innerHTML = `
                <div class="text-center py-5 text-secondary">
                    <i class="fa-solid fa-hotel fs-1 text-muted opacity-50 mb-2"></i>
                    <p class="small mb-0">No recently viewed properties yet.</p>
                </div>
            `;
            return;
        }

        dock.classList.remove('d-none');
        if (badge) badge.textContent = list.length;

        container.innerHTML = list.map(item => `
            <div class="card border rounded-3 p-2 shadow-xs hover-shadow position-relative bg-white" style="transition: all 0.2s ease;">
                <div class="d-flex gap-2.5 align-items-center">
                    <img src="${item.image}" alt="${item.name}" class="rounded-2 flex-shrink-0" style="width: 70px; height: 70px; object-fit: cover;">
                    <div class="flex-grow-1 min-w-0">
                        <a href="/hotels/${item.id}" class="text-dark fw-bold text-decoration-none text-truncate d-block" style="font-size: 13px;">
                            ${item.name}
                        </a>
                        <small class="text-secondary d-block text-truncate" style="font-size: 11px;">
                            <i class="fa-solid fa-location-dot text-danger me-1"></i> ${item.city || 'Bangladesh'}
                        </small>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold" style="font-size: 10px;">★ ${item.rating || '8.5'}</span>
                            <strong class="text-primary font-monospace" style="font-size: 13px;">${item.price}</strong>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function clearRecentProperties() {
        localStorage.removeItem(RECENT_KEY);
        renderRecentProperties();
        const offcanvasEl = document.getElementById('recentlyViewedOffcanvas');
        const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
        if (bsOffcanvas) bsOffcanvas.hide();
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderRecentProperties();
    });
</script>
