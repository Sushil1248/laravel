@extends(Auth::check() && Auth::user()->hasRole('Company') ? 'company.layouts.app' : 'admin.layouts.app')

@section('title', '- Edit Vehicle')


@section('content')
<section class="order-listing Invoice-listing edit-module">
    <div class="container">
        <div class="left-content d-flex">
            <div class="list-title d-flex">
                <i class="fas fa-cubes" style="font-size: 30px;"></i>
                <div class="list-content">
                    <h2 class="heading-text">Manage Vehicles</h2>
                    <h2 class="mobile-text d-none">Manage Vehicles</h2>
                    <p>
                        Add , View and Edit the details
                    </p>
                </div>
            </div>
            <div class="right-btns">
                <div class="">
                    <a class="nav-link btn navy-blue-btn" href="{{ route('c.vehicle.list') }}"  aria-expanded="false">
                    List Vehicle
                    </a>
                </div>
            </div>
        </div>
        <div class="order-listing Invoice-tabs">
            <x-alert/>
            <!-- tabs Start here -->
            <ul class="tabs nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">Update Vehicle
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Deleted Users</a>
                </li> --}}
            </ul>

            <!-- Search section End here -->
            <div class="tab-content invoice-tab-content">
                <div class="tab-pane active" id="tabs-1" role="tabpanel">
                    <div class="">
                        <form method="post" action="{{ route('c.vehicle.edit',['id'=>jsencode_userdata($companyDetail->id)]) }}" id="update-company">
                        @csrf
                            <div class="invoice-detail invoice-creation">
                                <div class="invoice-details-inner">
                                    <div class="detail-item-1 d-flex align-items-center">
                                        <div class="shipmemnt-details-item item1">
                                            <div class="list-content">
                                                <h2>Edit vehicle</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="Contract-details invoice-feild">
                                    <div class='ajax-response'></div>
                                    <ul>
                                        <li>
                                            <p>Vehicle name <span class="required-field">*</span></p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <input type="text" class="form-control" value="{{ old('name',$companyDetail->name) }}" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Company name" name="name">
                                            </div>
                                        </li>
                                        <li>
                                            <p>Vehicle Number</p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <input type="text" class="form-control"  value="{{ old( 'vehicle_num' , $companyDetail ? $companyDetail->vehicle_num : '' ) }}" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Vehicle Person" name="vehicle_num">
                                            </div>
                                        </li>

                                        <li>
                                            <p>Extra Notes</p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <input type="text" class="form-control"  value="{{ old('extra_notes',$companyDetail->extra_notes ? $companyDetail->extra_notes: '') }}" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Extra Notes" name="extra_notes">
                                            </div>
                                        </li>
                                        <li>
                                            <p>Status</p>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="status" value="1" {{ $companyDetail->status ? 'checked' : '' }} class="custom-control-input" id="user_status"  >
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
    $("#update-company").validate({
        ignoore: '',
        rules:{
            name:{
                required:true,
            },
            vehicle_num:{
                required:true,
            },
        },
        messages:{
            name:{
                required:"Vehicle name is required"
            },
            vehicle_num:{
                required:"Vehicle Number is required"
            },

        },
        errorPlacement: function(error, element) {
            console.log( element.closest("li") );
            error.appendTo( element.closest("li") );
        }

    });
</script>
@endsection
