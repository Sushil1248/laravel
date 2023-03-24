@extends(Auth::check() && Auth::user()->hasRole('1_Company') ? 'company.layouts.app' : 'admin.layouts.app')
@section('title', '- Company')


@section('content')
    <style>
        .card {
            width: 100%;
        }

        .card-header {
            background: #6e5252;
        }

        .card-header a {
            color: #fff;
        }

        .permission-group {
            margin-bottom: 20px;
        }

        .permission-group-title {
            margin: 0;
            font-size: 15px;
            font-weight: bold;
            text-transform: capitalize;
            color: #333;
        }

        .permission-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .permission-item {
            margin: 20px 13px;
        }

        .permission-label {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .permission-input {
            margin-right: 10px;
        }

        .permission-name {
            font-size: 16px;
            color: #221f1f;
            font-weight: 400;
        }

        .permission-item input[type=checkbox] {
            position: relative;
            border: 2px solid #3eaf86;
            border-radius: 2px;
            background: none;
            cursor: pointer;
            line-height: 0;
            margin: 0 .6em 0 0;
            outline: 0;
            padding: 0 !important;
            vertical-align: text-top;
            height: 20px;
            width: 20px;
            -webkit-appearance: none;
            opacity: .5;
        }

        .permission-item input[type=checkbox]:hover {
            opacity: 1;
        }

        .permission-item input[type=checkbox]:checked {
            background-color: #3eaf86;
            opacity: 1;
        }

        .permission-item input[type=checkbox]:before {
            content: '';
            position: absolute;
            right: 50%;
            top: 50%;
            width: 4px;
            height: 10px;
            border: solid #FFF;
            border-width: 0 2px 2px 0;
            margin: -1px -1px 0 -1px;
            transform: rotate(45deg) translate(-50%, -50%);
            z-index: 2;
        }
    </style>
    <section class="order-listing Invoice-listing edit-module">
        <div class="container">
            <div class="left-content d-flex">
                <div class="list-title d-flex">
                    <i class="fas fa-cubes" style="font-size: 30px;"></i>
                    <div class="list-content">
                        <h2 class="heading-text">Manage Access</h2>
                        <h2 class="mobile-text d-none">Manage Role and Permission</h2>
                        <p>
                            View and Update the permissions
                        </p>
                    </div>
                </div>
                <div class="right-btns">
                    <div class="">
                        <a class="nav-link btn navy-blue-btn" href="{{ route('roles.list') }}" aria-expanded="false">
                            List Roles
                        </a>
                    </div>
                </div>
            </div>
            <div class="order-listing Invoice-tabs">
                <x-alert />
                <!-- tabs Start here -->
                <ul class="tabs nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">Update Permissions</a>
                    </li>
                    {{-- <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Deleted Users</a>
                </li> --}}
                </ul>


                <!-- Search section End here -->
                <div class="tab-content invoice-tab-content table-responsive">
                    <div class="tab-pane active" id="tabs-1" role="tabpanel">
                        <div class="col-12 d-flex justify-content-center align-item-center mt-4">
                            <div class="col-12">
                                <div class="row">
                                    <div id="collapse-{{ $roleId }}" class="card-body mt-0 pt-0">
                                        @php $permissions = $record->permissions; @endphp
                                        <div class="row">
                                            @forelse ($permissionByGroup as $pkey=>$item)
                                                <div class="permission-group container mt-0">
                                                    @if (hasGroupPermission($item->group_name))
                                                        <h5 class="permission-group-title"
                                                            style="background:#3eaf86; color:#FFF; padding:10px;">
                                                            {{ $item->group_name }}</h5>
                                                        <ul class="permission-list">
                                                            @php $all_permissions = get_permission_by_user_group($item->group_name); @endphp
                                                            @forelse ($all_permissions as $all_permission)
                                                                @if (Auth::user()->hasRole('1_Company'))
                                                                    @can($all_permission->name)
                                                                        <li class="permission-item">
                                                                            <div class="form-group">
                                                                                <label class="permission-label">
                                                                                    <input class="permission-input toggle-class"
                                                                                        type="checkbox"
                                                                                        name="{{ $all_permission->name }}"
                                                                                        data-value="{{ $all_permission->name }}"
                                                                                        data-role="{{ $record->name }}"
                                                                                        @if ($record->hasPermissionTo($all_permission->name)) checked @endif>
                                                                                    <span
                                                                                        class="permission-name">{{ $all_permission->name }}</span>
                                                                                </label>
                                                                            </div>
                                                                        </li>
                                                                    @endcan
                                                                @else
                                                                    <li class="permission-item">
                                                                        <div class="form-group">
                                                                            <label class="permission-label">
                                                                                <input class="permission-input toggle-class"
                                                                                    type="checkbox"
                                                                                    name="{{ $all_permission->name }}"
                                                                                    data-value="{{ $all_permission->name }}"
                                                                                    data-role="{{ $record->name }}"
                                                                                    @if ($record->hasPermissionTo($all_permission->name)) checked @endif>
                                                                                <span
                                                                                    class="permission-name">{{ $all_permission->name }}</span>
                                                                            </label>
                                                                        </div>
                                                                    </li>
                                                                @endif


                                                            @empty
                                                                <li class="permission-item">
                                                                    <span class="text-secondary">There are currently no
                                                                        permissions created for this permission
                                                                        group!</span>
                                                                </li>
                                                            @endforelse
                                                        </ul>
                                                    @endif
                                                </div>
                                            @empty
                                                <span class="text-secondary ml-2">There are currently no permission created
                                                    or
                                                    assigned to this role!</span>
                                            @endforelse

                                        </div>
                                    </div>
                                </div>
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
        $(document).ready(function() {
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
