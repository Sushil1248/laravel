$(document).ready(function(){
	/** Add & Remove workout set **/
	$(document).on("click",".single-workout-set .add-set",function(){
		$(".all-workout-set").append( workout_set );
		reindexInputNames();
	});
	$(document).on("click",".single-workout-set .remove-set",function(){
		$(this).closest(".single-workout-set").remove();
		reindexInputNames();
	});
	/** Workout change type **/
    $(document).on("change",".workout-type",function(){
        if( $(this).is(':checked') ){
            var form = $(this).closest(".Contract-details");
            form.find(".workout-field").hide();
            form.find(`.workout-field.${$(this).val()}-field`).show();
            form.find(`.workout-field`).not(`.${$(this).val()}-field`).find("input").val('');//Clear input value
            form.find(".set-input").change();
        }
    });
	$(".workout-type").change();

});
function reindexInputNames(){
	$(".all-workout-set .single-workout-set").each(function(){
		var index = $(this).index();
		$(this).find("[name]").each(function(){
			let new_name = $(this).attr("name").replace(/workout_set\[[0-9]*\]/,`workout_set[${index}]`);
			$(this).attr("name",new_name);
		});
		$(this).find(".set-input").val( index + 1 );
	});
	$(".workout-type").change();
}