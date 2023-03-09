/* Ajax calls */
$(document).ready(function(){
	$('[data-toggle="tooltip"]').tooltip();
    jQuery(document).ajaxStart(function (event, request, settings) {
        jQuery(".main-content").addClass("loading");
    });
    jQuery(document).ajaxStop(function (event, request, settings) {
        jQuery(".main-content").removeClass("loading");
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
	$(".select-country").on("change",function(){
        /** Get state of country **/
        var state_options = `<option>Select State</option>`,
        state_select = $(this).closest("form").find("select.select-state"),
        city_select = $(this).closest("form").find("select.select-city"),
        selected_id = state_select.data("selected-id");
        if( $(this).val() ){
            $.get("/get-country-states/"+$(this).val(), function(data, status){
                if( data.status ){
                    for( let state_id in data.data ){
                        let selected = '';
                        if( selected_id == state_id )
                            selected = 'selected';
                        state_options += `<option value='${state_id}' ${selected}>${data.data[state_id]}</option>`;
                    }
                }
                state_select.html( state_options );
                if( city_select.data("selected-id") )
                    state_select.change();
            });
        }else{
            state_select.html( state_options );
        }
    }).change();

    $(".select-state").on("change",function(){
        /** Get state of country **/
        var city_options = `<option>Select City</option>`,
        city_select = $(this).closest("form").find("select.select-city"),
        selected_id = city_select.data("selected-id");
        if( $(this).val() ){
            $.get("/get-state-cities/"+$(this).val(), function(data, status){
                if( data.status ){
                    for( let city_id in data.data ){
                        let selected = '';
                        if( selected_id == city_id )
                            selected = 'selected';
                        city_options += `<option value='${city_id}' ${selected}>${data.data[city_id]}</option>`;
                    }
                }
                city_select.html( city_options );

            });
        }else{
            city_select.html( city_options );
        }
    });

    jQuery('.delete-temp').on('click', function (event) {
		event.preventDefault();
		const url = jQuery(this).attr('href');
		swal({
			title: 'Are you sure?',
			text: 'This record will be deleted temporarily',
			icon: 'warning',
			buttons: ["Cancel", "Yes!"],
		}).then(function(value) {
			if (value) {
				window.location.href = url;
			}
		});
	});


    jQuery('.add-device').on('click', function (event) {
		event.preventDefault();
		const url = jQuery(this).attr('href');
		swal({
			title: 'Would you like to add device?',
			text: 'You can delete the device any time.',
			icon: 'warning',
			buttons: ["Cancel", "Yes!"],
		}).then(function(value) {
			if (value) {
				window.location.href = url;
			}
		});
	});

	/** Change status **/
    $('.toggle-class').change(function() {
        console.log( $(this).data("change-status") );
        var change_status_url = $(this).data("change-status");
        if( change_status_url ){
            var status = $(this).prop('checked') == true ? 1 : 0;
            var id = $(this).data('id');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: change_status_url,
                data: {'status': status, 'id': id},
                success: function(data){
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
        }
    });

	/** Load media files **/
    var media_loader = false , current_media_page = 1;
    $(".all-media-boxes").on("scroll",function(){
        var current_box = $(this),
        input_name = $(this).find("input[type=radio]").attr("name");
        if( $(this).get(0).scrollHeight - ($(this).scrollTop() + $(this).height()) < 100 ){
            var next_page = $(this).find("[name=mpage]").last().val();
            if( media_loader == false && current_media_page != next_page ){
                current_media_page = next_page;
                media_loader = $.get( "/paginate-media-files?mpage="+next_page+"&selected="+$(this).data("selected-id")+"&input_name="+input_name, function( data ) {
                    current_box.append( data );
                    media_loader = false;
                });
            }
        }
    });
});

function initializeSelect2(){
    $('.js-data-example-ajax').each(function() {
        $(this).select2({
            ajax: {
                url: exercise_ajax,
                dataType: 'json',
                data: function (params) {
                    console.log( $(this) );
                    params.ignore_exercise = $(this).data("ignore-exercise");
                    return params;
                }
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }
        });
    });

}
initializeSelect2();
