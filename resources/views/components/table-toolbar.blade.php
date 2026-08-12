@props([
    'tableId',
    'exportName' => 'data',
    'addUrl' => null,
    'addText' => 'Add New',
    'searchPlaceholder' => 'Search records...'
])

<div class="saas-table-toolbar">
    <div class="saas-toolbar-actions">
        {{-- Copy --}}
        <button type="button" class="btn-tbl-copy" onclick="copyTableToClipboard('{{ $tableId }}')" title="Copy Table to Clipboard">
            <i class="fa-regular fa-copy"></i> Copy
        </button>

        {{-- XL (Excel) --}}
        <button type="button" class="btn-tbl-excel" onclick="exportTableExcel('{{ $tableId }}', '{{ $exportName }}')" title="Export to Excel">
            <i class="fa-solid fa-file-excel"></i> XL
        </button>

        {{-- CSV --}}
        <button type="button" class="btn-export-csv" onclick="exportTableCSV('{{ $tableId }}', '{{ $exportName }}')" title="Export to CSV">
            <i class="fa-solid fa-file-csv"></i> CSV
        </button>

        {{-- PDF --}}
        <button type="button" class="btn-export-pdf" onclick="exportTablePDF('{{ $tableId }}', '{{ $exportName }}')" title="Export / Print PDF">
            <i class="fa-solid fa-file-pdf"></i> PDF
        </button>

        {{-- Print --}}
        <button type="button" class="btn-tbl-print" onclick="printTable('{{ $tableId }}')" title="Print Table Report">
            <i class="fa-solid fa-print"></i> Print
        </button>

        {{-- Column Visibility (SL) --}}
        <div style="position:relative; display:inline-block;">
            <button type="button" class="btn-tbl-col" onclick="toggleColVis('{{ $tableId }}', this)" title="Column Visibility Settings">
                <i class="fa-solid fa-table-columns"></i> SL
            </button>
            <div class="col-vis-dropdown" id="colVisDropdown_{{ $tableId }}" style="display:none;"></div>
        </div>

        {{-- Select Row Mode --}}
        <button type="button" class="btn-tbl-select" onclick="toggleSelectAll('{{ $tableId }}', this)" title="Toggle Row Checkbox Select Mode">
            <i class="fa-solid fa-square-check"></i> Select
        </button>

        {{-- Optional Add Button --}}
        @if($addUrl)
            <a href="{{ $addUrl }}" class="btn-add-primary ms-1">
                <i class="fa-solid fa-plus"></i> {{ $addText }}
            </a>
        @endif
    </div>

    {{-- Instant Table Search --}}
    <div class="tbl-search-wrap">
        <i class="fa-solid fa-magnifying-glass tbl-search-icon"></i>
        <input type="text" class="tbl-search-input" placeholder="{{ $searchPlaceholder }}" onkeyup="filterTableSearch('{{ $tableId }}', this.value)">
    </div>
</div>
