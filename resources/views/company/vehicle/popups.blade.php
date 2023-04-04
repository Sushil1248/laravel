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
                            <h2>Vehicle details</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="Contract-details invoice-feild">
                <div class='ajax-response'></div>
                <ul>
                    <li>
                        <p>Vehicle name</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Vehicle name" name="name">
                        </div>
                    </li>

                    <li>
                        <p>Vehicle Number</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Vehicle Number" name="vehicle_num">
                        </div>
                    </li>

                    <li>
                        <p>Extra notes</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Extra Notes" name="extra_notes">
                        </div>
                    </li>
                </ul>

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
<div class="filter-sidebar filter-side-drawer order-sidebar create-module" id="create-vehicle-popup">
    <div class="">
        <form method="post" action="{{ route('vehicle.add') }}" id="create-vehicle">
        @csrf
            <div class="invoice-detail invoice-creation">

                <div class="invoice-details-inner">
                    <div class="detail-item-1 d-flex align-items-center">
                        <div class="shipmemnt-details-item item1">
                            <div class="list-content">
                                <h2>Create Vehicle</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="Contract-details invoice-feild">
                    <div class='ajax-response'></div>
                    <ul>
                        <li>
                            <p>Vehicle Name<span class="required-field">*</span></p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Vehicle name" name="name">
                            </div>
                            <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                        </li>
                        <li>
                            <p>Vehicle Number<span class="required-field">*</span></p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Enter Contact Number" name="vehicle_num">
                            </div>
                        </li>
                        <li>
                            <p>Extra Notes</p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Extra Notes" name="extra_notes">
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
