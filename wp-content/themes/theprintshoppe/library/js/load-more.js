jQuery(function($){
	$('.load-more-button > a').click(function(){
 
		var button = $(this),
		    data = {
			'action': 'loadmore',
			'query': ps_load_more_params.posts, // that's how we get params from wp_localize_script() function
			'page' : ps_load_more_params.current_page
		};
 
		$.ajax({
			url : ps_load_more_params.ajaxurl, // AJAX handler
			data : data,
			type : 'POST',
			beforeSend : function ( xhr ) {
				button.html('<i class="fas fa-circle-notch fa-spin"></i>'); // change the button text, you can also add a preloader image
			},
			success : function( data ){
				if( data ) { 
					button.html( 'Load More' ).parent().before(data).hide().fadeIn(); // insert new posts
					ps_load_more_params.current_page++;
 
					if ( ps_load_more_params.current_page == ps_load_more_params.max_page ) 
						button.parent().remove(); // if last page, remove the button
 
					// you can also fire the "post-load" event here if you use a plugin that requires it
					// $( document.body ).trigger( 'post-load' );
				} else {
					button.parent().remove(); // if no data, remove the button as well
				}
			},
			error : function (data){
				button.html('Error');
			}
		});
	});
});