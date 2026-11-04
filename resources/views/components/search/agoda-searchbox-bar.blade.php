@php
    $checkinCarbon = \Carbon\Carbon::parse($checkIn ?: now());
    $checkoutCarbon = \Carbon\Carbon::parse($checkOut ?: now()->addDays(1));
    $guestCount = intval($guests ?: 2);
    $roomsCount = intval(request('rooms', 1));
    $destVal = !empty(trim($destination ?? '')) ? trim($destination) : "Cox's Bazar";
@endphp

<form action="{{ route('search.index') }}" method="GET" id="searchHeaderForm">
    <input type="hidden" name="search_type" value="{{ $searchType ?? 'hotel' }}">
    <input type="hidden" name="check_in" id="checkInHiddenInput" value="{{ $checkinCarbon->format('Y-m-d') }}">
    <input type="hidden" name="check_out" id="checkOutHiddenInput" value="{{ $checkoutCarbon->format('Y-m-d') }}">
    <input type="hidden" name="guests" id="guestsHiddenInput" value="{{ $guestCount }}">
    <input type="hidden" name="rooms" id="roomsHiddenInput" value="{{ $roomsCount }}">

    <input type="date" id="checkInNativePicker" style="position:absolute; opacity:0; pointer-events:none;" value="{{ $checkinCarbon->format('Y-m-d') }}" onchange="document.getElementById('checkInHiddenInput').value=this.value; updateSearchDateDisplay('checkIn', this.value);">
    <input type="date" id="checkOutNativePicker" style="position:absolute; opacity:0; pointer-events:none;" value="{{ $checkoutCarbon->format('Y-m-d') }}" onchange="document.getElementById('checkOutHiddenInput').value=this.value; updateSearchDateDisplay('checkOut', this.value);">

    <div class="SearchBoxContainer SearchBoxContainer--AllRooms SearchBoxContainer--compact SearchBoxContainer--noanimation SearchBoxContainer--ImproveTextSearch" id="SearchBoxContainer" data-selenium="normal-property-searchbox" style="background: #142a4a; padding: 12px 0;">
        <div class="Searchbox Searchbox--horizontal" data-selenium="searchBox" style="max-width: 1120px; margin: 0 auto; padding: 0 16px; display: flex; align-items: center; gap: 8px;">
            <div class="Searchbox__wrapper" style="display: flex; flex: 1; align-items: center; gap: 6px;">
                
                {{-- Destination Input (Clean, No Icon matching screenshot) --}}
                <div tabindex="-1" class="Box-sc-kv6pi1-0 sc-fUnMCh bxexcZ jDzUYD IconBox IconBox--autocomplete" id="autocomplete-box" style="flex: 1.4; background: #ffffff; border-radius: 4px; height: 48px; display: flex; align-items: center; padding: 0 16px; border: 1px solid #d1d5db;">
                    <input aria-label="Enter a destination or property" role="combobox" data-selenium="textInput" tabindex="0" type="text" class="SearchBoxTextEditor SearchBoxTextEditor--autocomplete" placeholder="Enter a destination or property" name="destination" id="mainDestInput" value="{{ $destVal }}" style="width: 100%; border: none; outline: none; font-size: 15px; font-weight: 500; color: #2a2a2e; background: transparent;">
                </div>

                {{-- Check-In & Check-Out Group (Clean Two-Line Text, No Calendar Icon) --}}
                <div style="display: flex; flex: 1.6; background: #ffffff; border-radius: 4px; height: 48px; border: 1px solid #d1d5db; overflow: hidden;">
                    {{-- Check-In --}}
                    <div tabindex="0" class="IconBox IconBox--checkIn" id="check-in-box" onclick="document.getElementById('checkInNativePicker').showPicker();" style="flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 0 16px; cursor: pointer; border-right: 1px solid #e2e8f0; line-height: 1.25;">
                        <div id="checkInDisplayDate" style="font-size: 14px; font-weight: 700; color: #2a2a2e;">{{ $checkinCarbon->format('j M Y') }}</div>
                        <span id="checkInDisplayDay" style="font-size: 12px; color: #687176;">{{ $checkinCarbon->format('l') }}</span>
                    </div>

                    {{-- Check-Out --}}
                    <div tabindex="0" class="IconBox IconBox--checkOut" id="check-out-box" onclick="document.getElementById('checkOutNativePicker').showPicker();" style="flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 0 16px; cursor: pointer; line-height: 1.25;">
                        <div id="checkOutDisplayDate" style="font-size: 14px; font-weight: 700; color: #2a2a2e;">{{ $checkoutCarbon->format('j M Y') }}</div>
                        <span id="checkOutDisplayDay" style="font-size: 12px; color: #687176;">{{ $checkoutCarbon->format('l') }}</span>
                    </div>
                </div>

                {{-- Occupancy Box (Clean Two-Line Text, No People Icon or Chevron) --}}
                <div tabindex="0" class="IconBox IconBox--occupancy" id="occupancy-box" onclick="toggleOccupancyDropdown(event)" style="flex: 1.1; background: #ffffff; border-radius: 4px; height: 48px; display: flex; flex-direction: column; justify-content: center; padding: 0 16px; border: 1px solid #d1d5db; cursor: pointer; position: relative; line-height: 1.25;">
                    <div id="adultsDisplayVal" style="font-size: 14px; font-weight: 700; color: #2a2a2e;">{{ $guestCount }}&nbsp;adult{{ $guestCount > 1 ? 's' : '' }}</div>
                    <span id="roomsDisplayVal" style="font-size: 12px; color: #687176;">{{ $roomsCount }}&nbsp;room{{ $roomsCount > 1 ? 's' : '' }}</span>
                </div>

                {{-- Search Button (Full Rounded Pill Matching Screenshot) --}}
                <div style="width: 140px;">
                    <button data-selenium="searchButton" type="submit" style="width: 100%; height: 48px; background: #1b72e8; color: #ffffff; border: none; border-radius: 24px; font-weight: 700; font-size: 15px; cursor: pointer; letter-spacing: 0.5px; display: flex; align-items: center; justify-content: center; transition: background 0.15s ease;">
                        SEARCH
                    </button>
                </div>

            </div>
        </div>
    </div>
</form>

<script>
    function updateSearchDateDisplay(type, val) {
        if (!val) return;
        const d = new Date(val + 'T00:00:00');
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        const formattedDate = d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        const formattedDay = days[d.getDay()];

        if (type === 'checkIn') {
            const dateEl = document.getElementById('checkInDisplayDate');
            const dayEl = document.getElementById('checkInDisplayDay');
            if (dateEl) dateEl.textContent = formattedDate;
            if (dayEl) dayEl.textContent = formattedDay;
        } else if (type === 'checkOut') {
            const dateEl = document.getElementById('checkOutDisplayDate');
            const dayEl = document.getElementById('checkOutDisplayDay');
            if (dateEl) dateEl.textContent = formattedDate;
            if (dayEl) dayEl.textContent = formattedDay;
        }
    }

    function toggleOccupancyDropdown(e) {
        e.stopPropagation();
        let currentGuests = parseInt(document.getElementById('guestsHiddenInput').value) || 2;
        let newGuests = prompt("Enter number of guests:", currentGuests);
        if (newGuests && !isNaN(newGuests) && parseInt(newGuests) > 0) {
            newGuests = parseInt(newGuests);
            document.getElementById('guestsHiddenInput').value = newGuests;
            document.getElementById('adultsDisplayVal').textContent = newGuests + (newGuests > 1 ? ' adults' : ' adult');
        }
    }
</script>
