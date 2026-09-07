@extends('layouts.app')

@section('title', 'Blog | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item"><a href="app.html" title="Apps">Apps</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Blog">Blog</li>
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
								<div class="col-12">
									<label class="form-label small text-muted">Status:</label>
									<select class="form-select">
										<option selected>Choose...</option>
										<option>...</option>
									</select>
								</div>
								<div class="col-12">
									<label class="form-label small text-muted">Notifications:</label>
									<div class="form-check form-switch">
										<input class="form-check-input" type="checkbox" role="switch">
										<label class="form-check-label">Enabled</label>
									</div>
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
				<div class="row mb-3">
					<div class="col-sm-12">
						<div class="d-flex align-items-center justify-content-between flex-wrap">
							<h3 class="fw-bold mb-0">Blog List</h3>
						</div>
					</div>
				</div>
				<ul class="row g-4 justify-content-between list-unstyled li_animate mb-0 mt-2">
					<li class="col-lg-7 col-md-12">
						<article class="mb-4 pb-5 border-bottom">
							<h5><a class="title-font theme-text-color6" href="blog-detail.html" title="blog title">Why Time Management is Essential for Success</a></h5>
							<div class="d-flex align-items-center text-muted mb-3">
								<span class="pe-4">By <a href="#">HotelAir</a></span>
								<span class="pe-4">Feb 20, 2024</span>
								<span class="pe-4">1 comment</span>
							</div>
							<p class="fw-light mb-4">In today's fast-paced world, time management has become more important than ever before. Effective time management can make the difference between success and failure in any aspect of life, whether it's personal or professional.</p>
							<a class="btn btn-primary" href="blog-detail.html" title="Read more">
								Read more
							</a>
						</article>
						<article class="mb-4 pb-5 border-bottom">
							<h5><a class="title-font theme-text-color2" href="blog-detail.html" title="blog title">ChatGPT: The Future of Conversational AI</a></h5>
							<div class="d-flex align-items-center text-muted mb-3">
								<span class="pe-4">By <a href="#">HotelAir</a></span>
								<span class="pe-4">Feb 19, 2024</span>
								<span class="pe-4">18 comment</span>
							</div>
							<p class="fw-light mb-4">It is a cutting-edge technology that is designed to mimic human conversation and provide natural language processing capabilities. In this blog, we'll explore the future of conversational AI with ChatGPT.</p>
							<a class="btn btn-primary" href="blog-detail.html" title="Read more">
								Read more
							</a>
						</article>
						<article class="mb-4 pb-5 border-bottom">
							<h5><a class="title-font theme-text-color3" href="blog-detail.html" title="blog title">The Importance of Front-end Housekeeping Staff in Modern Web Design</a></h5>
							<div class="d-flex align-items-center text-muted mb-3">
								<span class="pe-4">By <a href="#">HotelAir</a></span>
								<span class="pe-4">Feb 13, 2024</span>
								<span class="pe-4">5 comment</span>
							</div>
							<p class="fw-light mb-4">Front-end Housekeeping Staff is a critical aspect of modern web design. It is the process of creating the user interface of a website or application, and it plays a crucial role in determining the user experience.</p>
							<a class="btn btn-primary" href="blog-detail.html" title="Read more">
								Read more
							</a>
						</article>
						<article class="mb-4 pb-5 border-bottom">
							<h5><a class="title-font theme-text-color4" href="blog-detail.html" title="blog title">The Top Benefits of Angular for Housekeeping Staff</a></h5>
							<div class="d-flex align-items-center text-muted mb-3">
								<span class="pe-4">By <a href="#">HotelAir</a></span>
								<span class="pe-4">Feb 10, 2024</span>
								<span class="pe-4">7 comment</span>
							</div>
							<p class="fw-light mb-4">Angular is a popular JavaScript fratasktrackrk that is widely used for Housekeeping Staff. It offers a variety of features and benefits that make it a top choice for Housekeeping Staffs. In this blog, we'll explore the top benefits of Angular for Housekeeping Staff.</p>
							<a class="btn btn-primary" href="blog-detail.html" title="Read more">
								Read more
							</a>
						</article>
						<article class="mb-4 pb-5 border-bottom">
							<h5><a class="title-font theme-text-color4" href="blog-detail.html" title="blog title">The Science Behind Optimism and How It Can Transform Your Life</a></h5>
							<div class="d-flex align-items-center text-muted mb-3">
								<span class="pe-4">By <a href="#">HotelAir</a></span>
								<span class="pe-4">Feb 10, 2024</span>
								<span class="pe-4">7 comment</span>
							</div>
							<p class="fw-light mb-4">We'll delve into the research behind optimism and its ability to reduce stress, improve relationships, and enhance overall well-being.</p>
							<a class="btn btn-primary" href="blog-detail.html" title="Read more">
								Read more
							</a>
						</article>
						<article class="mb-4 pb-5 border-bottom">
							<h5><a class="title-font theme-text-color2" href="blog-detail.html" title="blog title">The Future of Digital Ownership and Creativity</a></h5>
							<div class="d-flex align-items-center text-muted mb-3">
								<span class="pe-4">By <a href="#">HotelAir</a></span>
								<span class="pe-4">Feb 10, 2024</span>
								<span class="pe-4">7 comment</span>
							</div>
							<p class="fw-light mb-4">It will explore the basic concepts of NFTs, including how they work, what makes them unique, and their potential applications across a wide range of industries.</p>
							<a class="btn btn-primary" href="blog-detail.html" title="Read more">
								Read more
							</a>
						</article>
						<nav aria-label="Page navigation example">
							<ul class="pagination">
							  <li class="page-item"><a class="page-link" href="#">Previous</a></li>
							  <li class="page-item"><a class="page-link" href="#">1</a></li>
							  <li class="page-item"><a class="page-link" href="#">2</a></li>
							  <li class="page-item"><a class="page-link" href="#">3</a></li>
							  <li class="page-item"><a class="page-link" href="#">Next</a></li>
							</ul>
						</nav>
					</li>
					<li class="col-lg-4 col-md-12">
						<div class="overflow-hidden mb-xl-5 mb-3">
							  <img src="/assets/images/ads-01.png" alt="" class="img-fluid border rounded-4">
						</div>
						<div class="text-uppercase mb-3 pb-2 border-bottom">Discover more of what matters to you</div>
						<div class="card bg-transparent border-0 mb-xl-5 mb-3">
							<div class="d-flex flex-wrap tag-group">
								<a class="btn btn-hover-fill m-1" style="--hover-bg: var(--theme-color1)" href="#">AI</a>
								<a class="btn btn-hover-fill m-1" style="--hover-bg: var(--theme-color2)" href="#">ChatGPT</a>
								<a class="btn btn-hover-fill m-1" style="--hover-bg: var(--theme-color3)" href="#">Startup</a>
								<a class="btn btn-hover-fill m-1" style="--hover-bg: var(--theme-color2)" href="#">Leadership</a>
								<a class="btn btn-hover-fill m-1" style="--hover-bg: var(--theme-color4)" href="#">IOT</a>
								<a class="btn btn-hover-fill m-1" style="--hover-bg: var(--theme-color6)" href="#">Blockchain</a>
								<a class="btn btn-hover-fill m-1" style="--hover-bg: var(--theme-color5)" href="#">Dataanalytics</a>
							</div>
						</div>
						<div class="text-uppercase mb-3 pb-2 border-bottom">Instagram</div>
						<div class="card bg-transparent border-0">
							<div class="row g-1">
								<div class="col-4">
									<a class="rounded d-block" rel="ligthbox" href="#">
										<img class="img-fluid rounded" alt="Instagram post img" src="/assets/images/gallery/16.jpg">
									</a>
								</div>
								<div class="col-4">
									<a class="rounded d-block" rel="ligthbox" href="#">
										<img class="img-fluid rounded" alt="Instagram post img" src="/assets/images/gallery/17.jpg">
									</a>
								</div>
								<div class="col-4">
									<a class="rounded d-block" rel="ligthbox" href="#">
										<img class="img-fluid rounded" alt="Instagram post img" src="/assets/images/gallery/18.jpg">
									</a>
								</div>
								<div class="col-4">
									<a class="rounded d-block" rel="ligthbox" href="#">
										<img class="img-fluid rounded" alt="Instagram post img" src="/assets/images/gallery/19.jpg">
									</a>
								</div>
								<div class="col-4">
									<a class="rounded d-block" rel="ligthbox" href="#">
										<img class="img-fluid rounded" alt="Instagram post img" src="/assets/images/gallery/20.jpg">
									</a>
								</div>
								<div class="col-4">
									<a class="rounded d-block" rel="ligthbox" href="#">
										<img class="img-fluid rounded" alt="Instagram post img" src="/assets/images/gallery/6.jpg">
									</a>
								</div>
								<div class="col-4">
									<a class="rounded d-block" rel="ligthbox" href="#">
										<img class="img-fluid rounded" alt="Instagram post img" src="/assets/images/gallery/7.jpg">
									</a>
								</div>
								<div class="col-4">
									<a class="rounded d-block" rel="ligthbox" href="#">
										<img class="img-fluid rounded" alt="Instagram post img" src="/assets/images/gallery/8.jpg">
									</a>
								</div>
								<div class="col-4">
									<a class="rounded d-block" rel="ligthbox" href="#">
										<img class="img-fluid rounded" alt="Instagram post img" src="/assets/images/gallery/9.jpg">
									</a>
								</div>
							</div>
						</div>
					</li>
				</ul> <!--[ .row end ]-->

			</div>
@endsection

