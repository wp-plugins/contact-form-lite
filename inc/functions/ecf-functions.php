<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/*-------------------------------------------------------------------------------*/
/*   Backend Register JS & CSS
/*-------------------------------------------------------------------------------*/
function ecf_reg_script() {
	wp_register_style( 'ecf-pricing-css', plugins_url( 'css/pricing.css' , dirname(__FILE__) ), false, ECF_VERSION );
	wp_register_style( 'ecf-sldr', plugins_url( 'css/slider.css' , dirname(__FILE__) ), false, ECF_VERSION );
	wp_register_style( 'ecf-tabulous', plugins_url( 'css/tabulous.css' , dirname(__FILE__) ), false, ECF_VERSION );
	wp_register_style( 'ecf-colorpicker', plugins_url( 'css/colorpicker.css' , dirname(__FILE__) ), false, ECF_VERSION );
	wp_register_style( 'ecf-activate', plugins_url( 'css/activate.css' , dirname(__FILE__) ), false, ECF_VERSION );
	wp_register_style( 'ecf-tinymcecss', plugins_url( 'css/tinymce.css' , dirname(__FILE__) ), false, ECF_VERSION );
	wp_register_script( 'ecf-tinymcejs', plugins_url( 'js/tinymce.js' , dirname(__FILE__) ), false, ECF_VERSION );
	wp_register_script( 'ecf-colorpickerjs', plugins_url( 'js/colorpicker/colorpicker.js' , dirname(__FILE__) ), false, ECF_VERSION );	
	wp_register_script( 'ecf-eye', plugins_url( 'js/colorpicker/eye.js' , dirname(__FILE__) ), false, ECF_VERSION );
	wp_register_script( 'ecf-utils', plugins_url( 'js/colorpicker/utils.js' , dirname(__FILE__) ), false, ECF_VERSION );
	wp_register_style( 'ecf-formbuilder-css', plugins_url( 'css/formbuilder/formbuilder.css' , dirname(__FILE__) ), false, ECF_VERSION );
	wp_register_style( 'ecf-formbuilder-vendor-css', plugins_url( 'css/formbuilder/vendor/css/vendor.css' , dirname(__FILE__) ), false, ECF_VERSION );
	wp_register_style( 'ecf-introcss', plugins_url( 'css/introjs.min.css' , dirname(__FILE__) ), false, ECF_VERSION );
	wp_register_script( 'ecf-introjs', plugins_url( 'js/jquery/intro.min.js' , dirname(__FILE__) ), false, ECF_VERSION );
	wp_register_script( 'ecf-formbuilder-core', plugins_url( 'js/formbuilder/formbuilder-core.js' , dirname(__FILE__) ), false, ECF_VERSION );
	wp_register_script( 'ecf-formbuilder-js', plugins_url( 'js/formbuilder/formbuilder.js' , dirname(__FILE__) ), false, ECF_VERSION );
	wp_register_style( 'ecf-bootstrap-css', plugins_url( 'css/bootstrap/css/bootstrap.min.css' , dirname(__FILE__) ), false, ECF_VERSION );
	wp_register_script( 'ecf-bootstrap-js', plugins_url( 'js/bootstrap/bootstrap.min.js' , dirname(__FILE__) ) );
	wp_register_script( 'ecf-wnew', plugins_url( 'js/wnew/ecf-wnew.js' , dirname(__FILE__) ), false, ECF_VERSION );
	
}
add_action( 'admin_init', 'ecf_reg_script' );


/*-------------------------------------------------------------------------------*/
/*   Frontend Register JS & CSS
/*-------------------------------------------------------------------------------*/
function ecf_frontend_js() {
	
	wp_register_script( 'ecf-validate', ECF_URL. '/js/jquery/jquery.validate.min.js', false, ECF_VERSION );
	wp_register_style( 'ecf-frontend-css', ECF_URL. '/css/frontend.css', false, ECF_VERSION );
	wp_register_script( 'ecf-ladda', ECF_URL. '/js/jquery/ladda/ladda.jquery.js', false, ECF_VERSION );
	wp_register_script( 'ecf-ladda-js', ECF_URL. '/js/jquery/ladda/ladda.min.js', false, ECF_VERSION );
	wp_register_script( 'ecf-ladda-spin', ECF_URL. '/js/jquery/ladda/spin.js', false, ECF_VERSION );
	wp_register_script( 'ecf-notify', ECF_URL. '/js/jquery/notify.min.js', false, ECF_VERSION );	
	wp_register_script( 'ecf-placeholder', ECF_URL. '/js/jquery/jquery.placeholder.min.js', false, ECF_VERSION );	
}
add_action( 'wp_enqueue_scripts', 'ecf_frontend_js' );


/*-------------------------------------------------------------------------------*/
/*   AJAX Get Form List
/*-------------------------------------------------------------------------------*/
function ecf_grab_form_list_ajax() {
	
	if ( !isset( $_POST['grabform'] ) ) {
		die('');
		} 
		else {
			
			$list = array();
			
			global $post;
			
			$args = array(
  				'post_type' => 'easycontactform',
  				'order' => 'ASC',
  				'post_status' => 'publish',
  				'posts_per_page' => -1,
				);

				$myposts = get_posts( $args );
				foreach( $myposts as $post ) :	setup_postdata($post);

				$list[$post->ID] = array('val' => $post->ID, 'title' => esc_html(esc_js(the_title(NULL, NULL, FALSE))) );

				endforeach;
				
				}
		
			echo json_encode($list); //Send to Option List ( Array )
			die();


	}

add_action('wp_ajax_ecf_grab_form_list_ajax', 'ecf_grab_form_list_ajax');


/*-------------------------------------------------------------------------------*/
/*   CHECK BROWSER VERSION ( IE ONLY )
/*-------------------------------------------------------------------------------*/
function ecf_check_browser_version_admin( $sid ) {
	
	if ( is_admin() && get_post_type( $sid ) == 'easycontactform' ){

		preg_match( '/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $matches );
		if ( count( $matches )>1 ){
			$version = explode(".", $matches[1]);
			switch(true){
				case ( $version[0] <= '8' ):
				$msg = 'ie8';

			break; 
			  
				case ( $version[0] > '8' ):
		  		$msg = 'gah';
			  
			break; 			  

			  default:
			}
			return $msg;
		} else {
			$msg = 'notie';
			return $msg;
			}
	}
}


/*-------------------------------------------------------------------------------*/
/*  Random String
/*-------------------------------------------------------------------------------*/
function ecfRandomString($length) {
        $original_string = array_merge(range('a','z'), range('A', 'Z'));
        $original_string = implode('', $original_string);
        return substr(str_shuffle(strtolower( $original_string) ), 0, $length);
    }
	

/*-------------------------------------------------------------------------------*/
/*   CSS Compressor
/*-------------------------------------------------------------------------------*/
function ecf_css_compress( $minify ) {
	/* remove comments */
    	$minify = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $minify );

        /* remove tabs, spaces, newlines, etc. */
    	$minify = str_replace( array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $minify );
    		
        return $minify;
}

/*-------------------------------------------------------------------------------*/
/*   JS Compressor
/*-------------------------------------------------------------------------------*/
function ecf_js_compress( $minify ) {
	
$replace = array(
    '#\'([^\n\']*?)/\*([^\n\']*)\'#' => "'\1/'+\'\'+'*\2'", // remove comments from ' strings
    '#\"([^\n\"]*?)/\*([^\n\"]*)\"#' => '"\1/"+\'\'+"*\2"', // remove comments from " strings
    '#/\*.*?\*/#s'            => "",      // strip C style comments
    '#[\r\n]+#'               => "\n",    // remove blank lines and \r's
    '#\n([ \t]*//.*?\n)*#s'   => "\n",    // strip line comments (whole line only)
    '#([^\\])//([^\'"\n]*)\n#s' => "\\1\n",           
    '#\n\s+#'                 => "\n",    // strip excess whitespace
    '#\s+\n#'                 => "\n",    // strip excess whitespace
    '#(//[^\n]*\n)#s'         => "\\1\n", // extra line feed after any comments left
                                          // (important given later replacements)
    '#/([\'"])\+\'\'\+([\'"])\*#' => "/*" // restore comments in strings
  );

  $search = array_keys( $replace );
  $script = preg_replace( $search, $replace, $minify );

  $replace = array(
    "&&\n" => "&&",
    "||\n" => "||",
    "(\n"  => "(",
    ")\n"  => ")",
    "[\n"  => "[",
    "]\n"  => "]",
    "+\n"  => "+",
    ",\n"  => ",",
    "?\n"  => "?",
    ":\n"  => ":",
    ";\n"  => ";",
    "{\n"  => "{",
//  "}\n"  => "}", (because I forget to put semicolons after function assignments)
    "\n]"  => "]",
    "\n)"  => ")",
    "\n}"  => "}",
    "\n\n" => "\n"
  );

  $search = array_keys( $replace );
  $script = str_replace( $search, $replace, $script );

  return trim( $script );

}


/*-------------------------------------------------------------------------------*/
/*  Frontend Notification
/*-------------------------------------------------------------------------------*/
function ecf_notify( $tp, $pid = null ) {
	
	switch ( $tp ) {
		
		case 'formelement':
		$ecffront = '<div class="ecf_center"><div class="ecf-infobox">You have to insert at least one Name field, one Email field and one Textarea ( message box ) field.<br />Click <a href="'.admin_url( 'post.php?post='.$pid.'&action=edit' ).'">'.__('here', 'easycform').'</a> to edit your Form.</div></div>';
		break;
			
	default:
	break;		
			
	}
			
	echo $ecffront;
}


/*-------------------------------------------------------------------------------*/
/*  Slipt Newline
/*-------------------------------------------------------------------------------*/
function ecf_splitNewLine($text) {
    $code = preg_replace( '/\n$/','',preg_replace( '/^\n/','',preg_replace( '/[\r\n]+/',"\n",$text ) ) );
	$is =  explode("\n",$code);
	
	$results = array();
	
	foreach($is as $key) {
		$exploded = explode(':', $key);
		$results[$exploded[0]] = $exploded[1];
		}

 return $results;
	
}


/*-------------------------------------------------------------------------------*/
/*  Return to Bytes
/*-------------------------------------------------------------------------------*/
function ecf_return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}


/*-------------------------------------------------------------------------------*/
/* Input Clean Up
/*-------------------------------------------------------------------------------*/
function ecf_clean_input( $string, $preserve_space = 0 ) {
		if ( is_string( $string ) ) {
			if ( $preserve_space ) {
				return ecf_sanitize_string( strip_tags( stripslashes( $string ) ), $preserve_space );
			}
			return trim( ecf_sanitize_string( strip_tags( stripslashes( $string ) ) ) );
		} else if ( is_array( $string ) ) {
			reset( $string );
			while ( list($key, $value ) = each( $string ) ) {
				$string[ $key ] = ecf_sanitize_string( $value,$preserve_space );
			}
			return $string;
		} else {
			return $string;
		}
	}
	
function ecf_sanitize_string( $string, $preserve_space = 0 ) {
	if ( ! $preserve_space )
		$string = preg_replace("/ +/", ' ', trim( $string ) );

	return preg_replace( "/[<>]/", '_', $string );
	}
	
	
/*-------------------------------------------------------------------------------*/
/*  HEX to RGB
/*-------------------------------------------------------------------------------*/
function ecf_hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   //return implode(",", $rgb); // returns the rgb values separated by commas
   return implode(",", $rgb); // returns an array with the rgb values
}


/*-------------------------------------------------------------------------------*/
/*   AJAX Disable/Enable Auto Update
/*-------------------------------------------------------------------------------*/
function ecf_ajax_autoupdt() {
	
	check_ajax_referer( 'ecf-lite-nonce', 'security' );
	
	if ( !isset( $_POST['cmd'] ) ) {
		echo '0';
		wp_die();
		}
		
		else {
			update_option( "ecf-settings-automatic_update", $_POST['cmd'] );	
			echo '1';	
			wp_die();
			}
}
add_action( 'wp_ajax_ecf_ajax_autoupdt', 'ecf_ajax_autoupdt' );


/*-------------------------------------------------------------------------------*/
/*  Create Upgrade Metabox
/*-------------------------------------------------------------------------------*/
function ecf_upgrade_metabox () {
	$ecfbuy = '<div style="text-align:center;">';
	$ecfbuy .= '<a id="ecfprcngtableclr" style="outline: none !important;" href="#"><img style="cursor:pointer; margin-top: 7px;" src="'.plugins_url( 'images/buy-now.png' , dirname(__FILE__) ).'" width="241" height="95" alt="Buy Now!" ></a>';
	$ecfbuy .= '</div>';
echo $ecfbuy;	
}


/*-------------------------------------------------------------------------------*/
/*  Create Pro Demo Metabox
/*-------------------------------------------------------------------------------*/
function ecf_prodemo_metabox () {
	$enobuy = '<div style="text-align:center;">';
	$enobuy .= '<a id="ecfdemotableclr" style="outline: none !important;" target="_blank" href="http://demo.ghozylab.com/plugins/easy-contact-form-plugin/"><img style="cursor:pointer; margin-top: 7px;" src="'.plugins_url( 'images/view-demo-button.jpg' , dirname(__FILE__) ).'" width="232" height="60" alt="Pro Version Demo" ></a>';
	$enobuy .= '</div>';
echo $enobuy;	
}


/*-------------------------------------------------------------------------------*/
/*  RENAME POST BUTTON @since 1.0.1
/*-------------------------------------------------------------------------------*/
function easycform_change_publish_button( $translation, $text ) {
	if ( 'easycontactform' == get_post_type())
		if ( $text == 'Publish' ) {
    		return 'Save Form';
			}
			else if ( $text == 'Update' ) {
				return 'Update Form';
				}	

	return $translation;
}

add_filter( 'gettext', 'easycform_change_publish_button', 10, 2 );


/*-------------------------------------------------------------------------------*/
/*  WordPress Pointers 
/*-------------------------------------------------------------------------------*/
function easycform_pointer_header() {
    $enqueue = false;

    $dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

    if ( ! in_array( 'easycform_pointer', $dismissed ) ) {
        $enqueue = true;
        add_action( 'admin_print_footer_scripts', 'easycform_pointer_footer' );
    }

    if ( $enqueue ) {
        // Enqueue pointers
        wp_enqueue_script( 'wp-pointer' );
        wp_enqueue_style( 'wp-pointer' );
    }
}

function easycform_pointer_footer() {
    $pointer_content = '<h3>Thank You!</h3>';
	  $pointer_content .= '<p>You&#39;ve just installed '.ECF_ITEM_NAME.'. Click here to get short tutorial and user guide plugin.</p><p>To close this notify permanently just click dismiss button below.</p>';
?>

<script type="text/javascript">// <![CDATA[
jQuery(document).ready(function($) {
	
if (typeof(jQuery().pointer) != 'undefined') {	
    $('.ecf-intro-help').pointer({
        content: '<?php echo $pointer_content; ?>',
        position: {
            edge: 'left',
            align: 'center'
        },
        close: function() {
            $.post( ajaxurl, {
                pointer: 'easycform_pointer',
               action: 'dismiss-wp-pointer'
            });
        }
    }).pointer('open');
	
}

});
// ]]></script>
<?php
}


/*-------------------------------------------------------------------------------*/
/*   GENERATE SHARE BUTTONS
/*-------------------------------------------------------------------------------*/
function easycform_share() {
?>
<div style="position:relative; margin-top:6px;">
<ul class='easycform-social' id='easycform-cssanime'>
<li class='easycform-facebook'>
<a onclick="window.open('http://www.facebook.com/sharer.php?s=100&amp;p[title]=Check out the Best Contact Form Wordpress Plugin&amp;p[summary]=Best Contact Form Wordpress Plugin is powerful plugin to create Contact Form in minutes&amp;p[url]=http://demo.ghozylab.com/plugins/easy-contact-form-plugin/&amp;p[images][0]=http://content.ghozylab.com/wp-content/uploads/2015/02/best-cp-feed.png', 'sharer', 'toolbar=0,status=0,width=548,height=325');" href="javascript: void(0)" title="Share"><strong>Facebook</strong></a>
</li>
<li class='easycform-twitter'>
<a onclick="window.open('https://twitter.com/share?text=Best Wordpress Contact Form Plugin &url=http://demo.ghozylab.com/plugins/easy-contact-form-plugin/', 'sharer', 'toolbar=0,status=0,width=548,height=325');" title="Twitter" class="circle"><strong>Twitter</strong></a>
</li>
<li class='easycform-googleplus'>
<a onclick="window.open('https://plus.google.com/share?url=http://demo.ghozylab.com/plugins/easy-contact-form-plugin/','','width=415,height=450');"><strong>Google+</strong></a>
</li>
<li class='easycform-pinterest'>
<a onclick="window.open('http://pinterest.com/pin/create/button/?url=http://demo.ghozylab.com/plugins/easy-contact-form-plugin/;media=http://content.ghozylab.com/wp-content/uploads/2015/02/best-cp-feed.png;description=Best Contact Form Wordpress Plugin','','width=600,height=300');"><strong>Pinterest</strong></a>
</li>
</ul>
</div>

    <?php
	}
	
	
/*-------------------------------------------------------------------------------*/
/*  Update Notify
/*-------------------------------------------------------------------------------*/
function ecf_update_notify() {
	
    global $post;
		if ( !empty( $post ) && 'easycontactform' === $post->post_type && is_admin() ) {
	
    ?>
    <div class="error ecf-setupdate">
        <p><?php _e( 'We recommend you to enable plugin Auto Update so you\'ll get the latest features and other important updates from <strong>'.ECF_ITEM_NAME.'</strong>.<br />Click <a href="#"><strong><span id="ecfdoautoupdate">here</span></strong></a> to enable Auto Update.', 'easycform' ); ?></p>
    </div>
    
<script type="text/javascript">
	/*<![CDATA[*/
	/* Contact Form Lite */
jQuery(document).ready(function(){
	jQuery('#ecfdoautoupdate').click(function(){
		var cmd = 'active';
		ecf_enable_auto_update(cmd);
	});

function ecf_enable_auto_update(act) {
	var data = {
		action: 'ecf_enable_auto_update',
		security: '<?php echo wp_create_nonce( "ecf-update-nonce"); ?>',
		cmd: act,
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			if (response == 1) {
				alert('Great! Auto Update successfully activated.');
				jQuery('.ecf-setupdate').fadeOut('3000');
				}
				else {
				alert('Ajax request failed, please refresh your browser window.');
				}
				
			});
	}
	
});
	
/*]]>*/</script>
    
    <?php
	
	}
}

function ecf_enable_auto_update() {
	
	check_ajax_referer( 'ecf-update-nonce', 'security' );
	
	if ( !isset( $_POST['cmd'] ) ) {
		echo '0';
		wp_die();
		}
		
		else {
			if ( $_POST['cmd'] == 'active' ){
				update_option( "ecf-settings-automatic_update", $_POST['cmd'] );
				echo '1';				
				wp_die();
				}
	}
}
add_action( 'wp_ajax_ecf_enable_auto_update', 'ecf_enable_auto_update' );


/*-------------------------------------------------------------------------------*/
/*  Addons Page WP Pointer
/*-------------------------------------------------------------------------------*/
function ecf_addons_pointer() {
    $enqueue = false;

    $dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

    if ( ! in_array( 'ecf_add_pointer', $dismissed ) ) {
        $enqueue = true;
        add_action( 'admin_print_footer_scripts', 'ecf_add_pointer_footer' );
    }

    if ( $enqueue ) {
        // Enqueue pointers
        wp_enqueue_script( 'wp-pointer' );
        wp_enqueue_style( 'wp-pointer' );
    }
}

function ecf_add_pointer_footer() {
    $add_pointer_content = '<h3>Good News !</h3>';
	  $add_pointer_content .= '<p>In this version you can easily integrate several <strong>Pro Version</strong> features using Addons.<br /><br />You can check available addons <a href="http://localhost/wp/wp-admin/edit.php?post_type=easycontactform&page=ecf-addons">here</a></p><br />';
?>

<script type="text/javascript">// <![CDATA[
jQuery(document).ready(function($) {
	
if (typeof(jQuery().pointer) != 'undefined') {	
    $('#ecf_meta_formbuilder').pointer({
        content: '<?php echo $add_pointer_content; ?>',
        position: {
			edge: 'bottom',
            align: 'center'
        },
        close: function() {
            $.post( ajaxurl, {
                pointer: 'ecf_add_pointer',
               action: 'dismiss-wp-pointer'
            });
        }
    }).pointer('open');
	
}

});
// ]]></script>
<?php
}


/*-------------------------------------------------------------------------------*/
/* Get latest info on What's New page
/*-------------------------------------------------------------------------------*/
function ecf_lite_get_news() {
	
	if ( false === ( $cache = get_transient( 'ecflite_whats_new' ) ) ) {
		
	$addlist = get_option( "ecf_active_addons_lite" );	
		
	$url = array(
				'c' => 'news',
				'p' => 'ecflite',
				);	
		
		$feed = wp_remote_get( 'http://content.ghozylab.com/feed.php?'.http_build_query( $url ).'', array( 'sslverify' => false ) );
		
		if ( ! is_wp_error( $feed ) ) {
			if ( isset( $feed['body'] ) && strlen( $feed['body'] ) > 0 ) {
				$cache = wp_remote_retrieve_body( $feed );
				set_transient( 'ecflite_whats_new', $cache, 60 );
			}
		} else {
			$cache = '<div class="error"><p>' . __( 'There was an error retrieving the list from the server. Please try again later.', 'easycform' ) . '</div>';
		}
	}
	echo $cache;
}


/*-------------------------------------------------------------------------------*/
/* Generate EXTRA Page
/*-------------------------------------------------------------------------------*/
function ecf_earn_xtra_money() {
	
	wp_enqueue_script( 'ecf-wnew' );
	
	$aff_id 	= ecf_get_aff_option( 'ecf_affiliate_info', 'ecf_aff_id', '' );
	$aff_name 	= ecf_get_aff_option( 'ecf_affiliate_info', 'ecf_aff_name', '' );
	$aff_email 	= ecf_get_aff_option( 'ecf_affiliate_info', 'ecf_aff_email', '' );
	
		if( $aff_id != '' ) {
			
			$iscon = 'style="display:none;"'; $isdis = ''; $ists = 'Connected'; $intext = 'Disconnect'; $dnonce = 'data-nonce="'.wp_create_nonce( 'ecfaffiliate' ).'"'; $dcmd = 'data-cmd="ecf_affiliate_dis"';
		
		} else {
			
			$iscon = ''; $isdis = 'display:none;'; $ists = ''; $intext = 'Connect'; $dnonce = 'data-nonce="'.wp_create_nonce( 'ecfaffiliate' ).'"'; $dcmd = 'data-cmd="ecf_affiliate_con"';
			
			}
	
	
	ob_start(); ?>

		<div id="ecf-not-yet" <?php echo $iscon; ?>>
		<h3>If you don't have a GhozyLab Affiliate account yet, you can sign up today for free <a href="https://secure.ghozylab.com/affiliate-area/" target="_blank">here</a></h3>
		<p class="ecf-iscon" style="font-style:italic; color:#666; border-bottom: 1px dotted #CCC; margin-top: 35px; padding-bottom: 5px;"><?php _e('Fill your Affiliate Account Email or Payment Email and press Connect button to start earn extra Money with us!'); ?></p>
        </div>
        
		<div id="ecf-aff-registered" style="width: auto;<?php echo $isdis; ?>">
		<h3 id="ecf-aff-holder">Hi, <?php echo $aff_name.' ('.$aff_email. ' )'; ?></h3>
        <hr />
        </div>
        
		<form method="post">

			<?php settings_fields('ecf_aff_section'); ?>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th style="width:155px !important;" scope="row" valign="top">
							<?php _e('Account Email or Payment Email'); ?>
						</th>
						<td>
							<input id="ecf_aff_email" name="ecf_aff_email" type="text" class="regular-text" value="<?php esc_attr_e( $aff_email ); ?>" />
							<label id="is-status" style="color:green; font-style:italic;" class="description" for="ecf_aff_section_email"><?php echo $ists; ?></label>

					<?php if( false !== $aff_id ) { ?>
									<?php wp_nonce_field( 'ecf_aff_section_nonce', 'ecf_aff_section_nonce' ); ?>
									<br /><input style="margin-top: 10px;" <?php echo $dnonce; ?> <?php echo $dcmd; ?> type="submit" class="button-secondary" id="ecf-aff" name="ecf-aff" value="<?php echo $intext; ?>"/><span id="loader"></span><br /><br />
                                    <span class="ecf-aff-note">NOTE: To respect <a href="https://wordpress.org/plugins/about/guidelines/" target="_blank">Plugin Guidelines</a> ( point 10 ) so by pressing the connect button that means you are agree to displaying <strong>Powered by</strong> link in your form footer</span>
					<?php } ?>
                    
						</td>
					</tr>
				</tbody>
			</table>

		</form>	
        
         <hr style="margin-bottom:20px;">   
            
				<div class="feature-section">

					<img src="<?php echo ECF_URL . '/css/images/assets/aff-sc.png'; ?>" class="ecf-affiliate-screenshots"/>

					<h4><?php _e( 'How does it work?', 'easycform' );?></h4>
					<p><?php _e( 'After successfully registered with our Affiliate program what you have to do just :<ul style="margin-left: 30px;list-style-type: circle;"><li>Fill your Affiliate Account Email or Payment Email in field above and Hit Connect button</li><li>After connected you will see green connected status</li><li>Check your form and you will find your affiliate link in the bottom of your form like in the right side screenshot</li><li>Now when individuals follow that link and subsequently make a purchase, you will be credited for the transaction and you will receive a payout</li><li>Congratulations! You are ready to start to earn extra money :)</li></ul>', 'easycform' );?></p>
                    </div>
    
<?php        
echo ob_get_clean();
	
}


/*-------------------------------------------------------------------------------*/
/* Get Affiliate data
/*-------------------------------------------------------------------------------*/
function ecf_get_aff_option( $option_name, $key, $default = false ) {
	
	$options = get_option( $option_name );

	if ( $options ) {
		return (array_key_exists( $key, $options )) ? $options[$key] : $default;
	}

	return $default;
}


/*-------------------------------------------------------------------------------*/
/* Update Affiliate data
/*-------------------------------------------------------------------------------*/
function ecf_update_aff_info( $aff_data, $email ) {
	$aff = array(
	"ecf_aff_id" => trim( $aff_data->aff_id ),
	"ecf_aff_name" => trim( $aff_data->aff_name ),
	"ecf_aff_email" => trim( $email ),
	
		);
		
		update_option('ecf_affiliate_info', $aff);	
			
}


/*-------------------------------------------------------------------------------*/
/* Get Affiliate data ( API )
/*-------------------------------------------------------------------------------*/
function ecf_get_aff_data() {
	
	// run a quick security check
	 if( ! check_ajax_referer( 'ecfaffiliate', 'security' ) )
		return;

	switch( $_POST['command'] ){
		
		case 'ecf_affiliate_con':
		
			// listen for aff button to be clicked
			if( isset( $_POST['eml'] ) ) {
				
				$affemail = $_POST['eml'];
				
				$api_params = array(
					'ghozy_action' => 'get_aff_data',
					'email' 	=> $affemail
					);

				// Call the custom API.
				$response = _affiliateFetchmode( $api_params );

				if ( $response->status == true ) {
		
					ecf_update_aff_info( $response, $affemail );
					echo json_encode( $response );
		
				} else {
					
					$response = array(
						"status" => false,
						"aff_id" => false,
						"aff_name" => false,
						);
					
					echo json_encode( $response );
					
					}
		
			}
		
		break; 
		
		case 'ecf_affiliate_dis':
		
		delete_option( 'ecf_affiliate_info' );
		
					$response = array(
						"status" => 'disconnected',
						"aff_id" => false,
						"aff_name" => false,
						);
					
					echo json_encode( $response );
					  
		break;
		
		default:
		break;	
		
	}
	
	wp_die();

}

add_action('wp_ajax_ecf_get_aff_data', 'ecf_get_aff_data');


/*-------------------------------------------------------------------------------*/
/* Defined for using CURL or Not
/*-------------------------------------------------------------------------------*/
function _affiliateFetchmode( $api_params ) {
	
    if(function_exists('curl_version')){
		
		$response = wp_remote_get( add_query_arg( $api_params, ECF_API_URLCURL ), array( 'timeout' => 15, 'sslverify' => false ) );
		
		if ( is_wp_error( $response ) )
			return false;

			$dat = json_decode( wp_remote_retrieve_body( $response ) );
			
			}
  		
		else {
			
			$json_url = add_query_arg( $api_params, ECF_API_URL );
			$json = file_get_contents( $json_url );
			
			if ( is_wp_error( $json_url ) )
			return false;

			$dat = json_decode( $json );		
					
			}							
						
		return $dat;
			
}

