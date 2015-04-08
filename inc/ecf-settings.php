<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function ecf_opt_init() {
    $ecf_featured_page = add_submenu_page('edit.php?post_type=easycontactform', 'Global Settings', __('Global Settings', 'easycform'), 'edit_posts', 'ecf_settings_page', 'ecf_stt_page');
}
add_action( 'admin_menu', 'ecf_opt_init' );


function ecf_stt_page() {
	
	?>
    
    <div class="wrap">
    <div class="metabox-holder">
			<div class="postbox">
            <h3 style="padding-bottom: 8px; border-bottom: 1px solid #CCC;"><span class="setpre"></span><?php _e( 'Global Settings', 'easycform' ); ?></h3> 
            <form id="ecf_settings">
            <div style="padding: 5px 15px 15px 15px;">
            <h4><?php _e( "Auto Update Plugin", "easycform" ); ?> :</h4>
            <div style="margin-top: 10px;">
			<?php $ecf_opt_updt = get_option("ecf-settings-automatic_update"); ?>
            <input type="radio" name="ecf_sett_autoupd" onclick="ecf_ajax_autoupdt(this);" <?php echo $ecf_opt_updt == "1" ? "checked=\"checked\"" : "";?> value="1"><label style="vertical-align: baseline;"><?php _e( "Enable", "easycform" ); ?></label>
            <input type="radio" name="ecf_sett_autoupd" onclick="ecf_ajax_autoupdt(this);" <?php echo $ecf_opt_updt == "0" ? "checked=\"checked\"" : "";?> style="margin-left: 10px;" value="0"><label style="vertical-align: baseline;"><?php _e( "Disable", "easycform" ); ?></label>
            </div>
            </div>
            </form>
           </div>
	</div>
    </div>

<style>
.setpre {
	display:none;
	margin-right:10px;
	float: left;
	width:16px;
	height:16px;
	background-repeat:no-repeat;
	}
</style>

<script type="text/javascript">
/*<![CDATA[*/

	function ecf_ajax_autoupdt(cmd) {
		jQuery('.setpre').show().css('background-image','url(<?php echo plugins_url('images/89.gif' , __FILE__ ); ?>)');
		var data = {
			action: 'ecf_ajax_autoupdt',
			security: '<?php echo wp_create_nonce( "ecf-lite-nonce"); ?>',				
			cmd: jQuery(cmd).val(),
			};
			
			jQuery.post(ajaxurl, data, function(response) {
				if (response == 1) {
					jQuery('.setpre').css('background-image','url(<?php echo plugins_url('images/valid.png' , __FILE__ ); ?>)');
					setTimeout(function() {
					jQuery('.setpre').fadeOut();
					}, 3000);
					}						
					else {
						jQuery('.setpre').hide();
						alert('Ajax request failed, please refresh your browser window.');
						}
					});
	}
/*]]>*/
</script> 
	
<?php	
	
}

?>