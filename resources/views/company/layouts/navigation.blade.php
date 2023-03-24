<li class=" @empty($for_mobile) nav-item @endempty {{ Route::is('company_home') ? 'active' : '' }}">
    <a class="@if( empty($for_mobile) ) nav-link @else nav_link @endif" href="{{ route('company_home') }}">Dashboard <span class="sr-only">(current)</span></a>
</li>

@can('user-list')
<li class="nav-item">
    <div class="dropdown main-menu">
        <a class="nav-link dropdown-toggle {{ Route::is('c.list') ? 'active' : '' }}" data-toggle="dropdown">Users</a>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="{{ route('c.list') }}">All Users</a>
            @can('user-add')<a class="dropdown-item" href="{{ route('c.list',['open_section'=>'create-user-popup']) }}">Add User</a>@endcan
        </div>
    </div>
</li>
@endcan


@can('vehicle-list')
<li class="nav-item">
    <div class="dropdown main-menu">
        <a class="nav-link dropdown-toggle {{ Route::is('vehicle.*') ? 'active' : '' }}" data-toggle="dropdown">Vehicles</a>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="{{ route('vehicle.list') }}">All vehicles</a>
            <a class="dropdown-item" href="{{ route('vehicle.list',['open_section'=>'create-vehicle-popup']) }}">Add Vehicle</a>
        </div>
    </div>
</li>
@endcan


@can('role-list')
<li class="nav-item">
    <div class="dropdown main-menu">
            <a class="nav-link  {{ Route::is('roles.*') ? 'active' : '' }}" href="{{ route('roles.list') }}">Manage Roles</a>
    </div>
</li>
@endcan


