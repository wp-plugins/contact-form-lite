<?php

if ( ! defined( 'ABSPATH' ) ) exit;


function ecf_lite_get_addons_feed() {
	if ( false === ( $cache = get_transient( 'ecflite_addons_feed' ) ) ) {
		
	$addlist = get_option( "ecf_active_addons_lite" );	
		
	if ( is_array( $addlist ) ) {
		
		$lst = $addlist;
		
		} else {
			
			$lst = array();
			
			}
		
	$url = array(
				'c' => 'addons',
				'p' => 'ecflite',
				'addons' => $lst,
				);	
		
		$feed = wp_remote_get( 'http://content.ghozylab.com/feed.php?'.http_build_query( $url ).'', array( 'sslverify' => false ) );
		
		if ( ! is_wp_error( $feed ) ) {
			if ( isset( $feed['body'] ) && strlen( $feed['body'] ) > 0 ) {
				$cache = wp_remote_retrieve_body( $feed );
				set_transient( 'ecflite_addons_feed', $cache, 60 );
			}
		} else {
			$cache = '<div class="error"><p>' . __( 'There was an error retrieving the list from the server. Please try again later.', 'easycform' ) . '</div>';
		}
	}
	return $cache;
}
