<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/*-------------------------------------------------------------------------------*/
/*   Email Sender
/*-------------------------------------------------------------------------------*/	
function ecf_deliver_mail() {

	check_ajax_referer( trim( $_POST['formid'] ), 'security' );

	$result = array();
	$frmid = trim( $_POST['formid'] );
	$attachments = array();
	$aftersent = get_post_meta( $frmid, 'ecf_email_action_on_sent', true );
	$singelmnt = ecf_form_element_parsing( $frmid, null, $_POST['allelmnt'], '' );
		
	if ( trim( isset ( $singelmnt['to'] ) ) ) {
		$singelmnt['to'] = $singelmnt['to'];
		} else {
			$singelmnt['to'] = get_post_meta( $frmid, 'ecf_meta_admin_email', true );
			}
				
    // sanitize form values
	$name      = sanitize_text_field( $singelmnt['name'] );
	$email     = sanitize_email( $singelmnt['email'] );
	$to	       = sanitize_email( $singelmnt['to'] );
	$message   = $singelmnt['emailbody'];
    $headers[] = 'MIME-Version: 1.0' . "\r\n";
    $headers[] = 'Content-type: text/plain; charset=utf-8' . "\r\n";
    $headers[] = 'From: '.$name.' <'.$email.'>';
    $headers[] = 'Reply-To: '.$name.' <'.$email.'>';		
 
        // If email has been process for sending, display a success message
        if ( wp_mail( $to, 'From '.$name.'', $message, $headers, $attachments ) ) {
			

			// @since 1.0.13 ( Addons )
			if ( has_action( 'ecf_before_email_sent' ) ) {
				do_action( 'ecf_before_email_sent', $frmid, $name, $email, $singelmnt );
				}
			

			// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
			remove_filter( 'wp_mail_content_type', 'ecf_set_html_content_type' );

			function ecf_set_html_content_type() {
				return 'text/html';
				}

			$result['Ok'] = true;
			$result['msg'] = $aftersent[0];
			
			
			// @since 1.0.13 ( Addons )
			if ( has_action( 'ecf_after_email_sent' ) ) {
				do_action( 'ecf_after_email_sent', $email, $name, $frmid );
				}
				

			// @since 1.0.13 ( Addons )
			if ( has_action( 'ecf_analytics_after_email_sent' ) ) {
				do_action( 'ecf_analytics_after_email_sent', $frmid );
				}
			
        	} else {
				
            	$result['Ok'] = false;
				
				global $phpmailer;
				
				if ( isset( $phpmailer ) ) {
					
					$result['msg'] = $phpmailer->ErrorInfo;
					
					} else {
						
						$result['msg'] = 'Error!';	
					
					}
				
        		}
			
	
	
	echo json_encode( $result );	
	wp_die();
	
}
	
add_action('wp_ajax_ecf_deliver_mail', 'ecf_deliver_mail');
add_action('wp_ajax_nopriv_ecf_deliver_mail', 'ecf_deliver_mail');



/*-------------------------------------------------------------------------------*/
/*   Email Body Parsing
/*-------------------------------------------------------------------------------*/	
function ecf_form_element_parsing( $fid = null, $type = null, $jsnel, $atch = null ) {
	
	$emailplain = '';
	$singelmnt = array();
	$checkboxval = array();
	$attname = array();
	$tmplateval = array();
	
	$elready = json_decode( stripslashes($jsnel), true );
	
	foreach ($elready as $key => $val) {
		
			// sanitize if values =  Array
			if ( isset ( $val['value'] ) && is_array( $val['value'] ) ) {
				array_walk_recursive( $val['value'], "ecf_sanitize_array");
				}
		
			// sanitize textarea/message values
			if ( isset ( $val['type'] ) ) {	
			if ( $val['type'] == 'paragraph' || $val['type'] == 'message' ) {
				
				$val['value'] = esc_textarea( $val['value'] );
					
				if ( $val['type'] == 'message' ) {
					$singelmnt['message'] = $val['value'];
					$val['value'] = $val['value'];
					} else {
						$val['value'] = $val['value'];
						}
								
				} else {
					// Filter it!
					$val['value'] = esc_html( $val['value'] );
					$val['value'] = esc_js( $val['value'] );
					$val['value'] = htmlspecialchars( stripslashes( $val['value'] ), ENT_QUOTES, 'UTF-8' );	
					}
					
			}

				
			//  Sanitize Text Fields
			if ( isset ( $val['type'] ) ) {	
			if ( $val['type'] == 'text' || $val['type'] == 'website' ) {
				$val['value'] = sanitize_text_field( $val['value'] );
				}
			}
			
			// Get Client Email
			if ( isset ( $val['type'] ) ) {	
			if ( $val['type'] == 'email' ) {
				$tmplateval['email'] = sanitize_email( $val['value'] );
				$singelmnt['email'] = sanitize_email( $val['value'] );
				}
			}
				
			// Get Client Name
			if ( isset ( $val['type'] ) ) {	
			if ( $val['type'] == 'name' ) {
				$singelmnt['name'] = sanitize_text_field( $val['value'] );
				$tmplateval['name'] = sanitize_text_field( $val['value'] );
				}
			}
				
				
			if ( isset ( $val['type'] ) ) {	
				if ( $val['type'] == 'date' ) {
					$val['value'] = sanitize_text_field( $val['value'] );
					}
				}
		
		
			if ( isset ( $val['cbxgroup'] ) ) {
		
				$checkboxval = null;
		
				foreach ( $val['cbxgroup'] as $dor ) {
					$checkboxval[] = $dor;
					}
			
					$val['label'] = end($checkboxval);
					unset ($checkboxval[count($checkboxval)-1]);
					$val['value'] = $checkboxval;
					
				}
				
		

		
		// EMAIL FORMAT
			$emailplain .= $val['label'].''."\n".(is_array( $val['value']) ? implode("\n", $val['value']) : $val['value'] )."\n\n";		
	
		}
		
		$singelmnt['emailbody'] = $emailplain;

		return $singelmnt;

	}


/*-------------------------------------------------------------------------------*/
/*  Sanitize Array
/*-------------------------------------------------------------------------------*/	
function ecf_sanitize_array( &$value ) {
	
	$value = esc_html( $value );
	$value = esc_js( $value );
	$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
	
}


?>