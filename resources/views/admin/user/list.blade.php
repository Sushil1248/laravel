@extends('admin.layouts.app')
@section('title', '- Users')


@section('content')

<section class="order-listing Invoice-listing">
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
            @can('user-add')
            <div class="right-btns">
                <div class="d-flex">
                    <a class="nav-link btn navy-blue-btn open-section" data-target="create-user-popup" href="javascript:void(0)"  aria-expanded="false">
                    Create User
                    </a>
                    <a title="Send Notification to All Users" onclick="event.stopPropagation()" class="btn btn-sm open-section"  data-attribute="all_users" data-pass-id="users" data-target="push-notification-popup" href="javascript:void(0)" >
                        <i class="fas fa-bell" style="color:#33383a"></i>
                    </a>
                </div>
            </div>
            @endcan
        </div>
        <div class="order-listing Invoice-tabs">
            <x-alert/>
            <!-- tabs Start here -->
            <ul class="tabs nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">All Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="delete-user-tab" data-toggle="tab" href="#tabs-2" role="tab">Deleted Users</a>
                </li>
            </ul>
            <!-- Search section Start here -->
            <div class="list-header d-flex justify-content-between">
                <form class="form-inline my-2 my-lg-0">
                    {{-- <input class="form-control search-input" type="search" placeholder="Search User" aria-label="Search">
                    <button class="btn btn-outline-dark my-2 my-sm-0 form-control-feedback" type="submit"><img src="{{ asset('assets/images/search-filter.svg') }}"></button> --}}
                </form>
                <div class="list-filters d-flex  align-items-center   ">
                    <ul class="d-flex justify-content-between align-items-center">
                        @if( request('daterange_filter')  || request('search') )
                        <li>
                            <h6>Applied Filters:</h6>
                        </li>
                        @foreach( request()->only('daterange_filter','search') as $search_by => $search_value )
                        @if( $search_value )
                        <li><button class="filter-text">{{ $search_value }} <a href="{{ removeQueryParameter($search_by) }}"><img src="{{ asset('assets/images/close.svg') }}"></a> </button></li>
                        @endif
                        @endforeach
                        @endif
                        <li class="">
                            <a class="btn navy-blue-btn export-btn" data-target="create-users" href="{{ route('user.export')}}" aria-expanded="false">
                                <i class="fas fa-file-download"></i> Export
                            </a>
                        </li>
                        <li class="filters-btn">
                            <button class="filter-text open-section" data-target="user-filter" > <img src="{{ asset('assets/images/Filters lines.svg') }}"> Filters</button>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Search section End here -->
            <div class="tab-content invoice-tab-content">
                <!-- User listing -->
                <div class="tab-pane active" id="tabs-1" role="tabpanel">
                    <div class=" table-responsive list-items">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col" class="purchase-order-date">
                                        @sortablelink('first_name', 'Full Name')
                                    </th>
                                    <th scope="col" class="purchase-order-date">
                                        @sortablelink('email', 'Email')
                                    </th>
                                    <th scope="col">
                                        Company
                                    </th>

                                    <th scope="col">
                                        Mobile
                                    </th>

                                    <th scope="col">@sortablelink('created_at', 'Created Date')</th>
                                    <th scope="col" class="text-center status-text purchase-order-date" style="display:table-cell">@sortablelink('status', 'Status')</th>
                                    @if( auth()->user()->can('user-edit') || auth()->user()->can('user-delete') )
                                    <th scope="col" class="purchase-order-date text-center">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $singleUser)

                                <tr>
                                    <td class="purchase-order-date">
                                        @can('user-view')
                                        <a href="#" class="open-section get-user-detail" data-target="user-details" data-user-id="{{ jsencode_userdata($singleUser->id) }}">{{ $singleUser->full_name }}</a>
                                        @else
                                        {{ $singleUser->full_name }}
                                        @endcan
                                    </td>
                                    <td class="purchase-order-date">
                                        {{ $singleUser->email }}
                                    </td>
                                    <td class="purchase-order-date">
                                        <a title="view company" href="{{ route('company.list',['search'=>get_company_name($singleUser->company_id())]) }}" onclick="event.stopPropagation()">{{ $singleUser && $singleUser->company_id() ? ucfirst(get_company_name($singleUser->company_id())) : 'NA' }}</a>
                                    </td>

                                    <td>
                                        {{ $singleUser->user_detail && $singleUser->user_detail->mobile ? $singleUser->user_detail->mobile : 'NA' }}
                                    </td>

                                    <td>
                                        {{ changeDateFormat($singleUser->created_at) }}
                                    </td>
                                    <td class="text-center status-text">
                                        <input @if( !auth()->user()->can('user-status') ) disabled @endif data-id="{{ jsencode_userdata($singleUser->id) }}" class="toggle-class"  data-style="ios" type="checkbox" data-onstyle="success" data-offstyle="danger" data-height="20" data-width="70" data-toggle="toggle"  data-size="mini" data-on="Active" data-off="InActive" {{ $singleUser->status ? 'checked' : '' }}>
                                    </td>
                                    <td class="text-center purchase-order-date">
                                        <a title="Manage Devices" class="manage-devices" href="{{ route('user.devices',['id'=>jsencode_userdata($singleUser->id)]) }}">
                                            <i class="fas fa-mobile-phone" style="color:#33383a"></i>
                                        </a>&nbsp;&nbsp;
                                        @can('user-edit')
                                        <a title="Edit" href="{{ route('user.edit',['id'=>jsencode_userdata($singleUser->id)]) }}" onclick="event.stopPropagation()"><i class="fas fa-pencil-alt" style="color:#33383a"></i></a>&nbsp;&nbsp;
                                        <a title="Change Password" data-update-password="{{ route('user.update-password',['id'=>jsencode_userdata($singleUser->id)]) }}" class="open-section update-user-password" data-target="update-user-password" onclick="event.stopPropagation()">
                                            <i class="fas fa-key"></i>
                                        </a>&nbsp;&nbsp;
                                        @endcan
                                        @can('user-delete')
                                        <a title="Delete" onclick="event.stopPropagation()" class="delete-temp" href="{{ route('user.delete',['id'=>jsencode_userdata($singleUser->id)]) }}">
                                            <i class="fas fa-trash" style="color:#FF0000"></i>
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7">
                                        No user yet!
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-footer">
                                <tr>
                                    <td colspan="7">
                                        {{ $data->appends(request()->except('dpage','page','open_section'))->links() }}
                                        <p>
                                            Displaying {{$data->count()}} of {{ $data->total() }} user(s).
                                        </p>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- Delete user listing -->
                <div class="tab-pane" id="tabs-2" role="tabpanel">
                    <div class=" table-responsive list-items">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col" class="purchase-order-date">
                                        @sortablelink('first_name', 'Full Name')
                                    </th>
                                    <th scope="col" class="purchase-order-date">
                                        @sortablelink('email', 'Email')
                                    </th>
                                    <th scope="col">
                                        Company
                                    </th>
                                    <th scope="col">
                                        Mobile
                                    </th>
                                    <th scope="col">@sortablelink('created_at', 'Created Date')</th>
                                    <th scope="col" class="text-center status-text purchase-order-date" style="display:table-cell">@sortablelink('status', 'Status')</th>
                                    <th scope="col" class="purchase-order-date text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($deletedUsers as $singleUser)
                                <tr>
                                    <td class="purchase-order-date">
                                        <a href="#" class="open-section get-user-detail" data-target="user-details" data-user-id="{{ jsencode_userdata($singleUser->id) }}">
                                            {{ $singleUser->full_name }}
                                        </a>
                                    </td>
                                    <td class="purchase-order-date">
                                        {{ $singleUser->email }}
                                    </td>
                                    <td class="purchase-order-date">
                                        {{ $singleUser && $singleUser->company_id() ? ucfirst(get_company_name($singleUser->company_id())) : 'NA' }}
                                    </td>
                                    <td>
                                        {{ $singleUser->user_detail && $singleUser->user_detail->mobile ? $singleUser->user_detail->mobile : 'NA' }}
                                    </td>

                                    <td>
                                        {{ changeDateFormat($singleUser->created_at) }}
                                    </td>
                                    <td class="text-center status-text">
                                        <input data-id="{{ jsencode_userdata($singleUser->id) }}" class="toggle-class"  data-style="ios" type="checkbox" data-onstyle="success" data-height="20" data-width="70"  data-offstyle="danger" data-toggle="toggle"  data-size="mini" data-on="Active" data-off="InActive" {{ $singleUser->status ? 'checked' : '' }}>
                                    </td>
                                    <td class="text-center" class="purchase-order-date">
                                        <a onclick="event.stopPropagation()" title="Restore" href="{{ route('user.restore',['id'=>jsencode_userdata($singleUser->id)]) }}">
                                            <i class="fas fa-trash-restore"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr >
                                    <td colspan="7" class="purchase-order-date">
                                        No user deleted yet!
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-footer">
                                <tr>
                                    <td colspan="7">
                                        {{ $deletedUsers->appends(request()->except('page','open_section'))->links() }}
                                        <p>
                                            Displaying {{$deletedUsers->count()}} of {{ $deletedUsers->total() }} user(s).
                                        </p>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <!-- tabs End here -->
        </div>
    </div>
</section>
@parent
@endsection

@section('footer-html')
@include('admin.user.popups')
@include('admin.user.device-popup')
@endsection

@section('page-js')
<script>
    $(document).ready(function(){
        $('.datepicker').datepicker({
            format:"yyyy-mm-dd"
        });
        $('.toggle-class').change(function() {
            var status = $(this).prop('checked') == true ? 1 : 0;
            var id = $(this).data('id');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/user/changeStatus',
                data: {'status': status, 'id': id},
                success: function(data){
                    //swal("Success!",data.message, "success");
                    var Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                    });
                    Toast.fire({
                        icon: 'success',
                        title: data.message,
                    })
                }
            });
        });
        $("#update-user-password form").validate({
            ignoore: '',
            rules:{
                password:{
                    required:true,
                    minlength:6
                },
                password_confirmation:{
                    required:true,
                    equalTo: "#update-user-password form input[name=password]"
                }
            },
            messages:{
                password:{
                    required:"Password is required"
                },
                password_confirmation:{
                    required:"Confirm password is required"
                }
            }
        });
        $("#create-user").validate({
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
                password:{
                    required:true,
                    minlength:6
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
                },
                password:{
                    required:"Password is required"
                }
            },
            errorPlacement: function(error, element) {
                console.log( element.closest("li") );
                error.appendTo( element.closest("li") );
            },
            submitHandler: function(form) {
                var formData = jQuery(form);
                if( !formData.find(".ajax-response").length )
                    formData.prepend("<div class='ajax-response'></div>");
                var response_ajax = formData.find(".ajax-response"),
                urls = formData.prop('action'),
                submit_button = formData.find(".ajax-submit-button");
                response_ajax.html(''),
                btnText = submit_button.html();
                submit_button.html(btnText + '<i class="fa fa-spinner fa-spin"></i>');
                submit_button.attr("disabled", true);
                jQuery.ajax({
                    type: "POST",
                    url: urls,
                    data: formData.serialize(),
                    dataType: 'json',
                    success: function(data) {
                        console.log( data );
                        if (data.success == true) {

                            response_ajax.html('<div class="alert alert-success  alert-dismissible "><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ data.msg + '</div>');
                            response_ajax.removeClass("hidden");
                            submit_button.html(btnText);
                            submit_button.attr("disabled", false);
                            setTimeout(function() {
                                location.reload(true);
                            }, 1000);

                        } else if(data.success == false){
                            response_ajax.html('<div class="alert alert-danger  alert-dismissible "><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ data.msg + '</div>');
                            submit_button.html(btnText);
                            submit_button.attr("disabled", false);
                        }
                    },
                    error: function (jqXHR, exception) {
                        var msg = '';
                        if (jqXHR.status === 0) {
                            msg = 'Not connect.\n Verify Network.';
                        } else if (jqXHR.status == 404) {
                            msg = 'Requested page not found. [404]';
                        } else if (jqXHR.status == 500) {
                            msg = 'Internal Server Error [500].';
                        } else if (exception === 'parsererror') {
                            msg = 'Requested JSON parse failed.';
                        } else if (exception === 'timeout') {
                            msg = 'Time out error.';
                        } else if (exception === 'abort') {
                            msg = 'Ajax request aborted.';
                        } else {
                            var errors = jQuery.parseJSON(jqXHR.responseText);
                            var erro = '';
                            jQuery.each(errors['errors'], function(n, v) {
                                erro += '<p class="inputerror">' + v + '</p>';
                            });
                            response_ajax.html('<div class="alert alert-danger  alert-dismissible "><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ erro + '</div>');

                            submit_button.html(btnText);
                            submit_button.attr("disabled", false);
                            msg = 'Uncaught Error.\n' + jqXHR.responseText;
                        }
                        //window.location.reload();
                    }
                });
            }
        });
        /** Get user details **/
        $(".get-user-detail").on("click",function(){
            $.get("/user/details/"+$(this).data("user-id"), function(data, status){
                if( data.status ){
                    for (let input_name in data.data)
                        $(`#user-details input[name=${input_name}]`).val( data.data[input_name] );
                    $(`#user-details .submit-button`).attr( "href" , data.data.edit_user );
                    $(`#user-details .subscription-detail`).html('');
                    $(`#user-details .subscription-detail`).html( data.data.subscriptions );
                    // $(`#user-details .mobile-details`).html('');
                    $(`#user-details .mobile-details`).html( data.data.mobile_details );
                }
            });
        });
        @if( request('user_id') )
        if( $(".table tr .get-user-detail").length ){
            $(".table tr .get-user-detail").click();
        }
        @endif
        @if( request('dpage') )
        $("#delete-user-tab").click();
        @endif
        $(".update-user-password").on("click",function(){
            $("#update-user-password form").attr( "action" , $(this).data("update-password") );
        });
    });

    $("#notify-device").validate({
            ignoore: '',
            rules:{
                title:{
                    required:true,
                    maxlength:100
                },
                message:{
                    required:true
                }
            },
            messages:{
                title:{
                    required:"Please add a notification title"
                },
                message:{
                    required:"Please add a message"
                },
            },
            errorPlacement: function(error, element) {
                console.log( element.closest("li") );
                error.appendTo( element.closest("li") );
            },
            submitHandler: function(form) {
                var formData = jQuery(form);
                if( !formData.find(".ajax-response").length )
                    formData.prepend("<div class='ajax-response'></div>");
                var response_ajax = formData.find(".ajax-response"),
                urls = formData.prop('action'),
                submit_button = formData.find(".ajax-submit-button");
                response_ajax.html(''),
                btnText = submit_button.html();
                submit_button.html(btnText + '<i class="fa fa-spinner fa-spin"></i>');
                submit_button.attr("disabled", true);
                jQuery.ajax({
                    type: "POST",
                    url: urls,
                    data: formData.serialize(),
                    dataType: 'json',
                    success: function(data) {
                        console.log( data );
                        if (data.success == true) {

                            response_ajax.html('<div class="alert alert-success  alert-dismissible "><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ data.msg + '</div>');
                            response_ajax.removeClass("hidden");
                            submit_button.html(btnText);
                            submit_button.attr("disabled", false);
                            setTimeout(function() {
                                location.reload(true);
                            }, 1000);

                        } else if(data.success == false){
                            response_ajax.html('<div class="alert alert-danger  alert-dismissible "><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ data.msg + '</div>');
                            submit_button.html(btnText);
                            submit_button.attr("disabled", false);
                        }
                    },
                    error: function (jqXHR, exception) {
                        var msg = '';
                        if (jqXHR.status === 0) {
                            msg = 'Not connect.\n Verify Network.';
                        } else if (jqXHR.status == 404) {
                            msg = 'Requested page not found. [404]';
                        } else if (jqXHR.status == 500) {
                            msg = 'Internal Server Error [500].';
                        } else if (exception === 'parsererror') {
                            msg = 'Requested JSON parse failed.';
                        } else if (exception === 'timeout') {
                            msg = 'Time out error.';
                        } else if (exception === 'abort') {
                            msg = 'Ajax request aborted.';
                        } else {
                            var errors = jQuery.parseJSON(jqXHR.responseText);
                            var erro = '';
                            jQuery.each(errors['errors'], function(n, v) {
                                erro += '<p class="inputerror">' + v + '</p>';
                            });
                            response_ajax.html('<div class="alert alert-danger  alert-dismissible "><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ erro + '</div>');

                            submit_button.html(btnText);
                            submit_button.attr("disabled", false);
                            msg = 'Uncaught Error.\n' + jqXHR.responseText;
                        }
                        //window.location.reload();
                    }
                });
            }
        });
</script>
@endsection
