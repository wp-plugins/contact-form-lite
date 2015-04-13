<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function ecf_create_entries( $name = null, $eml = null, $msg = null, $frmid = null ) {

$new_post = array(
	'post_type' => 'ecfentries',
	'post_title' => $name,
	'post_content' => '',
	'post_status' => 'publish',
);
 
$post_id = wp_insert_post($new_post);
add_post_meta($post_id, 'ecf_sender_name', $name, true);
add_post_meta($post_id, 'ecf_sender_email', $eml, true);
add_post_meta($post_id, 'ecf_sender_msg', $msg, true);
add_post_meta($post_id, 'ecf_formid', $frmid, true);
}

?>