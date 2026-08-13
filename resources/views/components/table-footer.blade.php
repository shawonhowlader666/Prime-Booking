@props(['items', 'perPage' => 15])

<div class="stockifly-table-footer">
    <div class="footer-left">
        <span>Show</span>
        <select class="per-page-select" onchange="const url = new URL(window.location.href); url.searchParams.set('per_page', this.value); url.searchParams.set('page', 1); window.location.href = url.href;">
            <option value="10" {{ request('per_page', $perPage) == 10 ? 'selected' : '' }}>10</option>
            <option value="15" {{ request('per_page', $perPage) == 15 ? 'selected' : '' }}>15</option>
            <option value="25" {{ request('per_page', $perPage) == 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ request('per_page', $perPage) == 50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ request('per_page', $perPage) == 100 ? 'selected' : '' }}>100</option>
        </select>
        <span>entries</span>

        @if(isset($items) && method_exists($items, 'total'))
            <span class="ms-2 text-muted" style="font-size: 11.5px;">
                | Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ number_format($items->total()) }} entries
            </span>
        @endif
    </div>

    <div class="footer-right">
        @if(isset($items) && method_exists($items, 'links'))
            {{ $items->onEachSide(1)->links('vendor.pagination.stockifly') }}
        @endif
    </div>
</div>
