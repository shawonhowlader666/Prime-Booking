@extends('layouts.app')

@section('title', 'Staff Calendar | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Calendar">Calendar</li>
				</ol>
				<ul class="list-unstyled action d-flex align-items-center mb-0">
					<li class="dropdown">
						<a href="#" data-bs-toggle="dropdown" aria-expanded="false">
							<svg class="svg-stroke" xmlns="http://www.w3.org/2000/svg" width="22" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M3 3m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"></path>
								<path d="M9 12l6 0"></path>
								<path d="M12 9l0 6"></path>
							</svg>
						</a>
						<ul class="dropdown-menu dropdown-menu-end p-2 p-xl-3 shadow rounded-4">
							<li><a class="dropdown-item rounded-pill" href="#">New folder</a></li>
							<li><a class="dropdown-item rounded-pill" href="#">File upload</a></li>
							<li><a class="dropdown-item rounded-pill" href="#">Google docs</a></li>
							<li><a class="dropdown-item rounded-pill" href="#">Google sheets</a></li>
							<li><hr class="dropdown-divider"></li>
							<li><a class="dropdown-item rounded-pill" href="#">+ Invite</a></li>
							<li><a class="dropdown-item rounded-pill" href="#">Generate report</a></li>
							<li><a class="dropdown-item rounded-pill" href="#">New Reports</a></li>
						</ul>
					</li>
					<li class="dropdown ms-3">
						<a href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
							<svg class="svg-stroke" xmlns="http://www.w3.org/2000/svg" width="22" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z"></path>
							</svg>
						</a>
						<div class="dropdown-menu dropdown-menu-end p-2 p-xl-3 shadow rounded-4" style="width: 300px;">
							<h6>Filter Options</h6>
							<div class="row g-3 mt-3">
								<div class="col-12">
									<label class="form-label small text-muted">Sorted by:</label>
									<input type="search" class="form-control" placeholder="Search">
								</div>
								<div class="col-12 text-end mt-4">
									<button type="reset" class="btn btn-sm btn-light me-1">Reset</button>
									<button type="submit" class="btn btn-sm btn-primary">Apply</button>
								</div>
							</div>
						</div>
					</li>
				</ul>
			</div>

<div class="px-xl-5 px-lg-4 px-3 py-3 page-body">
				
				<div class="body-layout d-xl-flex align-items-stretch">
					<div class="body-sidebar card p-xl-4 p-3 border">
						<div class="mb-xl-4 mb-0 d-flex justify-content-between align-items-center">
							<h6 class="mb-0">My Calendar</h6>
							<button class="d-inline-flex d-xl-none navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#body_layout_offcanvas" aria-controls="body_layout_offcanvas" aria-label="Toggle navigation">
								<svg class="svg-stroke" xmlns="http://www.w3.org/2000/svg" width="24" height="26" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
									<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
									<path d="M4 6l16 0"></path>
									<path d="M4 12l16 0"></path>
									<path d="M4 18l16 0"></path>
								</svg>
							</button>
						</div>
						<nav class="navbar navbar-expand-xl py-0">
							<div class="offcanvas offcanvas-end p-xl-0 p-4" tabindex="-1" id="body_layout_offcanvas">
								<div class="d-flex align-items-center mb-3">
									<img class="avatar lg rounded-circle border border-3" src="assets/images/profile_av.png" alt="avatar">
									<div class="ms-3">
										<h4 class="mb-0 text-gradient title-font">{{ auth()->user()?->name ?? 'Management Team' }}</h4>
										<span class="text-muted small">{{ auth()->user()?->email ?? 'staff@hotelair.com' }}</span>
									</div>
								</div>
								<ul class="list-unstyled d-flex align-items-start justify-content-between rounded border p-2">
                                    <li class="px-2 py-1">
                                        <p class="text-muted mb-0">Meeting</p>
                                        <h5 class="mb-0">{{ isset($events) ? $events->where('event_type', 'MEETING')->count() : 4 }}</h5>
                                    </li>
                                    <li class="px-2 py-1">
                                        <p class="text-muted mb-0">Zoom call</p>
                                        <h5 class="mb-0">{{ isset($events) ? $events->where('event_type', 'CALL')->count() : 2 }}</h5>
                                    </li>
                                    <li class="px-2 py-1">
                                        <p class="text-muted mb-0">Event's</p>
                                        <h5 class="mb-0">{{ isset($events) ? $events->count() : 6 }}</h5>
                                    </li>
                                </ul>
								<ul class="list-group list-group-flush">
									<li class="list-group-item d-flex align-items-start px-0 pb-2">
										<span class="text-muted">Today Schedule</span>
									</li>
									@if(isset($events) && $events->count() > 0)
										@foreach($events->take(4) as $ev)
											<li class="list-group-item d-flex align-items-start px-0 py-3">
												<div class="avatar rounded-circle no-thumbnail mt-1"><i class="fa fa-calendar-check-o"></i></div>
												<div class="ms-3">
													<span class="text-muted small">{{ $ev->start_time->format('h:i A') }}</span>
													<h6 class="mb-0">{{ $ev->title }}</h6>
												</div>
											</li>
										@endforeach
									@else
										<li class="list-group-item d-flex align-items-start px-0 py-3">
											<div class="avatar rounded-circle no-thumbnail mt-1"><i class="fa fa-bell"></i></div>
											<div class="ms-3">
												<span class="text-muted small">10:45 - 11:30 AM</span>
												<h6 class="mb-0">Team Meeting with Designer</h6>
												<div class="d-flex align-items-center mt-2">
													<a href="#" data-bs-toggle="tooltip" title="Avatar"><img class="avatar sm rounded-circle" src="assets/images/xs/avatar3.jpg" alt="avatar"></a>
													<a href="#" data-bs-toggle="tooltip" title="Avatar"><img class="avatar sm rounded-circle" src="assets/images/xs/avatar1.jpg" alt="avatar"></a>
												</div>
											</div>
										</li>
										<li class="list-group-item d-flex align-items-start px-0 py-3">
											<div class="avatar rounded-circle no-thumbnail mt-1"><i class="fa fa-phone"></i></div>
											<div class="ms-3">
												<span class="text-muted small">11:45 - 12:30 AM</span>
												<h6 class="mb-0">Clients Call Update</h6>
											</div>
										</li>
									@endif
								</ul>
							</div>
						</nav>
					</div>
					<div class="body-content bg-card border ms-lg-4 p-lg-4 p-1 rounded">
						<div id='my_calendar'></div>
					</div>
				</div>

			</div>
@endsection

@push('scripts')
<script src="assets/bundles/fullcalendar.bundle.js"></script>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		var calendarEl = document.getElementById('my_calendar');
		if (calendarEl && typeof FullCalendar !== 'undefined') {
			var calendar = new FullCalendar.Calendar(calendarEl, {
				timeZone: 'UTC',
				initialView: 'dayGridMonth',
				editable: true,
				selectable: true,
				events: [
					@if(isset($events))
						@foreach($events as $ev)
							{
								title: "{{ addslashes($ev->title) }}",
								start: "{{ $ev->start_time->toIso8601String() }}",
								end: "{{ $ev->end_time ? $ev->end_time->toIso8601String() : $ev->start_time->toIso8601String() }}"
							},
						@endforeach
					@endif
				]
			});
			calendar.render();
		}
	});
</script>
@endpush
