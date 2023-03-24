@extends(Auth::check() && Auth::user()->role == 'Company' ? 'company.layouts.app' : 'admin.layouts.app')
@section('title', '- Company')


@section('content')

<section class="order-listing Invoice-listing">
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
            @can('company-add')
            <div class="right-btns">
                <div class="">
                    <a class="nav-link btn navy-blue-btn open-section" data-target="create-company-popup" href="javascript:void(0)"  aria-expanded="false">
                    Create Comapny
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
                    <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">All Companies</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="delete-user-tab" data-toggle="tab" href="#tabs-2" role="tab">Deleted Companies</a>
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
                            <a class="btn navy-blue-btn export-btn" data-target="create-companies" href="{{ route('company.export')}}" aria-expanded="false">
                                <i class="fas fa-file-download"></i> Export
                            </a>
                        </li>
                        <li class="filters-btn">
                            <button class="filter-text open-section" data-target="company-filter" > <img src="{{ asset('assets/images/Filters lines.svg') }}"> Filters</button>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Search section End here -->
            <div class="tab-content invoice-tab-content">
                <!-- company listing -->
                <div class="tab-pane active" id="tabs-1" role="tabpanel">
                    <div class=" table-responsive list-items">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col" class="purchase-order-date">
                                        @sortablelink('company_name', 'Company Name')
                                    </th>
                                    <th scope="col" class="purchase-order-date">
                                        @sortablelink('company_email', 'Email')
                                    </th>
                                    <th scope="col">
                                        Contact Person
                                    </th>
                                    <th scope="col">
                                        Contact Number
                                    </th>

                                    <th scope="col">@sortablelink('created_at', 'Created Date')</th>
                                    <th scope="col" class="text-center status-text purchase-order-date" style="display:table-cell">@sortablelink('status', 'Status')</th>
                                    @if( auth()->user()->can('company-edit') || auth()->user()->can('company-delete') )
                                    <th scope="col" class="purchase-order-date text-center">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $singleCompany)
                                <tr>
                                    <td class="purchase-order-date">
                                        @can('company-view')
                                        <a href="#" class="open-section get-company-detail" data-target="company-details" data-user-id="{{ jsencode_userdata($singleCompany->id) }}">{{ $singleCompany->company_detail->company_name }}</a>
                                        @else
                                        {{ $singleCompany->company_detail->company_name }}
                                        @endcan
                                    </td>
                                    <td class="purchase-order-date">
                                        {{ $singleCompany->email }}
                                    </td>
                                    <td class="purchase-order-date">
                                        {{ $singleCompany && $singleCompany->company_detail->contact_person ? ucfirst($singleCompany->company_detail->contact_person) : 'NA' }}
                                    </td>
                                    <td>
                                        {{ $singleCompany && $singleCompany->company_detail->contact_number ? $singleCompany->company_detail->contact_number : 'NA' }}
                                    </td>

                                    <td>
                                        {{ changeDateFormat($singleCompany->created_at) }}
                                    </td>
                                    <td class="text-center status-text">
                                        <input @if( !auth()->user()->can('company-status') ) disabled @endif data-id="{{ jsencode_userdata($singleCompany->id) }}" class="toggle-class"  data-style="ios" type="checkbox" data-onstyle="success" data-offstyle="danger" data-height="20" data-width="70" data-toggle="toggle"  data-size="mini" data-on="Active" data-off="InActive" {{ $singleCompany->status ? 'checked' : '' }}>
                                    </td>
                                    <td class="text-center purchase-order-date">
                                        @can('company-edit')
                                        <a title="Edit" href="{{ route('company.edit',['id'=>jsencode_userdata($singleCompany->id)]) }}" onclick="event.stopPropagation()"><i class="fas fa-pencil-alt" style="color:#33383a"></i></a>&nbsp;&nbsp;
                                        @endcan
                                        @can('company-delete')
                                        <a title="Delete" onclick="event.stopPropagation()" class="delete-temp" href="{{ route('company.delete',['id'=>jsencode_userdata($singleCompany->id)]) }}">
                                            <i class="fas fa-trash" style="color:#FF0000"></i>
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7">
                                        No Company yet!
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-footer">
                                <tr>
                                    <td colspan="7">
                                        {{ $data->appends(request()->except('dpage','page','open_section'))->links() }}
                                        <p>
                                            Displaying {{$data->count()}} of {{ $data->total() }} company(s).
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
                                        @sortablelink('company_name', 'Company Name')
                                    </th>
                                    <th scope="col" class="purchase-order-date">
                                        @sortablelink('email', 'Company Email')
                                    </th>
                                    <th scope="col">
                                        Contact Person
                                    </th>
                                    <th scope="col">
                                        Contact Number
                                    </th>

                                    <th scope="col">@sortablelink('created_at', 'Created Date')</th>
                                    <th scope="col" class="text-center status-text purchase-order-date" style="display:table-cell">@sortablelink('status', 'Status')</th>
                                    <th scope="col" class="purchase-order-date text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @isset($deletedCompanies)
                                    @forelse ($deletedCompanies as $singleCompany)
                                    <tr>
                                        <td class="purchase-order-date">
                                            <a href="#" class="open-section get-company-detail" data-target="company-details" data-user-id="{{ jsencode_userdata($singleCompany->id) }}">
                                                {{ $singleCompany->company_detail->company_name }}
                                            </a>
                                        </td>
                                        <td class="purchase-order-date">
                                            {{ $singleCompany->email }}
                                        </td>
                                        <td>
                                            {{ $singleCompany && $singleCompany->contact_person ? $singleCompany->company_detail->contact_person : 'NA' }}
                                        </td>
                                        <td>
                                            {{ $singleCompany && $singleCompany->contact_number ? $singleCompany->company_detail->contact_number : 'NA' }}
                                        </td>
                                        <td>
                                            {{ changeDateFormat($singleCompany->created_at) }}
                                        </td>
                                        <td class="text-center status-text">
                                            <input data-id="{{ jsencode_userdata($singleCompany->id) }}" class="toggle-class"  data-style="ios" type="checkbox" data-onstyle="success" data-height="20" data-width="70"  data-offstyle="danger" data-toggle="toggle"  data-size="mini" data-on="Active" data-off="InActive" {{ $singleCompany->status ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center" class="purchase-order-date">
                                            <a onclick="event.stopPropagation()" title="Restore" href="{{ route('company.restore',['id'=>jsencode_userdata($singleCompany->id)]) }}">
                                                <i class="fas fa-trash-restore"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr >
                                        <td colspan="7" class="purchase-order-date">
                                            No Company deleted yet!
                                        </td>
                                    </tr>
                                    @endforelse
                                @endisset
                            </tbody>
                            <tfoot class="table-footer">
                                <tr>
                                    <td colspan="7">
                                        @isset($deletedUsers)
                                        {{ $deletedUsers->appends(request()->except('page','open_section'))->links() }}
                                        <p>
                                            Displaying {{$deletedUsers->count()}} of {{ $deletedUsers->total() }} user(s).
                                        </p>
                                        @endisset
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
@include('admin.company.popups')
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
                url: '/company/changeStatus',
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
        $("#update-company-password form").validate({
            ignoore: '',
            rules:{
                password:{
                    required:true,
                    minlength:6
                },
                password_confirmation:{
                    required:true,
                    equalTo: "#update-company-password form input[name=password]"
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
        $("#create-company").validate({
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
                contact_person_email:{
                    required:true,
                    email:true
                },
                password:{
                    required:true,
                    minlength:6
                },
                contact_number:{
                    required:true,
                    number: true
                },
            },
            messages:{
                company_name:{
                    required:"Company name is required"
                },
                contact_person:{
                    required:"Contact person name is required"
                },
                contact_number:{
                    required:"Contact number is required"
                },
                company_email:{
                    required:"Email is required"
                },
                password:{
                    required:"Password is required"
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
                            response_ajax.html('<div class="alert alert-danger  alert-dismissible "><a href="#" class="close" data-dism.validate({iss="alert" aria-label="close">&times;</a>'+ data.msg + '</div>');
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
        $(".get-company-detail").on("click",function(){
            $.get("/company/details/"+$(this).data("user-id"), function(data, status){
                console.log(data)
                if( data.status ){
                    for (let input_name in data.data)
                    $(`#company-details input[name=${input_name}]`).val( data.data[input_name] );
                    $(`#company-details .submit-button`).attr( "href" , data.data.edit_user );
                }
            });
        });
        @if( request('user_id') )
        if( $(".table tr .get-company-detail").length ){
            $(".table tr .get-company-detail").click();
        }
        @endif
        @if( request('dpage') )
        $("#delete-company-tab").click();
        @endif
        $(".update-company-password").on("click",function(){
            $("#update-company-password form").attr( "action" , $(this).data("update-password") );
        });
    });
</script>
@endsection
