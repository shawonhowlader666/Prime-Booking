@if ($paginator->hasPages())
<nav aria-label="Search results pagination">
    <ul class="pagination d-flex align-items-center gap-1 flex-wrap justify-content-center mb-0" style="list-style:none; padding:0;">
        @if ($paginator->onFirstPage())
            <li><span class="d-flex align-items-center justify-content-center fw-bold text-muted" style="width:36px;height:36px;border-radius:50%;background:#f1f5f9;font-size:14px;cursor:not-allowed;"><i class="fa-solid fa-chevron-left" style="font-size:11px;"></i></span></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}" class="d-flex align-items-center justify-content-center fw-bold text-dark text-decoration-none" style="width:36px;height:36px;border-radius:50%;background:#ffffff;border:1.5px solid #e2e8f0;font-size:14px;transition:all 0.15s;" onmouseenter="this.style.background='#f0f4ff';this.style.borderColor='#2067e1';" onmouseleave="this.style.background='#ffffff';this.style.borderColor='#e2e8f0';"><i class="fa-solid fa-chevron-left" style="font-size:11px;"></i></a></li>
        @endif
        @foreach ($elements as $element)
            @if (is_string($element))
                <li><span class="d-flex align-items-center justify-content-center text-muted fw-bold" style="width:36px;height:36px;font-size:13px;">...</span></li>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li><span class="d-flex align-items-center justify-content-center text-white fw-bold" style="width:36px;height:36px;border-radius:50%;background:#2067e1;font-size:13px;box-shadow:0 3px 10px rgba(32,103,225,0.35);">{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}" class="d-flex align-items-center justify-content-center fw-bold text-dark text-decoration-none" style="width:36px;height:36px;border-radius:50%;background:#ffffff;border:1.5px solid #e2e8f0;font-size:13px;transition:all 0.15s;" onmouseenter="this.style.background='#f0f4ff';this.style.borderColor='#2067e1';" onmouseleave="this.style.background='#ffffff';this.style.borderColor='#e2e8f0';">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}" class="d-flex align-items-center justify-content-center fw-bold text-dark text-decoration-none" style="width:36px;height:36px;border-radius:50%;background:#ffffff;border:1.5px solid #e2e8f0;font-size:14px;transition:all 0.15s;" onmouseenter="this.style.background='#f0f4ff';this.style.borderColor='#2067e1';" onmouseleave="this.style.background='#ffffff';this.style.borderColor='#e2e8f0';"><i class="fa-solid fa-chevron-right" style="font-size:11px;"></i></a></li>
        @else
            <li><span class="d-flex align-items-center justify-content-center fw-bold text-muted" style="width:36px;height:36px;border-radius:50%;background:#f1f5f9;font-size:14px;cursor:not-allowed;"><i class="fa-solid fa-chevron-right" style="font-size:11px;"></i></span></li>
        @endif
    </ul>
    <p class="text-muted text-center mt-2 mb-0" style="font-size:12px;">Showing {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} of {{ $paginator->total() }} properties</p>
</nav>
@endif
