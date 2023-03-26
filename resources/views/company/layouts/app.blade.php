<!DOCTYPE html>
<html>
    <head>
        <title>{{ config('app.name') }} @yield('title')</title>
        <!--meta-tags-->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!--css-->
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.24/sweetalert2.css"/>
        <link rel="stylesheet" href="{{ asset('assets/css/common.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/all-style.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/common-responsive.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/new-style.css') }}">
        @yield('page-css')
    </head>
    <body>
        <header>
            <nav class="navbar navbar-expand-xl navbar-dark">
                <button class="toggler-btn" type="button" data-toggle="collapse">
                    <span class="toggler-icon-mobile d-none">
                        <img src="{{ asset('assets/images/mobile-toggle.svg') }}">
                        <i class="fas fa-times"></i>
                    </span>
                    <span class="page-header-text d-none">FREIGHT MANAGEMENT</span>
                </button>
                <a class="navbar-brand desktop-logo" href="{{ route('company_home') }}">
					<span class="page-header-text" style="font-size:30px; font-weight:bold;">
                        FREIGHT MANAGEMENT<br />
                    </span>
				</a>
                <div class="dropdown mobile-logo d-none">
                    <button type="button" class="btn bg-transparent dropdown-toggle" data-toggle="dropdown">
                        <i class="far fa-user" style="font-size: 25px;color: #FFF;background: transparent;"></i>
                    </button>
                    <div class="dropdown-menu">
                        @can('change-password')<a class="dropdown-item open-section" href="#" data-target="password-setting">Update Password</a>@endcan
                        @can('profile-update')<a class="dropdown-item open-section" href="#" data-target="profile-setting">My Profile</a>@endcan
                        <a class="dropdown-item" href="{{ route('logout-ad') }}">Logout</a>
                    </div>
                </div>
                <div class="collapse navbar-collapse" id="navbarSupportedContentXL">
                    <ul class="navbar-nav mr-auto" style="width:80%">
                        @include('company.layouts.navigation')
                    </ul>

                    <ul class="right-icons d-flex"  style="width:8%">

                        <li class="user-img header-profile-img">
							<div class="dropdown">
								<button type="button" class="btn bg-transparent dropdown-toggle" data-toggle="dropdown">
                                    <small style="color:#FFF; font-weight:bold;">
                                        @if(Auth::user()->hasRole('1_Company')){{Auth::user()->company_detail->company_name}}@else {{Auth::user()->full_name}} @endif
                                    </small>
									<i class="far fa-user" style="font-size: 25px;color: #FFF;background: transparent;"></i>
								</button>
								<div class="dropdown-menu">
                                    @can('change-password') <a class="dropdown-item open-section" href="#" data-target="password-setting">Update Password</a>@endcan
                                    @can('profile-update')<a class="dropdown-item open-section" href="#" data-target="profile-setting">My Profile</a>@endcan
									<a class="dropdown-item" href="{{ route('logout-ad') }}">Logout</a>
								</div>
							</div>
                        </li>
                    </ul>
                </div>
            </nav>
			<div class="navigation-drawer">
                <div class="navigation-list">
                    <ul>
                        @include('admin.layouts.navigation',['for_mobile'=>true])
                    </ul>
                </div>
                <div class="mobile-footer">
                    <div class="navigate-profile-img">
                        <i class="far fa-user" style="font-size: 25px;color: #000;background: transparent;"></i>
                        <div class="profile-content">
                            <p>
                                {{Auth::user()->full_name}}
                                <a href="{{ route('logout-ad') }}"><img src="{{ asset('assets/images/logout-icon.svg') }}" alt="logout-image"></a>
                            </p>
                            <span>{{ Auth::user()->email }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="main-content">
            <div class="overlay"></div>
            @section('content')
            <!-- footer start -->
            <footer>
                <div class="copyright-text d-flex align-items-center justify-content-between">
                    <div class="copyright-text-left">
                        <p>
                            Copyright {{ config('app.name') }} {{ now()->format("Y") }}. All rights reserved
                        </p>
                    </div>
                    <div class="copyright-text-right">
                        <ul>
                            <li style="border:none">&nbsp;</li>
                        </ul>
                    </div>
                </div>
            </footer>
            <!-- footer end -->
            @show
        </main>


        <!-- My profile -->
        <div class="filter-sidebar filter-side-drawer" id="profile-setting">
            <form class="company" action="{{ route('c.edit',['id'=> jsencode_userdata(Auth::user()->id)])  }}" id="modalProfileSubmit" enctype="multipart/form-data">@csrf
                <h5 class="filter-sidebar-title">Update Profile</h5>
                <div class="filter-listing">
                    <div class="flash-message"></div>
                    <div class="filter-listing-item">

                        <div class="form-group">
                            <label class="input-label">Email</label>
                            <input type="text" name="company_email" id="company_email"
                                value="@if(Auth::user()){{ Auth::user()->email }}@endif" class="form-control form-control-user"
                                placeholder="Email">
                        </div>
                        <div class="form-group">
                            <label class="input-label">Comapany Name</label>
                            <input type="text" name="company_name"
                                value="@if(Auth::user()){{ Auth::user()->company_detail()->pluck('company_name')->first() }}@endif"
                                class="form-control form-control-user" placeholder="Company Name">
                        </div>
                        <div class="form-group">
                            <label class="input-label">Contact Person Name</label>
                            <input type="text" name="contact_person"
                                value="@if(Auth::user()){{ Auth::user()->company_detail()->pluck('contact_person')->first() }}@endif"
                                class="form-control form-control-user" placeholder="Contact Person Name">
                        </div>

                        <div class="form-group">
                            <label class="input-label">Contact Person Email</label>
                            <input type="text" name="contact_person_email"
                                value="@if(Auth::user()){{Auth::user()->company_detail()->pluck('contact_person_email')->first() }}@endif"
                                class="form-control form-control-user" placeholder="Contact Person Email">
                                <input type="hidden" name="role" value="Company">
                                <input type="hidden" name="status" value=1>
                        </div>

                        <div class="form-group">
                            <label class="input-label">Contact Person Number</label>
                            <input type="text" name="contact_number"
                                value="@if(Auth::user()){{ Auth::user()->company_detail()->pluck('contact_number')->first() }}@endif"
                                class="form-control form-control-user" placeholder="Contact Number">
                        </div>

                    </div>
                </div>
                <div class="filter-footer">
                    <button class="white-btn close-section" type="button">Cancel</button>
                    <button class="dark-blue-btn">Update</button>
                </div>
                <div class="filter-cross">
                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 1L1 9M1 1L9 9" stroke="#12344D" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>
            </form>
        </div>

        <!-- My password -->
        <div class="filter-sidebar filter-side-drawer" id="password-setting">
            <form class="" action="{{ route('password.update') }}" id="modalchangepassSubmit">@csrf
                <h5 class="filter-sidebar-title">Update Password</h5>

                <div class="filter-listing">
                    <div class="flash-messages"></div>
                    <div class="filter-listing-item">
                        <div class="input-group">
                            <label class="input-label">Old password</label>
                            <input type="password" class="form-control" name="oldpassword" placeholder="Old password">
                        </div>
                        <div class="input-group">
                            <label class="input-label">New password</label>
                            <input type="password" class="form-control" name="newpassword" placeholder="New password">
                        </div>
                        <div class="input-group">
                            <label class="input-label">Confirm new password</label>
                            <input type="password" class="form-control" name="newpassword_confirmation" placeholder="Confirm new password">
                        </div>
                    </div>
                </div>

                <div class="filter-footer">
                    <button class="white-btn close-section"  type="button">Cancel</button>
                    <button class="dark-blue-btn" id="savedBtnPass">Update</button>
                </div>

                <div class="filter-cross">
                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 1L1 9M1 1L9 9" stroke="#12344D" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>
            </form>
        </div>
        @yield("footer-html")
        <!--JS-->

        <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.24/sweetalert2.all.js"></script>
	    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" ></script>
        <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/jquery.tinymce.min.js"></script>
		<script>
            var open_section_popup = "{{ request('open_section') }}";
        </script>
        <script src="{{ asset('assets/js/common.js') }}"></script>
        <script src="{{ asset('assets/js/custom.js') }}"></script>
        @yield('page-js')
    </body>
</html>
