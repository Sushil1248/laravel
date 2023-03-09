<!-- Update password popup -->
<div class="filter-sidebar filter-side-drawer" id="update-user-password">
    <h5 class="filter-sidebar-title">Update user password</h5>
    <form method="post" >
        @csrf
        <div class="filter-listing">
            <div class="filter-listing-item">
                <label class="input-label">Set new password</label>
                <div class="input-group">
                    <input type="password" class="form-control" value="" name="password"  placeholder="New password">
                </div>
                <div class="input-group">
                    <input type="password" class="form-control" value="" name="password_confirmation" placeholder="Confirm new password">
                </div>
            </div>
        </div>
        <div class="filter-footer">
            <button class="white-btn close-section" type="button">Close</button>
            <button class="dark-blue-btn" type="submit">Update password</button>
        </div>
    </form>
    <div class="filter-cross">
        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 1L1 9M1 1L9 9" stroke="#12344D" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
    </div>
</div>
<!-- End update password popup -->
<!-- Filter popup -->
<div class="filter-sidebar filter-side-drawer" id="user-filter">
    <h5 class="filter-sidebar-title">Filters</h5>
    <form>
        <div class="filter-listing">
            <div class="input-group">
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search">
            </div>
            <div class="filter-listing-item">
                <label class="input-label">Date Range</label>
                <div class="input-group">
                    {{-- <div class="input-group-prepend">
                        <span class="input-group-text">Q&A Deadline</span>
                    </div> --}}
                    <input type="text" class="form-control multi-date-rangepicker" value="{{ request('daterange_filter') }}" name="daterange_filter" autocomplete="off"  placeholder="From date">
                </div>

            </div>
            <div class="filter-listing-item">
            </div>

        </div>
        <div class="filter-footer">
            <button class="white-btn close-section" type="button">Close Filters</button>
            <button class="dark-blue-btn" type="submit">Apply Filters</button>
        </div>
    </form>
    <div class="filter-cross">
        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 1L1 9M1 1L9 9" stroke="#12344D" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
    </div>
</div>
<!-- End filter popup  -->
<!-- Detail popup  -->
<div class="filter-sidebar filter-side-drawer order-sidebar view-module" id="user-details" @if(request('user_id')) data-page-refresh="1" data-refresh-url="{{ route('user.list') }}" @endif>
    <div class="">
        <div class="invoice-detail invoice-creation">

            <div class="invoice-details-inner">
                <div class="detail-item-1 d-flex align-items-center">
                    <div class="shipmemnt-details-item item1">
                        <div class="list-content">
                            <h2>User details</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="Contract-details invoice-feild">
                <div class='ajax-response'></div>
                <ul>
                    <li>
                        <p>First name</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="First name" name="first_name">
                        </div>
                    </li>
                    <li>
                        <p>Last name</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Last name" name="last_name">
                        </div>
                    </li>
                    <li>
                        <p>Email</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Email" name="email">
                        </div>
                    </li>

                    <li>
                        <p>Company Name</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Email" name="company">
                        </div>
                    </li>

                    <li>
                        <p>Mobile</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Mobile number" name="mobile">
                        </div>
                    </li>

                    <li>
                        <p>Gender</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Gender" name="gender">
                        </div>
                    </li>

                    <li>
                        <p>Date of birth</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Date of birth" name="dob">
                        </div>
                    </li>

                    <li>
                        <p>Country</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Country" name="country">
                        </div>
                    </li>
                    <li>
                        <p>State</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="State" name="state">
                        </div>
                    </li>
                    <li>
                        <p>City</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="City" name="city">
                        </div>
                    </li>
                    <li>
                        <p>Address</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Address" name="address">
                        </div>
                    </li>
                </ul>


                {{-- <div class="mobile-details">

                </div> --}}


                {{-- <div class="question-list">
                    <h4>Subscriptions</h4>
                    <div class="subscription-detail">

                    </div>

                </div> --}}

                <div class="footer-menus_button">
                    {{-- <div class="invoice-list">

                    </div> --}}
                    <div class="submit-btns">
                        <ul>
                            <li class="close-section"><a href="#">Cancel</a></li>
                            <li><a href="#" class="submit-button">Edit </a></li>
                        </ul>
                    </div>
                </div>


            </div>
        </div>

        <div class="filter-cross">
            <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 1L1 9M1 1L9 9" stroke="#12344D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </div>
    </div>
</div>
<!-- End Detail popup  -->
<!-- Create popup  -->
<div class="filter-sidebar filter-side-drawer order-sidebar create-module" id="create-user-popup">
    <div class="">
        <form method="post" action="{{ route('user.add') }}" id="create-user">
        @csrf
            <div class="invoice-detail invoice-creation">

                <div class="invoice-details-inner">
                    <div class="detail-item-1 d-flex align-items-center">
                        <div class="shipmemnt-details-item item1">
                            <div class="list-content">
                                <h2>Create User</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="Contract-details invoice-feild">
                    <div class='ajax-response'></div>
                    <ul>
                        <li>
                            <p>First name<span class="required-field">*</span></p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="First name" name="first_name">
                            </div>
                        </li>
                        <li>
                            <p>Last name<span class="required-field">*</span></p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Last name" name="last_name">
                            </div>
                        </li>

                        <li>
                            <p>Email<span class="required-field">*</span></p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Email" name="email">
                            </div>
                        </li>

                        <li>
                            <p>Company</p>
                            <div class="input-group input-group-sm invoice-value">
                                <select class="custom-select select-company" name="company">
                                    <option value="">Select company</option>
                                    @foreach( $company as $companyId => $companyName )
                                    <option value="{{($companyId) }}">{{ $companyName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </li>

                        <li>
                            <p>Password<span class="required-field">*</span></p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="password" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Password" name="password">
                            </div>
                        </li>

                        <li>
                            <p>Mobile</p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Mobile number" name="mobile">
                            </div>
                        </li>

                        <li>
                            <p>Gender</p>
                            <div class="custom-control custom-radio">
                                <input type="radio" name="gender" value="male"  class="custom-control-input" id="gender_male">
                                <label class="custom-control-label" for="gender_male">
                                    Male
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" name="gender" value="female"  class="custom-control-input" id="gender_female">
                                <label class="custom-control-label" for="gender_female">
                                    Female
                                </label>
                            </div>
                        </li>

                        <li>
                            <p>Date of birth</p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="text" class="form-control datepicker" data-date-end-date="0d" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Date of birth" name="dob">
                            </div>
                        </li>


                        <li>
                            <p>Country</p>
                            <div class="input-group input-group-sm invoice-value">
                                <select class="custom-select select-country" name="country">
                                    <option value="">Select Country</option>
                                    @foreach( $country as $countryId => $countryName )
                                    <option value="{{ jsencode_userdata($countryId) }}">{{ $countryName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </li>
                        <li>
                            <p>State</p>
                            <div class="input-group input-group-sm invoice-value">
                                <select class="custom-select select-state" name="state">
                                    <option value="">Select State</option>
                                </select>
                            </div>
                        </li>
                        <li>
                            <p>City</p>
                            <div class="input-group input-group-sm invoice-value">
                                <select class="custom-select select-city" name="city">
                                    <option value="">Select City</option>
                                </select>
                            </div>
                        </li>
                        <li>
                            <p>Address</p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Address" name="address">
                            </div>
                        </li>
                    </ul>
                    <div class="footer-menus_button">
                        <div class="invoice-list">

                        </div>
                        <div class="submit-btns">
                            <ul>
                                <li class="close-section"><a href="#">Cancel</a></li>
                                <li><a href="#" class="submit-button ajax-submit-button" onclick="$(this).closest('form').submit()">Submit </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="filter-cross">
            <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 1L1 9M1 1L9 9" stroke="#12344D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </div>
    </div>
</div>
<!-- End create popup  -->

