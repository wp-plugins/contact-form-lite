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

	?>

    <!-- START JS for Form ID: <?php echo $fid; ?> -->
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			
		jQuery("#preloader-<?php echo $rnd; ?>").fadeOut(1500, function () {
			jQuery("#ecf-form-<?php echo $rnd; ?>").fadeIn(300);
			});
			
			$( '.ladda-button' ).ladda( 'bind' );

			$(function() {
				
				// Validation
				$("#form-<?php echo $rnd; ?>").validate(
				{					
					// Rules for form validation
					rules:
					{
					<?php echo $opt['frmelval']; ?>,
				
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
						 ecf_onsubmit(jQuery('.form-<?php echo $rnd; ?>'));
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
		
					jQuery('input, textarea', form).each(function(key){
						
						items = {};
						
						if (typeof $(this).data('type') === 'undefined') { return true; }

						items['type'] = $(this).data('type');
						items['label'] = $(this).data('label');
						items['value'] = this.value;
						items['name'] = this.name;

						eldat.push(items);
							
						}); // END  form).each(function(key){

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
			
			if ( $v['field_type'] == 'paragraph' || $v['field_type'] == 'message' ) {
				$lblclass = 'textarea';
				}
				else {
					$lblclass = 'input';
					}
			
					echo '<section>';
					if ( $v['field_type'] == 'paragraph' || $v['field_type'] == 'message' || $v['field_type'] == 'name' || $v['field_type'] == 'text' || $v['field_type'] == 'email' || $v['field_type'] == 'website' ) {
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
				
						echo '</label>';
						echo '</section>';
			
			}
		}
		
		?>       
       		 </fieldset>
        		<footer>
                    <button data-style="slide-down" id="cf-submitted<?php echo $rnd; ?>" class="ecfbutton ladda-button" type="submit" name="cf-submitted<?php echo $rnd; ?>"><span class="ladda-label">SEND</span></button> 
				</footer>
			</form>
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