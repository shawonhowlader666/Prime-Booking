@extends('layouts.app')

@section('title', 'Inbox | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Inbox">Inbox</li>
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
				
				<div class="sub-layout d-flex flex-row align-items-stretch">
					<!--[ Start:: user list ]-->
					<div class="order-1 overflow-hidden border bg-card rounded-3 sticky-lg-top">
						<ul class="nav nav-tabs tab-card border-0 li_animate p-3 bg-dark z-2 position-relative" role="tablist" data-bs-theme="dark">
                            <li class="nav-item dropdown">
                                <a class="nav-link px-0 me-xl-4 me-3 dropdown-toggle fs-6" href="#" role="button" data-bs-toggle="dropdown">Inbox</a>
                                <ul class="li_animate dropdown-menu shadow rounded-4 p-2">
                                    <li><a class="dropdown-item rounded-pill" href="{{ route('inbox') }}">Primary</a></li>
                                    <li><a class="dropdown-item rounded-pill" href="{{ route('inbox', ['folder' => 'updates']) }}">Updates</a></li>
                                    <li><a class="dropdown-item rounded-pill" href="{{ route('inbox', ['folder' => 'promotions']) }}">Promotions</a></li>
                                </ul>
                            </li>
                            <li class="nav-item"><a class="nav-link px-0 me-xl-4 me-3 fs-6 {{ (isset($folder) && $folder === 'trash') ? 'active' : '' }}" href="{{ route('inbox', ['folder' => 'trash']) }}">Trash</a></li>
                            <li class="nav-item ms-auto">
								<button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#Compose">Compose</button>
                            </li>
                        </ul>
						<ul class="list-group list-group-flush user-list mb-0 custom_scroll z-1" role="tablist">
							@if(isset($emails) && $emails->count() > 0)
								@foreach($emails as $email)
									<li class="list-group-item py-3 d-flex">
										<img class="avatar rounded-circle" src="assets/images/xs/avatar{{ ($email->id % 9) + 1 }}.jpg" alt="">
										<div class="flex-grow-1 ms-3">
											<a href="javascript:void(0);" class="d-flex justify-content-between">
												<small>{{ $email->recipient_email }}<i class="fa fa-circle ms-1 status text-success"></i></small>
												<small class="time">{{ $email->created_at->format('h:i A') }}</small>
											</a>
											<div class="d-flex justify-content-between align-items-center mb-2">
												<span class="text-truncate user-msg">{{ $email->subject }}</span>
												<div class="d-flex align-items-center">
													<i class="fa fa-star {{ $email->is_starred ? 'text-warning' : 'gray-3' }} me-2"></i>
													<input class="form-check-input m-0" type="checkbox" value="{{ $email->id }}">
												</div>
											</div>
											<span class="small text-muted">{{ Str::limit(strip_tags($email->body), 80) }}</span>
										</div>
									</li>
								@endforeach
							@else
								<li class="list-group-item py-3 d-flex">
									<img class="avatar rounded-circle" src="assets/images/xs/avatar1.jpg" alt="">
									<div class="flex-grow-1 ms-3">
										<a href="javascript:void(0);" class="d-flex justify-content-between">
											<small>Andrew Patrick<i class="fa fa-circle ms-1 status text-warning"></i></small>
											<small class="time">5:15PM</small>
										</a>
										<div class="d-flex justify-content-between align-items-center mb-2">
											<span class="text-truncate user-msg">Apply for Housekeeping Staff</span>
											<div class="d-flex align-items-center">
												<i class="fa fa-star gray-3 me-2"></i>
												<input class="form-check-input m-0" type="checkbox" value="option1">
											</div>
										</div>
										<span class="small text-muted">it is a long established fact that a reader will be distracted by the readable content</span>
									</div>
								</li>
							@endif
						</ul>
					</div>
					<!--[ Start:: email details ]-->
					<div class="order-2 flex-grow-1 ms-lg-4 border bg-card rounded">
						<div class="px-xl-4 action-header">
							<ul class="navbar-nav flex-row me-auto">
								<li class="nav-item"><a class="btn btn-sm gray-6" href="#"><i class="fa fa-archive"></i></a></li>
								<li class="nav-item"><a class="btn btn-sm gray-6" href="#"><i class="fa fa-trash"></i></a></li>
								<li class="nav-item"><a class="btn btn-sm gray-6" href="#"><i class="fa fa-exclamation-circle"></i></a></li>
								<li class="nav-item"><a class="btn btn-sm gray-6" href="#"><i class="fa fa-tasks"></i><span class="ms-2 d-none d-lg-inline-block">Add to tasks</span></a></li>
								<li class="nav-item"><a class="btn btn-sm gray-6" href="#"><i class="fa fa-arrow-circle-right"></i><span class="ms-2 d-none d-lg-inline-block">Move to</span></a></li>
							</ul>
							<button type="button" class="btn btn-sm btn-outline-danger close-toggle"><i class="fe fe-settings mr-2"></i>Close</button>
						</div>
						<div class="px-xl-4 bg-body p-3">
							<div class="row align-items-center">
								<div class="col-auto pe-0">
									<a href="#"><img src="assets/images/xs/avatar1.jpg" alt="..." class="avatar rounded-circle"></a>
								</div>
								<div class="col ml-n2">
									<div class="fw-bold">{{ isset($emails) && $emails->count() > 0 ? $emails->first()->recipient_email : 'Front Desk Inquiries' }}</div>
									<div class="dropdown"> To: <a class="btn btn-link dropdown-toggle p-0" href="#" role="button" data-bs-toggle="dropdown">Me</a>
										<div class="dropdown-menu p-3 border-0 shadow w240 rounded-4">
											<div class="mb-2">
												<div class="mb-0">from:</div>
												<small class="text-muted">PMS Operations <a href="#">operations@hotelair.com</a></small>
											</div>
										</div>
									</div>
								</div>
								<div class="col-auto d-none d-xl-inline-block"><span>{{ now()->format('M d, Y h:i A') }}</span></div>
							</div>
						</div>
						<div class="px-xl-4 action-body">
							<h6>{{ isset($emails) && $emails->count() > 0 ? $emails->first()->subject : 'Staff & Reservation Dispatch Notifications' }}</h6>
							<p>{{ isset($emails) && $emails->count() > 0 ? $emails->first()->body : 'All daily arrival records, room night tariffs, and payment gateways are operating with ultra-high concurrency performance.' }}</p>
						</div>
					</div>
				</div>

			</div>
@endsection

@push('modals')
<!--[ Start:: Compose Email Modal ]-->
<div class="offcanvas offcanvas-end" tabindex="-1" id="Compose">
	<div class="offcanvas-header">
		<h5 class="offcanvas-title">Compose Email</h5>
		<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
	</div>
	<div class="offcanvas-body">
		<form action="{{ route('inbox.send') }}" method="POST">
			@csrf
			<div class="form-floating mb-2">
				<input type="email" name="to" class="form-control" placeholder="name@example.com" required>
				<label>To</label>
			</div>
			<div class="form-floating mb-2">
				<input type="text" name="subject" class="form-control" placeholder="Subject" required>
				<label>Subject</label>
			</div>
			<div class="form-group mb-3">
				<textarea name="body" class="form-control" rows="8" placeholder="Type your email message here..." required></textarea>
			</div>
			<div class="d-flex justify-content-end">
				<button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="offcanvas">Discard</button>
				<button type="submit" class="btn btn-primary">Send Email</button>
			</div>
		</form>
	</div>
</div>
@endpush
