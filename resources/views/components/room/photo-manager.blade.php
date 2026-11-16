@props([
    'photos' => [],
    'prefix' => 'room_' . uniqid(),
    'title' => 'Current Room Photos',
])

@php
    $photosList = is_array($photos) ? $photos : (json_decode($photos ?? '[]', true) ?: []);
    $gridId = $prefix . '_photos_grid';
    $fileInputId = $prefix . '_file_input';
    $countId = $prefix . '_photos_count';
@endphp

<div class="col-12 room-photo-manager-component" id="{{ $prefix }}_component_wrapper">
    <div class="card border-0 shadow-sm" style="background:#f8fafc; border:1px solid #cbd5e1 !important; border-radius:6px;">
        <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span class="fw-bold text-dark d-flex align-items-center" style="font-size:12.5px;">
                <i class="fa-solid fa-images text-primary me-1.5"></i> {{ $title }} (<span id="{{ $countId }}">{{ count($photosList) }}</span>)
            </span>
            <small class="text-muted d-flex align-items-center" style="font-size:11px;">
                <i class="fa-solid fa-arrows-up-down-left-right text-primary me-1"></i>
                <span>Drag to reorder photos &bull; First is #1 Cover &bull; Check Delete to remove</span>
            </small>
        </div>
        <div class="card-body p-2.5">
            <div class="row g-2 room-photos-sortable-container" id="{{ $gridId }}" data-prefix="{{ $prefix }}">
                
                {{-- Existing Saved Room Photos --}}
                @foreach($photosList as $idx => $url)
                <div class="col-md-3 col-sm-4 col-6 room-photo-card-item" data-url="{{ $url }}" draggable="true">
                    <div class="p-1.5 bg-white rounded border position-relative d-flex flex-column gap-1 room-photo-box" 
                         style="border-color:#cbd5e1 !important; transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.2s ease; cursor: grab;">
                        
                        {{-- Hidden input that transmits order based on DOM position --}}
                        <input type="hidden" name="existing_images_order[]" value="{{ $url }}" class="room-photo-order-input">

                        {{-- Order Badge (Updates dynamically on drag) --}}
                        <div class="position-absolute top-0 start-0 m-1.5" style="z-index: 5;">
                            <span class="badge room-order-badge {{ $idx === 0 ? 'bg-primary' : 'bg-dark bg-opacity-75' }}" 
                                  style="font-size: 10px; font-weight: 700; padding: 3px 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.35);">
                                @if($idx === 0)
                                    <i class="fa-solid fa-star text-warning me-0.5"></i> #1 (Cover)
                                @else
                                    #{{ $idx + 1 }}
                                @endif
                            </span>
                        </div>

                        {{-- Drag Grip Handle Hint --}}
                        <div class="position-absolute top-0 end-0 m-1.5 px-1 py-0.5 rounded bg-white bg-opacity-85 text-secondary room-drag-handle" 
                             style="font-size: 10px; z-index: 5; pointer-events: none; box-shadow: 0 1px 3px rgba(0,0,0,0.2);">
                            <i class="fa-solid fa-grip-vertical"></i>
                        </div>

                        {{-- Photo Thumbnail --}}
                        <div class="rounded overflow-hidden position-relative" style="height: 85px; background:#0f172a;">
                            <img src="{{ media_url($url) }}" class="w-100 h-100" style="object-fit:cover;" alt="Room Photo" loading="lazy">
                            
                            {{-- Marked for Delete Visual Overlay --}}
                            <div class="position-absolute inset-0 d-none room-delete-overlay flex-column align-items-center justify-content-center"
                                 style="top:0;left:0;right:0;bottom:0; background:rgba(239, 68, 68, 0.75); color:#ffffff; font-size:11px; font-weight:700;">
                                <i class="fa-solid fa-trash-can fs-6 mb-0.5"></i>
                                <span>Will Delete</span>
                            </div>
                        </div>

                        {{-- Card Controls Footer --}}
                        <div class="d-flex align-items-center justify-content-between mt-0.5 px-0.5">
                            <div class="form-check form-check-sm mb-0">
                                <input class="form-check-input room-del-check" type="checkbox" name="delete_images[]" value="{{ $url }}" 
                                       id="{{ $prefix }}_del_{{ $idx }}" onchange="handleRoomPhotoDeleteToggle(this)">
                                <label class="form-check-label text-danger fw-semibold" for="{{ $prefix }}_del_{{ $idx }}" style="font-size:11px; cursor:pointer;">
                                    <i class="fa-solid fa-trash-can me-0.5"></i> Delete
                                </label>
                            </div>
                            <span class="text-muted small d-inline-flex align-items-center" style="font-size:10px; cursor:grab;">
                                <i class="fa-solid fa-up-down-left-right me-0.5"></i> Drag
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- The "+ Add Photo" Tile (Always exactly identical size as other photo cards) --}}
                <div class="col-md-3 col-sm-4 col-6 room-photo-add-tile-col" id="{{ $prefix }}_add_tile_col">
                    <div class="p-1.5 rounded border-2 border-dashed h-100 d-flex flex-column align-items-center justify-content-center text-center room-photo-add-tile" 
                         onclick="document.getElementById('{{ $fileInputId }}').click();"
                         style="border-color: #3b82f6 !important; background: #eff6ff; min-height: 118px; cursor: pointer; transition: all 0.2s ease;">
                        
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-1" 
                             style="width: 32px; height: 32px;">
                            <i class="fa-solid fa-plus fs-6"></i>
                        </div>
                        <span class="fw-bold text-primary" style="font-size: 12px; line-height: 1.2;">+ Add Photo</span>
                        <small class="text-muted" style="font-size: 10px;">Click or Drop image</small>

                        {{-- Hidden File Input --}}
                        <input type="file" id="{{ $fileInputId }}" name="image_files[]" multiple accept="image/*" class="d-none"
                               onchange="handleRoomPhotoFileSelect(this, '{{ $gridId }}', '{{ $prefix }}_add_tile_col', '{{ $countId }}')">
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Standalone Styles & Universal Drag/Drop + Upload Engine --}}
@once
<style>
    .room-photo-box:hover {
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12) !important;
        transform: translateY(-2px);
    }
    .room-photo-add-tile:hover {
        background: #dbeafe !important;
        border-color: #1d4ed8 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15) !important;
    }
    .room-photo-card-item.sortable-ghost,
    .room-photo-card-item.drag-over {
        opacity: 0.35;
        transform: scale(0.96);
    }
    .room-photo-card-item.sortable-chosen {
        cursor: grabbing !important;
    }
    .room-photo-card-item.sortable-drag {
        opacity: 0.95;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
    }
    .room-photo-box.marked-for-delete {
        border-color: #f87171 !important;
        background: #fef2f2 !important;
    }
    .room-photo-box.marked-for-delete .room-delete-overlay {
        display: flex !important;
    }
    .room-photo-box.marked-for-delete img {
        filter: grayscale(80%) blur(1px);
    }
</style>

{{-- High-Performance SortableJS with Native HTML5 Fallback --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    // Universal badge & count updater
    function updateRoomPhotoOrderBadges(gridEl) {
        if (!gridEl) return;
        const prefix = gridEl.dataset.prefix;
        const items = gridEl.querySelectorAll('.room-photo-card-item:not(.room-photo-add-tile-col)');
        const countSpan = document.getElementById(prefix + '_photos_count');
        if (countSpan) countSpan.textContent = items.length;

        items.forEach((item, index) => {
            const badge = item.querySelector('.room-order-badge');
            if (badge) {
                if (index === 0) {
                    badge.className = 'badge room-order-badge bg-primary';
                    badge.innerHTML = '<i class="fa-solid fa-star text-warning me-0.5"></i> #1 (Cover)';
                } else {
                    badge.className = 'badge room-order-badge bg-dark bg-opacity-75';
                    badge.textContent = '#' + (index + 1);
                }
            }
        });
    }

    // Toggle visual feedback when user checks "Delete"
    function handleRoomPhotoDeleteToggle(chk) {
        const box = chk.closest('.room-photo-box');
        if (!box) return;
        if (chk.checked) {
            box.classList.add('marked-for-delete');
        } else {
            box.classList.remove('marked-for-delete');
        }
    }

    // Handle selecting new photos from the "+ Add Photo" tile
    function handleRoomPhotoFileSelect(input, gridId, addTileId, countId) {
        const files = input.files;
        if (!files || files.length === 0) return;

        const grid = document.getElementById(gridId);
        const addTile = document.getElementById(addTileId);
        if (!grid || !addTile) return;

        Array.from(files).forEach((file, fIdx) => {
            if (!file.type.startsWith('image/')) return;

            const objectUrl = URL.createObjectURL(file);
            const col = document.createElement('div');
            col.className = 'col-md-3 col-sm-4 col-6 room-photo-card-item room-photo-new-preview';
            col.setAttribute('draggable', 'true');

            col.innerHTML = `
                <div class="p-1.5 bg-white rounded border position-relative d-flex flex-column gap-1 room-photo-box" 
                     style="border-color:#10b981 !important; transition: transform 0.15s ease, box-shadow 0.15s ease; cursor: grab;">
                    
                    <div class="position-absolute top-0 start-0 m-1.5" style="z-index: 5;">
                        <span class="badge room-order-badge bg-success" style="font-size: 10px; font-weight: 700; padding: 3px 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.35);">
                            <i class="fa-solid fa-circle-check me-0.5"></i> New
                        </span>
                    </div>

                    <div class="position-absolute top-0 end-0 m-1.5 px-1 py-0.5 rounded bg-white bg-opacity-85 text-secondary room-drag-handle" 
                         style="font-size: 10px; z-index: 5; pointer-events: none; box-shadow: 0 1px 3px rgba(0,0,0,0.2);">
                        <i class="fa-solid fa-grip-vertical"></i>
                    </div>

                    <div class="rounded overflow-hidden position-relative" style="height: 85px; background:#0f172a;">
                        <img src="${objectUrl}" class="w-100 h-100" style="object-fit:cover;" alt="New Photo Preview">
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-0.5 px-0.5">
                        <button type="button" class="btn btn-link text-danger p-0 fw-semibold text-decoration-none" 
                                style="font-size:11px;" onclick="removeNewRoomPhotoPreview(this, '${gridId}')">
                            <i class="fa-solid fa-xmark me-0.5"></i> Cancel
                        </button>
                        <span class="text-muted small d-inline-flex align-items-center" style="font-size:10px;">
                            <i class="fa-solid fa-up-down-left-right me-0.5"></i> Drag
                        </span>
                    </div>
                </div>
            `;

            grid.insertBefore(col, addTile);
            bindNativeDragEvents(col);
        });

        updateRoomPhotoOrderBadges(grid);
    }

    // Cancel / remove a newly selected preview card before submission
    function removeNewRoomPhotoPreview(btn, gridId) {
        const item = btn.closest('.room-photo-card-item');
        if (item) {
            item.remove();
            const grid = document.getElementById(gridId);
            if (grid) updateRoomPhotoOrderBadges(grid);
        }
    }

    // Native Drag and Drop Fallback Engine (Runs immediately on all cards)
    let draggedRoomPhotoItem = null;

    function bindNativeDragEvents(el) {
        el.addEventListener('dragstart', function(e) {
            draggedRoomPhotoItem = this;
            this.classList.add('sortable-drag');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', '');
        });

        el.addEventListener('dragend', function() {
            this.classList.remove('sortable-drag');
            document.querySelectorAll('.room-photo-card-item').forEach(i => i.classList.remove('drag-over'));
            const grid = this.closest('.room-photos-sortable-container');
            if (grid) updateRoomPhotoOrderBadges(grid);
            draggedRoomPhotoItem = null;
        });

        el.addEventListener('dragover', function(e) {
            e.preventDefault();
            if (!draggedRoomPhotoItem || draggedRoomPhotoItem === this) return;
            if (this.classList.contains('room-photo-add-tile-col')) return;

            e.dataTransfer.dropEffect = 'move';
            this.classList.add('drag-over');
        });

        el.addEventListener('dragleave', function() {
            this.classList.remove('drag-over');
        });

        el.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            if (!draggedRoomPhotoItem || draggedRoomPhotoItem === this) return;
            if (this.classList.contains('room-photo-add-tile-col')) return;

            const grid = this.parentElement;
            const items = Array.from(grid.querySelectorAll('.room-photo-card-item:not(.room-photo-add-tile-col)'));
            const fromIndex = items.indexOf(draggedRoomPhotoItem);
            const toIndex = items.indexOf(this);

            if (fromIndex < toIndex) {
                grid.insertBefore(draggedRoomPhotoItem, this.nextSibling);
            } else {
                grid.insertBefore(draggedRoomPhotoItem, this);
            }

            updateRoomPhotoOrderBadges(grid);
        });
    }

    // Initialize Sortable on DOM Ready (SortableJS + Native Drag Fallback)
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.room-photos-sortable-container').forEach(grid => {
            // 1. Initialize SortableJS if loaded
            if (typeof Sortable !== 'undefined') {
                new Sortable(grid, {
                    animation: 200,
                    draggable: '.room-photo-card-item:not(.room-photo-add-tile-col)',
                    filter: '.room-photo-add-tile-col',
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    onMove: function(evt) {
                        return !evt.related.classList.contains('room-photo-add-tile-col');
                    },
                    onEnd: function() {
                        updateRoomPhotoOrderBadges(grid);
                    }
                });
            }

            // 2. Also bind native HTML5 drag events to every item for 100% offline robustness
            grid.querySelectorAll('.room-photo-card-item:not(.room-photo-add-tile-col)').forEach(item => {
                bindNativeDragEvents(item);
            });

            // 3. Support drag & drop files directly onto "+ Add Photo" tile
            const addTile = grid.querySelector('.room-photo-add-tile');
            if (addTile) {
                ['dragenter', 'dragover'].forEach(eventName => {
                    addTile.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        addTile.style.borderColor = '#1d4ed8';
                        addTile.style.background = '#dbeafe';
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    addTile.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        addTile.style.borderColor = '#3b82f6';
                        addTile.style.background = '#eff6ff';
                    }, false);
                });

                addTile.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    if (files && files.length > 0) {
                        const fileInput = addTile.querySelector('input[type="file"]');
                        if (fileInput) {
                            fileInput.files = files;
                            fileInput.dispatchEvent(new Event('change'));
                        }
                    }
                }, false);
            }
        });
    });
</script>
@endonce
