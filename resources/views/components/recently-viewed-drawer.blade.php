{{-- Agoda Recently Viewed Properties Floating Dock & Offcanvas Drawer --}}
<div id="recentlyViewedDock" class="position-fixed bottom-0 end-0 m-3 d-none" style="z-index: 1040;">
    <button type="button" class="btn btn-dark rounded-pill px-3.5 py-2 shadow-lg fw-bold d-flex align-items-center gap-2" data-bs-toggle="offcanvas" data-bs-target="#recentlyViewedOffcanvas" style="background: #1e293b; border: 1px solid #475569; font-size: 13px;">
        <i class="fa-solid fa-clock-rotate-left text-primary"></i>
        <span>Recently Viewed</span>
        <span class="badge bg-primary rounded-pill" id="recentBadgeCount">0</span>
    </button>
</div>

{{-- Offcanvas Drawer --}}
<div class="offcanvas offcanvas-end rounded-start-4 border-0 shadow-lg" tabindex="-1" id="recentlyViewedOffcanvas" aria-labelledby="recentlyViewedLabel" style="width: 390px; max-width: 92vw;">
    <div class="offcanvas-header border-bottom py-3 px-4 bg-white" style="border-color: #e2e8f0 !important;">
        <div class="d-flex align-items-center gap-2">
            <span class="rounded-circle d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 34px; height: 34px;">
                <i class="fa-solid fa-clock-rotate-left fs-6"></i>
            </span>
            <h5 class="offcanvas-title fw-bold text-dark mb-0" id="recentlyViewedLabel" style="font-size: 16px; font-family: 'Plus Jakarta Sans', sans-serif;">Recently Viewed</h5>
        </div>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3 overflow-y-auto" style="background: #f8fafc;">
        <div class="d-flex justify-content-between align-items-center mb-2.5 px-1">
            <small class="text-secondary fw-bold" style="font-size: 12px; color: #64748b !important;">Your recent searches &amp; stays</small>
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
            <div class="card border shadow-xs position-relative bg-white" style="border-color: #e2e8f0 !important; border-radius: 10px !important; padding: 12px; transition: all 0.2s ease; overflow: hidden;">
                <div class="d-flex align-items-center" style="gap: 12px; width: 100%; min-width: 0;">
                    <img src="${item.image}" alt="${item.name}" class="flex-shrink-0" style="width: 74px; height: 74px; object-fit: cover; border-radius: 8px; background: #f1f5f9;">
                    <div style="flex: 1; min-width: 0; overflow: hidden;">
                        <a href="/hotels/${item.id}" class="text-dark fw-bold text-decoration-none d-block hover-primary" style="font-size: 13.5px; line-height: 1.3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-family: 'Plus Jakarta Sans', sans-serif;" title="${item.name}">
                            ${item.name}
                        </a>
                        <div class="text-secondary d-flex align-items-center mt-1" style="font-size: 11.5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #64748b !important;">
                            <i class="fa-solid fa-location-dot text-danger flex-shrink-0" style="margin-right: 5px; font-size: 11px;"></i>
                            <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${item.city || 'Bangladesh'}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-1.5 pt-1.5 border-top" style="border-color: #f1f5f9 !important;">
                            <span class="badge" style="background:#e0edff; color:#2067e1; font-weight:700; font-size:10.5px; border-radius:4px; padding: 2px 6px;">★ ${item.rating || '8.5'}</span>
                            <strong class="fw-bold" style="font-size: 13.5px; color: #2067e1 !important; font-family: 'Plus Jakarta Sans', sans-serif;">${item.price}</strong>
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
