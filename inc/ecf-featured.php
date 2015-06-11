<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function easycform_featured_init() {
    $easycform_featured_init = add_submenu_page('edit.php?post_type=easycontactform', 'Premium Plugins', __('Premium Plugins', 'easycform'), 'edit_posts', 'easycform_featured_plugins', 'easycform_featured_page');
}

function easycform_featured_page() {
	ob_start(); ?>
	<div class="wrap" id="ghozy-featured">
		<h2>
			<?php _e( 'GhozyLab Premium Plugins', 'easycform' ); ?>
		</h2>
		<p><?php _e( 'These plugins available on Lite and Pro version.', 'easycform' ); ?></p>
		<?php echo easycform_get_feed(); ?>
	</div>
	<?php
	echo ob_get_clean();
}


function easycform_get_feed() {
	if ( false === ( $cache = get_transient( 'easycform_featured_feed' ) ) ) {
		$feed = wp_remote_get( 'http://content.ghozylab.com/feed.php?c=featuredplugins', array( 'sslverify' => false ) );
		if ( ! is_wp_error( $feed ) ) {
			if ( isset( $feed['body'] ) && strlen( $feed['body'] ) > 0 ) {
				$cache = wp_remote_retrieve_body( $feed );
				set_transient( 'easycform_featured_feed', $cache, 3600 );
			}
		} else {
			$cache = '<div class="error"><p>' . __( 'There was an error retrieving the list from the server. Please try again later.', 'easycform' ) . '</div>';
		}
	}
	return $cache;
}