jQuery( document ).ready( function ( $ ) {
	
	// show the popup with the form to edit a custom post
	$(".edit-custom-posts").on("click", ".edit_post_link",
	    
		function(){
			var post_id=parseInt($(this).attr("id").match(/\d+/)[0], 10);
	    	$(".loader").show();
			$.post(ajaxurl, {
					post_id:    post_id,
 					action:     'update_post_form'
                }, function (response) {
                	$(".modalBox").show();
                	$(".popupBox").show();
                	$("#edit_post_box").html(response);
                	$(".loader").hide();
                }
            );
                   
		}
		
	);
	
	// Submit the form to edit a custom post. 
	$("#edit_post_box").on("submit","#edit_post_form",function(e){
	    $(".loader").show();

		$.post(ajaxurl, $(this).serialize(), function (response) {

			$("#gizmos_table").html(response);

			var post_id=$("#post_id").val();
			 
			$(".popupBox").fadeOut();
       		$(".modalBox").fadeOut();
       		$(".loader").hide();
                	
        });
		
		e.preventDefault()
	});
	
	$('.edit-custom-posts').on("click",".popupCloser",function(){
       $(".popupBox").fadeOut();
       $(".modalBox").fadeOut();

   });
   
   $("#edit_post_box").on("submit","#delete_post_form",function(e){
		
	    $(".loader").show();
	   // alert($(this).serialize());
		$.post(ajaxurl, $(this).serialize(), function (response) {
			
			var post_id=$("#post_id").val();
			
			$("#gizmos_table").html(response);
			$(".popupBox").fadeOut();
       		$(".modalBox").fadeOut();
       		$(".loader").hide();
                	
        });
		
		e.preventDefault()
	});
});