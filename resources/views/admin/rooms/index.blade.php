@extends('layouts.admin')
@section('title', 'Room Types: ' . $property->name . ' | Admin')

@section('content')

{{-- PAGE HEADER & ACTION TOOLBAR --}}
<div class="page-header-card">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2.5">
        <div>
            <h1 class="page-title m-0 d-flex align-items-center">
                <i class="fa-solid fa-bed text-primary me-2"></i> Room Inventory
            </h1>
            <div class="page-breadcrumb mt-1.5">
                <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
                <span class="sep">-</span><a href="{{ route('admin.properties.index') }}">Properties</a>
                <span class="sep">-</span><a href="{{ route('admin.properties.edit', $property->id) }}">{{ Str::limit($property->name, 22) }}</a>
                <span class="sep">-</span><strong style="color:#1e293b;">Room Types</strong>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Export Toolbar --}}
            <div class="btn-group btn-group-sm" role="group" style="height:32px;">
                <button type="button" class="btn btn-outline-secondary fw-semibold px-2.5" onclick="exportAdminTableData('excel')" title="Export to Excel" style="font-size:11.5px;">
                    <i class="fa-solid fa-file-excel me-1 text-success"></i> Excel
                </button>
                <button type="button" class="btn btn-outline-secondary fw-semibold px-2.5" onclick="exportAdminTableData('csv')" title="Export to CSV" style="font-size:11.5px;">
                    <i class="fa-solid fa-file-csv me-1 text-primary"></i> CSV
                </button>
                <button type="button" class="btn btn-outline-secondary fw-semibold px-2.5" onclick="exportAdminTableData('pdf')" title="Export to PDF" style="font-size:11.5px;">
                    <i class="fa-solid fa-file-pdf me-1 text-danger"></i> PDF
                </button>
                <button type="button" class="btn btn-outline-secondary fw-semibold px-2.5" onclick="window.print()" title="Print Page" style="font-size:11.5px;">
                    <i class="fa-solid fa-print me-1"></i> Print
                </button>
            </div>

            <a href="{{ route('admin.properties.edit', $property->id) }}" class="btn btn-outline-secondary btn-sm fw-semibold px-3 d-inline-flex align-items-center" style="height:32px; font-size:12px; border-radius:4px;">
                <i class="fa-solid fa-arrow-left me-1.5"></i> Back to Property
            </a>

            <a href="{{ route('admin.rooms.create', $property->id) }}" class="btn-add-primary" style="height:32px; font-size:12px; padding:0 14px; border-radius:4px; display:inline-flex; align-items:center;">
                <i class="fa-solid fa-plus me-1.5"></i> Add Room Type
            </a>
        </div>
    </div>
</div>

<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3" style="border-radius:6px;">
            <i class="fa-solid fa-circle-check me-1.5"></i> {{ session('success') }}
        </div>
    @endif

    {{-- MAIN CLEAN DATA CARD --}}
    <div class="data-table-card p-0 mb-4" style="border-radius:6px; background:#ffffff; border:1px solid #e8e8e8; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
        {{-- Card Header with Property Context and Search --}}
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2.5" style="background:#fafafa;">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-white text-dark border px-2.5 py-1.5" style="font-size:12px; font-weight:600;">
                    <i class="fa-solid fa-hotel text-primary me-1"></i> {{ $property->name }}
                </span>
                <span class="badge bg-primary-light text-primary fw-bold px-2 py-1" style="font-size:11px; background:#e6f7ff; border:1px solid #91d5ff; border-radius:4px;">
                    {{ $rooms->count() }} Room Types Configured
                </span>
            </div>
            <div style="width:260px; max-width:100%;">
                <input type="text" id="adminRoomSearchInput" class="form-control form-control-sm" placeholder="🔍 Search room name, bed..." onkeyup="filterAdminRoomsSearch(this.value)" style="height:32px; font-size:12.5px; border:1px solid #d9d9d9; border-radius:4px;">
            </div>
        </div>

        {{-- Table View --}}
        <div class="table-responsive">
            <table class="table table-stockifly align-middle mb-0" id="adminRoomsTable">
                <thead>
                    <tr>
                        <th style="padding-left: 20px !important;">Room Category</th>
                        <th>Bed &amp; Occupancy</th>
                        <th>Price / Night</th>
                        <th>Total Units</th>
                        <th>Inclusions</th>
                        <th style="text-align:right; padding-right: 20px !important;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rooms as $room)
                    <tr class="admin-room-row" data-name="{{ strtolower($room->name) }}" data-bed="{{ strtolower($room->bed_type ?? '') }}">
                        <td style="padding-left: 20px !important;">
                            <div class="d-flex align-items-center gap-2.5">
                                <div style="width:36px; height:36px; border-radius:6px; background:#f0f7ff; color:#2067e1; display:flex; align-items:center; justify-content:center; font-size:15px; flex-shrink:0; border:1px solid #d0e2ff;">
                                    <i class="fa-solid fa-bed"></i>
                                </div>
                                <div>
                                    <strong style="font-size:13.5px; color:#1e293b; display:block;">{{ $room->name }}</strong>
                                    <span style="font-size:11px; color:#64748b;">
                                        ID #{{ $room->id }} • {{ $room->room_size_sqm ? $room->room_size_sqm . ' m² • ' : '' }}{{ !empty($room->facilities) ? count($room->facilities) . ' facilities' : 'Standard setup' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <span class="badge bg-light text-dark border" style="font-size:11.5px; font-weight:600;">
                                    <i class="fa-solid fa-bed text-primary me-1"></i> {{ $room->bed_type ?: 'Standard Bed' }}
                                </span>
                                <span class="badge bg-light text-secondary border" style="font-size:11px;">
                                    <i class="fa-solid fa-user me-1"></i> {{ $room->max_adults ?? 2 }} Adults @if($room->max_children) + {{ $room->max_children }} Child @endif
                                </span>
                            </div>
                        </td>
                        <td>
                            <strong style="color:var(--primary); font-size:14.5px;">৳ {{ number_format($room->price_per_night) }}</strong>
                            <small class="text-muted d-block" style="font-size:10.5px;">per night</small>
                        </td>
                        <td>
                            <span class="badge bg-success-light text-success fw-bold px-2 py-1" style="font-size:11px; background:#f6ffed; border:1px solid #b7eb8f; border-radius:4px;">
                                <i class="fa-solid fa-door-open me-1"></i> {{ $room->total_rooms ?? 10 }} Units Available
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                @if($room->breakfast_included)
                                    <span class="badge bg-success text-white" style="font-size:10.5px; border-radius:3px;">
                                        <i class="fa-solid fa-mug-hot me-0.5"></i> Breakfast
                                    </span>
                                @endif
                                @if($room->free_cancellation)
                                    <span class="badge bg-info text-white" style="font-size:10.5px; border-radius:3px;">
                                        <i class="fa-solid fa-rotate-left me-0.5"></i> Free Cancel
                                    </span>
                                @endif
                                @if(!$room->breakfast_included && !$room->free_cancellation)
                                    <span class="text-muted" style="font-size:11px;">Room Only</span>
                                @endif
                            </div>
                        </td>
                        <td style="text-align:right; padding-right: 20px !important;">
                            <div class="d-inline-flex gap-1.5 align-items-center">
                                <a href="{{ route('admin.rooms.edit', [$property->id, $room->id]) }}" class="btn btn-sm btn-light border px-2.5 py-1" title="Edit Room Type" style="font-size:11.5px; height:28px; border-radius:4px;">
                                    <i class="fa-solid fa-pen text-primary me-1"></i> Edit
                                </a>
                                <form action="{{ route('admin.rooms.destroy', [$property->id, $room->id]) }}" method="POST" onsubmit="return confirm('Delete room type &quot;{{ $room->name }}&quot;?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger px-2 py-1" title="Delete Room" style="font-size:11.5px; height:28px; border-radius:4px;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <div style="font-size:32px; color:#cbd5e1; margin-bottom:8px;"><i class="fa-solid fa-bed"></i></div>
                            <h6 class="fw-bold text-dark mb-1">No Room Types Configured</h6>
                            <p class="mb-3" style="font-size:13px; color:#64748b;">Add room categories to enable hotel booking for this property.</p>
                            <a href="{{ route('admin.rooms.create', $property->id) }}" class="btn btn-primary btn-sm fw-bold px-3 py-1.5" style="background:#2067e1; border-radius:4px;">
                                <i class="fa-solid fa-plus me-1"></i> Add Room Type
                            </a>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
/**
 * Filter Room Categories by Search Term
 */
function filterAdminRoomsSearch(term) {
    const query = term.toLowerCase().trim();
    document.querySelectorAll('.admin-room-row').forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
    });
}

/**
 * Enterprise Table Export Tools (Excel, CSV, PDF, Print)
 */
function exportAdminTableData(type) {
    const table = document.getElementById('adminRoomsTable');
    if (!table) return;

    let rows = [];
    const trs = table.querySelectorAll('tr');
    trs.forEach(tr => {
        let rowData = [];
        const thsOrTds = tr.querySelectorAll('th, td');
        for (let i = 0; i < thsOrTds.length - 1; i++) {
            rowData.push(thsOrTds[i].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim());
        }
        if (rowData.length > 0) rows.push(rowData);
    });

    if (type === 'csv' || type === 'excel') {
        let csvContent = "data:text/csv;charset=utf-8," + rows.map(e => e.map(cell => `"${cell.replace(/"/g, '""')}"`).join(",")).join("\n");
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `room_inventory_${new Date().toISOString().slice(0,10)}.${type === 'excel' ? 'xls' : 'csv'}`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } else if (type === 'pdf') {
        window.print();
    }
}
</script>
@endsection
