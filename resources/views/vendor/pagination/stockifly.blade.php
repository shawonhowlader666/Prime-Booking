@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation">
        <ul class="pagination pagination-sm m-0" style="gap: 4px; display: flex; align-items: center;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link" style="border-radius: 4px !important; border: 1px solid #e2e8f0; color: #cbd5e1; font-size: 11.5px; min-width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; padding: 0 8px; background: #f8fafc;">
                        <i class="fa-solid fa-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" style="border-radius: 4px !important; border: 1px solid #cbd5e1; color: #475569; font-size: 11.5px; min-width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; padding: 0 8px; background: #ffffff;">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" style="border-radius: 4px !important; border: none; background: transparent; color: #94a3b8; font-size: 12px; padding: 0 4px; height: 28px; display: flex; align-items: center;">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link" style="border-radius: 4px !important; border: 1px solid var(--primary, #1890ff); background: var(--primary, #1890ff); color: #ffffff; font-size: 12px; font-weight: 700; min-width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; padding: 0 8px;">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}" style="border-radius: 4px !important; border: 1px solid #e2e8f0; background: #ffffff; color: #475569; font-size: 12px; font-weight: 500; min-width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; padding: 0 8px;">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" style="border-radius: 4px !important; border: 1px solid #cbd5e1; color: #475569; font-size: 11.5px; min-width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; padding: 0 8px; background: #ffffff;">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link" style="border-radius: 4px !important; border: 1px solid #e2e8f0; color: #cbd5e1; font-size: 11.5px; min-width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; padding: 0 8px; background: #f8fafc;">
                        <i class="fa-solid fa-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
