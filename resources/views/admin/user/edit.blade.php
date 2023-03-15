@extends('admin.layouts.app')
@section('title', '- Users')


@section('content')
<section class="order-listing Invoice-listing edit-module">
    <div class="container">
        <div class="left-content d-flex">
            <div class="list-title d-flex">
                <i class="fas fa-users" style="font-size: 30px;"></i>
                <div class="list-content">
                    <h2 class="heading-text">Manage Users</h2>
                    <h2 class="mobile-text d-none">Manage Users</h2>
                    <p>
                        Add , View and Edit the details
                    </p>
                </div>
            </div>
            <div class="right-btns">
                <div class="">
                    <a class="nav-link btn navy-blue-btn" href="{{ route('user.list') }}"  aria-expanded="false">
                    List User
                    </a>
                </div>
            </div>
        </div>
        <div class="order-listing Invoice-tabs">
            <x-alert/>
            <!-- tabs Start here -->
            <ul class="tabs nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">Update User</a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Deleted Users</a>
                </li> --}}
            </ul>

            <!-- Search section End here -->
            <div class="tab-content invoice-tab-content">
                <div class="tab-pane active" id="tabs-1" role="tabpanel">
                    <div class="">
                        <form method="post" action="{{ route('user.edit',['id'=>jsencode_userdata($userDetail->id)]) }}" id="update-user">
                        @csrf
                            <div class="invoice-detail invoice-creation">
                                <div class="invoice-details-inner">
                                    <div class="detail-item-1 d-flex align-items-center">
                                        <div class="shipmemnt-details-item item1">
                                            <div class="list-content">
                                                <h2>Edit User</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="Contract-details invoice-feild">
                                    <div class='ajax-response'></div>
                                    <ul>
                                        <li>
                                            <p>First name <span class="required-field">*</span></p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <input type="text" class="form-control" value="{{ old('first_name',$userDetail->first_name) }}" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="First name" name="first_name">
                                            </div>
                                        </li>
                                        <li>
                                            <p>Last name <span class="required-field">*</span></p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <input type="text" class="form-control" value="{{ old( 'last_name' , $userDetail->last_name) }}" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Last name" name="last_name">
                                            </div>
                                        </li>
                                        <li>
                                            <p>Email <span class="required-field">*</span></p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <input type="text"  value="{{ old('email' , $userDetail->email) }}" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Email" name="email">
                                            </div>
                                        </li>

                                        <li>
                                            <p>Company</p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <select class="custom-select select-company" name="company">
                                                    <option value="">Select company</option>
                                                    @foreach( $company as $companyId => $companyName )
                                                    <option value="{{ jsencode_userdata($companyId) }}"  @isset($userDetail){{ $userDetail->company_id() == $companyId ? 'selected' : '' }} @endisset>{{ $companyName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </li>

                                        <li>
                                            <p>Mobile</p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <input type="text" class="form-control"  value="{{ old( 'mobile' , $userDetail->user_detail ? $userDetail->user_detail->mobile : '' ) }}" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Mobile number" name="mobile">
                                            </div>
                                        </li>

                                        <li>
                                            <p>Gender</p>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="gender" value="male" {{ old('gender',$userDetail->user_detail ? $userDetail->user_detail->gender : '') == "male" ? "checked" : "" }}  class="custom-control-input" id="gender_male">
                                                <label class="custom-control-label" for="gender_male">
                                                    Male
                                                </label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="gender" value="female" {{ old('gender',$userDetail->user_detail ? $userDetail->user_detail->gender : '') == "female" ? "checked" : "" }} class="custom-control-input" id="gender_female">
                                                <label class="custom-control-label" for="gender_female">
                                                    Female
                                                </label>
                                            </div>
                                        </li>

                                        <li>
                                            <p>Date of birth</p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <input type="text" class="form-control datepicker" data-date-end-date="0d" value="{{ old('dob',$userDetail->user_detail ? $userDetail->user_detail->dob : '') }}" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Date of birth" name="dob">
                                            </div>
                                        </li>


                                        <li>
                                            <p>Country</p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <select class="custom-select select-country" name="country">
                                                    <option value="">Select Country</option>
                                                    @php
                                                        $selected_country_id = old('country') ? jsdecode_userdata(old('country')) : ($userDetail->user_detail ? $userDetail->user_detail->country_id : '') ;
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
                                                <select class="custom-select select-state" name="state" data-selected-id="{{ old('state' ,$userDetail->user_detail ? jsencode_userdata($userDetail->user_detail->state_id) : '') }}">
                                                    <option value="">Select State</option>
                                                </select>
                                            </div>
                                        </li>
                                        <li>
                                            <p>City</p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <select class="custom-select select-city" name="city" data-selected-id="{{ old('city',$userDetail->user_detail ? jsencode_userdata($userDetail->user_detail->city_id) : '' ) }}">
                                                    <option value="">Select City</option>
                                                </select>
                                            </div>
                                        </li>
                                        <li>
                                            <p>Address</p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <input type="text" class="form-control"  value="{{ old('address',$userDetail->user_detail ? $userDetail->user_detail->address : '') }}" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Address" name="address">
                                            </div>
                                        </li>
                                        <li>
                                            <p>Status</p>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="status" value="1" {{ $userDetail->status ? 'checked' : '' }} class="custom-control-input" id="user_status"  >
                                                <label class="custom-control-label" for="user_status">

                                                </label>
                                            </div>
                                        </li>
                                    </ul>
                                    <div class="footer-menus_button">
                                        <div class="invoice-list">

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
    $("#update-user").validate({
        ignoore: '',
        rules:{
            first_name:{
                required:true,
                maxlength:100
            },
            last_name:{
                required:true,
                maxlength:100
            },
            email:{
                required:true,
                email:true
            },
            mobile:{
                number: true
            },
            weight:{
                number: true
            },
            height:{
                number: true
            },
        },
        messages:{
            first_name:{
                required:"First name is required"
            },
            last_name:{
                required:"Last name is required"
            },
            email:{
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
