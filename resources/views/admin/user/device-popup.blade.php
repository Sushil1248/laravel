
<!--Add Device popup  -->
<div class="filter-sidebar filter-side-drawer order-sidebar create-module" id="create-device-popup">
    <div class="">
        <form method="post" action="{{ route('user.adddevice') }}" id="create-device">
        @csrf
            <div class="invoice-detail invoice-creation">

                <div class="invoice-details-inner">
                    <div class="detail-item-1 d-flex align-items-center">
                        <div class="shipmemnt-details-item item1">
                            <div class="list-content">
                                <h2>Create Device</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="Contract-details invoice-feild">
                    <div class='ajax-response'></div>
                    <ul>
                        <li>
                            <p>Device Name<span class="required-field">*</span></p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Device Name" name="device_name">
                                <input type="hidden" name="user_id" value="@isset($userId){{$userId}}@endisset">
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

<!--Add Device popup  -->
<div class="filter-sidebar filter-side-drawer order-sidebar create-module" id="push-notification-popup">
    <div class="">
        <form method="post" action="{{ route('user.device.push-notification') }}" id="notify-device">
        @csrf
            <div class="invoice-detail invoice-creation">

                <div class="invoice-details-inner">
                    <div class="detail-item-1 d-flex align-items-center">
                        <div class="shipmemnt-details-item item1">
                            <div class="list-content">
                                <h2>Send Push Notification &nbsp; <span class="device_name"></span></h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="Contract-details invoice-feild">
                    <div class='ajax-response'></div>
                    <ul class="d-flex" style="flex-direction: column;">
                        <li>
                            <p>Title<span class="required-field">*</span></p>
                            <div class="input-group input-group-sm invoice-value">
                                <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Add Title" name="title">
                                <input type="hidden" class="dynamic_name" name="dyn_name" value="">
                            </div>
                        </li>
                        <li>
                            <p>Message<span class="required-field">*</span></p>
                            <div class="input-group input-group-sm invoice-value">
                               <textarea name="message" id="message" class="form-control border-0" cols="30" rows="3"></textarea>
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
