@extends('layouts.app')

@section('title', 'Staff Chat | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Chat">Chat</li>
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
					<div class="order-1 overflow-hidden bg-card rounded-3 border sticky-lg-top">
						<div class="c-list p-3">
							<input type="search" class="form-control" placeholder="Search for...">
						</div>
						<ul class="list-group list-group-flush user-list mb-0 custom_scroll ps-2" role="tablist">
							@if(isset($contacts) && $contacts->count() > 0)
								@foreach($contacts as $contact)
									<li class="list-group-item b-dashed">
										<a href="javascript:void(0);" class="d-flex {{ ($activeContact && $activeContact->id === $contact->id) ? 'active' : '' }}">
											<img class="avatar rounded-circle" src="assets/images/xs/avatar{{ ($contact->id % 9) + 1 }}.jpg" alt="{{ $contact->name }}">
											<div class="flex-fill ms-3">
												<h6 class="d-flex justify-content-between mb-0"><span>{{ $contact->name }}</span> <small class="msg-time">{{ $contact->role }}</small></h6>
												<span class="text-muted small">{{ $contact->email }}</span>
											</div>
										</a>
									</li>
								@endforeach
							@else
								<li class="list-group-item b-dashed">
									<a href="javascript:void(0);" class="d-flex active">
										<img class="avatar rounded-circle" src="assets/images/xs/avatar1.jpg" alt="">
										<div class="flex-fill ms-3">
											<h6 class="d-flex justify-content-between mb-0"><span>Front Desk Staff</span> <small class="msg-time">Online</small></h6>
											<span class="text-muted small">Available for guest service inquiries</span>
										</div>
									</a>
								</li>
							@endif
						</ul>
					</div>
					<!--[ Start:: details ]-->
					<div class="order-2 flex-grow-1 ms-lg-4 bg-card border rounded">
						<div class="action-header px-xl-4">
							<a class="d-flex" href="#group_detail" data-bs-toggle="offcanvas" title="">
								<div class="avatar rounded-circle no-thumbnail">{{ $activeContact ? substr($activeContact->name, 0, 2) : 'HT' }}</div>
								<div class="ms-3">
									<h6 class="mb-0">{{ $activeContact ? $activeContact->name : 'Hotel Operations Team' }}</h6>
									<small class="text-muted">Online</small>
								</div>
							</a>
							<div class="d-flex align-items-center">
								<a class="gray-5 px-3" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#chat-search"><i class="fa fa-search"></i></a>
								<a class="gray-5 px-3 d-none d-lg-block" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#video_call"><i class="fa fa-video-camera"></i></a>
								<a class="gray-5 px-3 d-none d-lg-block" href="javascript:void(0);"><i class="fa fa-gear"></i></a>
								<button type="button" class="btn btn-sm btn-danger close-toggle ms-4"><i class="fe fe-settings mr-2"></i>Close</button>
							</div>
						</div>
						<div class="collapse bg-body py-2" id="chat-search">
							<div class="input-group px-lg-4 px-3">
								<input type="search" class="form-control border-0" placeholder="Search. Components, Layouts, etc...">
								<div class="input-group-append ms-3">
									<button class="btn btn-primary" type="submit">Search</button>
								</div>
							</div>
						</div>
						<div class="action-body custom_scroll px-xl-4">
							<ul class="list-unstyled mb-0">
								@if(isset($messages) && $messages->count() > 0)
									@foreach($messages as $msg)
										@if($msg->sender_id === (auth()->id() ?? 1))
											<!--[ Chat: right ]-->
											<li class="mb-4 d-flex flex-row-reverse align-items-end">
												<div class="max-width-70 text-end">
													<div class="user-info mb-1">
														<span class="text-muted small">{{ $msg->created_at->format('h:i A, M d') }}</span>
													</div>
													<div class="p-2 bg-body rounded">
														<div class="message">{{ $msg->message }}</div>
													</div>
												</div>
											</li>
										@else
											<!--[ Chat: left ]-->
											<li class="mb-4 d-flex flex-row align-items-end">
												<div class="max-width-70">
													<div class="user-info mb-1">
														<img class="avatar xs rounded-circle me-1" src="assets/images/xs/avatar{{ ($msg->sender_id % 9) + 1 }}.jpg" alt="avatar">
														<span class="text-muted small">{{ $msg->sender?->name ?? 'Staff' }} • {{ $msg->created_at->format('h:i A') }}</span>
													</div>
													<div class="bg-info bg-opacity-10 p-2 rounded">
														<div class="message">{{ $msg->message }}</div>
													</div>
												</div>
											</li>
										@endif
									@endforeach
								@else
									<!--[ Chat: left ]-->
									<li class="mb-4 d-flex flex-row align-items-end">
										<div class="max-width-70">
											<div class="user-info mb-1">
												<img class="avatar xs rounded-circle me-1" src="assets/images/xs/avatar2.jpg" alt="avatar">
												<span class="text-muted small">10:10 AM, Today</span>
											</div>
											<div class="bg-info bg-opacity-10 p-2 rounded">
												<div class="message">Welcome to Prime PMS Internal Communications Hub!</div>
											</div>
										</div>
									</li>
									<!--[ Chat: right ]-->
									<li class="mb-4 d-flex flex-row-reverse align-items-end">
										<div class="max-width-70 text-end">
											<div class="user-info mb-1">
												<span class="text-muted small">10:12 AM, Today</span>
											</div>
											<div class="p-2 bg-body rounded">
												<div class="message">All systems active and synchronized.</div>
											</div>
										</div>
									</li>
								@endif
							</ul>
						</div>
						<form action="{{ route('chat.send') }}" method="POST" class="action-footer chat-msg d-flex px-xl-4">
							@csrf
							<input type="hidden" name="receiver_id" value="{{ $activeContact ? $activeContact->id : '' }}">
							<div class="btn btn-link file-input">
								<input type="file" class="form-control" name="file-input" id="file-input">
								<label for="file-input" class="fa fa-paperclip"></label>
							</div>
							<input type="text" name="message" class="form-control bg-transparent border-0" placeholder="Enter text here..." required>
							<button class="btn btn-dark text-uppercase" type="submit">Send</button>
						</form>
					</div>
				</div>

			</div>
@endsection
