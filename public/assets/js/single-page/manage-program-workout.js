$(document).ready(function(){
    var saved_exercises = [];
    function resetAddForm( hard_reset = 0 ){
        if( hard_reset || !$(".all-days .single-day").length )
            $(".all-days").html(week_day_html);
        setTimeout(function(){
            reindexInputNames();
        });
    }
    /** Workout change type **/
    $(document).on("change",".workout-type",function(){
        if( $(this).is(':checked') ){
            var next_element = $(this).closest(".single-exercise").find(".all-workout-set");
            next_element.find(".workout-field").hide();
            next_element.find(`.workout-field.${$(this).val()}-field`).show();
            next_element.find(`.workout-field`).not(`.${$(this).val()}-field`).find("input").val('');//Clear input value
            next_element.find(".set-input").change();
        }
    });
    
    $(document).on("change",".warm-up-type",function(){
        var number_of_warmups = 1;
        if( $(this).val() == "extended" )
            number_of_warmups   = 3;
        var all_workout_warmup = $(this).closest(".single-exercise").find(".all-workout-warmup");
        /* Add workout HTML */
        for( let counter = 0 ; counter < number_of_warmups ; counter++ )
            if( all_workout_warmup.find("ul.exercise-warm-up").get(counter) == undefined )
                all_workout_warmup.append( warm_up_exercises );
        /* Remove workout HTML */
        while( number_of_warmups < all_workout_warmup.find("ul.exercise-warm-up").length )
            all_workout_warmup.find("ul.exercise-warm-up").last().remove();
        reindexInputNames();
    });

    $(document).on("change",".exercise-type",function(){
        if( $(this).val() == "superset" ){
            // $(this).closest(".single-exercise").find(".add-exercise").click();
            $(this).closest(".single-exercise").after( day_exercise );
            reindexInputNames();
            saveExercises();
            $(this).closest(".single-exercise").next(".single-exercise").addClass("is-super-set");
        }else{
            $(this).closest(".single-exercise").next(".single-exercise.is-super-set").remove();
        }
    });
    /** Add exercise **/
    $(document).on("click",".add-exercise",function( event ){
        event.preventDefault();
        $(this).closest(".add-exercises").append( day_exercise );
        reindexInputNames();
        saveExercises();
    });
    /** Remove Exercise **/
    $(document).on("click",".remove-exercise",function( event ){
        event.preventDefault();
        $(this).closest(".single-exercise").next(".single-exercise.is-super-set").remove();
        $(this).closest(".single-exercise").remove();
        reindexInputNames();
        saveExercises();
    });

    function saveExercises(){
        return;
        saved_exercises = [];
        $(".single-day").each(function(){
            //return;
            var week_field = $(this).find(".select-week").val(),
            day_field = $(this).find(".select-day").val(),
            single_day = $(this);
            if( week_field && day_field ){
                let key = week_field + "-" + day_field;
                if( !(key in saved_exercises) ){
                    saved_exercises[key] = [];
                }
                $(this).find(".add-exercises .select-exercise").each(function(){
                    if( $(this).val() ){
                        if( !($(this).val() in saved_exercises[key]) ){
                            saved_exercises[key][$(this).val()] = $(this);
                        }
                    }
                });
                /** Having all exercises of single day **/
                single_day.find(".add-exercises .select-exercise option").show();
                single_day.find(".add-exercises .select-exercise option").each(function(){
                    var option_value = $(this).val();
                    if( key in program_exercises && program_exercises[key].indexOf(option_value) !== -1 ){
                        $(this).hide();
                        if( $(this).parent().val() == option_value ){
                            $(this).parent().val('');
                        }
                    }
                    if( option_value in saved_exercises[key] && !saved_exercises[key][option_value].is( $(this).parent() ) ){
                        $(this).hide();
                        if( $(this).parent().val() == option_value ){
                            $(this).parent().val('');
                        }
                    }
                });
            }else{
                single_day.find(".add-exercises .select-exercise option").show();
            }
            
        });
    }
    saveExercises();
    /** Reset exercise dropdown and saved exercises **/
    $(document).on("change",".select-week,.select-day,.select-exercise",function(){
        saveExercises();
    });




    /* Reindex all input names of repeater */
    function reindexInputNames(){
        $(".single-day").each(function(){
            
            var index = $(this).index();
            /* Day indexing */
            $(this).find("[name]").each(function(){
                let new_name = $(this).attr("name").replace(/details\[[0-9]*\]/,`details[${index}]`);
                $(this).attr("name",new_name);
            });
            $(this).find(".add-exercises div.single-exercise").each(function( current_index ){
                /* Exercise indexing */
                $(this).find("[name]").each(function(){
                    var new_name = $(this).attr("name").replace(/\[exercise\]\[[0-9]*\]/,`[exercise][${current_index}]`);
                    $(this).attr("name",new_name);
                });

                /* Exercise workout indexing */
                $(this).find(".all-workout-set").find(".single-workout-set").each(function(){
                    var index = $(this).index();
                    $(this).find("[name]").each(function(){
                        let new_name = $(this).attr("name").replace(/\[workout_set\]\[[0-9]*\]/,`\[workout_set\][${index}]`);
                        $(this).attr("name",new_name);
                    });
                    $(this).find(".set-input").val( index + 1 );
                });

                /* Exercise warmup-workout indexing */
                $(this).find(".all-workout-warmup").find(".exercise-warm-up").each(function(){
                    var index = $(this).index();
                    $(this).find("[name]").each(function(){
                        let new_name = $(this).attr("name").replace(/\[warmup_set\]\[[0-9]*\]/,`\[warmup_set\][${index}]`);
                        $(this).attr("name",new_name);
                    });
                    $(this).find(".set-input").val( index + 1 );
                });
            });
        });
        
        $(".workout-type").change();
        initializeSelect2();
    }
    function after_success( data ){
        program_exercises = data.program_exercises;
    }
    $("#create-program-workout-form").validate({
        ignore: '.is-super-set .custom-select.exercise-type',
        rules:{
            exercise_id:{
                required:true
            }
        },
        messages:{
            exercise_id:{
                required:"Workout exercise is required"
            },
        },
        showErrors: function(errorMap, errorList) {
            this.defaultShowErrors();
            if( $("label.error").length && !$("label.error:visible").length ){
                let error_div_index = $("label.error").closest(".single-day").index();
                $(`.workout-type-navigation li:nth-child(${error_div_index+1}) a`).click()
            }
        },        
        errorPlacement: function(error, element) {
            error.appendTo( element.closest("li") );
        },
        unhighlight: function( element, errorClass, validClass ) {
			if ( element.type === "radio" ) {
				this.findByName( element.name ).removeClass( errorClass ).addClass( validClass );
			} else {
				$( element ).removeClass( errorClass ).addClass( validClass );
			}
            $(element).closest("li").find("label.error").remove();
		},
        submitHandler: function(form) {
            submit_ajax_form(form,after_success);
        },
        onfocusout: function( element ) {
			console.log("FOCUSOUT");
		},
		onkeyup: function( element, event ) {
            console.log("KEYUP");
		}
    });
    /** Workout set repeater **/
    /** Add & Remove workout set **/
	$(document).on("click",".single-workout-set .add-set",function( event ){
        event.preventDefault();
		$(this).closest(".all-workout-set").append( workout_set );
		reindexInputNames();
	});
	$(document).on("click",".single-workout-set .remove-set",function( event ){
        event.preventDefault();
		$(this).closest(".single-workout-set").remove();
		reindexInputNames();
	});
	resetAddForm(  );
    $("form#create-program-workout-form").on("reset",function(){
        resetAddForm( 1 );
    });
    
    $(".workout-type-navigation a").on("click",function(){
		var parent_element = $(this).closest(".program-workout-types");
		parent_element.removeClass("normal express");
		$(this).closest(".program-workout-types").addClass( $(this).data("workout-type") );
        reindexInputNames();
	});
    $(".workout-type-navigation a").first().click();
});