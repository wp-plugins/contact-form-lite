<?php
/**
 * Weclome Page Class
 *
 * @package     ECF
 * @since       1.0.11
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ECF_Welcome Class
 *
 * A general class for About and Credits page.
 *
 * @since 1.0.11
 */
class ECF_Welcome {

	/**
	 * @var string The capability users should have to view the page
	 */
	public $minimum_capability = 'manage_options';

	/**
	 * Get things started
	 *
	 * @since 1.0.11
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'ecf_admin_menus') );
		add_action( 'admin_head', array( $this, 'ecf_admin_head' ) );
		add_action( 'admin_init', array( $this, 'ecf_welcome_page' ) );
	}

	/**
	 * Register the Dashboard Pages which are later hidden but these pages
	 * are used to render the Welcome and Credits pages.
	 *
	 * @access public
	 * @since 1.4
	 * @return void
	 */
	public function ecf_admin_menus() {

			// What's New / Overview
    		add_submenu_page('edit.php?post_type=easycontactform', 'What\'s New', 'What\'s New<span style="font-weight: bold;font-size:8px;letter-spacing: 1px;color:#fff; border: solid 1px #fff; padding: 0 5px 0 5px; border-radius: 15px; -moz-border-radius: 15px;-webkit-border-radius: 15px; background: red; margin-left: 7px;">NEW</span>', $this->minimum_capability, 'ecf-whats-new', array( $this, 'ecf_about_screen') );
			
			// Changelog Page
    		add_submenu_page('edit.php?post_type=easycontactform', ECF_ITEM_NAME.' Changelog', ECF_ITEM_NAME.' Changelog', $this->minimum_capability, 'ecf-changelog', array( $this, 'ecf_changelog_screen') );
			
			// Getting Started Page
    		add_submenu_page('edit.php?post_type=easycontactform', 'Getting started with '.ECF_ITEM_NAME.'', 'Getting started with '.ECF_ITEM_NAME.'', $this->minimum_capability, 'ecf-getting-started', array( $this, 'ecf_getting_started_screen') );
			
			// Free Plugins Page
    		add_submenu_page('edit.php?post_type=easycontactform', 'GhozyLab Free Plugin', 'GhozyLab Free Plugin', $this->minimum_capability, 'ecf-free-plugins', array( $this, 'free_plugins_screen') );
			
			// Premium Plugins Page
    		add_submenu_page('edit.php?post_type=easycontactform', 'Premium Plugins', 'Premium Plugins', $this->minimum_capability, 'ecf-premium-plugins', array( $this, 'premium_plugins_screen') );
			
			// Addons Page
    		add_submenu_page('edit.php?post_type=easycontactform', 'Addons', 'Addons', $this->minimum_capability, 'ecf-addons', array( $this, 'addons_plugins_screen') );
			
		
			// Analytics Page
			add_submenu_page('edit.php?post_type=easycontactform', 'Form Analytics', __('Form Analytics', 'easycform'), $this->minimum_capability, 'easycform-form-analytics', 'easycform_analytics');	
			
			// Pricing Page
			add_submenu_page('edit.php?post_type=easycontactform', 'Pricing & compare tables', __('UPGRADE to PRO', 'easycform'), $this->minimum_capability, 'easycform_comparison', 'easycform_pricing_table');
			
			// Settings Page
			add_submenu_page('edit.php?post_type=easycontactform', 'Global Settings', __('Global Settings', 'easycform'), $this->minimum_capability, 'ecf_settings_page', 'ecf_stt_page');
			
				
	}

	/**
	 * Hide Individual Dashboard Pages
	 *
	 * @access public
	 * @since 1.0.11
	 * @return void
	 */
	public function ecf_admin_head() {
		remove_submenu_page( 'edit.php?post_type=easycontactform', 'ecf-changelog' );
		remove_submenu_page( 'edit.php?post_type=easycontactform', 'ecf-getting-started' );
		remove_submenu_page( 'edit.php?post_type=easycontactform', 'ecf-free-plugins' );
		remove_submenu_page( 'edit.php?post_type=easycontactform', 'ecf-premium-plugins' );
		remove_submenu_page( 'edit.php?post_type=easycontactform', 'ecf-addons' );
		

		// Badge for welcome page
		$badge_url = ECF_URL . '/css/images/assets/mailman-logo.png';
		?>
		<style type="text/css" media="screen">
		/*<![CDATA[*/
		.ecf-badge {
			padding-top: 150px;
			height: 128px;
			width: 128px;
			color: #666;
			font-weight: bold;
			font-size: 14px;
			text-align: center;
			text-shadow: 0 1px 0 rgba(255, 255, 255, 0.8);
			margin: 0 -5px;
			background: url('<?php echo $badge_url; ?>') no-repeat;
		}

		.about-wrap .ecf-badge {
			position: absolute;
			top: 0;
			right: 0;
		}

		.ecf-welcome-screenshots {
			float: right;
			margin-left: 10px!important;
		}

		.about-wrap .feature-section {
			margin-top: 20px;
		}
		
		
		.about-wrap .feature-section .plugin-card h4 {
    		margin: 0px 0px 12px;
    		font-size: 18px;
    		line-height: 1.3;
		}
		
		.about-wrap .feature-section .plugin-card-top p {
    		font-size: 13px;
    		line-height: 1.5;
    		margin: 1em 0px;
		}	
				
		.about-wrap .feature-section .plugin-card-bottom {
    		font-size: 13px;
		}	
		
		.customh3 {

		}
		
		
		.customh4 {
			display:inline-block;
			border-bottom: 1px dashed #CCC;
		}
		
		

		/*]]>*/
		</style>
		<?php
	}

	/**
	 * Navigation tabs
	 *
	 * @access public
	 * @since 1.0.11
	 * @return void
	 */
	public function ecf_tabs() {
		$selected = isset( $_GET['page'] ) ? $_GET['page'] : 'ecf-whats-new';
		?>
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php echo $selected == 'ecf-whats-new' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'ecf-whats-new' ), 'edit.php?post_type=easycontactform' ) ) ); ?>">
				<?php _e( "What's New", 'easycform' ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'ecf-getting-started' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'ecf-getting-started' ), 'edit.php?post_type=easycontactform' ) ) ); ?>">
				<?php _e( 'Getting Started', 'easycform' ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'ecf-addons' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'ecf-addons' ), 'edit.php?post_type=easycontactform' ) ) ); ?>">
				<?php _e( 'Addons', 'easycform' ); ?>
			</a>
            
			<a class="nav-tab <?php echo $selected == 'ecf-free-plugins' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'ecf-free-plugins' ), 'edit.php?post_type=easycontactform' ) ) ); ?>">
				<?php _e( 'Free Plugins', 'easycform' ); ?>
			</a>
            
			<a class="nav-tab <?php echo $selected == 'ecf-premium-plugins' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'ecf-premium-plugins' ), 'edit.php?post_type=easycontactform' ) ) ); ?>">
				<?php _e( 'Premium Plugins', 'easycform' ); ?>
			</a>
            
            
		</h2>
		<?php
	}

	/**
	 * Render About Screen
	 *
	 * @access public
	 * @since 1.0.11
	 * @return void
	 */
	public function ecf_about_screen() {
		list( $display_version ) = explode( '-', ECF_VERSION );
		?>
		<div class="wrap about-wrap">
			<h1><?php printf( __( 'Welcome to '.ECF_ITEM_NAME.'', 'easycform' ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for installing '.ECF_ITEM_NAME.'. This plugin is ready to make your form more fancy, safer, and better!', 'easycform' ), $display_version ); ?></div>
			<div class="ecf-badge"><?php printf( __( 'Version %s', 'easycform' ), $display_version ); ?></div>

			<?php $this->ecf_tabs(); ?>
            
            <?php ecf_lite_get_news();  ?>

			<div class="ecf-container-cnt">
				<h3 class="customh3"><?php _e( 'New Welcome Page', 'easycform' );?></h3>

				<div class="feature-section">

					<p><?php _e( 'Version 1.0.13 introduces a comprehensive welcome page interface. The easy way to get important informations about this product and other related plugins.', 'easycform' );?></p>
                    
					<p><?php _e( 'In this page, you will find four important Tabs named Getting Started, Addons, Free Plugins and Premium Plugins.', 'easycform' );?></p>

				</div>
			</div>

			<div class="ecf-container-cnt">
				<h3 class="customh3"><?php _e( 'ADDONS', 'easycform' );?></h3>

				<div class="feature-section">

					<p><?php _e( 'Need some Pro version features to be applied in your Free version? What you have to do just go to <strong>Addons</strong> page and choose any Addons that you want to install. All listed addons are Premium version.', 'easycform' );?></p>

				</div>
			</div>

			<div class="ecf-container-cnt">
				<h3><?php _e( 'Additional Updates', 'easycform' );?></h3>

				<div class="feature-section col three-col">
					<div>

						<h4><?php _e( 'CSS Clean and Optimization', 'easycform' );?></h4>
						<p><?php _e( 'We\'ve improved some css class to make your form for look fancy and better.', 'easycform' );?></p>

					</div>

					<div>

						<h4><?php _e( 'Disable Notifications', 'easycform' );?></h4>
						<p><?php _e( 'In this version you will no longer see some annoying notifications in top of form editor page. Thanks for who suggested it.' ,'easycform' );?></p>
                        
					</div>

					<div class="last-feature">

						<h4><?php _e( 'Improved WP Mail Function', 'easycform' );?></h4>
						<p><?php _e( ' WP Mail function has been improved to be more robust and fast so you can send an email only in seconds.', 'easycform' );?></p>

					</div>

				</div>
			</div>

			<div class="return-to-dashboard">&middot;<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'ecf-changelog' ), 'edit.php?post_type=easycontactform' ) ) ); ?>"><?php _e( 'View the Full Changelog', 'easycform' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Changelog Screen
	 *
	 * @access public
	 * @since 1.0.11
	 * @return void
	 */
	public function ecf_changelog_screen() {
		list( $display_version ) = explode( '-', ECF_VERSION );
		?>
		<div class="wrap about-wrap">
			<h1><?php _e( ECF_ITEM_NAME. ' Changelog', 'easycform' ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for installing '.ECF_ITEM_NAME.'. This plugin is ready to make your form more fancy, safer, and better!', 'easycform' ), $display_version ); ?></div>
			<div class="ecf-badge"><?php printf( __( 'Version %s', 'easycform' ), $display_version ); ?></div>

			<?php $this->ecf_tabs(); ?>

			<div class="ecf-container-cnt">
				<h3><?php _e( 'Full Changelog', 'easycform' );?></h3>
				<div>
					<?php echo $this->parse_readme(); ?>
				</div>
			</div>

		</div>
		<?php
	}

	/**
	 * Render Getting Started Screen
	 *
	 * @access public
	 * @since 1.9
	 * @return void
	 */
	public function ecf_getting_started_screen() {
		list( $display_version ) = explode( '-', ECF_VERSION );
		?>
		<div class="wrap about-wrap">
			<h1><?php printf( __( 'Welcome to '.ECF_ITEM_NAME.' %s', 'easycform' ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for installing '.ECF_ITEM_NAME.'. This plugin is ready to make your form more fancy, safer, and better!', 'easycform' ), $display_version ); ?></div>
			<div class="ecf-badge"><?php printf( __( 'Version %s', 'easycform' ), $display_version ); ?></div>

			<?php $this->ecf_tabs(); ?>

			<p class="about-description"><?php _e( 'There are no complicated instructions for using Contact Form plugin because this plugin designed to make all easy. Please watch the following video and we believe that you will easily to understand it just in minutes :', 'easycform' ); ?></p>

			<div class="ecf-container-cnt">
				<div class="feature-section">
                <iframe width="853" height="480" src="https://www.youtube.com/embed/_3lsRi9C77k?rel=0" frameborder="0" allowfullscreen></iframe>
			</div>
            </div>

			<div class="ecf-container-cnt">
				<h3><?php _e( 'Need Help?', 'easycform' );?></h3>

				<div class="feature-section">

					<h4><?php _e( 'Phenomenal Support','easycform' );?></h4>
					<p><?php _e( 'We do our best to provide the best support we can. If you encounter a problem or have a question, post a question in the <a href="https://wordpress.org/support/plugin/contact-form-lite" target="_blank">support forums</a>.', 'easycform' );?></p>

					<h4><?php _e( 'Need Even Faster Support?', 'easycform' );?></h4>
					<p><?php _e( 'Just upgrade to <a target="_blank" href="http://demo.ghozylab.com/plugins/easy-contact-form-plugin/pricing-compare-tables/">Pro version</a> and you will get Priority Support are there for customers that need faster and/or more in-depth assistance.', 'easycform' );?></p>

				</div>
			</div>

			<div class="ecf-container-cnt">
				<h3><?php _e( 'Stay Up to Date', 'easycform' );?></h3>

				<div class="feature-section">

					<h4><?php _e( 'Get Notified of Addons Releases','easycform' );?></h4>
					<p><?php _e( 'New Addons that make '.ECF_ITEM_NAME.' even more powerful are released nearly every single week. Subscribe to the newsletter to stay up to date with our latest releases. <a target="_blank" href="http://eepurl.com/bq3RcP" target="_blank">Signup now</a> to ensure you do not miss a release!', 'easycform' );?></p>

				</div>
			</div>

		</div>
		<?php
	}
	
	
	
	/**
	 * Render Free Plugins
	 *
	 * @access public
	 * @since 1.0.11
	 * @return void
	 */
	public function free_plugins_screen() {
		list( $display_version ) = explode( '-', ECF_VERSION );
		?>
		<div class="wrap about-wrap">
			<h1><?php printf( __( 'Welcome to '.ECF_ITEM_NAME.' %s', 'easycform' ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for installing '.ECF_ITEM_NAME.'. This plugin is ready to make your form more fancy, safer, and better!', 'easycform' ), $display_version ); ?></div>
			<div class="ecf-badge"><?php printf( __( 'Version %s', 'easycform' ), $display_version ); ?></div>

			<?php $this->ecf_tabs(); ?>

			<div class="ecf-container-cnt">

				<div class="feature-section">
					<?php echo easycform_free_plugin_page(); ?>
				</div>
			</div>

		</div>
		<?php
	}
	
	
	/**
	 * Render Premium Plugins
	 *
	 * @access public
	 * @since 1.0.11
	 * @return void
	 */
	public function premium_plugins_screen() {
		list( $display_version ) = explode( '-', ECF_VERSION );
		?>
		<div class="wrap about-wrap" id="ghozy-featured">
			<h1><?php printf( __( 'Welcome to '.ECF_ITEM_NAME.' %s', 'easycform' ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for installing '.ECF_ITEM_NAME.'. This plugin is ready to make your form more fancy, safer, and better!', 'easycform' ), $display_version ); ?></div>
			<div class="ecf-badge"><?php printf( __( 'Version %s', 'easycform' ), $display_version ); ?></div>

			<?php $this->ecf_tabs(); ?>

			<div class="ecf-container-cnt">
			<p style="margin-bottom:50px;"class="about-description"></p>

				<div class="feature-section">
					<?php echo easycform_get_feed(); ?>
				</div>
			</div>

		</div>
		<?php
	}
	
	
	
	/**
	 * Render Addons Page
	 *
	 * @access public
	 * @since 1.0.11
	 * @return void
	 */
	public function addons_plugins_screen() {
		list( $display_version ) = explode( '-', ECF_VERSION );
		?>
		<div class="wrap about-wrap" id="ghozy-addons">
			<h1><?php printf( __( 'Welcome to '.ECF_ITEM_NAME.' %s', 'easycform' ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for installing '.ECF_ITEM_NAME.'. This plugin is ready to make your form more fancy, safer, and better!', 'easycform' ), $display_version ); ?></div>
			<div class="ecf-badge"><?php printf( __( 'Version %s', 'easycform' ), $display_version ); ?></div>

			<?php $this->ecf_tabs(); ?>

			<div class="ecf-container-cnt">
			<p style="margin-bottom:50px;"class="about-description"></p>

				<div class="feature-section">
					<?php echo ecf_lite_get_addons_feed(); ?>
				</div>
			</div>

		</div>
		<?php
	}
	

	/**
	 * Parse the EDD readme.txt file
	 *
	 * @since 2.0.3
	 * @return string $readme HTML formatted readme file
	 */
	public function parse_readme() {
		$file = file_exists( ECF_PLUGIN_DIR . 'readme.txt' ) ? ECF_PLUGIN_DIR . 'readme.txt' : null;

		if ( ! $file ) {
			$readme = '<p>' . __( 'No valid changlog was found.', 'easycform' ) . '</p>';
		} else {
			$readme = file_get_contents( $file );
			$readme = nl2br( esc_html( $readme ) );
			$readme = explode( '== Changelog ==', $readme );
			$readme = end( $readme );

			$readme = preg_replace( '/`(.*?)`/', '<code>\\1</code>', $readme );
			$readme = preg_replace( '/[\040]\*\*(.*?)\*\*/', ' <strong>\\1</strong>', $readme );
			$readme = preg_replace( '/[\040]\*(.*?)\*/', ' <em>\\1</em>', $readme );
			$readme = preg_replace( '/= (.*?) =/', '<h4>\\1</h4>', $readme );
			$readme = preg_replace( '/\[(.*?)\]\((.*?)\)/', '<a href="\\2">\\1</a>', $readme );
		}

		return $readme;
	}

	/**
	 * Sends user to the Welcome page on first activation of EDD as well as each
	 * time EDD is upgraded to a new version
	 *
	 * @access public
	 * @since 1.4
	 * @return void
	 */
	public function ecf_welcome_page() {	
		
    if ( is_admin() && get_option( 'activatedecf' ) == 'ecf-activate' && !is_network_admin() ) {
		delete_option( 'activatedecf' );
		wp_safe_redirect( admin_url( 'edit.php?post_type=easycontactform&page=ecf-whats-new' ) ); exit;
		
    	}

	}
}
new ECF_Welcome();
