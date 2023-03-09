<!-- Popup filter -->
<div class="filter-sidebar filter-side-drawer" id="media-filter">
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
                {{-- <div class="label-flex d-flex align-items-center">
                    <label class="input-label">Date Range</label>
                    <a href="javascript:void(0)" class="btn reset-btn">
                        Reset
                        <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M19.1667 2.33331V7.33331M19.1667 7.33331H14.1667M19.1667 7.33331L15.3 3.69998C14.4044 2.80391 13.2964 2.14932 12.0794 1.79729C10.8623 1.44527 9.57596 1.40727 8.34028 1.68686C7.10459 1.96645 5.95987 2.55451 5.01293 3.39616C4.06598 4.23782 3.34768 5.30564 2.92504 6.49998M0.833374 15.6666V10.6666M0.833374 10.6666H5.83337M0.833374 10.6666L4.70004 14.3C5.59566 15.1961 6.70368 15.8506 7.92071 16.2027C9.13774 16.5547 10.4241 16.5927 11.6598 16.3131C12.8955 16.0335 14.0402 15.4455 14.9872 14.6038C15.9341 13.7621 16.6524 12.6943 17.075 11.5"
                                stroke="#026AA2" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div> --}}
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
<!-- End Popup filter -->


<!-- Create popup -->
<div class="filter-sidebar filter-side-drawer order-sidebar create-module" id="upload-media-popup" data-page-refresh="0">
    <div class="filter-listing">
        <div class="invoice-detail invoice-creation">
            <div class="invoice-details-inner">
                <div class="detail-item-1 d-flex align-items-center">
                    <div class="shipmemnt-details-item item1">
                        <div class="list-content">
                            <h2>Upload Media</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="Contract-details invoice-feild">
                <div class='ajax-response'></div>
                <form action="{{ route('media.add') }}" class="dropzone" id="my-great-dropzone">
                @csrf
                </form>
                <div class="footer-menus_button">
                    <div class="invoice-list">
                        
                    </div>
                    <div class="submit-btns">
                        <ul>
                            <li class="close-section"><a href="#">Cancel</a></li>
                            
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
<!-- End Create popup -->