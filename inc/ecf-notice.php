<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( get_option("ecf-settings-automatic_update") != 'active' ) {
	add_action( 'admin_notices', 'ecf_update_notify' );
	}

add_action('admin_notices', 'ecf_aff_admin_notice');

function ecf_aff_admin_notice() {
    global $current_user, $post;
		if ( 'easycontactform' === $post->post_type && is_admin() ) {
        	$user_id = $current_user->ID;
        	/* Check that the user hasn't already clicked to ignore the message */
   	 		if ( ! get_user_meta($user_id, 'ecf_ignore_notice') ) {
       	 		echo '<div class="updated"><p>'; 
        		printf(__('Earn <span style="color: red;">EXTRA MONEY</span> and get 30&#37; affiliate share from every sale you make!&nbsp;&nbsp;<a href="http://ghozylab.com/plugins/affiliate-program/" target="_blank">JOIN GHOZYLAB AFFILIATE PROGRAM NOW!</a><span style="float: right;"><a href="%1$s">Hide Notice</a><span>'), '?ecf_nag_ignore=0');
        		echo "</p></div>";
    			}
			}
}


add_action('admin_init', 'ecf_nag_ignore');

function ecf_nag_ignore() {

    global $current_user;
        $user_id = $current_user->ID;
        /* If user clicks to ignore the notice, add that to their user meta */
        if ( isset($_GET['ecf_nag_ignore']) && '0' == $_GET['ecf_nag_ignore'] ) {
             add_user_meta($user_id, 'ecf_ignore_notice', 'true', true);
    }
}



?>