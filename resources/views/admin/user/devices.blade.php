@extends('admin.layouts.app')
@section('title', '- Devices')


@section('content')
<section class="order-listing Invoice-listing">
    <div class="container">
        <div class="left-content d-flex">
            <div class="list-title d-flex">
                <i class="fas fa-mobile" style="font-size: 30px;"></i>
                <div class="list-content">
                    <h2 class="heading-text">Manage {{ucfirst($userData->fullName) }}'s Devices</h2>
                    <h2 class="mobile-text d-none">Manage Devices</h2>
                    <p>
                        Add , View and Edit the details
                    </p>
                </div>
            </div>
            @can('user-add')
            <div class="right-btns">
                <div class="">
                    <a class="nav-link btn navy-blue-btn open-section " data-target="create-device-popup" href="javascript:void(0)">
                    Add Device
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
                    <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">All Devices</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="delete-user-tab" data-toggle="tab" href="#tabs-2" role="tab">Deleted Devices</a>
                </li>
            </ul>
            <!-- Search section Start here -->
            <div class="list-header d-flex justify-content-between">
                <form class="form-inline my-2 my-lg-0">
                    {{-- <input class="form-control search-input" type="search" placeholder="Search User" aria-label="Search">
                    <button class="btn btn-outline-dark my-2 my-sm-0 form-control-feedback" type="submit"><img src="{{ asset('assets/images/search-filter.svg') }}"></button> --}}
                </form>
            </div>
            <!-- Search section End here -->
            <div class="tab-content invoice-tab-content">
                <!-- User listing -->
                @isset($data)
                    <div class="tab-pane active" id="tabs-1" role="tabpanel">
                        <div class=" table-responsive list-items">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col" class="purchase-order-date">
                                            @sortablelink('device_name', 'Device Name')
                                        </th>
                                        <th scope="col" class="purchase-order-date">
                                           Activation Code
                                        </th>
                                        <th scope="col">@sortablelink('created_at', 'Created Date')</th>
                                        <th scope="col" class="text-center status-text purchase-order-date" style="display:table-cell">@sortablelink('status', 'Status')</th>
                                        <th scope="col" class="purchase-order-date text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $singleDevice)
                                    <tr>
                                        <td class="purchase-order-date">
                                            <a href="#" class="open-section get-user-detail" data-target="user-details" data-user-id="{{ jsencode_userdata($singleDevice->id) }}">
                                                {{ $singleDevice->device_name }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $singleDevice->device_activation_code ? ($singleDevice->device_activation_code): 'NA' }}
                                        </td>
                                        <td>
                                            {{ changeDateFormat($singleDevice->created_at) }}
                                        </td>
                                        <td class="text-center status-text">
                                            <input data-id="{{ jsencode_userdata($singleDevice->id) }}" class="toggle-class"  data-style="ios" type="checkbox" data-onstyle="success" data-height="20" data-width="70"  data-offstyle="danger" data-toggle="toggle"  data-size="mini" data-on="Active" data-off="InActive" {{ $singleDevice->status ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center purchase-order-date">
                                            @can('user-delete')
                                            <a title="Delete" onclick="event.stopPropagation()" class="delete-temp" href="{{ route('user.deviceDelete',['id'=>jsencode_userdata($singleDevice->id)]) }}">
                                                <i class="fas fa-trash" style="color:#FF0000"></i>
                                            </a>
                                            @endcan
                                        </td>

                                    </tr>
                                    @empty
                                    <tr >
                                        <td colspan="7" class="purchase-order-date">
                                            No Device deleted yet!
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
                @endisset
                <!-- Delete user listing -->
                @isset($deletedDevices)
                    <div class="tab-pane" id="tabs-2" role="tabpanel">
                        <div class=" table-responsive list-items">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col" class="purchase-order-date">
                                            @sortablelink('device_name', 'Device Name')
                                        </th>
                                        <th scope="col" class="purchase-order-date">
                                           Activation Code
                                        </th>
                                        <th scope="col">@sortablelink('created_at', 'Created Date')</th>
                                        <th scope="col" class="text-center status-text purchase-order-date" style="display:table-cell">@sortablelink('status', 'Status')</th>
                                        <th scope="col" class="purchase-order-date text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($deletedDevices as $singleDevice)
                                    <tr>
                                        <td class="purchase-order-date">
                                            <a href="#" class="open-section get-user-detail" data-target="user-details" data-user-id="{{ jsencode_userdata($singleDevice->id) }}">
                                                {{ $singleDevice->device_name }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $singleDevice->device_activation_code ? ($singleDevice->device_activation_code): 'NA' }}
                                        </td>
                                        <td>
                                            {{ changeDateFormat($singleDevice->created_at) }}
                                        </td>
                                        <td class="text-center status-text">
                                            <input data-id="{{ jsencode_userdata($singleDevice->id) }}" class="toggle-class"  data-style="ios" type="checkbox" data-onstyle="success" data-height="20" data-width="70"  data-offstyle="danger" data-toggle="toggle"  data-size="mini" data-on="Active" data-off="InActive" {{ $singleDevice->status ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center" class="purchase-order-date">
                                            <a onclick="event.stopPropagation()" title="Restore" href="{{ route('user.restore',['id'=>jsencode_userdata($singleDevice->id)]) }}">
                                                <i class="fas fa-trash-restore"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr >
                                        <td colspan="7" class="purchase-order-date">
                                            No Device deleted yet!
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="table-footer">
                                    <tr>
                                        <td colspan="7">
                                            {{ $deletedDevices->appends(request()->except('page','open_section'))->links() }}
                                            <p>
                                                Displaying {{$deletedDevices->count()}} of {{ $deletedDevices->total() }} user(s).
                                            </p>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endisset
            </div>
            <!-- tabs End here -->
        </div>
    </div>
</section>
@parent
@endsection

@section('footer-html')
@include('admin.user.device-popup')
@endsection

@section('page-js')
<script>
    $(document).ready(function(){
        $("#create-device").validate({
            ignoore: '',
            rules:{
                device_name:{
                    required:true,
                    maxlength:100
                },
            },
            messages:{
                device_name:{
                    required:"Device name is required"
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
        $('.toggle-class').change(function() {
            var status = $(this).prop('checked') == true ? 1 : 0;
            var id = $(this).data('id');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/user/changeDeviceStatus',
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
    });
</script>
@endsection
