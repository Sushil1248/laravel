<li class=" @empty($for_mobile) nav-item @endempty {{ Route::is('home') ? 'active' : '' }}">
    <a class="@if( empty($for_mobile) ) nav-link @else nav_link @endif" href="{{ route('home') }}">Dashboard <span class="sr-only">(current)</span></a>
</li>


@can('user-list')
<li class="nav-item">
    <div class="dropdown main-menu">
        <a class="nav-link dropdown-toggle {{ Route::is('company.*') ? 'active' : '' }}" data-toggle="dropdown">Companies</a>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="{{ route('company.list') }}">All Companies</a>
            <a class="dropdown-item" href="{{ route('company.list',['open_section'=>'create-company-popup']) }}">Add Company</a>
        </div>
    </div>
</li>
@endcan

@can('user-list')
{{-- <li class=" @empty($for_mobile) nav-item @endempty {{ Route::is('user.*') ? 'active' : '' }}">
    <a class="@if( empty($for_mobile) ) nav-link @else nav_link @endif" href="{{ route('user.list') }}" >Users </a>
</li> --}}
<li class="nav-item">
    <div class="dropdown main-menu">
        <a class="nav-link dropdown-toggle {{ Route::is('user.*') ? 'active' : '' }}" data-toggle="dropdown">Users</a>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="{{ route('user.list') }}">All Users</a>
            <a class="dropdown-item" href="{{ route('user.list',['open_section'=>'create-user-popup']) }}">Add User</a>
        </div>
    </div>
</li>
@endcan



{{-- @can('media-list')
 <li class=" @empty($for_mobile) nav-item @endempty  {{ Route::is('media.*') ? 'active' : '' }}">
    <a class="@if( empty($for_mobile) ) nav-link @else nav_link @endif" href="{{ route('media.list') }}" >Media </a>
</li>
<li class="nav-item">
    <div class="dropdown main-menu">
        <a class="nav-link dropdown-toggle {{ Route::is('media.*') ? 'active' : '' }}" data-toggle="dropdown">Media</a>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="{{ route('media.list') }}">All Media</a>
            <a class="dropdown-item" href="{{ route('media.list',['open_section'=>'upload-media-popup']) }}">Upload Media</a>
        </div>
    </div>
</li>
@endcan --}}

