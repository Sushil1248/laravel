$(document).ready(function(){
    /** Remove query string **/
    function removeURLParameter(url, parameter) {
        //prefer to use l.search if you have a location/link object
        var urlparts = url.split('?');
        if (urlparts.length >= 2) {
            var prefix = encodeURIComponent(parameter) + '=';
            var pars = urlparts[1].split(/[&;]/g);
            //reverse iteration as may be destructive
            for (var i = pars.length; i-- > 0;) {
                //idiom for string.startsWith
                if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                    pars.splice(i, 1);
                }
            }
            return urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');
        }
        return url;
    }
    const params = new Proxy(new URLSearchParams(window.location.search), {
        get: (searchParams, prop) => searchParams.get(prop),
    });
    if( params.open_section )
        window.history.pushState({},"", removeURLParameter(window.location.href,"open_section") );
    // Password Change Code
    jQuery("form#modalchangepassSubmit").validate({
        ignore: '',
        rules: {
            oldpassword: {
                required: true,
            },
            newpassword: {
                required: true,
            },
            newpassword_confirmation:{
                required: true,
                equalTo: "#modalchangepassSubmit input[name=newpassword]"
            }
        },
        // Specify validation error messages
        messages: {
            oldpassword: {
                required: 'Old password is required',
            },
            newpassword: {
                required: 'New password is required',
            },
            newpassword_confirmation: {
                required: 'Confirm password is required',
            }
        },
        submitHandler: function(form) {
            jQuery(".flash-messages").html('');
            jQuery("#errorsDeprtPass").html('');
            var btnText = jQuery("#savedBtnPass").html();
            jQuery("#savedBtnPass").html(btnText + '<i class="fa fa-spinner fa-spin"></i>');
            jQuery("#savedBtnPass").attr("disabled", true);
            var formData = jQuery(form);
            var urls = formData.prop('action');
            jQuery.ajax({
                type: "POST",
                url: urls,
                data: formData.serialize(),
                dataType: 'json',
                success: function(data) {
                    if (data.success == true) {
                        jQuery(".flash-messages").html('<div class="alert alert-success  alert-dismissible "><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ data.msg + '</div>');
                        jQuery(".flash-messages").removeClass("hidden");
                        jQuery("#savedBtnPass").html('Update');
                        jQuery("#savedBtnPass").attr("disabled", false);
                        setTimeout(function() {
                            location.reload(true);
                        }, 1000);

                    } else if(data.success == false){
                        jQuery(".flash-messages").html('<div class="alert alert-danger  alert-dismissible "><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ data.msg + '</div>');
                        jQuery("#savedBtnPass").html('Update');
                        jQuery("#savedBtnPass").attr("disabled", false);
                    }
                },
                error: function (jqXHR, exception) {
                    var msg = '';

                    if (jqXHR.status === 302) {
                        window.location.reload();
                    }
                    else if (jqXHR.status === 0) {
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
                        jQuery(".flash-messages").html('<div class="alert alert-danger  alert-dismissible "><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ erro + '</div>');

                        jQuery("#savedBtnPass").html('Update');
                        jQuery("#savedBtnPass").attr("disabled", false);
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    console.info(msg);
                }
            });
        }
    });
    /* Profile Post Request Ajax Code*/
    jQuery("form#modalProfileSubmit").validate({
        rules: {
            adminemail: {
                required: true
            },
            first_name: {
                required: true,
            },
            last_name: {
                required: true,
            }
        },
        // Specify validation error messages
        messages: {
            adminemail: {
                required: 'Email address is required',
                email: 'Provide an valid email address',
            },
            first_name: {
                required: 'First name is required',
            },
            last_name: {
                required: 'Last name is required',
            }
        },
        submitHandler: function(form) {
            var flash_message = $(form).find(".flash-message");
            flash_message.html('');
            jQuery("#errorsDeprtP").html('');
            var btnText = jQuery("#savedBtnP").html();
            jQuery("#savedBtnP").html(btnText + '<i class="fa fa-spinner fa-spin"></i>');
            jQuery("#savedBtnP").attr("disabled", true);
            var formData = new FormData(form);
            var formdata = jQuery(form);
            var urls = formdata.prop('action');
            jQuery.ajax({
                type: "POST",
                url: urls,
                data: formData,
                dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data.success == true) {
                        flash_message.html('<div class="alert alert-success  alert-dismissible "><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ data.msg + '</div>');
                        flash_message.removeClass("hidden");
                        jQuery("#savedBtnP").html('Update');
                        jQuery("#savedBtnP").attr("disabled", false);
                        setTimeout(function() {
                            closePopup();
                            //location.reload(true);
                        }, 1000);

                    } else if(data.success == false){
                        flash_message.html('<div class="alert alert-danger  alert-dismissible "><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ data.msg + '</div>');
                        flash_message.addClass("hidden");
                        jQuery("#savedBtnP").html('Update');
                        jQuery("#savedBtnP").attr("disabled", false);
                    }
                },
                error: function (jqXHR, exception) {
                    var msg = '';

                    if (jqXHR.status === 302) {
                        swal({
                            title: "Warning",
                            text: "Session timeout!",
                            icon: "warning",
                        });
                        window.location.reload();
                    }
                    else if (jqXHR.status === 0) {
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
                        flash_message.html('<div class="alert alert-danger  alert-dismissible "><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ erro + '</div>');
                        jQuery("#savedBtnP").html('Update');
                        jQuery("#savedBtnP").attr("disabled", false);
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    console.info(msg);
                }
            });
        }
    });
    /* Mobile menu */
    $(".toggler-icon-mobile").on("click",function(){
        $("body").toggleClass("show-mobile-menu");
    });


    //
    /** Add reps **/
    // $(document).on("change keyup",".workout-field.sets-field .set-input",function(){
    //     var form = $(this).closest("ul"),
    //     multiple_parent = form.find(".multiple-reps"),
    //     rep_input = form.find(".rep-input").parent(),
    //     number_of_sets = parseInt($(this).val()) || 1;
    //     if( rep_input.length ){
    //         for( let counter = 0; counter < number_of_sets ; counter++ ){
    //             if( !multiple_parent.find("div.input-group").eq(counter).length ){
    //                 multiple_parent.append( rep_input[0].outerHTML );
    //                 multiple_parent.find("div.input-group").eq(counter).find("input").val('');
    //             }
    //         }
    //         while( number_of_sets < multiple_parent.find("div.input-group").length )
    //             multiple_parent.find("div.input-group").eq(number_of_sets).remove();

    //     }
    // });


    /* Open section */
    $('.open-section').click(function (e) {
        if( $('.filter-side-drawer#' + $(this).data("target") ).length ){
            e.preventDefault();
            $('.filter-side-drawer#' + $(this).data("target") ).toggleClass('open');
            $('body').toggleClass('side-drawer-open');
            $(".workout-type").change();
        }
    });
    /** Open popup section be default **/
    if( open_section_popup && $(`.open-section[data-target=${open_section_popup}]`).length ){
        $(`.open-section[data-target=${open_section_popup}]`).click();
    }
    /* Close section */
    $('.filter-cross,.close-section').click(function (e) {
        closePopup();
    });
    $(document).on('keydown', function(event) {
        if (event.key == "Escape") {
           closePopup();
        }
    });

    /* If click outside close section */
    $(document).click('body,html',function (e) {
        var prevent_elements = " .filter-side-drawer , .open-section , .prev , .next , .daterangepicker .cancelBtn , .daterangepicker , .dz-hidden-input , .loading .overlay , .select2-selection__choice__remove , .remove-exercise , .remove-day , .remove-set , .select2-selection__rendered , .select2-search__field";
        if( !$(prevent_elements).is( $(e.target) ) && !$(e.target).closest(prevent_elements).length ){
            //closePopup();
        }
        /** Close mobile navigation **/
        prevent_elements = ".navigation-drawer,.toggler-icon-mobile";
        if( !$(prevent_elements).is( $(e.target) ) && !$(prevent_elements).has( $(e.target) ).length  ){
            $("body").removeClass("show-mobile-menu");
        }
    });

    /** Image preview **/
    $(".show-image-preview").on("change",function(){
        if( $(this).get(0).files ){
            $(this).closest("li").find("img.image-preview").attr("src",URL.createObjectURL($(this).get(0).files[0]));
        }
    });
    $(document).on("click",".all-media-boxes .box-item",function(){
        $(this).closest(".media-container").find(".select-media-image").attr("src", $(this).find("img").attr("src") );
    });
    /** Date range picker **/
    $('.multi-date-rangepicker').daterangepicker({locale: {
            format: 'YYYY-MM-DD'
        },
        maxDate: new Date,
        autoUpdateInput: false
    }).on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
    });
    /** Tooltips **/
    $('[title]:not([title=""])').tooltip({
        container: 'body'
    });


    /** Allow two numbers after decimal **/
    $(document).on('keypress','.two-decimal',function (e) {
        var character = String.fromCharCode(e.keyCode)
        var newValue = this.value + character;
        if (isNaN(newValue) || parseFloat(newValue) * 100 % 1 > 0) {
            e.preventDefault();
            return false;
        }
    });
    /* Allow only digit */
    $(document).on('keypress','.only-digit',function (e) {
        var character = parseInt(String.fromCharCode(e.keyCode));
        if( !Number.isInteger(character)  ){
            e.preventDefault();
        }
    });
    /** Textarea Tinymce **/
    $('.tinymce-textarea1').tinymce({
        width : "100%",
        menubar: 'edit insert view format table tools',
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });

    /** Exercise js **/
    var requireRm = $("#require_rm");
    var rmParent = $("#require_rm_parentEx");
    rmParent.hide();

    requireRm.change(function() {
        if (requireRm.is(':checked')) {
            rmParent.show();
        }else{
            rmParent.hide();
        }
    });

});
/** Toast message **/
var ToastMessage = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});
/* Submit form by aJax */
function submit_ajax_form( form , after_success = null ){
    var formData = jQuery(form);
    if( !formData.find(".ajax-response").length )
        formData.prepend("<div class='ajax-response'></div>");
    var response_ajax = formData.find(".ajax-response"),
    urls = formData.prop('action'),
    submit_button = formData.find(".ajax-submit-button");
    response_ajax.html(''),
    btnText = submit_button.html();
    submit_button.append(' <i class="fa fa-spinner fa-spin"></i>');
    submit_button.attr("disabled", true);
    formData = new FormData( $(form).get(0) );
    jQuery.ajax({
        type: "POST",
        url: urls,
        data: formData,
        dataType: 'json',
        processData: false,  // tell jQuery not to process the data
        contentType: false,  // tell jQuery not to set contentType
        success: function(data) {
            if (data.success == true) {
                alert("sghrt");
                if( after_success )
                    after_success( data.data );
                response_ajax.html('<div class="alert alert-success  alert-dismissible "><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ data.msg + '</div>');
                response_ajax.removeClass("hidden");
                submit_button.find("i").remove();
                submit_button.attr("disabled", false);
                $(form).closest(".filter-side-drawer.open").data("page-refresh",1);
                if( formData.get('add_new') == 1 ){
                    $(form).get(0).reset();
                    return;
                }
                if( 'data' in data && 'redirect_to' in data.data && data.data.redirect_to ){
                    location.href = data.data.redirect_to;
                    return;
                }
                setTimeout(function() {
                    if( $(form).data("redirect-to") )/** Used while editing **/
                        location.href = $(form).data("redirect-to");
                    else{
                        closePopup();
                    }
                }, 1000);

            } else if(data.success == false){
                response_ajax.html('<div class="alert alert-danger  alert-dismissible "><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ data.msg + '</div>');
                submit_button.find("i").remove();
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
                submit_button.find("i").remove();
                submit_button.attr("disabled", false);
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            if( !msg )
                window.location.reload();
        }
    });


}
function closePopup(){
    if( $('.filter-side-drawer.open form').length ){
        if( $('.filter-side-drawer.open form#modalProfileSubmit').length ){
            /* If update profile remain as it is */
        }else{
            $('.filter-side-drawer.open form').get(0).reset();
        }

        let validator = $('.filter-side-drawer.open form').data("validator");
        if( validator )
            validator.resetForm();
    }
    if( $(".filter-side-drawer.open").data("page-refresh") ){
        if( $(".filter-side-drawer.open").data("refresh-url") )
            location.href = $(".filter-side-drawer.open").data("refresh-url");
        else
            location.reload(true);
    }else{
        $('.filter-side-drawer').removeClass('open');
        $('body').removeClass('side-drawer-open');
    }
}


