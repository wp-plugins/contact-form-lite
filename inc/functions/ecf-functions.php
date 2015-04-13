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