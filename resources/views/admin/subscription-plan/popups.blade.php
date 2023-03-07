<!-- Filter popups -->
<div class="filter-sidebar filter-side-drawer" id="steph-workout-filter">
    <h5 class="filter-sidebar-title">Filters</h5>
    <form>
        <div class="filter-listing">
            <div class="input-group">
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search">
            </div>
            <div class="filter-listing-item">
                <label class="input-label">Date Range</label>
                <div class="input-group">
                    
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
<!-- End Filter popups -->

<!-- Create popup -->
<div class="filter-sidebar filter-side-drawer order-sidebar create-module" id="create-subscription" data-page-refresh="0">
    <div class="filter-listing">
        <form method="post" action="{{ route('subscription-plan.add') }}" id="create-subscription-form" enctype="multipart/form-data">
        @csrf
            <div class="invoice-detail invoice-creation">
                <div class="invoice-details-inner">
                    <div class="detail-item-1 d-flex align-items-center">
                        <div class="shipmemnt-details-item item1">
                            <div class="list-content">
                                <h2>Create Subscription Plan</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="Contract-details invoice-feild">
                    <div class='ajax-response'></div>
                    <ul style="    grid-template-columns: 1fr 1fr 1fr;">
                        <li>
                            <p>Name<span class="required-field">*</span></p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Name" name="name"> 
                            </div>
                        </li>
                        <li>
                            <p>Price($)<span class="required-field">*</span></p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="text" class="form-control two-decimal" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Price" name="price">
                            </div>
                        </li>
                        <li>
                            <p>Validity(Months)<span class="required-field">*</span></p>
                            <div class="input-group input-group-sm invoice-value">
                                <select class="custom-select" name="number_of_months">
                                    <option value="">Number of months</option>
                                    @for( $counter = 1; $counter <= 12 ; $counter++ )
                                    <option>{{ $counter }}</option>
                                    @endfor
                                </select>
                            </div>
                        </li>
                    </ul>
                    <ul style="grid-template-columns: 1fr 1fr;">
                        <li>
                            <p>Description</p>
                            <div class="input-group input-group-sm invoice-value">
                                <textarea type="text" class="form-control comment-box" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Description" name="description"></textarea>
                            </div>
                        </li>
                        <li>
                            <p>Features Offered</p>
                            <div class="input-group input-group-sm invoice-value">
                                <textarea type="text" class="form-control comment-box" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Features Offered" name="features_offered"></textarea>
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
<!-- End Create popup -->

<!-- Detail popup -->
<div class="filter-sidebar filter-side-drawer order-sidebar view-module" id="workout-details">
    <div class="filter-listing">
        <div class="invoice-detail invoice-creation">
            <div class="invoice-details-inner">
                <div class="detail-item-1 d-flex align-items-center">
                    <div class="shipmemnt-details-item item1">
                        <div class="list-content">
                            <h2>Workout Detail</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="Contract-details invoice-feild">
                <div class='ajax-response'></div>
                <ul>
                    <li>
                        <p>Workout name</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Workout name" name="name"> 
                        </div>
                    </li>
                    
                    <li>
                        <p>Category</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Category" name="category"> 
                        </div>
                    </li>
                    
                    <li>
                        <p>Exercise</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="exercise" name="exercise"> 
                        </div>
                    </li>
                    
                    <li>
                        <p>Sets</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Sets" name="sets"> 
                        </div>
                    </li>
                    
                    <li>
                        <p>Reps</p>
                        <div class="input-group input-group-sm invoice-value">
                            <input type="text" class="form-control" readonly aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Workout name" name="reps"> 
                        </div>
                    </li>
                </ul>
                <ul style="grid-template-columns: 1fr 1fr 1fr;" class="create-asn">
                    <li>
                        <p>Workout Information</p>
                        <div class="input-group input-group-sm invoice-value">
                            <textarea type="text" readonly class="form-control comment-box" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Information" name="workout_info"></textarea>
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
<!-- End Detail popup -->