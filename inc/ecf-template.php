<?php

if ( ! defined('ABSPATH') ) {
	die('Please do not load this file directly!');
}

/*-------------------------------------------------------------------------------*/
/*   Form generator ( Wrap, Script & CSS
/*-------------------------------------------------------------------------------*/
function ecf_markup_generator( $fid, $rnd ) {

	ob_start();
	
	$avname = 0;
	$avemail = 0;
	$avmsg = 0;

	$opt = ecf_opt_generator( $fid, $rnd );

	$frmArray = json_decode( trim ( $opt['frmformat'] ), true);
	
	
	// @since 1.0.17 ( Addons )	
	if( has_filter( 'ecf_form_header' ) ) {
		
		$isheader = apply_filters( 'ecf_form_header', $opt, null, $fid, $rnd );
			
		} else {
	
	if ( $opt['fo_is_head_ttl'] == 'on') {
		
		if ( $opt['fo_head_txt'] && $opt['fo_head_txt'] != 'none' ) {
			$isheader = '<header>'.$opt['fo_head_txt'].'</header>';
			}

			elseif ( $opt['fo_head_txt'] == '' && get_the_title( $fid ) ) {
				$isheader = '<header>'.esc_html( esc_js( get_the_title( $fid ) ) ).'</header>';
				}
			
				else {
			 		$isheader = '';
				}
			
	 	} else {
		 	$isheader = '';
	 		}
			
	}
			
		
		// @since 1.0.15	
		if( has_filter( 'ecf_addons_before_form_render' ) ) {
			
			echo apply_filters( 'ecf_addons_before_form_render', $opt, $fid );
			
			}	

	?>

    <!-- START JS for Form ID: <?php echo $fid; ?> -->
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			
		jQuery("#preloader-<?php echo $rnd; ?>").fadeOut(1500, function () {
			jQuery("#ecf-form-<?php echo $rnd; ?>").fadeIn(300);
			});
			
			$( '.ladda-button' ).ladda( 'bind' );

			$(function() {
				
		<?php // @since 1.0.15	
		if( has_filter( 'ecf_addons_before_form_validate' ) ) {
			
			echo apply_filters( 'ecf_addons_before_form_validate',  $opt, $fid );
			
			} ?>
				
				// Validation
				$("#form-<?php echo $rnd; ?>").validate(
				{					
					// Rules for form validation
					rules:
					{
					<?php echo $opt['frmelval']; ?>,
							
				<?php // @since 1.0.15	
				if( has_filter( 'ecf_addons_when_form_validate' ) ) {
			
					echo apply_filters( 'ecf_addons_when_form_validate',  $opt );
			
					} ?>
					
					},
										
					// Messages for form validation
					messages:
					{
					<?php if ( $opt['frmerrmsg'] != 'none' ) { echo $opt['frmelvalmsg']; } ?>
					},					
					// Do not change code below
					errorPlacement: function(error, element) {
						error.insertAfter(element.parent());
						},
						
					submitHandler: function (form) {
				<?php // @since 1.0.15	
				if( has_filter( 'ecf_addons_form_onsubmit' ) ) {
			
					echo apply_filters( 'ecf_addons_form_onsubmit',  $opt, $rnd );
			
					} else { ?>
						 ecf_onsubmit(jQuery('.form-<?php echo $rnd; ?>'));
						 
						 <?php } ?>
						 
						 },
						 
					invalidHandler: function (form) {	
					  	$.ladda( 'stopAll' );
					 	},
						 
					onkeyup: false,
					onfocusout: false,
					onclick: false
					
				});
			});	
			
			
			/* Form Submit ( Ajax ) */
			function ecf_onsubmit(form){	

				if(form.attr('action')=='#'){
					
					data = {};
					eldat = [];
					data['action'] = 'ecf_deliver_mail';
					data['formid'] = '<?php echo $fid; ?>';
					data['security'] = '<?php echo wp_create_nonce( trim($fid) ); ?>';
		
		
						<?php 
						if( has_filter( 'ecf_addons_form_element_parsing' ) ) {

							echo apply_filters( 'ecf_addons_form_element_parsing', '' );
								
							} else {
							?>

					jQuery('input, textarea', form).each(function(key){
						
						items = {};
						
						if (typeof $(this).data('type') === 'undefined') { return true; }
						

						<?php } ?>

						items['type'] = $(this).data('type');
						items['label'] = $(this).data('label');
						items['value'] = this.value;
						items['name'] = this.name;

						eldat.push(items);
							
						}); // END  form).each(function(key){
							

						<?php // @since 1.0.13
						
						if( has_filter( 'ecf_addons_element_helper' ) ) {
							if ( $frm ) {
								echo apply_filters( 'ecf_addons_element_helper', $fid, $opt['frmformat'] );
								} else {
									echo apply_filters( 'ecf_addons_element_helper', $fid, null );
									}
							}
							?>
							

						data['allelmnt'] = JSON.stringify(eldat);

						submitForm();
				
					return false;
					
				} // End if(form.attr('action')=='#'){
				
			} // End ecf_onsubmit 
			
			// Start submitForm		
			  function submitForm() {
				  
				jQuery.ajax({
					url: '<?php echo admin_url('admin-ajax.php'); ?>', 
					type: 'POST',
					dataType: 'json',
					data: data, 
					success: function(data) {
				
						if(data.Ok==true) {
									// success
							$("#form-<?php echo $rnd; ?>").get(0).reset();	
								
							if(data.msg == 'redirect') {
								window.location = "<?php echo $opt['actafter'][2]; ?>";
								} else {
									notifyme('<?php echo $opt['actafter'][1]; ?>', 'n', 'success', 'left middle');
									}
		
							}
							else {
								$("#form-<?php echo $rnd; ?>").get(0).reset();
								notifyme(data.msg, 'n', 'error', 'left middle');
								}
								
							$.ladda( 'stopAll' );	
									
							}
						});
						
				<?php // @since 1.0.15	
				if( has_filter( 'ecf_addons_after_form_submit' ) ) {
			
					echo apply_filters( 'ecf_addons_after_form_submit',  $opt );
			
					} ?>
						
					} // End submitForm
	
					
			// Notify	
			  function notifyme(msg, b, typ, pos) {
				  if (b == 'n') {
					  b = 'cf-submitted<?php echo $rnd; ?>';
				  } else {
					 b = 'atc<?php echo $rnd; ?>';
				  }
				  
				  $("#"+b).gnotify(msg,{
					  style: "nbootstrap",
					  elementPosition: pos,
					  className: typ
					  });
					  
					  msg = null;
					  typ = null;
					  
					}

		});		
		</script>
    <!-- END JS for Form ID: <?php echo $fid; ?> -->
    
    
    	<?php 
			if( has_filter( 'ecf_addons_add_inline_styles' ) ) {
				
				 echo apply_filters( 'ecf_addons_add_inline_styles', null );
				 
			} ?>

    
<!-- START Form Markup for Form ID: <?php echo $fid; ?> -->
<div id="preloader-<?php echo $rnd; ?>" class="ecfpreloader"></div>   
    <div id="ecf-form-<?php echo $rnd; ?>" class="ecf-body" style="display:none;">					
		<form method="post" enctype="multipart/form-data" action="#" id="form-<?php echo $rnd; ?>" class="ecf-form form-<?php echo $rnd; ?>">
        	<?php echo $isheader; ?>
    		<fieldset>	
    <?php

	foreach( $frmArray as $key => $value ) {
		
		foreach( $value as $k => $v ) {
			
			if ( isset( $v['placeholder'] ) && trim ( $v['placeholder'] ) !='' ) {
				$isplchldr = 'placeholder="'.$v['placeholder'].'"';
				} else {
					$isplchldr = null;
					}
			
			
			// @since 1.0.13
			if( has_filter( 'ecf_addons_form_element_logic' ) ) {
				
				 
				 if ( is_array( apply_filters( 'ecf_addons_form_element_logic', $v ) ) ) {
					 
					 $frmdata = apply_filters( 'ecf_addons_form_element_logic', $v );
					 
					 $isphonemask = $frmdata[0];
					 $isphoneplchldr = $frmdata[1];
					 $lblclass = $frmdata[2];
					 
				 	}
				 
			}
				else {
					
					if ( $v['field_type'] == 'paragraph' || $v['field_type'] == 'message' ) {
						$lblclass = 'textarea';
						}
						else {
							$lblclass = 'input';
							}
					
				}
			
					echo '<section>';		
						
					// @since 1.0.13
					if( has_filter( 'ecf_addons_fields_rules' ) ) {
						
						if ( in_array( $v['field_type'], apply_filters( 'ecf_addons_fields_rules', '' ) ) ) {
						
							$addflds = $v['field_type'];
						
							}
						
						} else {
							
							$addflds = 'noadd';
							
							}
					
					
					if ( $v['field_type'] == 'paragraph' || $v['field_type'] == 'message' || $v['field_type'] == 'name' || $v['field_type'] == 'text' || $v['field_type'] == 'email' || $v['field_type'] == 'website' || $v['field_type'] == $addflds ) {
						echo '<label class="label">'.$v['label'].'</label>';
						}
					echo '<label class="'.$lblclass.'">';
							
			// Start Generate Form Element
			switch( $v['field_type'] ){
				
				case 'name':
				
					$avname = $avname + 1;
					echo '<input data-type="'.$v['field_type'].'" data-label="'.$v['label'].'" id="'.$v['field_type'].$k.'" type="text" name="'.$v['field_type'].$k.'" '.$isplchldr.'/>';

				break;			
				
				case 'text':
				
					echo '<input data-type="'.$v['field_type'].'" data-label="'.$v['label'].'" id="'.$v['field_type'].$k.'" type="text" name="'.$v['field_type'].$k.'" '.$isplchldr.'/>';

				break;
				
				case 'email':
				
					$avemail = $avemail + 1;
					echo '<input data-type="'.$v['field_type'].'" data-label="'.$v['label'].'" id="'.$v['field_type'].$k.'" type="text" name="'.$v['field_type'].$k.'" '.$isplchldr.'/>';

				break;
				
				case 'website':

					echo '<input data-type="'.$v['field_type'].'" data-label="'.$v['label'].'" id="'.$v['field_type'].$k.'" type="text" name="'.$v['field_type'].$k.'" placeholder="http://"/>';

				break;
				
				case 'paragraph':
				
					
					echo '<textarea data-type="'.$v['field_type'].'" data-label="'.$v['label'].'" id="'.$v['field_type'].$k.'" name="'.$v['field_type'].$k.'" rows="7" '.$isplchldr.'></textarea>';

				break;
				
				case 'message':
				
					$avmsg = $avmsg + 1;
					echo '<textarea data-type="'.$v['field_type'].'" data-label="'.$v['label'].'" id="'.$v['field_type'].$k.'" name="'.$v['field_type'].$k.'" rows="7" '.$isplchldr.'></textarea>';

				break;
				
				default: // End Foreach
				break;
				
				}
				
				
			// @since 1.0.13
			if( has_filter( 'ecf_addons_add_form_element' ) ) {
				
				 apply_filters( 'ecf_addons_add_form_element', $v, $k, $rnd, $isplchldr, $isphonemask, $isphoneplchldr );
				 
			}
				
				
						echo '</label>';
						echo '</section>';
			
			}
		}
		
		?>      
        
				<?php // @since 1.0.15	
				if( has_filter( 'ecf_addons_after_form_render' ) ) {
			
					echo apply_filters( 'ecf_addons_after_form_render',  $opt );
			
					} ?>
        
         
       		 </fieldset>
        		<footer>
                    <button data-style="slide-down" id="cf-submitted<?php echo $rnd; ?>" class="ecfbutton ladda-button" type="submit" name="cf-submitted<?php echo $rnd; ?>"><span class="ladda-label">SEND</span></button> 
				</footer>
			</form>
            
            <?php  if ( ecf_get_aff_option( 'ecf_affiliate_info', 'ecf_aff_id', '' ) ) { ?>
            <span class="ecf-aff-link">Powered by <a href="https://secure.ghozylab.com/demo/?ref=<?php echo ecf_get_aff_option( 'ecf_affiliate_info', 'ecf_aff_id', '' ); ?>&goto=ecf" target="_blank">Easy Contact Form Plugin</a></span>
            
            <?php } ?>
            
            </div>  
<!-- END Form Markup for Form ID: <?php echo $fid; ?> -->
			
	<?php
	
	$theform = ob_get_clean();
	
	if ( $avemail < 0 || $avmsg < 0 || $avname < 0 ) {
		echo ecf_notify('formelement', $fid);
		} else {
			echo $theform;		
			}
			
	} // End ecf_markup_generator -----------------------------------------------*/
	

?>