<?php


/*-------------------------------------------------------------------------------------------------------*/
/*   Option Meta Generator
/*-------------------------------------------------------------------------------------------------------*/
function ecf_opt_generator( $id, $rand ) {
	
	$opt = array();
	$optvalidate = array();
	$optvalmsg = array();
	
	$opt['frmformat'] = get_post_meta( $id, 'ecf_formbuilder_format', true );
	$opt['frmerrmsg'] = get_post_meta( $id, 'ecf_meta_form_err_msg', true );
	$opt['iscaptcha'] = get_post_meta( $id, 'ecf_meta_use_captcha', true );
	$opt['captchapub'] = get_post_meta( $id, 'ecf_meta_captcha_pub', true );
	$opt['captchalbl'] = get_post_meta( $id, 'ecf_meta_captcha_label', true );
	$opt['captchathm'] = get_post_meta( $id, 'ecf_meta_captcha_themes', true );	
	$opt['captchastyle'] = get_post_meta( $id, 'ecf_meta_captcha_style', true );
	$opt['cusmaxlimit'] = get_post_meta( $id, 'ecf_meta_admin_email_maxup', true );
	$opt['actafter'] = get_post_meta( $id, 'ecf_email_action_on_sent', true );
	$opt['multiattach'] = get_post_meta( $id, 'ecf_meta_multiattach', true );
	
	// Form Style & Layout
	$opt['fo_width'] = get_post_meta( $id, 'ecf_meta_form_width', true );
	$opt['fo_bg_col'] = get_post_meta( $id, 'ecf_meta_form_back_col', true );
	$opt['fo_brdr'] = get_post_meta( $id, 'ecf_meta_form_border', true );
	$opt['fo_brdr_col'] = get_post_meta( $id, 'ecf_meta_form_border_col', true );	
	$opt['fo_is_shw'] = get_post_meta( $id, 'ecf_meta_form_isshadow', true );
	$opt['fo_sdw_col'] = get_post_meta( $id, 'ecf_meta_form_shadow_col', true );
	
	// Header Area//
	$opt['fo_head_col'] = get_post_meta( $id, 'ecf_meta_form_header_col', true );
	$opt['fo_is_head_ttl'] = get_post_meta( $id, 'ecf_meta_form_istitle', true );
	$opt['fo_head_txt'] = get_post_meta( $id, 'ecf_meta_form_header_txt', true );
	$opt['fo_head_txt_col'] = get_post_meta( $id, 'ecf_meta_form_title_col', true );
	
	// Form Fields
	$opt['fo_field_bor_col'] = get_post_meta( $id, 'ecf_meta_form_fields_br_col', true );
	$opt['fo_field_bk_col'] = get_post_meta( $id, 'ecf_meta_form_fields_bk_col', true );
	$opt['fo_field_btn_col'] = get_post_meta( $id, 'ecf_meta_form_fields_btn_col', true );
	$opt['fo_field_btn_txt'] = get_post_meta( $id, 'ecf_meta_form_fields_btn_txt', true );
	$opt['fo_field_btn_anm'] = get_post_meta( $id, 'ecf_meta_form_fields_btn_anim', true );	
	$opt['fo_txt_col'] = get_post_meta( $id, 'ecf_meta_form_text_col', true );		
	
	
	// Custom CSS
	$opt['fo_custom_css'] = get_post_meta( $id, 'ecf_meta_customcss', true );
	
	// Custom JS
	$opt['fo_custom_js'] = get_post_meta( $id, 'ecf_meta_customjs', true );
	
		
	$frmvalArray = json_decode( trim ( $opt['frmformat'] ), true);
	foreach( $frmvalArray as $key => $value ) {
		foreach( $value as $k => $v ) {
			
			if ( $v['required'] ) {
				$v['required'] = 'true';
				} else {
					$v['required'] = 'false';
					}
					
			if ( $v['field_type'] == 'email' ) {
				$emlval = ',email: true';
				} else {
					$emlval = '';
					}	
					
			if ( $v['field_type'] == 'message' ) {
				$minl = ',minlength: 10';
				} else {
					$minl = '';
					}
					
			if ( $v['field_type'] == 'checkboxes' || $v['field_type'] == 'radio' ) {
				$k = $v['cid'];
				} else {
					$k = $k;
					}	
									
					
			$optvalidate[] = ''.$v['field_type'].$k.':{required: '.$v['required'].$emlval.$minl.'}';
			$optvalmsg[] = ''.$v['field_type'].$k.':{required: "'.$opt['frmerrmsg'].'"}';

			}
		}
		
	$opt['frmelval'] = implode(',', $optvalidate);	
	$opt['frmelvalmsg'] = implode(',', $optvalmsg);	
	
	return $opt;

	}
	

/*-------------------------------------------------------------------------------------------------------*/
/*   Option Meta Generator
/*-------------------------------------------------------------------------------------------------------*/
function ecf_checkbox_helper( $fid ) {
	
	$frmvalArray = json_decode( trim ( get_post_meta( $fid, 'ecf_formbuilder_format', true ) ), true);
	foreach( $frmvalArray as $key => $value ) {
		foreach( $value as $k => $v ) {
			
			if ( $v['field_type'] == 'checkboxes' ) { 
			$cid = $v['cid'];
			
			if ( $v['label'] != '' ) {
				$thelable = $v['label'];
				} else {
					$thelable = 'Untitled';
					}
			
			
			?>
            
var cb<?php echo $cid; ?> = [];   
cbitems<?php echo $cid; ?> = {};	
        
$('input[name="<?php echo $v['field_type'].$v['cid']; ?>"]:checked').each(function() {
  
   cb<?php echo $cid; ?>.push($(this).val());
   
});
	cb<?php echo $cid; ?>.push('<?php echo $thelable; ?>');
	cbitems<?php echo $cid; ?>['cbxgroup'] = cb<?php echo $cid; ?>;
	eldat.push(cbitems<?php echo $cid; ?>);
 
				<?php }
			
			}
		}
	
	}
    

?>