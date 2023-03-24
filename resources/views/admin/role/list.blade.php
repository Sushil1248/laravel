@extends(Auth::check() && Auth::user()->hasRole('1_Company') ? 'company.layouts.app' : 'admin.layouts.app')
@section('title', '- Role Management')


@section('content')
    <div class="container">
        <div class="left-content d-flex">
            <div class="list-title d-flex">
                <i class="fas fa-cubes" style="font-size: 30px;"></i>
                <div class="list-content">
                    <h2 class="heading-text">Manage Roles</h2>
                    <h2 class="mobile-text d-none">Manage Roles</h2>
                    <p>
                        Add , View and Edit the details
                    </p>
                </div>
            </div>
            @can('role-add')
                <div class="right-btns">
                    <div class="">
                        <a class="nav-link btn navy-blue-btn open-section" data-target="create-role-popup"
                            href="javascript:void(0)" aria-expanded="false">
                            Create Role
                        </a>
                    </div>
                </div>
            @endcan
        </div>

        <div class="tab-content invoice-tab-content">
            <!-- rolelisting -->
            <div class="tab-pane active" id="tabs-1" role="tabpanel">
                <div class=" table-responsive list-items">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col" class="purchase-order-date">
                                    @sortablelink('role_name', 'Role Name')
                                </th>
                                <th scope="col">@sortablelink('created_at', 'Created Date')</th>
                                {{--  <th scope="col" class="text-center status-text purchase-order-date" style="display:table-cell">@sortablelink('status', 'Status')</th> --}}
                                @if (auth()->user()->can('role-edit') ||
                                        auth()->user()->can('role-delete'))
                                    <th scope="col" class="purchase-order-date text-center">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $key => $role)
                                <tr>
                                    <td class="purchase-order-date">
                                        @can('role-view')
                                            <a href="#" class="open-section get-user-detail" data-target="user-details"
                                                data-user-id="{{ jsencode_userdata($role->id) }}">{{ trim_role_name($role->name) }}</a>
                                        @else
                                            {{ trim_role_name($role->name) }}
                                        @endcan
                                    </td>
                                    <td>
                                        {{ changeDateFormat($role->created_at) }}
                                    </td>
                                    {{-- <td class="text-center status-text">
                                    <input @if (!auth()->user()->can('user-status')) disabled @endif data-id="{{ jsencode_userdata($role->id) }}" class="toggle-class"  data-style="ios" type="checkbox" data-onstyle="success" data-offstyle="danger" data-height="20" data-width="70" data-toggle="toggle"  data-size="mini" data-on="Active" data-off="InActive" {{ $role->status ? 'checked' : '' }}>
                                </td> --}}
                                    <td class="text-center purchase-order-date">
                                        @can('role-edit')
                                            <a title="Edit"
                                                href="{{ route('roles.edit', ['id' => jsencode_userdata($role->id)]) }}"
                                                onclick="event.stopPropagation()"><i class="fas fa-pencil-alt"
                                                    style="color:#33383a"></i></a>&nbsp;&nbsp;
                                        @endcan
                                        {{-- @can('role-delete')
                                            <a title="Delete" onclick="event.stopPropagation()" class="delete-temp" href="{{ route('role.delete',['id'=>jsencode_userdata($role->id)]) }}">
                                                <i class="fas fa-trash" style="color:#FF0000"></i>
                                            </a>
                                        @endcan --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        No role yet!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-footer">
                            <tr>
                                <td colspan="7">
                                    {{ $data->appends(request()->except('dpage', 'page', 'open_section'))->links() }}
                                    <p>
                                        Displaying {{ $data->count() }} of {{ $data->total() }} role(s).
                                    </p>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- Delete user listing -->

        </div>

    </div>
@endsection
@section('footer-html')
    @include('admin.role.popups')
@endsection

@section('page-js')
    <script>
        $(document).ready(function() {
            $("#create-role").validate({
                ignoore: '',
                rules: {
                    name: {
                        required: true,
                        maxlength: 100
                    },
                },
                messages: {
                    name: {
                        required: "Role name is required"
                    },
                },
                errorPlacement: function(error, element) {
                    console.log(element.closest("li"));
                    error.appendTo(element.closest("li"));
                },
                submitHandler: function(form) {
                    var formData = jQuery(form);
                    if (!formData.find(".ajax-response").length)
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
                            console.log(data);
                            if (data.success == true) {

                                response_ajax.html(
                                    '<div class="alert alert-success  alert-dismissible "><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' +
                                    data.msg + '</div>');
                                response_ajax.removeClass("hidden");
                                submit_button.html(btnText);
                                submit_button.attr("disabled", false);
                                setTimeout(function() {
                                    location.reload(true);
                                }, 1000);

                            } else if (data.success == false) {
                                response_ajax.html(
                                    '<div class="alert alert-danger  alert-dismissible "><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' +
                                    data.msg + '</div>');
                                submit_button.html(btnText);
                                submit_button.attr("disabled", false);
                            }
                        },
                        error: function(jqXHR, exception) {
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
                                response_ajax.html(
                                    '<div class="alert alert-danger  alert-dismissible "><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' +
                                    erro + '</div>');

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
                var value = $(this).data('value');
                var role = $(this).data('role');
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: '/roles/assign-permission',
                    data: {
                        'status': status,
                        'value': value,
                        'role': role
                    },
                    success: function(data) {
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
        })
    </script>
@endsection
