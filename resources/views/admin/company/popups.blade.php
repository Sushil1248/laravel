<!-- Update password popup -->
<div class="filter-sidebar filter-side-drawer" id="update-company-password">
    <h5 class="filter-sidebar-title">Update Company password</h5>
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
<div class="filter-sidebar filter-side-drawer" id="company-filter">
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
<div class="filter-sidebar filter-side-drawer order-sidebar view-module" id="company-details" @if(request('company')) data-page-refresh="1" data-refresh-url="{{ route('company.list') }}" @endif>
    <div class="">
        <div class="invoice-detail invoice-creation">

            <div class="invoice-details-inner">
                <div class="detail-item-1 d-flex align-items-center">
                    <div class="shipmemnt-details-item item1">
                        <div class="list-content">
                            <h2>Company details</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="Contract-details invoice-feild">
                <div class='ajax-response'></div>
                <ul>
                    <li>
                        <p>Company name</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Company name" name="company_name">
                        </div>
                    </li>
                    <li>
                        <p>Company Email</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Email" name="company_email">
                        </div>
                    </li>

                    <li>
                        <p>Contact Person</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Contact Person" name="contact_person">
                        </div>
                    </li>

                    <li>
                        <p>Contact Person Email</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="email" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Contact Person Email" name="contact_person_email">
                        </div>
                    </li>

                    <li>
                        <p>Contact Number</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Contact Number" name="contact_number">
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

                {{--
                <div class="mobile-details">

                </div> --}}

                <div class="footer-menus_button">
                    <div class="invoice-list">

                    </div>
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
<div class="filter-sidebar filter-side-drawer order-sidebar create-module" id="create-company-popup">
    <div class="">
        <form method="post" action="{{ route('company.add') }}" id="create-company">
        @csrf
            <div class="invoice-detail invoice-creation">

                <div class="invoice-details-inner">
                    <div class="detail-item-1 d-flex align-items-center">
                        <div class="shipmemnt-details-item item1">
                            <div class="list-content">
                                <h2>Create Company</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="Contract-details invoice-feild">
                    <div class='ajax-response'></div>
                    <ul>
                        <li>
                            <p>Company Name<span class="required-field">*</span></p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Company name" name="company_name">
                            </div>
                        </li>
                        <li>
                            <p>Company Email<span class="required-field">*</span><span style="color:#FFF; background:#3eaf86; font-size:10px; padding:2px 4px; border-radius:5px;">This email will use for login</span></p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Email" name="company_email">
                            </div>
                        </li>
                        <li>
                            <p>Contact Person<span class="required-field">*</span></p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Enter Contact Person Name" name="contact_person">
                            </div>
                        </li>
                        <li>
                            <p>Contact Person Email<span class="required-field">*</span></p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="email" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Enter Contact Number" name="contact_person_email">
                                <input type="hidden" name="role" value="1_Company">
                            </div>
                        </li>
                        <li>
                            <p>Contact Number<span class="required-field">*</span></p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Enter Contact Number" name="contact_number">
                            </div>
                        </li>
                        <li>
                            <p>Password<span class="required-field">*</span></p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="password" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Password" name="password">
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
