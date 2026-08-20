<?php $__env->startSection('title', 'Search Hotels & Stays in ' . ($destination ?: 'Bangladesh') . ' | PRIME BOOKING'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('components.search.loading-skeleton-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<?php
    $checkinCarbon = \Carbon\Carbon::parse($checkIn ?: now());
    $checkoutCarbon = \Carbon\Carbon::parse($checkOut ?: now()->addDays(7));
    $guestCount = intval($guests ?: 2);
    $roomsCount = intval(request('rooms', 1));
?>
<div style="background-color: #1d2b45; padding: 12px 0; border-bottom: 1px solid #334155;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 16px;">
        <form action="<?php echo e(route('search.index')); ?>" method="GET" class="row g-2 align-items-center" id="searchHeaderForm" onsubmit="showAgodaSearchLoading();">
            <input type="hidden" name="search_type" value="<?php echo e($searchType ?? 'hotel'); ?>">

            
            <div class="col-12 col-lg-3">
                <div class="bg-white rounded-3 d-flex align-items-center px-3 shadow-xs position-relative" style="height: 48px;">
                    <i class="fa-solid fa-magnifying-glass text-secondary me-2 fs-6"></i>
                    <input type="text" name="destination" id="mainDestInput" class="form-control border-0 p-0 fw-bold text-dark" value="<?php echo e($destination); ?>" placeholder="Enter destination or property" style="font-size: 14px; box-shadow: none;">
                    <input type="hidden" name="lat" id="gpsLatInput" value="<?php echo e(request('lat')); ?>">
                    <input type="hidden" name="lng" id="gpsLngInput" value="<?php echo e(request('lng')); ?>">
                    <button type="button" class="btn btn-link p-0 text-primary ms-1" title="Search Near My Current GPS Location" onclick="useCurrentLocation()" style="font-size: 15px; text-decoration: none;">
                        <i class="fa-solid fa-location-crosshairs" id="gpsCrosshairIcon"></i>
                    </button>
                </div>
            </div>

            
            <div class="col-6 col-md-3 col-lg-2">
                <div class="bg-white rounded-3 px-3 py-1 d-flex align-items-center gap-2 shadow-xs position-relative" style="height: 48px; cursor: pointer;" onclick="document.getElementById('checkInNativeInput').showPicker();">
                    <i class="fa-regular fa-calendar text-secondary fs-5"></i>
                    <div style="line-height: 1.15;">
                        <strong class="d-block text-dark" id="checkInDisplayDate" style="font-size: 13px;"><?php echo e($checkinCarbon->format('j M Y')); ?></strong>
                        <small class="text-secondary" id="checkInDisplayDay" style="font-size: 11px;"><?php echo e($checkinCarbon->format('l')); ?></small>
                    </div>
                    <input type="date" name="check_in" id="checkInNativeInput" value="<?php echo e($checkinCarbon->format('Y-m-d')); ?>" class="position-absolute opacity-0" style="bottom: 0; left: 0; width: 1px; height: 1px;" onchange="updateSearchDateDisplay('checkIn', this.value);">
                </div>
            </div>

            
            <div class="col-6 col-md-3 col-lg-2">
                <div class="bg-white rounded-3 px-3 py-1 d-flex align-items-center gap-2 shadow-xs position-relative" style="height: 48px; cursor: pointer;" onclick="document.getElementById('checkOutNativeInput').showPicker();">
                    <i class="fa-regular fa-calendar text-secondary fs-5"></i>
                    <div style="line-height: 1.15;">
                        <strong class="d-block text-dark" id="checkOutDisplayDate" style="font-size: 13px;"><?php echo e($checkoutCarbon->format('j M Y')); ?></strong>
                        <small class="text-secondary" id="checkOutDisplayDay" style="font-size: 11px;"><?php echo e($checkoutCarbon->format('l')); ?></small>
                    </div>
                    <input type="date" name="check_out" id="checkOutNativeInput" value="<?php echo e($checkoutCarbon->format('Y-m-d')); ?>" class="position-absolute opacity-0" style="bottom: 0; left: 0; width: 1px; height: 1px;" onchange="updateSearchDateDisplay('checkOut', this.value);">
                </div>
            </div>

            
            <div class="col-12 col-md-4 col-lg-3 position-relative">
                <div class="bg-white rounded-3 px-3 py-1 d-flex align-items-center justify-content-between shadow-xs dropdown-toggle" style="height: 48px; cursor: pointer;" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-users text-secondary fs-5"></i>
                        <div style="line-height: 1.15;">
                            <strong class="d-block text-dark" id="guestCountDisplay" style="font-size: 13px;"><?php echo e($guestCount); ?> adult<?php echo e($guestCount > 1 ? 's' : ''); ?></strong>
                            <small class="text-secondary" id="roomCountDisplay" style="font-size: 11px;"><?php echo e($roomsCount); ?> room<?php echo e($roomsCount > 1 ? 's' : ''); ?></small>
                        </div>
                    </div>
                </div>

                
                <div class="dropdown-menu p-3 shadow-lg border-0 rounded-3 mt-2" style="width: 280px; z-index: 1050;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <strong class="d-block text-dark" style="font-size: 13px;">Adults</strong>
                            <small class="text-muted" style="font-size: 11px;">Ages 18 or above</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" onclick="adjustGuestCounter('guests', -1);">-</button>
                            <input type="hidden" name="guests" id="guestsHiddenInput" value="<?php echo e($guestCount); ?>">
                            <span class="fw-bold px-1" id="guestsValText"><?php echo e($guestCount); ?></span>
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" onclick="adjustGuestCounter('guests', 1);">+</button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 border-top pt-2">
                        <div>
                            <strong class="d-block text-dark" style="font-size: 13px;">Rooms</strong>
                            <small class="text-muted" style="font-size: 11px;">Total units needed</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" onclick="adjustGuestCounter('rooms', -1);">-</button>
                            <input type="hidden" name="rooms" id="roomsHiddenInput" value="<?php echo e($roomsCount); ?>">
                            <span class="fw-bold px-1" id="roomsValText"><?php echo e($roomsCount); ?></span>
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" onclick="adjustGuestCounter('rooms', 1);">+</button>
                        </div>
                    </div>
                    <button type="button" class="btn text-white w-100 btn-sm fw-bold rounded-2" style="background: #2067e1;" onclick="bootstrap.Dropdown.getInstance(this.closest('.position-relative').querySelector('.dropdown-toggle')).hide();">
                        Done
                    </button>
                </div>
            </div>

            
            <div class="col-12 col-md-2 col-lg-2">
                <button type="submit" class="btn text-white w-100 fw-bold rounded-3 shadow-sm" style="background-color: #2067e1; height: 48px; font-size: 14px; letter-spacing: 0.5px;">
                    SEARCH
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    function updateSearchDateDisplay(type, dateStr) {
        if (!dateStr) return;
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return;

        const formattedDate = `${d.getDate()} ${monthNames[d.getMonth()]} ${d.getFullYear()}`;
        const formattedDay = dayNames[d.getDay()];

        if (type === 'checkIn') {
            document.getElementById('checkInDisplayDate').textContent = formattedDate;
            document.getElementById('checkInDisplayDay').textContent = formattedDay;
        } else {
            document.getElementById('checkOutDisplayDate').textContent = formattedDate;
            document.getElementById('checkOutDisplayDay').textContent = formattedDay;
        }
    }

    function adjustGuestCounter(field, delta) {
        if (field === 'guests') {
            const inp = document.getElementById('guestsHiddenInput');
            let val = Math.max(1, Math.min(30, (parseInt(inp.value) || 2) + delta));
            inp.value = val;
            document.getElementById('guestsValText').textContent = val;
            document.getElementById('guestCountDisplay').textContent = `${val} adult${val > 1 ? 's' : ''}`;
        } else if (field === 'rooms') {
            const inp = document.getElementById('roomsHiddenInput');
            let val = Math.max(1, Math.min(10, (parseInt(inp.value) || 1) + delta));
            inp.value = val;
            document.getElementById('roomsValText').textContent = val;
            document.getElementById('roomCountDisplay').textContent = `${val} room${val > 1 ? 's' : ''}`;
        }
    }

    function useCurrentLocation() {
        if (!navigator.geolocation) {
            alert("Geolocation is not supported by your browser.");
            return;
        }
        const destInput = document.getElementById('mainDestInput');
        const icon = document.getElementById('gpsCrosshairIcon');
        const origPlaceholder = destInput.placeholder;
        destInput.placeholder = "Detecting your GPS location...";
        if (icon) icon.className = "fa-solid fa-spinner fa-spin";

        navigator.geolocation.getCurrentPosition(function(pos) {
            document.getElementById('gpsLatInput').value = pos.coords.latitude;
            document.getElementById('gpsLngInput').value = pos.coords.longitude;
            destInput.value = "Near My Location";
            if (icon) icon.className = "fa-solid fa-location-crosshairs";
            document.getElementById('searchHeaderForm').submit();
        }, function(err) {
            destInput.placeholder = origPlaceholder;
            if (icon) icon.className = "fa-solid fa-location-crosshairs";
            alert("Could not access your location. Please ensure location permissions are enabled.");
        }, { timeout: 10000 });
    }
</script>


<div style="background: #fff0f3; border-bottom: 1px solid #fecdd3; padding: 10px 0;">
    <div class="d-flex align-items-center justify-content-between" style="max-width: 1240px; margin: 0 auto; padding: 0 16px; font-size: 13px;">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger rounded-pill px-2 py-1"><i class="fa-solid fa-tag"></i></span>
            <strong class="text-dark">Looking for instant coupons?</strong>
            <span class="text-secondary d-none d-md-inline">Check out our Coupons &amp; Deals page for today's BDT discounts</span>
        </div>
        <a href="<?php echo e(route('deals')); ?>" class="btn btn-sm btn-outline-danger bg-white rounded-pill px-3 py-1 fw-bold" style="font-size: 12px;">See all coupons</a>
    </div>
</div>


<div style="max-width: 1240px; margin: 0 auto; padding: 24px 16px;">
    <div class="row g-4">
        
        
        <div class="col-lg-3">
            <?php echo $__env->make('components.search.filter-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        
        <div class="col-lg-9" id="searchResultsContainer">
            
            
            <div class="mb-4 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: #e8f5e9; border: 1px solid #c8e6c9; border-radius: 12px !important; padding: 16px 20px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 15px; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">Additional discounts up to 12% on Prime Homes</h6>
                        <small class="text-secondary d-block" style="font-size: 12.5px; line-height: 1.3;">More spacious. More local. More of why you travel.</small>
                    </div>
                </div>
                <div>
                    <a href="<?php echo e(route('search.index', array_merge(request()->query(), ['search_type' => 'homes']))); ?>" class="btn text-white fw-bold shadow-xs" style="background-color: #2067e1; border-radius: 8px; font-size: 13px; padding: 8px 18px;">
                        Show more Homes
                    </a>
                </div>
            </div>

            
            <?php if(!empty($destination)): ?>
            <?php
                $cityInsight = \App\Services\Search\CityInsightService::getInsights($destination);
            ?>
            <div class="mb-3 p-3 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-3 shadow-xs" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 42px; height: 42px; background: #ffffff; border: 1px solid #86efac;">
                        <i class="<?php echo e($cityInsight['icon']); ?> fs-5"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <strong class="text-dark fw-bold" style="font-size: 14px;"><?php echo e($destination); ?></strong>
                            <span class="badge fw-semibold px-2.5 py-1" style="font-size: 11px; background: #dcfce7; color: #15803d; border: 1px solid #86efac; border-radius: 20px;">
                                <i class="fa-solid fa-sparkles me-1 text-success" style="font-size: 10px;"></i><?php echo e($cityInsight['season_badge']); ?>

                            </span>
                            <span class="text-secondary fw-semibold" style="font-size: 12.5px;">
                                <i class="fa-solid fa-cloud-sun text-warning me-1"></i><?php echo e($cityInsight['temp']); ?> · <?php echo e($cityInsight['condition']); ?>

                            </span>
                        </div>
                        <div class="text-secondary" style="font-size: 12px; line-height: 1.35; color: #475569;">
                            <i class="fa-solid fa-circle-info text-success me-1 opacity-75"></i><?php echo e($cityInsight['tip']); ?>

                        </div>
                    </div>
                </div>
                <div>
                    <span class="badge bg-white text-dark border px-3 py-1.5 shadow-xs fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 11.5px; border-color: #cbd5e1 !important; border-radius: 20px;">
                        <i class="fa-solid fa-bolt text-warning"></i> Best Rates Guaranteed
                    </span>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if(!empty($popularAreas)): ?>
            <div class="d-flex align-items-center gap-2 mb-4 flex-wrap" style="padding: 4px 0;">
                <span class="small text-muted fw-bold me-1" style="font-size: 12.5px;">Popular areas in <?php echo e($destination ?: 'city'); ?>:</span>
                <?php $__currentLoopData = $popularAreas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hood): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('search.index', array_merge(request()->query(), ['destination' => $destination, 'q' => $hood]))); ?>"
                       class="btn btn-sm btn-outline-secondary rounded-pill fw-semibold <?php if(request('q') == $hood): ?> active bg-primary text-white border-primary <?php endif; ?>"
                       style="font-size: 12px; border-color: #cbd5e1; color: #475569; padding: 5px 14px;">
                        <?php echo e($hood); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

            
            <?php
                $hasFilters = request()->hasAny(['q','min_price','max_price','guest_rating','star_rating','amenities','bed_type','room_feature','pay_later','free_cancel','property_type']);
                $clearBaseParams = array_filter(['destination'=>request('destination'),'check_in'=>request('check_in'),'check_out'=>request('check_out'),'guests'=>request('guests'),'rooms'=>request('rooms')]);
            ?>
            <?php if($hasFilters): ?>
            <div class="d-flex align-items-center gap-1 mb-3 pb-2 border-bottom flex-wrap">
                <span class="small text-muted fw-bold me-1" style="font-size: 11.5px;">Active filters:</span>

                <?php if(request('q')): ?>
                    <a href="<?php echo e(route('search.index', request()->except('q'))); ?>" class="badge bg-light text-dark border px-2 py-1 rounded-pill text-decoration-none fw-semibold" style="font-size: 11px;">
                        "<?php echo e(Str::limit(request('q'), 20)); ?>" <i class="fa-solid fa-xmark text-danger ms-1"></i>
                    </a>
                <?php endif; ?>

                <?php if(request('min_price') && (float)request('min_price') > 0): ?>
                    <a href="<?php echo e(route('search.index', request()->except('min_price'))); ?>" class="badge bg-light text-dark border px-2 py-1 rounded-pill text-decoration-none fw-semibold" style="font-size: 11px;">
                        Min ৳<?php echo e(number_format((float)request('min_price'))); ?> <i class="fa-solid fa-xmark text-danger ms-1"></i>
                    </a>
                <?php endif; ?>

                <?php if(request('max_price') && (float)request('max_price') < 10000000): ?>
                    <a href="<?php echo e(route('search.index', request()->except('max_price'))); ?>" class="badge bg-light text-dark border px-2 py-1 rounded-pill text-decoration-none fw-semibold" style="font-size: 11px;">
                        Max ৳<?php echo e(number_format((float)request('max_price'))); ?> <i class="fa-solid fa-xmark text-danger ms-1"></i>
                    </a>
                <?php endif; ?>

                <?php $__currentLoopData = (array)request('guest_rating',[]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('search.index', array_merge(request()->except('guest_rating'), ['guest_rating' => array_diff((array)request('guest_rating',[]), [$gr])]))); ?>" class="badge bg-light text-dark border px-2 py-1 rounded-pill text-decoration-none fw-semibold" style="font-size: 11px;">
                        Rating <?php echo e($gr); ?>+ <i class="fa-solid fa-xmark text-danger ms-1"></i>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php $__currentLoopData = (array)request('star_rating',[]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('search.index', array_merge(request()->except('star_rating'), ['star_rating' => array_diff((array)request('star_rating',[]), [$sr])]))); ?>" class="badge bg-light text-dark border px-2 py-1 rounded-pill text-decoration-none fw-semibold" style="font-size: 11px;">
                        <?php echo e($sr); ?>★ <i class="fa-solid fa-xmark text-danger ms-1"></i>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php $__currentLoopData = (array)request('property_type',[]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('search.index', array_merge(request()->except('property_type'), ['property_type' => array_diff((array)request('property_type',[]), [$pt])]))); ?>" class="badge bg-light text-dark border px-2 py-1 rounded-pill text-decoration-none fw-semibold" style="font-size: 11px;">
                        <?php echo e(ucfirst($pt)); ?> <i class="fa-solid fa-xmark text-danger ms-1"></i>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php $__currentLoopData = (array)request('amenities',[]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $am): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('search.index', array_merge(request()->except('amenities'), ['amenities' => array_diff((array)request('amenities',[]), [$am])]))); ?>" class="badge bg-light text-dark border px-2 py-1 rounded-pill text-decoration-none fw-semibold" style="font-size: 11px;">
                        <?php echo e($am); ?> <i class="fa-solid fa-xmark text-danger ms-1"></i>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php $__currentLoopData = (array)request('bed_type',[]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('search.index', array_merge(request()->except('bed_type'), ['bed_type' => array_diff((array)request('bed_type',[]), [$bt])]))); ?>" class="badge bg-light text-dark border px-2 py-1 rounded-pill text-decoration-none fw-semibold" style="font-size: 11px;">
                        <?php echo e(ucfirst($bt)); ?> bed <i class="fa-solid fa-xmark text-danger ms-1"></i>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php $__currentLoopData = (array)request('room_feature',[]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('search.index', array_merge(request()->except('room_feature'), ['room_feature' => array_diff((array)request('room_feature',[]), [$rf])]))); ?>" class="badge bg-light text-dark border px-2 py-1 rounded-pill text-decoration-none fw-semibold" style="font-size: 11px;">
                        <?php echo e(str_replace('_',' ',ucfirst($rf))); ?> <i class="fa-solid fa-xmark text-danger ms-1"></i>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php if(request('pay_later')): ?>
                    <a href="<?php echo e(route('search.index', request()->except('pay_later'))); ?>" class="badge bg-light text-dark border px-2 py-1 rounded-pill text-decoration-none fw-semibold" style="font-size: 11px;">
                        No credit card <i class="fa-solid fa-xmark text-danger ms-1"></i>
                    </a>
                <?php endif; ?>

                <?php if(request('free_cancel')): ?>
                    <a href="<?php echo e(route('search.index', request()->except('free_cancel'))); ?>" class="badge bg-light text-dark border px-2 py-1 rounded-pill text-decoration-none fw-semibold" style="font-size: 11px;">
                        Free cancel <i class="fa-solid fa-xmark text-danger ms-1"></i>
                    </a>
                <?php endif; ?>

                <a href="<?php echo e(route('search.index', $clearBaseParams)); ?>" class="text-decoration-none fw-bold ms-auto text-danger" style="font-size: 11.5px; white-space:nowrap;">
                    <i class="fa-solid fa-rotate-left me-1"></i>Clear all
                </a>
            </div>
            <?php endif; ?>

            
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div>
                    <h4 class="fw-bold mb-0 text-dark" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px;">
                        <span id="resultsCount"><?php echo e($searchResults['total_count']); ?></span> properties in <?php echo e($destination ?: 'Bangladesh'); ?>

                    </h4>
                </div>
                <div class="d-flex align-items-center gap-2">
                    
                    <button class="btn btn-outline-secondary btn-sm rounded-pill fw-bold d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFilterOffcanvas" aria-controls="mobileFilterOffcanvas" style="font-size:12px;">
                        <i class="fa-solid fa-sliders me-1"></i> Filters
                    </button>
                    <label class="small text-muted fw-semibold mb-0 d-none d-sm-inline">Sort by:</label>
                    <select id="sortBySelect" class="form-select form-select-sm rounded-3 fw-semibold" style="width: 190px; font-size: 13px; border-color: #cbd5e1;">
                        <option value="featured" <?php echo e(request('sort_by','featured') == 'featured' ? 'selected' : ''); ?>>Best match</option>
                        <option value="price_low" <?php echo e(request('sort_by') == 'price_low' ? 'selected' : ''); ?>>Lowest price first</option>
                        <option value="price_high" <?php echo e(request('sort_by') == 'price_high' ? 'selected' : ''); ?>>Highest price first</option>
                        <option value="rating" <?php echo e(request('sort_by') == 'rating' ? 'selected' : ''); ?>>Highest guest rating</option>
                        <option value="newest" <?php echo e(request('sort_by') == 'newest' ? 'selected' : ''); ?>>Newest listings</option>
                    </select>
                </div>
            </div>

            
            <div class="d-flex align-items-center gap-2 overflow-x-auto pb-2 mb-2" style="scrollbar-width: none;">
                <?php
                    $quickAmenities = (array) request('amenities', []);
                    $hasBreakfast = in_array('Breakfast included', $quickAmenities);
                    $hasPool = in_array('Swimming pool', $quickAmenities);
                    $hasWifi = in_array('Free WiFi', $quickAmenities);
                    $isFreeCancel = request('free_cancel') == 1;
                    $isPayLater = request('pay_later') == 1;
                    $is5Star = request('star_rating') == '5';
                    $isTopRated = request('guest_rating') == '9';
                ?>

                
                <a href="<?php echo e(route('search.index', array_merge(request()->query(), ['free_cancel' => $isFreeCancel ? null : 1]))); ?>" 
                   class="btn btn-sm rounded-pill fw-semibold text-nowrap d-inline-flex align-items-center shadow-xs" 
                   style="font-size: 12px; padding: 6px 14px; <?php echo e($isFreeCancel ? 'background: #2067e1; color: #fff; border-color: #2067e1;' : 'background: #fff; color: #334155; border: 1px solid #cbd5e1;'); ?>">
                    <i class="fa-solid fa-check <?php echo e($isFreeCancel ? 'text-white' : 'text-success'); ?>" style="margin-right: 7px; font-size: 11px;"></i>
                    <span>Free cancellation</span>
                </a>

                
                <?php
                    $breakfastParams = request()->query();
                    if ($hasBreakfast) {
                        $breakfastParams['amenities'] = array_values(array_diff($quickAmenities, ['Breakfast included']));
                    } else {
                        $breakfastParams['amenities'] = array_merge($quickAmenities, ['Breakfast included']);
                    }
                ?>
                <a href="<?php echo e(route('search.index', $breakfastParams)); ?>" 
                   class="btn btn-sm rounded-pill fw-semibold text-nowrap d-inline-flex align-items-center shadow-xs" 
                   style="font-size: 12px; padding: 6px 14px; <?php echo e($hasBreakfast ? 'background: #2067e1; color: #fff; border-color: #2067e1;' : 'background: #fff; color: #334155; border: 1px solid #cbd5e1;'); ?>">
                    <i class="fa-solid fa-mug-saucer <?php echo e($hasBreakfast ? 'text-white' : 'text-warning'); ?>" style="margin-right: 7px; font-size: 11.5px;"></i>
                    <span>Breakfast included</span>
                </a>

                
                <?php
                    $poolParams = request()->query();
                    if ($hasPool) {
                        $poolParams['amenities'] = array_values(array_diff($quickAmenities, ['Swimming pool']));
                    } else {
                        $poolParams['amenities'] = array_merge($quickAmenities, ['Swimming pool']);
                    }
                ?>
                <a href="<?php echo e(route('search.index', $poolParams)); ?>" 
                   class="btn btn-sm rounded-pill fw-semibold text-nowrap d-inline-flex align-items-center shadow-xs" 
                   style="font-size: 12px; padding: 6px 14px; <?php echo e($hasPool ? 'background: #2067e1; color: #fff; border-color: #2067e1;' : 'background: #fff; color: #334155; border: 1px solid #cbd5e1;'); ?>">
                    <i class="fa-solid fa-person-swimming <?php echo e($hasPool ? 'text-white' : 'text-info'); ?>" style="margin-right: 7px; font-size: 12px;"></i>
                    <span>Swimming pool</span>
                </a>

                
                <a href="<?php echo e(route('search.index', array_merge(request()->query(), ['pay_later' => $isPayLater ? null : 1]))); ?>" 
                   class="btn btn-sm rounded-pill fw-semibold text-nowrap d-inline-flex align-items-center shadow-xs" 
                   style="font-size: 12px; padding: 6px 14px; <?php echo e($isPayLater ? 'background: #2067e1; color: #fff; border-color: #2067e1;' : 'background: #fff; color: #334155; border: 1px solid #cbd5e1;'); ?>">
                    <i class="fa-solid fa-wallet <?php echo e($isPayLater ? 'text-white' : 'text-success'); ?>" style="margin-right: 7px; font-size: 11.5px;"></i>
                    <span>Pay at hotel</span>
                </a>

                
                <a href="<?php echo e(route('search.index', array_merge(request()->query(), ['guest_rating' => $isTopRated ? null : 9]))); ?>" 
                   class="btn btn-sm rounded-pill fw-semibold text-nowrap d-inline-flex align-items-center shadow-xs" 
                   style="font-size: 12px; padding: 6px 14px; <?php echo e($isTopRated ? 'background: #2067e1; color: #fff; border-color: #2067e1;' : 'background: #fff; color: #334155; border: 1px solid #cbd5e1;'); ?>">
                    <i class="fa-solid fa-star text-warning" style="margin-right: 7px; font-size: 11px;"></i>
                    <span>Superb 9.0+</span>
                </a>

                
                <a href="<?php echo e(route('search.index', array_merge(request()->query(), ['star_rating' => $is5Star ? null : 5]))); ?>" 
                   class="btn btn-sm rounded-pill fw-semibold text-nowrap d-inline-flex align-items-center shadow-xs" 
                   style="font-size: 12px; padding: 6px 14px; <?php echo e($is5Star ? 'background: #2067e1; color: #fff; border-color: #2067e1;' : 'background: #fff; color: #334155; border: 1px solid #cbd5e1;'); ?>">
                    <i class="fa-solid fa-crown text-warning" style="margin-right: 7px; font-size: 11.5px;"></i>
                    <span>5-Star Stays</span>
                </a>
            </div>

            
            <div class="d-flex flex-column gap-3" id="propertyCardsFeed">
                <?php $__empty_1 = true; $__currentLoopData = $searchResults['merged_results']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php echo $__env->make('components.search.property-card', ['item' => $item], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    
                    <?php
                        $suggestedFallback = \App\Models\Property::select([
                                'id','name','slug','type','city','address',
                                'star_rating','rating_score','total_reviews',
                                'price_per_night','original_price','primary_image',
                                'free_cancellation','no_credit_card_required',
                                'latitude','longitude','status',
                            ])
                            ->active()
                            ->orderByDesc('rating_score')
                            ->limit(4)
                            ->get();

                    ?>
                    <div class="card border-0 shadow-xs rounded-4 p-4 text-center bg-white my-2">
                        <div class="mb-3" style="font-size: 48px;">🔍</div>
                        <h5 class="fw-bold text-dark mb-1">No properties found in "<?php echo e($destination ?: 'this area'); ?>"</h5>
                        <p class="text-secondary small mb-3">Try removing filters, expanding your price range, or explore nearby destinations below.</p>
                        <div class="d-flex gap-2 flex-wrap justify-content-center mb-2">
                            <a href="<?php echo e(route('search.index', array_filter(['destination'=>$destination,'check_in'=>request('check_in'),'check_out'=>request('check_out'),'guests'=>request('guests')]))); ?>" class="btn btn-sm btn-outline-primary rounded-pill fw-bold" style="font-size:12px;">Remove all filters</a>
                            <a href="<?php echo e(route('search.index', ['destination'=>'Cox\'s Bazar','check_in'=>request('check_in'),'check_out'=>request('check_out'),'guests'=>request('guests')])); ?>" class="btn btn-sm btn-outline-secondary rounded-pill fw-semibold" style="font-size:12px;">Try Cox's Bazar</a>
                            <a href="<?php echo e(route('search.index', ['destination'=>'Dhaka','check_in'=>request('check_in'),'check_out'=>request('check_out'),'guests'=>request('guests')])); ?>" class="btn btn-sm btn-outline-secondary rounded-pill fw-semibold" style="font-size:12px;">Try Dhaka</a>
                        </div>
                    </div>
                    <?php if($suggestedFallback->count() > 0): ?>
                    <div class="mt-3">
                        <h6 class="fw-bold text-dark mb-3" style="font-size:15px; font-family:'Plus Jakarta Sans',sans-serif;">
                            <i class="fa-solid fa-star text-warning me-2"></i>You might also like — Top Rated Stays
                        </h6>
                        <div class="d-flex flex-column gap-3">
                            <?php $__currentLoopData = $suggestedFallback; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo $__env->make('components.search.property-card', ['item' => $item], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <?php if(isset($searchResults['paginator']) && $searchResults['paginator']->hasPages()): ?>
                <div class="mt-4 d-flex justify-content-center" id="paginationContainer">
                    <?php echo e($searchResults['paginator']->appends(request()->query())->links('vendor.pagination.prime-booking')); ?>

                </div>
            <?php endif; ?>

        </div>

    </div>
</div>

<style>
    #agodaMapFilterCol::-webkit-scrollbar, #agodaMapCardsCol::-webkit-scrollbar {
        width: 5px;
    }
    #agodaMapFilterCol::-webkit-scrollbar-track, #agodaMapCardsCol::-webkit-scrollbar-track {
        background: #f8fafc;
    }
    #agodaMapFilterCol::-webkit-scrollbar-thumb, #agodaMapCardsCol::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    #agodaMapFilterCol::-webkit-scrollbar-thumb:hover, #agodaMapCardsCol::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>


<div class="modal fade" id="interactiveMapModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(4px); background: rgba(0, 0, 0, 0.7);">
    <div class="modal-dialog modal-dialog-centered" style="width: 96vw; max-width: 1540px; height: 95vh; margin: 2.5vh auto;">
        <div class="modal-content border-0 overflow-hidden" style="height: 100%; border-radius: 0 !important; box-shadow: 0 25px 65px rgba(0, 0, 0, 0.6); background: #ffffff;">
            
            
            <div class="d-flex justify-content-between align-items-center px-4 bg-white border-bottom" style="z-index: 1050; height: 48px; border-color: #e2e8f0 !important;">
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold d-flex align-items-center" id="toggleMapFilterSidebarBtn" style="border: 1px solid #bfdbfe; color: #2067e1; font-size: 12px; background: #f0f7ff;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;">
                            <line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line>
                        </svg>
                        <span id="toggleFilterBtnText">Hide filters</span>
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-link p-0 text-primary fs-4 text-decoration-none d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="Close" style="color: #2067e1 !important; line-height: 1; width: 30px; height: 30px;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            
            <div class="modal-body p-0 d-flex overflow-hidden" style="height: calc(100% - 48px);">
                
                
                <div id="agodaMapFilterCol" class="bg-white border-end overflow-y-auto" style="width: 280px; min-width: 280px; padding: 16px 20px !important; flex-shrink: 0; transition: all 0.3s ease; border-color: #e2e8f0 !important;">
                    
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center rounded-pill" style="background: #f8fafc; height: 38px; padding: 0 12px; border: 1px solid #cbd5e1;">
                            <i class="fa-solid fa-magnifying-glass text-secondary" style="font-size: 13px; margin-right: 8px; flex-shrink: 0;"></i>
                            <input type="text" id="mapSearchInput" class="border-0 bg-transparent w-100 shadow-none text-dark fw-medium" placeholder="Text search" style="font-size: 12.5px; outline: none; padding: 0;" onkeyup="filterMapItems()">
                        </div>
                    </div>

                    
                    <div class="mb-3 pb-2.5 border-bottom" style="border-color: #f1f5f9 !important;">
                        <label class="fw-bold text-dark d-block mb-1.5" style="font-size: 12.5px;">Your budget (per night)</label>
                        <input type="range" id="mapPriceRange" class="form-range my-1.5" min="0" max="<?php echo e($maxConvertedPrice ?? 1000); ?>" step="<?php echo e(($maxConvertedPrice ?? 1000) > 5000 ? 100 : 5); ?>" value="<?php echo e($maxConvertedPrice ?? 1000); ?>" oninput="onMapSliderChange(this.value);">
                        <div class="d-flex align-items-center gap-2 mt-1.5">
                            <div class="d-flex flex-column gap-1" style="width: 95px;">
                                <div class="border rounded p-1 px-2 d-flex align-items-center justify-content-between" style="background: #ffffff; border-color: #cbd5e1 !important; height: 28px;">
                                    <span class="text-secondary fw-semibold" style="font-size: 10px; margin-right: 4px;"><?php echo e(\App\Helpers\CurrencyHelper::current()); ?></span>
                                    <input type="number" id="mapMinBudgetInput" class="form-control border-0 p-0 shadow-none fw-bold text-end text-dark" value="0" min="0" max="1000000" style="font-size: 11.5px; width: 55px; background: transparent;" oninput="onMapTypedBudgetChange()">
                                </div>
                                <div class="border rounded p-1 px-2 d-flex align-items-center justify-content-between" style="background: #ffffff; border-color: #cbd5e1 !important; height: 28px;">
                                    <span class="text-secondary fw-semibold" style="font-size: 10px; margin-right: 4px;"><?php echo e(\App\Helpers\CurrencyHelper::current()); ?></span>
                                    <input type="number" id="mapMaxBudgetInput" class="form-control border-0 p-0 shadow-none fw-bold text-end text-dark" value="<?php echo e($maxConvertedPrice ?? 1000); ?>" min="0" max="1000000" style="font-size: 11.5px; width: 55px; background: transparent;" oninput="onMapTypedBudgetChange()">
                                </div>
                            </div>
                            <span class="text-muted" style="letter-spacing: -1px; font-size: 10.5px;">----------</span>
                        </div>
                    </div>

                    
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-1">
                        <span class="fw-bold text-dark" style="font-size: 12.5px;">Your filters</span>
                        <a href="javascript:void(0);" onclick="clearAllMapFilters();" class="text-primary text-decoration-none fw-bold" style="font-size: 11px;">CLEAR</a>
                    </div>
                    <div id="activeMapFiltersBadgeContainer" class="mb-2.5 d-flex flex-wrap gap-1">
                        
                    </div>

                    
                    <div class="mb-3 pb-2.5 border-bottom" style="border-color: #f1f5f9 !important;">
                        <label class="fw-bold text-dark d-block mb-2" style="font-size: 12.5px;">Popular filters for <?php echo e($destination ?: 'Chittagong'); ?></label>
                        <div class="d-flex flex-column gap-2">
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check" value="breakfast" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Breakfast included</span>
                                <span class="text-muted small">(<?php echo e(count($searchResults['merged_results'])); ?>)</span>
                            </label>
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check" value="free_cancel" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Free cancellation</span>
                                <span class="text-muted small">(<?php echo e(count($searchResults['merged_results'])); ?>)</span>
                            </label>
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check" value="pay_later" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Pay at the hotel</span>
                                <span class="text-muted small">(<?php echo e(count($searchResults['merged_results'])); ?>)</span>
                            </label>
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check" value="rating_8" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Guest rating: 8+ Excellent</span>
                                <span class="text-muted small">(<?php echo e(max(1, (int)(count($searchResults['merged_results']) * 0.7))); ?>)</span>
                            </label>
                        </div>
                    </div>

                    
                    <div class="mb-3 pb-2.5 border-bottom" style="border-color: #f1f5f9 !important;">
                        <label class="fw-bold text-dark d-block mb-2" style="font-size: 12.5px;">Property type</label>
                        <div class="d-flex flex-column gap-2">
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check" value="type_entire" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Entire homes &amp; apartments</span>
                                <span class="text-muted small">(4)</span>
                            </label>
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check" value="type_apartment" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Apartment/Flat</span>
                                <span class="text-muted small">(4)</span>
                            </label>
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check" value="type_hotel" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Hotels &amp; Resorts</span>
                                <span class="text-muted small">(<?php echo e(count($searchResults['merged_results'])); ?>)</span>
                            </label>
                        </div>
                    </div>

                    
                    <div class="mb-3 pb-2.5 border-bottom" style="border-color: #f1f5f9 !important;">
                        <label class="fw-bold text-dark d-block mb-2" style="font-size: 12.5px;">Room amenities</label>
                        <div class="d-flex flex-column gap-2">
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check map-amenity-check" value="ac" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Air conditioning</span>
                                <span class="text-muted small">(5)</span>
                            </label>
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check map-amenity-check" value="washing_machine" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Washing machine</span>
                                <span class="text-muted small">(4)</span>
                            </label>
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check map-amenity-check" value="heating" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Heating</span>
                                <span class="text-muted small">(5)</span>
                            </label>
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check map-amenity-check" value="wifi" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Free Wi-Fi</span>
                                <span class="text-muted small">(12)</span>
                            </label>
                        </div>
                    </div>

                </div>

                
                <div id="agodaMapCardsCol" class="bg-white border-end overflow-y-auto" style="width: 460px; min-width: 460px; padding: 16px 20px !important; flex-shrink: 0; border-color: #e2e8f0 !important;">
                    
                    
                    <div class="mb-2.5 pb-2 border-bottom" style="border-color: #f1f5f9 !important;">
                        <div class="fw-bold text-dark" style="font-size: 14.5px; margin-bottom: 2px;">
                            <span id="visibleCardsCount"><?php echo e(count($searchResults['merged_results'])); ?></span> properties available
                        </div>
                        <div class="text-secondary d-flex align-items-center gap-1.5 mb-2" style="font-size: 11.5px; color: #64748b !important;">
                            <i class="fa-regular fa-calendar" style="font-size: 11px; margin-right: 4px;"></i> <?php echo e($checkinCarbon->diffInDays($checkoutCarbon)); ?> nights (<?php echo e($checkinCarbon->format('M j')); ?> – <?php echo e($checkoutCarbon->format('M j')); ?>)
                        </div>
                        <div class="position-relative mt-1">
                            <label style="position: absolute; top: -7px; left: 10px; background: #ffffff; padding: 0 4px; font-size: 10px; font-weight: 600; color: #64748b; z-index: 1;">Sort by</label>
                            <select id="mapSortSelect" class="form-select form-select-sm fw-bold text-dark shadow-none" style="height: 38px; border-radius: 4px; border-color: #cbd5e1; font-size: 12.5px; padding-left: 10px;" onchange="sortMapItems(this.value)">
                                <option value="recommended">Recommended</option>
                                <option value="price_low">Lowest price first</option>
                                <option value="rating_high">Guest rating</option>
                            </select>
                        </div>
                    </div>

                    
                    <div id="agodaMapCardsList" class="d-flex flex-column gap-2">
                        
                    </div>
                </div>

                
                <div class="flex-grow-1 position-relative h-100" style="background:#e5e7eb;">
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

                    
                    <div class="position-absolute" style="top: 18px; left: 18px; z-index: 1000; width: 290px;">
                        <div class="bg-white rounded-pill d-flex align-items-center border" style="height: 44px; padding: 0 16px; box-shadow: 0 4px 16px rgba(0,0,0,0.14); border-color: #cbd5e1 !important;">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#2067e1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 10px; min-width: 17px;">
                                <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="mapHeaderSearchInput" class="border-0 bg-transparent w-100 text-dark fw-medium" placeholder="Search on map..." autocomplete="off" style="outline:none; font-size:13px; padding: 0;" onkeyup="handleMapSearchAutocomplete(this.value)">
                            <button type="button" class="btn btn-link p-0 text-muted d-none ms-2" id="clearMapSearchBtn" onclick="clearMapSearch()" style="text-decoration:none;"><i class="fa-solid fa-circle-xmark fs-6"></i></button>
                        </div>

                        
                        <div id="agodaMapSuggestDropdown" class="bg-white rounded-3 shadow-lg border mt-1.5 overflow-hidden d-none" style="max-height: 280px; overflow-y: auto; border-color: #cbd5e1 !important;">
                            <div id="agodaMapSuggestList" class="py-1"></div>
                        </div>
                    </div>

                    <div id="agodaMapContainer" style="width: 100%; height: 100%; z-index: 1;"></div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php
    $defaultLat = 22.3569; // Chattogram / Bangladesh Default Center
    $defaultLng = 91.7832;
    $currCode = \App\Helpers\CurrencyHelper::current();

    $mapProperties = collect($searchResults['merged_results'])->map(function($p, $idx) use ($defaultLat, $defaultLng, $currCode) {
        $isObj       = is_object($p);
        $id          = $isObj ? ($p->id ?? $idx + 1) : ($p['id'] ?? $idx + 1);
        $name        = $isObj ? ($p->name ?? 'Property') : ($p['name'] ?? 'Property');
        $slug        = $isObj ? ($p->slug ?? $p->id ?? 1) : ($p['slug'] ?? $p['id'] ?? 1);
        $rawPriceBDT = (float)($isObj ? ($p->price_per_night ?? $p->price ?? 0) : ($p['price_per_night'] ?? $p['price'] ?? 0));
        if ($rawPriceBDT <= 0) $rawPriceBDT = 3500;
        $convertedPrice = round(\App\Services\CurrencyService::convert($rawPriceBDT, 'BDT', $currCode), 2);
        $formattedPrice = \App\Services\CurrencyService::format($rawPriceBDT);
        $city        = $isObj ? ($p->city ?? $p->address ?? 'Chattogram') : ($p['city'] ?? $p['address'] ?? 'Chattogram');
        $image       = $isObj ? ($p->primary_image ?? '') : ($p['primary_image'] ?? '');
        $score       = (float)($isObj ? ($p->rating_score ?? 8.5) : ($p['rating_score'] ?? 8.5));
        $reviews     = (int)($isObj ? ($p->total_reviews ?? 4) : ($p['total_reviews'] ?? 4));
        $lat         = (float)($isObj ? ($p->latitude  ?? 0) : ($p['latitude']  ?? 0));
        $lng         = (float)($isObj ? ($p->longitude ?? 0) : ($p['longitude'] ?? 0));
        $type        = $isObj ? ($p->type ?? 'hotel') : ($p['type'] ?? 'hotel');
        $freeCancel  = (bool)($isObj ? ($p->free_cancellation ?? true) : ($p['free_cancellation'] ?? true));
        $payLater    = (bool)($isObj ? ($p->no_credit_card_required ?? true) : ($p['no_credit_card_required'] ?? true));

        return [
            'id'          => $id,
            'name'        => $name,
            'price_raw'   => $convertedPrice,
            'price'       => $formattedPrice,
            'city'        => $city,
            'image'       => $image ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=500',
            'score'       => $score > 0 ? number_format($score, 1) : '8.8',
            'rating_text' => $score >= 9 ? 'Exceptional' : ($score >= 8 ? 'Excellent' : 'Very Good'),
            'reviews'     => $reviews,
            'url'         => route('property.show', $slug),
            'lat'         => $lat,
            'lng'         => $lng,
            'has_gps'     => ($lat !== 0.0 && $lng !== 0.0),
            'type'        => ucfirst($type),
            'free_cancel' => $freeCancel,
            'pay_later'   => $payLater,
        ];
    });

    $maxConvertedPrice = ceil(($mapProperties->max('price_raw') ?: 500) * 1.2);

    $gpsProps = $mapProperties->where('has_gps', true);
    if ($gpsProps->count() > 0) {
        $centerLat = $gpsProps->avg('lat');
        $centerLng = $gpsProps->avg('lng');
    } else {
        $centerLat = $defaultLat;
        $centerLng = $defaultLng;
    }

    // Scatter properties that lack exact coordinates around center
    $mapProperties = $mapProperties->map(function($p, $idx) use ($centerLat, $centerLng) {
        if (!$p['has_gps']) {
            $latOffset = (($idx % 5) - 2) * 0.012;
            $lngOffset = (($idx % 2 === 0 ? 1 : -1) * (($idx % 4) + 1)) * 0.009;
            $p['lat'] = round($centerLat + $latOffset, 6);
            $p['lng'] = round($centerLng + $lngOffset, 6);
        }
        return $p;
    });
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var allProperties = <?php echo json_encode($mapProperties, 15, 512) ?>;
        var currentFiltered = [...allProperties];
        var mapModal = document.getElementById('interactiveMapModal');
        var mapInitialized = false;
        var map;
        var miniMap;
        var markersMap = {};

        // ── 1. Mini Sidebar Map Live Scatter Renderer ──
        var miniMapEl = document.getElementById('agodaMiniSidebarMap');
        if (miniMapEl) {
            try {
                miniMap = L.map('agodaMiniSidebarMap', {
                    center: [<?php echo e($centerLat); ?>, <?php echo e($centerLng); ?>],
                    zoom: 11,
                    zoomControl: false,
                    attributionControl: false,
                    dragging: false,
                    touchZoom: false,
                    doubleClickZoom: false,
                    scrollWheelZoom: false,
                    boxZoom: false,
                    keyboard: false
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 18
                }).addTo(miniMap);

                allProperties.forEach(function(item) {
                    var dotIcon = L.divIcon({
                        className: 'agoda-mini-dot',
                        html: '<div style="width:9px;height:9px;background:#2067e1;border:1.5px solid #ffffff;border-radius:50%;box-shadow:0 1px 4px rgba(0,0,0,0.4);"></div>',
                        iconSize: [9, 9],
                        iconAnchor: [4.5, 4.5]
                    });
                    L.marker([item.lat, item.lng], {icon: dotIcon}).addTo(miniMap);
                });
            } catch(e) {
                console.warn('MiniMap init:', e);
            }
        }

        // ── 2. Render Left Column Property Cards ──
        window.renderMapCards = function(items) {
            var container = document.getElementById('agodaMapCardsList');
            var countEl = document.getElementById('visibleCardsCount');
            var modalCount = document.getElementById('mapModalPropertyCount');
            if (countEl) countEl.textContent = items.length;
            if (modalCount) modalCount.textContent = items.length;
            if (!container) return;

            if (items.length === 0) {
                container.innerHTML = '<div class="text-center p-4 text-muted" style="font-size:12.5px;"><i class="fa-solid fa-hotel fs-3 mb-2 opacity-50"></i><br>No properties found matching filter.</div>';
                return;
            }

            var html = '';
            items.forEach(function(item, idx) {
                var isFirst = (idx === 0);
                html += `
                    <div class="card agoda-map-card" id="mapCard_${item.id}" style="border: 1.5px solid ${isFirst ? '#2067e1' : '#e2e8f0'}; border-radius: 4px; cursor: pointer; background: #ffffff; display: flex; flex-direction: row; height: 145px; margin-bottom: 8px; transition: all 0.15s ease; overflow: hidden; position: relative; ${isFirst ? 'box-shadow: 0 2px 10px rgba(32, 103, 225, 0.14);' : ''}" onmouseenter="highlightMarker(${item.id})" onmouseleave="unhighlightMarker(${item.id})" onclick="window.open('${item.url}', '_blank')">
                        <!-- Left Hotel Photo (135px width, 100% height) -->
                        <div style="width: 135px; min-width: 135px; height: 100%; position: relative; overflow: hidden; background: #f1f5f9;">
                            <img src="${item.image}" alt="${item.name}" style="width: 100%; height: 100%; object-fit: cover;">
                            <button type="button" class="btn position-absolute top-0 end-0 m-1 p-0 rounded-circle" style="background: rgba(255,255,255,0.92); border: none; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; z-index: 5;" onclick="event.stopPropagation(); toggleMapCardWishlist(this, ${item.id})" title="Save to wishlist">
                                <i class="fa-regular fa-heart text-danger" style="font-size: 11px;"></i>
                            </button>
                        </div>
                        <!-- Right Info -->
                        <div style="flex: 1; padding: 8px 12px; display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 6px;">
                                    <h6 style="font-size: 13.5px; font-weight: 700; color: #1e293b; margin: 0; line-height: 1.25; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;" title="${item.name}">${item.name}</h6>
                                    <div style="text-align: right; flex-shrink: 0;">
                                        <div style="font-size: 14.5px; font-weight: 800; color: #1e293b; line-height: 1;">${item.price}</div>
                                        <div style="font-size: 9px; color: #64748b; margin-top: 2px; line-height: 1.1;">Per night before taxes<br>and fees</div>
                                    </div>
                                </div>
                                <div style="color: #f59e0b; font-size: 10.5px; margin-top: 1px; letter-spacing: 0.5px;">★★★</div>
                                <div style="margin-top: 2px;">
                                    <span style="color: #2067e1; font-size: 13px; font-weight: 800;">${item.score} ${item.rating_text}</span>
                                    <div style="font-size: 10.5px; color: #64748b;">${item.reviews} reviews</div>
                                </div>
                            </div>
                            <div style="margin-top: 2px;">
                                ${item.free_cancel ? '<div style="color: #15803d; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 5px;"><span style="display: inline-block; width: 5px; height: 5px; background: #15803d; border-radius: 50%;"></span> Free cancellation</div>' : ''}
                                <div style="color: #15803d; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 5px; margin-top: 1px;"><span style="display: inline-block; width: 5px; height: 5px; background: #15803d; border-radius: 50%;"></span> Breakfast</div>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        };

        // ── Wishlist toggle in map modal cards ──
        window.toggleMapCardWishlist = function(btn, propertyId) {
            var icon = btn.querySelector('i');
            var isWished = icon.classList.contains('fa-solid');
            if (isWished) {
                icon.classList.replace('fa-solid', 'fa-regular');
                btn.style.background = 'rgba(255,255,255,0.85)';
            } else {
                icon.classList.replace('fa-regular', 'fa-solid');
                btn.style.background = 'rgba(255,255,255,0.95)';
                fetch('/wishlist/toggle/' + propertyId, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '', 'X-Requested-With': 'XMLHttpRequest' }
                }).catch(function() {});
            }
        };

        // ── 3. Highlighting and Marker Popups (Agoda 1:1 Parity) ──
        window.highlightMarker = function(id) {
            // Highlight Marker Pin on Map (Agoda Blue Active)
            if (markersMap[id]) {
                var el = markersMap[id].getElement();
                if (el) {
                    var badge = el.querySelector('.custom-agoda-pin-inner');
                    if (badge) {
                        badge.style.backgroundColor = '#2067e1';
                        badge.style.color = '#ffffff';
                        badge.style.borderColor = '#ffffff';
                        badge.style.transform = 'scale(1.12)';
                        badge.style.boxShadow = '0 6px 18px rgba(32, 103, 225, 0.45)';
                        badge.style.zIndex = '9999';
                    }
                }
            }

            // Highlight Property Card
            var card = document.getElementById('mapCard_' + id);
            if (card) {
                card.style.borderColor = '#2067e1';
                card.style.borderWidth = '2px';
                card.style.boxShadow = '0 6px 20px rgba(32, 103, 225, 0.15)';
            }
        };

        window.unhighlightMarker = function(id) {
            // Restore Marker Pin on Map
            if (markersMap[id]) {
                var el = markersMap[id].getElement();
                if (el) {
                    var badge = el.querySelector('.custom-agoda-pin-inner');
                    if (badge) {
                        badge.style.backgroundColor = '#ffffff';
                        badge.style.color = '#2067e1';
                        badge.style.borderColor = '#2067e1';
                        badge.style.transform = 'scale(1)';
                        badge.style.boxShadow = '0 2px 8px rgba(0,0,0,0.12)';
                        badge.style.zIndex = '1';
                    }
                }
            }

            // Restore Property Card
            var card = document.getElementById('mapCard_' + id);
            if (card) {
                card.style.borderColor = '#e2e8f0';
                card.style.borderWidth = '1px';
                card.style.boxShadow = 'none';
            }
        };

        window.focusProperty = function(id) {
            var prop = allProperties.find(p => p.id == id);
            if (prop && map) {
                map.setView([prop.lat, prop.lng], 16, {animate: true});
                if (markersMap[id]) markersMap[id].openPopup();
            }
        };

        window.openHotelOnMap = function(id) {
            var modalEl = document.getElementById('interactiveMapModal');
            if (!modalEl) return;
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
            setTimeout(function() {
                if (window.focusProperty) {
                    window.focusProperty(id);
                }
                if (window.highlightMarker) {
                    window.highlightMarker(id);
                }
                var card = document.getElementById('mapCard_' + id);
                if (card) {
                    card.scrollIntoView({behavior: 'smooth', block: 'center'});
                }
            }, 450);
        };

        window.onMapSliderChange = function(val) {
            var maxInput = document.getElementById('mapMaxBudgetInput');
            if (maxInput) maxInput.value = val;
            filterMapItems();
        };

        window.onMapTypedBudgetChange = function() {
            var maxVal = parseFloat(document.getElementById('mapMaxBudgetInput')?.value || 0);
            var slider = document.getElementById('mapPriceRange');
            if (slider && !isNaN(maxVal) && maxVal > 0) {
                slider.value = maxVal;
            }
            filterMapItems();
        };

        // ── 4. Filter and Sorting Algorithms (Agoda 1:1 Complete Parity) ──
        window.filterMapItems = function() {
            var query = (document.getElementById('mapSearchInput')?.value || '').toLowerCase().trim();
            var minBudget = parseFloat(document.getElementById('mapMinBudgetInput')?.value || 0);
            var maxBudget = parseFloat(document.getElementById('mapMaxBudgetInput')?.value || 999999);
            var checks = Array.from(document.querySelectorAll('.map-filter-check:checked')).map(c => c.value);
            var guestRatingRadio = document.querySelector('.map-filter-radio:checked')?.value;

            // Render active filter badges at top of sidebar
            var badgeContainer = document.getElementById('activeMapFiltersBadgeContainer');
            if (badgeContainer) {
                var badgesHtml = '';
                checks.forEach(function(val) {
                    var el = document.querySelector(`.map-filter-check[value="${val}"]`);
                    var label = el ? el.parentElement.textContent.trim() : val;
                    badgesHtml += `
                        <span class="badge d-inline-flex align-items-center gap-1.5 px-2 py-1" style="background:#e0edff; color:#2067e1; font-weight:600; font-size:11px; border-radius:14px;">
                            ${label} <i class="fa-solid fa-xmark" style="cursor:pointer;" onclick="uncheckFilter('${val}')"></i>
                        </span>
                    `;
                });
                badgeContainer.innerHTML = badgesHtml;
            }

            currentFiltered = allProperties.filter(function(item) {
                if (query && !item.name.toLowerCase().includes(query) && !item.city.toLowerCase().includes(query)) return false;
                if (item.price_raw < minBudget || item.price_raw > maxBudget) return false;
                if (checks.includes('free_cancel') && !item.free_cancel) return false;
                if (checks.includes('pay_later') && !item.pay_later) return false;
                if (checks.includes('rating_8') && parseFloat(item.score) < 8.0) return false;
                if (guestRatingRadio && parseFloat(item.score) < parseFloat(guestRatingRadio)) return false;
                if (checks.includes('type_hotel') && !item.type.toLowerCase().includes('hotel')) return false;
                if (checks.includes('type_apartment') && !item.type.toLowerCase().includes('apartment')) return false;
                if (checks.includes('type_entire') && !item.type.toLowerCase().includes('entire') && !item.type.toLowerCase().includes('villa')) return false;
                if (checks.includes('area_chittagong') && !item.city.toLowerCase().includes('chittagong') && !item.city.toLowerCase().includes('chattogram')) return false;
                if (checks.includes('area_kotwali') && !item.name.toLowerCase().includes('kotwali') && !item.city.toLowerCase().includes('kotwali')) return false;
                return true;
            });

            renderMapCards(currentFiltered);
            updateMapMarkers(currentFiltered);
        };

        window.updateMapMarkers = function(items) {
            if (!map || !markersMap) return;
            var visibleIds = new Set(items.map(function(i) { return i.id; }));
            Object.keys(markersMap).forEach(function(id) {
                var m = markersMap[id];
                if (!m) return;
                var el = m.getElement();
                if (visibleIds.has(parseInt(id)) || visibleIds.has(id)) {
                    if (!map.hasLayer(m)) map.addLayer(m);
                    if (el) el.style.display = 'block';
                } else {
                    if (el) el.style.display = 'none';
                }
            });
        };

        window.uncheckFilter = function(val) {
            var el = document.querySelector(`.map-filter-check[value="${val}"]`);
            if (el) {
                el.checked = false;
                filterMapItems();
            }
        };

        window.clearAllMapFilters = function() {
            document.querySelectorAll('.map-filter-check').forEach(c => c.checked = false);
            document.querySelectorAll('.map-filter-radio').forEach(r => r.checked = false);
            var searchInput = document.getElementById('mapSearchInput');
            var headerSearch = document.getElementById('mapHeaderSearchInput');
            var priceRange = document.getElementById('mapPriceRange');
            var minInput = document.getElementById('mapMinBudgetInput');
            var maxInput = document.getElementById('mapMaxBudgetInput');
            if (searchInput) searchInput.value = '';
            if (headerSearch) headerSearch.value = '';
            if (minInput) minInput.value = 0;
            if (maxInput) maxInput.value = <?php echo e($maxConvertedPrice ?? 1000); ?>;
            if (priceRange) priceRange.value = <?php echo e($maxConvertedPrice ?? 1000); ?>;
            filterMapItems();
        };

        window.sortMapItems = function(sortType) {
            if (sortType === 'price_low') {
                currentFiltered.sort((a, b) => a.price_raw - b.price_raw);
            } else if (sortType === 'rating_high') {
                currentFiltered.sort((a, b) => b.score - a.score);
            } else {
                currentFiltered.sort((a, b) => a.id - b.id);
            }
            renderMapCards(currentFiltered);
        };

        // ── 6. Agoda-Exact Smart Location Autocomplete Engine ──
        var geoLocations = [
            { name: "Kaptai", subtitle: "Bangladesh", type: "city", lat: 22.4967, lng: 92.2244 },
            { name: "Kolkata", subtitle: "West Bengal, India", type: "city", lat: 22.5726, lng: 88.3639 },
            { name: "Khagrachari", subtitle: "Bangladesh", type: "city", lat: 23.1322, lng: 91.9490 },
            { name: "Kaptai Lake", subtitle: "Bangladesh", type: "landmark", lat: 22.4967, lng: 92.2244 },
            { name: "Khulna", subtitle: "Bangladesh", type: "city", lat: 22.8456, lng: 89.5403 },
            { name: "Khulna City", subtitle: "Khulna, Bangladesh", type: "city", lat: 22.8456, lng: 89.5403 },
            { name: "Khulna Division", subtitle: "Bangladesh", type: "division", lat: 22.8456, lng: 89.5403 },
            { name: "Khulna Railway Station", subtitle: "Jashore Road, Khulna, Bangladesh", type: "station", lat: 22.8200, lng: 89.5500 },
            { name: "Chattogram", subtitle: "Chittagong Division, Bangladesh", type: "city", lat: 22.3569, lng: 91.7832 },
            { name: "Cox's Bazar", subtitle: "Chittagong Division, Bangladesh", type: "city", lat: 21.4272, lng: 92.0058 },
            { name: "Dhaka", subtitle: "Dhaka Division, Bangladesh", type: "city", lat: 23.8103, lng: 90.4125 },
            { name: "Sylhet", subtitle: "Sylhet Division, Bangladesh", type: "city", lat: 24.8949, lng: 91.8687 },
            { name: "Sreemangal", subtitle: "Moulvibazar, Sylhet, Bangladesh", type: "city", lat: 24.3065, lng: 91.7296 },
            { name: "Kuakata", subtitle: "Patuakhali, Barishal, Bangladesh", type: "city", lat: 21.8167, lng: 90.1167 },
            { name: "Bandarban", subtitle: "Chittagong Hill Tracts, Bangladesh", type: "city", lat: 22.1953, lng: 92.2184 },
            { name: "Rangamati", subtitle: "Chittagong Hill Tracts, Bangladesh", type: "city", lat: 22.6533, lng: 92.1789 },
            { name: "Saint Martin's Island", subtitle: "Bay of Bengal, Cox's Bazar", type: "landmark", lat: 20.6273, lng: 92.3225 },
            { name: "Agrabad Commercial Area", subtitle: "Chattogram, Bangladesh", type: "landmark", lat: 22.3275, lng: 91.8123 },
            { name: "GEC Circle", subtitle: "Chattogram, Bangladesh", type: "landmark", lat: 22.3587, lng: 91.8214 },
            { name: "Gulshan-2", subtitle: "Dhaka, Bangladesh", type: "landmark", lat: 23.7925, lng: 90.4167 }
        ];

        window.handleMapSearchAutocomplete = function(val) {
            var clearBtn = document.getElementById('clearMapSearchBtn');
            var dropdown = document.getElementById('agodaMapSuggestDropdown');
            var list = document.getElementById('agodaMapSuggestList');
            var sideSearch = document.getElementById('mapSearchInput');
            if (sideSearch) sideSearch.value = val;

            if (!val || val.trim().length === 0) {
                if (clearBtn) clearBtn.classList.add('d-none');
                if (dropdown) dropdown.classList.add('d-none');
                filterMapItems();
                return;
            }

            if (clearBtn) clearBtn.classList.remove('d-none');
            var query = val.toLowerCase().trim();

            // Filter predefined geo hierarchy + real property database
            var matchedGeos = geoLocations.filter(g => g.name.toLowerCase().includes(query) || g.subtitle.toLowerCase().includes(query));
            var matchedProps = allProperties.filter(p => p.name.toLowerCase().includes(query) || p.city.toLowerCase().includes(query));

            var results = [];
            matchedGeos.forEach(g => results.push({ title: g.name, subtitle: g.subtitle, lat: g.lat, lng: g.lng, isGeo: true }));
            matchedProps.slice(0, 5).forEach(p => results.push({ title: p.name, subtitle: p.city + ' • ' + p.price, id: p.id, lat: p.lat, lng: p.lng, isProp: true }));

            if (results.length === 0) {
                if (dropdown) dropdown.classList.add('d-none');
            } else {
                var html = '';
                results.forEach(function(r) {
                    html += `
                        <div class="px-3 py-2 agoda-suggest-item" style="cursor:pointer; border-bottom:1px solid #f1f5f9; transition:background 0.15s;" onmouseenter="this.style.background='#f8fafc'" onmouseleave="this.style.background='transparent'" onclick="selectMapSuggestion('${r.title.replace(/'/g, "\\'")}', ${r.lat}, ${r.lng}, ${r.id || 'null'})">
                            <div class="fw-bold text-dark" style="font-size:13.5px; font-family:'Plus Jakarta Sans',sans-serif; color:#0f172a;">${r.title}</div>
                            <small class="text-secondary d-block" style="font-size:11px; line-height:1.2;">${r.subtitle}</small>
                        </div>
                    `;
                });
                if (list) list.innerHTML = html;
                if (dropdown) dropdown.classList.remove('d-none');
            }

            filterMapItems();
        };

        window.selectMapSuggestion = function(title, lat, lng, propId) {
            var headerInput = document.getElementById('mapHeaderSearchInput');
            var sideInput = document.getElementById('mapSearchInput');
            var dropdown = document.getElementById('agodaMapSuggestDropdown');

            if (headerInput) headerInput.value = title;
            if (sideInput) sideInput.value = title;
            if (dropdown) dropdown.classList.add('d-none');

            if (map && lat && lng) {
                map.setView([lat, lng], propId ? 16 : 14, { animate: true });
                if (propId && markersMap[propId]) {
                    markersMap[propId].openPopup();
                }
            }
            filterMapItems();
        };

        window.clearMapSearch = function() {
            var headerInput = document.getElementById('mapHeaderSearchInput');
            var sideInput = document.getElementById('mapSearchInput');
            var clearBtn = document.getElementById('clearMapSearchBtn');
            var dropdown = document.getElementById('agodaMapSuggestDropdown');

            if (headerInput) headerInput.value = '';
            if (sideInput) sideInput.value = '';
            if (clearBtn) clearBtn.classList.add('d-none');
            if (dropdown) dropdown.classList.add('d-none');
            filterMapItems();
        };

        // ── 5. Master Modal Initialization & Toggle ──
        var filterCol = document.getElementById('agodaMapFilterCol');
        var toggleBtn = document.getElementById('toggleMapFilterSidebarBtn');
        var toggleText = document.getElementById('toggleFilterBtnText');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                if (filterCol.style.display === 'none') {
                    filterCol.style.display = 'block';
                    toggleText.textContent = 'Hide filters';
                } else {
                    filterCol.style.display = 'none';
                    toggleText.textContent = 'Show filters';
                }
                setTimeout(() => map && map.invalidateSize(), 300);
            });
        }

        mapModal.addEventListener('shown.bs.modal', function () {
            renderMapCards(allProperties);

            if (!mapInitialized) {
                map = L.map('agodaMapContainer', {zoomControl: false}).setView([<?php echo e($centerLat); ?>, <?php echo e($centerLng); ?>], 13);
                L.control.zoom({position: 'bottomright'}).addTo(map);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors | PRIME BOOKING'
                }).addTo(map);

                allProperties.forEach(function(item) {
                    var isSoldOut = item.price_raw <= 0 || item.price.includes('N/A');
                    var pinHtml = isSoldOut 
                        ? '<div class="custom-agoda-pin-inner" style="background:#ffffff;color:#64748b;font-weight:700;font-size:11px;padding:3px 8px;border-radius:18px;box-shadow:0 2px 8px rgba(0,0,0,0.12);border:1.5px solid #cbd5e1;cursor:pointer;white-space:nowrap;transition:all 0.2s;">Sold out</div>'
                        : '<div class="custom-agoda-pin-inner" style="background:#ffffff;color:#2067e1;font-weight:800;font-size:11.5px;padding:3px 9px;border-radius:18px;box-shadow:0 2px 8px rgba(0,0,0,0.12);border:1.5px solid #2067e1;cursor:pointer;white-space:nowrap;transition:all 0.2s;">' + item.price + '</div>';

                    var customIcon = L.divIcon({
                        className: 'custom-agoda-price-pin',
                        html: pinHtml,
                        iconSize: [80, 28],
                        iconAnchor: [40, 14]
                    });

                    var popupContent = `
                        <div style="width:200px;font-family:'Plus Jakarta Sans',sans-serif;">
                            <img src="${item.image}" style="width:100%;height:100px;object-fit:cover;border-radius:6px;margin-bottom:6px;">
                            <div style="font-weight:700;font-size:12.5px;color:#0f172a;line-height:1.2;margin-bottom:3px;">${item.name}</div>
                            <div style="font-size:11px;color:#64748b;margin-bottom:6px;"><i class="fa-solid fa-location-dot text-danger me-1"></i>${item.city}</div>
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="background:#2067e1;color:#fff;font-weight:700;font-size:11px;padding:2px 6px;border-radius:4px;">${item.score}</span>
                                <span style="font-weight:800;font-size:13.5px;color:#0f172a;">${item.price}</span>
                            </div>
                            <a href="${item.url}" target="_blank" style="display:block;text-align:center;background:#16a34a;color:#fff;font-weight:700;font-size:11px;padding:5px 0;border-radius:6px;text-decoration:none;margin-top:8px;">View Details →</a>
                        </div>
                    `;

                    var m = L.marker([item.lat, item.lng], {icon: customIcon})
                        .addTo(map)
                        .bindPopup(popupContent);

                    m.on('click', function() {
                        var card = document.getElementById('mapCard_' + item.id);
                        if (card) card.scrollIntoView({behavior: 'smooth', block: 'center'});
                    });

                    markersMap[item.id] = m;
                });

                // Viewport dynamic sync + "Search as I move the map" toggle
                map.on('moveend', function() {
                    var bounds = map.getBounds();
                    var inViewport = currentFiltered.filter(function(i) { return bounds.contains([i.lat, i.lng]); });
                    var countEl = document.getElementById('visibleCardsCount');
                    if (countEl) countEl.textContent = inViewport.length;

                    // If "Search as I move" toggle is on, re-render cards to viewport only
                    var moveToggle = document.getElementById('searchAsIMoveToggle');
                    if (moveToggle && moveToggle.checked) {
                        renderMapCards(inViewport);
                        // Also dim markers outside viewport
                        Object.keys(markersMap).forEach(function(id) {
                            var prop = allProperties.find(function(p) { return p.id == id; });
                            var el = markersMap[id].getElement();
                            if (!el) return;
                            var badge = el.querySelector('.custom-agoda-pin-inner');
                            if (!badge) return;
                            var inView = prop && bounds.contains([prop.lat, prop.lng]);
                            badge.style.opacity = inView ? '1' : '0.35';
                        });
                    }
                });

                mapInitialized = true;
            } else {
                map.invalidateSize();
            }
        });
    });
</script>


<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileFilterOffcanvas" aria-labelledby="mobileFilterOffcanvasLabel" style="width: min(360px, 90vw);">
    <div class="offcanvas-header border-bottom" style="background:#1d2b45;">
        <h6 class="offcanvas-title text-white fw-bold m-0" id="mobileFilterOffcanvasLabel">
            <i class="fa-solid fa-sliders me-2"></i>Filter Results
        </h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <?php echo $__env->make('components.search.filter-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div>


<?php
    $jsonLdItems = collect($searchResults['merged_results'])->take(10)->map(function($p, $idx) {
        $isObj = is_object($p);
        $name  = $isObj ? ($p->name ?? '') : ($p['name'] ?? '');
        $url   = $isObj ? route('property.show', $p->slug ?? $p->id) : route('property.show', $p['slug'] ?? $p['id'] ?? 1);
        $img   = $isObj ? ($p->primary_image ?? '') : ($p['primary_image'] ?? '');
        $price = (float)($isObj ? ($p->price_per_night ?? 0) : ($p['price_per_night'] ?? 0));
        return [
            '@type'    => 'ListItem',
            'position' => $idx + 1,
            'item'     => [
                '@type'       => 'Hotel',
                'name'        => $name,
                'url'         => $url,
                'image'       => $img ?: null,
                'priceRange'  => $price > 0 ? ('৳' . number_format($price)) : null,
            ],
        ];
    })->values()->toArray();

    $jsonLdData = [
        '@context'        => 'https://schema.org',
        '@type'           => 'ItemList',
        'name'            => 'Hotels & Stays in ' . ($destination ?: 'Bangladesh'),
        'description'     => 'Search results for ' . ($searchResults['total_count'] ?? 0) . ' hotels and stays in ' . ($destination ?: 'Bangladesh') . ' on PRIME BOOKING',
        'numberOfItems'   => count($jsonLdItems),
        'itemListElement' => $jsonLdItems,
    ];
?>
<?php if(count($jsonLdItems)): ?>
<script type="application/ld+json">
<?php echo json_encode($jsonLdData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>

</script>
<?php endif; ?>


<div class="position-fixed bottom-0 start-50 translate-middle-x mb-4" style="z-index: 1039;">
    <button type="button" class="btn text-white fw-bold shadow-lg rounded-pill px-4 py-2 d-flex align-items-center gap-2" style="background-color: #2067e1; font-size: 13.5px; border: 2.5px solid #ffffff; letter-spacing: 0.3px; box-shadow: 0 8px 24px rgba(32, 103, 225, 0.4) !important;" data-bs-toggle="modal" data-bs-target="#interactiveMapModal">
        <i class="fa-solid fa-map-location-dot fs-5"></i> Map view
    </button>
</div>


<script>
(function() {
    // ── A. Destination autocomplete on top search bar ──────────────────────────
    var destInput = document.getElementById('mainDestInput');
    var acDropdown = null;
    var acTimer = null;

    if (destInput) {
        acDropdown = document.createElement('div');
        acDropdown.id = 'topBarAcDropdown';
        acDropdown.className = 'position-absolute bg-white rounded-3 shadow-lg border mt-1 d-none';
        acDropdown.style.cssText = 'z-index:2000; width:340px; max-height:340px; overflow-y:auto; top:100%; left:0;';
        destInput.closest('.position-relative').appendChild(acDropdown);

        destInput.addEventListener('input', function() {
            clearTimeout(acTimer);
            var q = this.value.trim();
            if (q.length < 1) { acDropdown.classList.add('d-none'); return; }
            acTimer = setTimeout(function() { fetchAcSuggestions(q); }, 200);
        });

        destInput.addEventListener('blur', function() {
            setTimeout(function() { acDropdown.classList.add('d-none'); }, 200);
        });
    }

    function fetchAcSuggestions(q) {
        fetch('/api/search/autocomplete?q=' + encodeURIComponent(q) + '&search_type=<?php echo e($searchType ?? "hotel"); ?>')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var locs = (data.data && data.data.locations) ? data.data.locations : (data.locations || []);
                var props = (data.data && data.data.properties) ? data.data.properties : (data.properties || []);
                renderTopAcDropdown(locs, props, q);
            }).catch(function() {});
    }

    function renderTopAcDropdown(locs, props, q) {
        if (!acDropdown) return;
        if (!locs.length && !props.length) { acDropdown.classList.add('d-none'); return; }
        var html = '';
        var iconMap = { city:'fa-city', landmark:'fa-location-dot', country:'fa-globe', station:'fa-train' };
        if (locs.length) {
            html += '<div class="px-3 pt-2 pb-1" style="font-size:10px;font-weight:700;color:#94a3b8;letter-spacing:1px;">DESTINATIONS</div>';
            locs.slice(0, 5).forEach(function(l) {
                var icon = iconMap[l.type] || 'fa-location-dot';
                html += '<div class="px-3 py-2" style="cursor:pointer;border-bottom:1px solid #f1f5f9;" '
                    + 'onmousedown="selectTopAcItem(' + JSON.stringify(l.city||l.name||l.destination) + ')" '
                    + 'onmouseenter="this.style.background=\'#f8fafc\'" onmouseleave="this.style.background=\'transparent\'">'
                    + '<div class="d-flex align-items-center gap-2">'
                    + '<span style="width:28px;height:28px;background:#e0edff;border-radius:6px;display:flex;align-items:center;justify-content:center;"><i class="fa-solid ' + icon + ' text-primary" style="font-size:12px;"></i></span>'
                    + '<div><div class="fw-bold text-dark" style="font-size:13px;">' + (l.city||l.name||l.destination) + '</div>'
                    + '<small class="text-muted" style="font-size:11px;">' + (l.country||l.subtitle||'') + (l.property_count ? ' • '+l.property_count+' properties' : '') + '</small></div>'
                    + '</div></div>';
            });
        }
        if (props.length) {
            html += '<div class="px-3 pt-2 pb-1" style="font-size:10px;font-weight:700;color:#94a3b8;letter-spacing:1px;">PROPERTIES</div>';
            props.slice(0, 3).forEach(function(p) {
                html += '<div class="px-3 py-2" style="cursor:pointer;" '
                    + 'onmousedown="selectTopAcItem(' + JSON.stringify(p.name) + ')" '
                    + 'onmouseenter="this.style.background=\'#f8fafc\'" onmouseleave="this.style.background=\'transparent\'">'
                    + '<div class="d-flex align-items-center gap-2">'
                    + (p.image ? '<img src="'+p.image+'" style="width:32px;height:32px;border-radius:4px;object-fit:cover;">' : '<span style="width:32px;height:32px;background:#f1f5f9;border-radius:4px;display:inline-block;"></span>')
                    + '<div><div class="fw-bold text-dark" style="font-size:13px;">' + p.name + '</div>'
                    + '<small class="text-muted" style="font-size:11px;">' + (p.city||'') + (p.price ? ' • from '+p.price : '') + '</small></div>'
                    + '</div></div>';
            });
        }
        acDropdown.innerHTML = html;
        acDropdown.classList.remove('d-none');
    }

    window.selectTopAcItem = function(val) {
        if (destInput) { destInput.value = val; }
        if (acDropdown) acDropdown.classList.add('d-none');
        document.getElementById('searchHeaderForm').submit();
    };

    // ── B. AJAX Sort (no full page reload) ───────────────────────────────────
    var sortSelect = document.getElementById('sortBySelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            var filterForm = document.getElementById('filterSidebarForm');
            var resultsContainer = document.getElementById('searchResultsContainer');
            if (!resultsContainer || !filterForm) { return; }

            var params = new URLSearchParams();
            var fd = new FormData(filterForm);
            for (var pair of fd.entries()) { if (pair[1]) params.append(pair[0], pair[1]); }
            params.set('sort_by', sortSelect.value);
            params.delete('page');

            var url = filterForm.action + '?' + params.toString();
            resultsContainer.style.opacity = '0.4';
            resultsContainer.style.transition = 'opacity 0.15s';

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(r) { return r.text(); })
                .then(function(html) {
                    var doc = new DOMParser().parseFromString(html, 'text/html');
                    var fresh = doc.getElementById('searchResultsContainer');
                    if (fresh) resultsContainer.innerHTML = fresh.innerHTML;
                    resultsContainer.style.opacity = '1';
                    history.pushState(null, '', url);
                }).catch(function() { resultsContainer.style.opacity = '1'; });
        });
    }
})();
</script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.main', ['activePage' => 'services'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>