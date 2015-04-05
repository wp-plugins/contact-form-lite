<?php

function ecf_enqueue_on_the_fly(){ 
	global $post;

	if( function_exists('has_shortcode') )
	{
		if( has_shortcode( $post->post_content, 'easy-contactform')) {
			wp_enqueue_script( 'ecf-ladda-spin' );
			wp_enqueue_script( 'ecf-notify' );
			wp_enqueue_script( 'ecf-ladda-js' );
			
			global $is_IE;
			if ( $is_IE ) {
				wp_enqueue_script( 'ecf-placeholder' );
				}
			}
		}
		else
		{
			if( emg_old_has_shortcode('easy-contactform')) {
				wp_enqueue_script( 'ecf-ladda-spin' );
				wp_enqueue_script( 'ecf-notify' );
				wp_enqueue_script( 'ecf-ladda-js' );
				
				global $is_IE;
				if ( $is_IE ) {
					wp_enqueue_script( 'ecf-placeholder' );
					}
				}
			}						

				
}

add_action( 'wp_enqueue_scripts', 'ecf_enqueue_on_the_fly');

?>