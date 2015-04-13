<?php

if ( ! defined('ABSPATH') ) {
	die('Please do not load this file directly!');
}

/*-------------------------------------------------------------------------------*/
/*  POST/PAGE SHORTCODE
/*-------------------------------------------------------------------------------*/
function ecf_shortcode( $attsn ) {

	extract( shortcode_atts( array(
	'id' => -1
	), $attsn ) );
	
	ob_start();

if ( $id != '' ) {
	
	$finid = explode(",", $id);
	$medinarr = $finid;

	$ecfargs = array(
		'post__in' => $finid, 
		'post_type' => 'easycontactform',
		);
	}   


 
$ecf_query = new WP_Query( $ecfargs );

if ( $ecf_query->have_posts() ):


while ( $ecf_query->have_posts() ) : $ecf_query->the_post();

	wp_enqueue_script( 'ecf-validate' );
	wp_enqueue_style( 'ecf-frontend-css' );
	wp_enqueue_script( 'ecf-ladda-spin' );
	wp_enqueue_script( 'ecf-notify' );
	wp_enqueue_script( 'ecf-ladda-js' );
	wp_enqueue_script( 'ecf-ladda' );
		
	global $is_IE;
	if ( $is_IE ) {
		wp_enqueue_script( 'ecf-placeholder' );
		}
		
		// START GENERATE FORM
		require_once 'ecf-template.php';
		ecf_markup_generator( get_the_id(), ecfRandomString(6) );

?>

<?php
endwhile;
else:
echo '<div style="clear: both; display: block; text-align:center; margin-left: auto; margin-right: auto;"><img src="'.plugins_url('images/ajax-loader.gif' , __FILE__).'" width="32" height="32"/></div>'; 

$contnt = ob_get_clean();
return $contnt;  

endif;
wp_reset_postdata();


$content = ob_get_clean();
return $content;
	
}

add_shortcode( 'easy-contactform', 'ecf_shortcode' );

?>