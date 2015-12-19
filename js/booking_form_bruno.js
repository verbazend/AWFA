/*var formObject = {
	/*run : function(obj) {
		if (obj.val() === '') {
			obj.nextAll('.update').html('<option value="">----</option>').attr('disabled', true);
		} else {
			var id = obj.attr('id');
			var v = obj.val();
			jQuery.getJSON('/test/mod/update.php', { id : id, value : v }, function(data) {
				if (!data.error) {
					obj.next('.update').html(data.list).removeAttr('disabled');
				} else {
					obj.nextAll('.update').html('<option value="">----</option>').attr('disabled', true);
				}
			});
		}
	}*/
		/*run : function(obj) {
			if (obj.val() === '') {
				obj.parent().nextAll('.update').html('<option value="">----</option>').attr('disabled', false);
			} else {
				var id = obj.attr('id');
				var v = obj.val();
				jQuery.getJSON('/updateform.php', { id : id, value : v }, function(data) {
					//obj.parent().next().children('.update').html(data.list).prop('disabled', false);
					obj.parent().next().children('.update').html(data.list).removeAttr('disabled');
				});
			}
		}
		
};

$(function() {
	
	$('.update').live('change', function() {
		formObject.run($(this));
	});
	
	
	
});*/



var formObject = {

			reset : function(obj) {
					obj.parent().nextAll('.updateLocations').html('<option value="">----</option>').attr('disabled', false);
					obj.parent().nextAll('.updateCourse').html('<option value="">----</option>').attr('disabled', false);
					var id = obj.attr('id');
					var v = obj.val();
					jQuery.getJSON('/updateform.php', { id : id, value : v }, function(data) {
						//obj.parent().next().children('.update').html(data.list).prop('disabled', false);
						obj.parent().next().children('.updateLocations').html(data.list).removeAttr('disabled');
						obj.parent().nextAll().children('.updateCourse').html('<option value="">----</option>').removeAttr('disabled');
						//$( ".hasDatepicker" ).datepicker( "setDate", null );
						$(".hasDatepicker").val('Select Date');
						$( ".hasDatepicker" ).datepicker( "destroy" );
					});
			},

			runcourse : function(obj) {
				obj.parent().nextAll('.updateCourse').html('<option value="">----</option>').attr('disabled', false);
				var id = obj.attr('id');
				var v = obj.val();
				//'var $el = obj.parent().prevAll('#location').find('option:selected');
				var l = $('#location').val();
				jQuery.getJSON('/updateform.php', { id : id, value : v, locationid : l }, function(data) {
					//obj.parent().next().children('.update').html(data.list).prop('disabled', false);
					obj.parent().next().children('.updateCourse').html(data.list).removeAttr('disabled');
					//$( ".hasDatepicker" ).datepicker( "setDate", null );
					$(".hasDatepicker").val('Select Date');
					$( ".hasDatepicker" ).datepicker( "destroy" );
				});
			},
			
			rundate : function(obj) {
				$(".hasDatepicker").val('Select Date');
				$(".hasDatepicker").datepicker( "destroy" );
			}
			
	};

	$(function() {

		$('.updateState').change(function(){
			formObject.reset($(this));
		});
		
		$('.updateLocations').change(function(){
			formObject.runcourse($(this));
		});
		
		$('.updateCourse').change(function(){
			formObject.rundate($(this));
		});

		$('#datepicker').change(function(){
			$('#sidebarsubmit').removeAttr('disabled')
		});

	});
	
	
	
	$(function() {

		$('.update_state_internal').change(function(){
			
			var state = $(".update_state_internal :selected").text();
			$.ajax({
			    url:"/updateforminternal.php?id=state",
				type:"post",
				data:'state='+ state,
				dataType: "html",
				success:function(data){
				$(".update_location_internal").html(data);
				}
			});
			$(".hasDatepicker").val('Select Date');
				$(".hasDatepicker").datepicker( "destroy" );
		});
		
		$('.update_location_internal').change(function(){
			var location = $(".update_location_internal :selected").val();
			$.ajax({
			    url:"/updateforminternal.php?id=location",
				type:"post",
				data:'location='+ location,
				dataType: "html",
				success:function(data){
				$(".update_course_internal").html(data);
				}
			});
			$(".hasDatepicker").val('Select Date');
				$(".hasDatepicker").datepicker( "destroy" );
		});
		
		$('.update_course_internal').change(function(){
			formObject.rundate($(this));
		});

	});