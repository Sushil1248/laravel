@extends('admin.layouts.app')
@section('title', '- Media')

@section('page-css')
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
@endsection

@section('content')
<section class="order-listing Invoice-listing">
    <div class="container">
        <div class="left-content d-flex">
            <div class="list-title d-flex">
                <i class="fas fa-images" style="font-size: 30px;"></i>
                <div class="list-content">
                    <h2 class="heading-text">Manage Media</h2>
                    <h2 class="mobile-text d-none">Manage Media</h2>
                    <p>
                        Add , View and Edit the details
                    </p>
                </div>
            </div>
            <div class="right-btns">
                <div class="">
                    <a class="nav-link btn navy-blue-btn open-section" data-target="upload-media-popup" href="javascript:void(0)"  aria-expanded="false">
                    Upload Media
                    </a>
                </div>
            </div>
        </div>
        <div class="order-listing Invoice-tabs">
            <x-alert/>
            <!-- tabs Start here -->
            <ul class="tabs nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">All Media</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="delete-media-tab" data-toggle="tab" href="#tabs-2" role="tab">Deleted Media</a>
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
                        <li class="filters-btn">
                            <button class="filter-text open-section" data-target="media-filter" > <img src="{{ asset('assets/images/Filters lines.svg') }}"> Filters</button>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Search section End here -->
            <div class="tab-content invoice-tab-content">
                <!-- User listing -->
                <div class="tab-pane active" id="tabs-1" role="tabpanel">
                    @include('admin.media.table-list')
                    
                </div>
                <!-- Delete user listing -->
                <div class="tab-pane" id="tabs-2" role="tabpanel">
                    @include('admin.media.table-list',['deleteRecords'  =>  1,'data'=>$deletedData])
                    
                </div>
            </div>
            <!-- tabs End here -->
        </div>
    </div>
</section>
@parent
@endsection

@section('footer-html')
@include('admin.media.popups')
@endsection

@section('page-js')
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script>
    /* Initialize dropzone for media upload */
    Dropzone.options.myGreatDropzone = { 
        paramName: "file", 
        maxFilesize: 2, 
        acceptedFiles: 'image/*',
        init:function() {
            this.on("addedfile", (file) => {
                jQuery(".main-content").addClass("loading");
            });
            this.on("success", (file,message) => {
                $("#upload-media-popup").data("page-refresh","1");
            });
            this.on("complete",()=>{
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    jQuery(".main-content").removeClass("loading");
                    
                    ToastMessage.fire({
                        icon: 'success',
                        title: "Images has been uploaded."
                    }) 
                }
            });

        }
    }
    
    $('.toggle-class').change(function() { 
        var status = $(this).prop('checked') == true ? 1 : 0; 
        var id = $(this).data('id'); 
        $.ajax({
            type: "GET",
            dataType: "json",
            url: '/media/changeStatus',
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
    
    
    /** Get user details **/
    $(".get-media-detail").on("click",function(){
        $.get("/user/details/"+$(this).data("user-id"), function(data, status){
            if( data.status ){
                for (let input_name in data.data)
                    $(`#user-details input[name=${input_name}]`).val( data.data[input_name] );
            }
        });
    });
    @if( request('dpage') )
    $("#delete-media-tab").click();
    @endif
    
</script>
@endsection