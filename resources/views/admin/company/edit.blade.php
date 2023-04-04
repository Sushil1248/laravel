@extends('admin.layouts.app')
@section('title', '- Company')


@section('content')
<section class="order-listing Invoice-listing edit-module">
    <div class="container">
        <div class="left-content d-flex">
            <div class="list-title d-flex">
                <i class="fas fa-cubes" style="font-size: 30px;"></i>
                <div class="list-content">
                    <h2 class="heading-text">Manage Companies</h2>
                    <h2 class="mobile-text d-none">Manage Companies</h2>
                    <p>
                        Add , View and Edit the details
                    </p>
                </div>
            </div>
            <div class="right-btns">
                <div class="">
                    <a class="nav-link btn navy-blue-btn" href="{{ route('company.list') }}"  aria-expanded="false">
                    List Company
                    </a>
                </div>
            </div>
        </div>
        <div class="order-listing Invoice-tabs">
            <x-alert/>
            <!-- tabs Start here -->
            <ul class="tabs nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">Update Company</a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Deleted Users</a>
                </li> --}}
            </ul>

            <!-- Search section End here -->
            <div class="tab-content invoice-tab-content">
                <div class="tab-pane active" id="tabs-1" role="tabpanel">
                    <div class="">
                        <form method="post" action="{{ route('company.edit',['id'=>jsencode_userdata($companyDetail->id)]) }}" id="update-company">
                        @csrf
                            <div class="invoice-detail invoice-creation">
                                <div class="invoice-details-inner">
                                    <div class="detail-item-1 d-flex align-items-center">
                                        <div class="shipmemnt-details-item item1">
                                            <div class="list-content">
                                                <h2>Edit Company</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="Contract-details invoice-feild">
                                    <div class='ajax-response'></div>
                                    <ul>
                                        <li>
                                            <p>Company name <span class="required-field">*</span></p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <input type="text" class="form-control" value="{{ old('company_name',$companyDetail->company_detail->company_name) }}" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Company name" name="company_name">
                                            </div>
                                        </li>

                                        <li>
                                            <p>Company Email<span class="required-field">*</span><span style="color:#FFF; background:#3eaf86; font-size:10px; padding:2px 4px; border-radius:5px;">This email will use for login</span></p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <input type="text"  value="{{ old('company_email' , $companyDetail->email) }}" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Email" name="company_email">
                                            </div>
                                        </li>

                                        <li>
                                            <p>Contact Person</p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <input type="text" class="form-control"  value="{{ old( 'contact_person' , $companyDetail ? $companyDetail->company_detail->contact_person : '' ) }}" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Contact Person" name="contact_person">
                                            </div>
                                        </li>

                                        <li>
                                            <p>Contact Number</p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <input type="text" class="form-control"  value="{{ old( 'contact_number' , $companyDetail ? $companyDetail->company_detail->contact_number : '' ) }}" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Contact Person" name="contact_number">
                                            </div>
                                        </li>

                                        <li>
                                            <p>Contact Person Email<span class="required-field">*</span></p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <input type="email" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Enter Contact Number" name="contact_person_email"  value="{{ old( 'contact_person_email' , $companyDetail ? $companyDetail->company_detail->contact_person_email : '' ) }}" >
                                                <input type="hidden" name="role" value="1_Company">
                                            </div>
                                        </li>
                                        <li>
                                            <p>Country</p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <select class="custom-select select-country" name="country">
                                                    <option value="">Select Country</option>
                                                    @php
                                                        $selected_country_id = old('country') ? jsdecode_userdata(old('country')) : ($companyDetail->company_detail ? $companyDetail->company_detail->country_id : '') ;
                                                    @endphp
                                                    @foreach( $country as $countryId => $countryName )
                                                    <option value="{{ jsencode_userdata($countryId) }}" {{ $selected_country_id == $countryId ? 'selected' : '' }} >{{ $countryName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </li>
                                        <li>
                                            <p>State</p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <select class="custom-select select-state" name="state" data-selected-id="{{ old('state' ,$companyDetail->company_detail ? jsencode_userdata($companyDetail->company_detail->state_id) : '') }}">
                                                    <option value="">Select State</option>
                                                </select>
                                            </div>
                                        </li>
                                        <li>
                                            <p>City</p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <select class="custom-select select-city" name="city" data-selected-id="{{ old('city',$companyDetail->company_detail ? jsencode_userdata($companyDetail->company_detail->city_id) : '' ) }}">
                                                    <option value="">Select City</option>
                                                </select>
                                            </div>
                                        </li>
                                        <li>
                                            <p>Address</p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <input type="text" class="form-control"  value="{{ old('address',$companyDetail->company_detail ? $companyDetail->company_detail->address : '') }}" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Address" name="address">
                                            </div>
                                        </li>
                                        @can('company-status')
                                        <li>
                                            <p>Status</p>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="status" value="1" {{ $companyDetail->status ? 'checked' : '' }} class="custom-control-input" id="user_status"  >
                                                <label class="custom-control-label" for="user_status">

                                                </label>
                                            </div>
                                        </li>
                                        @endcan
                                    </ul>
                                    <div class="footer-menus_button">
                                        <div class="invoice-list">
                                            <input type="hidden" name="role" value="Company">
                                        </div>
                                        <div class="submit-btns">
                                            <ul>
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
            </div>
            <!-- tabs End here -->
        </div>
    </div>
</section>
@parent
@endsection


@section('page-js')
<script>
    $('.datepicker').datepicker({
        format:"yyyy-mm-dd"
    });
    $("#update-company").validate({
        ignoore: '',
        rules:{
            company_name:{
                required:true,
                maxlength:100
            },
            contact_person:{
                required:true,
                maxlength:100
            },
            company_email:{
                required:true,
                email:true
            },
            contact_number:{
                number: true
            },
            contact_person_email:{
                required:true,
                email:true
            }
        },
        messages:{
            company_name:{
                required:"Company name is required"
            },
            company_email:{
                required:"Email is required"
            }
        },
        errorPlacement: function(error, element) {
            console.log( element.closest("li") );
            error.appendTo( element.closest("li") );
        }

    });
</script>
@endsection
