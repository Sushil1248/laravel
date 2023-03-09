@extends('admin.layouts.app')
@section('title', '- Cms Page')

@section('content')
<section class="order-listing Invoice-listing">
    <div class="container">
        <div class="left-content d-flex">
            <div class="list-title d-flex">
                <i class="fas fa-file-code fa-3x"></i>
                <div class="list-content">
                    <h2 class="heading-text">Manage Cms Page</h2>
                    <h2 class="mobile-text d-none">Manage Cms Page</h2>
                    <p>
                        View and Edit the details
                    </p>
                </div>
            </div>
            <div class="right-btns">
                <div class="">
                    <!--<a class="nav-link btn navy-blue-btn open-section" data-target="create-cms-page" href="javascript:void(0)"  aria-expanded="false">
                    Create Cms Page
                    </a>-->
                </div>
            </div>
        </div>
        <div class="order-listing Invoice-tabs">
            <x-alert/>
            <!-- tabs Start here -->
            <ul class="tabs nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">All Cms Page</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="delete-exercise-tab" data-toggle="tab" href="#tabs-2" role="tab">Deleted Cms Page</a>
                </li>
            </ul>
            <!-- Search section End here -->
            <div class="tab-content invoice-tab-content">
                <!-- User listing -->
                <div class="tab-pane active" id="tabs-1" role="tabpanel">
                    @include('admin.cms-management.table-list')
                </div>
                <!-- Delete user listing -->
                <div class="tab-pane" id="tabs-2" role="tabpanel">
                    @include('admin.cms-management.table-list',['deleteRecords'  =>  1,'data'=>$deletedData])
                </div>
            </div>
            <!-- tabs End here -->
        </div>
    </div>
</section>
@parent
@endsection

@section('footer-html')
@include('admin.cms-management.popups')
@endsection

@section('page-js')
<script>


    $("#create-cms-page-form").validate({
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
    
    $('.toggle-class').change(function() { 
        var status = $(this).prop('checked') == true ? 1 : 0; 
        var id = $(this).data('id'); 
        $.ajax({
            type: "GET",
            dataType: "json",
            url: '/cms-management/changeStatus',
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
    
    
    /** Get workout details **/
    $(".get-workout-detail").on("click",function(){
        $.get("/steph-workout/details/"+$(this).data("workout-id"), function(data, status){
            if( data.status ){
                for (let input_name in data.data)
                    $(`#workout-details [name=${input_name}]`).val( data.data[input_name] );
                $(`#workout-details .submit-button`).attr( "href" , data.data.edit_workout );
            }
        });
    });
    @if( request('dpage') )
    $("#delete-exercise-tab").click();
    @endif
    
</script>
@endsection