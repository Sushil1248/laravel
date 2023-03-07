@extends('admin.layouts.app')
@section('title', '- Cms Page')


@section('content')
<section class="order-listing Invoice-listing edit-module">
    <div class="container">
        <div class="left-content d-flex">
            <div class="list-title d-flex">
                <i class="fas fa-file-code fa-3x"></i>
                <div class="list-content">
                    <h2 class="heading-text">Manage Cms Page</h2>
                    <h2 class="mobile-text d-none">Manage Cms Page</h2>
                    <p>
                        Add , View and Edit the details
                    </p>
                </div>
            </div>
            <div class="right-btns">
                <div class="">
                    <a class="nav-link btn navy-blue-btn" href="{{ route('cms-management.list') }}"  aria-expanded="false">
                    List Cms Page
                    </a>
                </div>
            </div>
        </div>
        <div class="order-listing Invoice-tabs">
            <x-alert/>
            <!-- tabs Start here -->
            <ul class="tabs nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">Update Cms Page</a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Deleted Users</a>
                </li> --}}
            </ul>
            
            <!-- Search section End here -->
            <div class="tab-content invoice-tab-content">
                <div class="tab-pane active" id="tabs-1" role="tabpanel">
                    <div class="">
                        <form method="post" action="{{ route('cms-management.add',['workout_id' => jsencode_userdata($cmsDetail->id) ]) }}" id="create-exercise-form" enctype="multipart/form-data" data-redirect-to={{ route('cms-management.list') }}>
                        @csrf
                            @php
                            extract( $cmsDetail->toArray() );
                            @endphp
                            <div class="invoice-detail invoice-creation">
                                <div class="invoice-details-inner">
                                    <div class="detail-item-1 d-flex align-items-center">
                                        <div class="shipmemnt-details-item item1">
                                            <div class="list-content">
                                                <h2>Update Cms Page</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="Contract-details invoice-feild">
                                    <div class='ajax-response'></div>
                                    <ul style="grid-template-columns: 1fr 2fr 3fr;">
                                        <li>
                                            <p>Name<span class="required-field">*</span></p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <input type="text" class="form-control" aria-label="Small" value="{{ $name }}" aria-describedby="inputGroup-sizing-sm" placeholder="Page name" name="name"> 
                                            </div>
                                        </li>
                                        
                                        <li>
                                            <p>Short Content</p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <textarea type="text" class="form-control comment-box" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Short content" name="short_content">{{$short_content}}</textarea>
                                            </div>
                                        </li>
                                        <li>
                                            <p>Content</p>
                                            <div class="input-group input-group-sm invoice-value">
                                                <textarea type="text" class="form-control comment-box tinymce-textarea" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Content" name="content">{!!$content!!}</textarea>
                                            </div>
                                        </li>
                                    </ul>
                                    <x-media-files :select-id="$cmsDetail->media_id"/>
                                    <div class="footer-menus_button">
                                        <div class="invoice-list">
                                            
                                        </div>
                                        <div class="submit-btns">
                                            <ul>
                                                <li class="close-section"><a href="#">Cancel</a></li>
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
    $("#create-exercise-form").validate({
        ignoore: '',
        rules:{
            name:{
                required:true,
                maxlength:100
            }
        },
        messages:{
            name:{
                required:"CMS page name is required"
            }
        },
        errorPlacement: function(error, element) {
            console.log( element.closest("li") );
            error.appendTo( element.closest("li") );
        },
        submitHandler: function(form) {
            submit_ajax_form(form);
        }
    });
</script>
@endsection