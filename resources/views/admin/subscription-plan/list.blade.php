@extends('admin.layouts.app')
@section('title', '- Subscription Plan')

@section('content')
<section class="order-listing Invoice-listing">
    <div class="container">
        <div class="left-content d-flex">
            <div class="list-title d-flex">
                <i class="fas fa-file-code fa-3x"></i>
                <div class="list-content">
                    <h2 class="heading-text">Manage Subscription Plan</h2>
                    <h2 class="mobile-text d-none">Manage Subscription Plan</h2>
                    <p>
                        Add , View and Edit the details
                    </p>
                </div>
            </div>
            <div class="right-btns">
                <div class="">
                    <a class="nav-link btn navy-blue-btn open-section" data-target="create-subscription" href="javascript:void(0)"  aria-expanded="false">
                    Create Subscription Plan
                    </a>
                </div>
            </div>
        </div>
        <div class="order-listing Invoice-tabs">
            <x-alert/>
            <!-- tabs Start here -->
            <ul class="tabs nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">All Subscription Plan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="delete-exercise-tab" data-toggle="tab" href="#tabs-2" role="tab">Deleted Subscription Plan</a>
                </li>
            </ul>
            <!-- Search section Start here -->
            <div class="list-header d-flex justify-content-between">
                <form class="form-inline my-2 my-lg-0">

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
                            <button class="filter-text open-section" data-target="steph-workout-filter" > <img src="{{ asset('assets/images/Filters lines.svg') }}"> Filters</button>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Search section End here -->
            <div class="tab-content invoice-tab-content">
                <!-- User listing -->
                <div class="tab-pane active" id="tabs-1" role="tabpanel">
                    @include('admin.subscription-plan.table-list')
                </div>
                <!-- Delete user listing -->
                <div class="tab-pane" id="tabs-2" role="tabpanel">
                    @include('admin.subscription-plan.table-list',['deleteRecords'  =>  1,'data'=>$deletedData])
                </div>
            </div>
            <!-- tabs End here -->
        </div>
    </div>
</section>
@parent
@endsection

@section('footer-html')
@include('admin.subscription-plan.popups')
@endsection

@section('page-js')
<script>


    $("#create-subscription-form").validate({
        ignoore: '',
        rules:{
            name:{
                required:true,
                maxlength:100
            },
            price:{
                required:true,
                number:true
            },
            number_of_months:{
                required:true
            }
        },
        messages:{
            name:{
                required:"Name is required"
            },
            price:{
                required:"Price is required"
            },
            number_of_months:{
                required:"Program month is required"
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
            url: '/subscription-plan/changeStatus',
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