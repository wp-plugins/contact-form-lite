<?php
/**
 * Add a custom Meta Box
 *
 * @param array $meta_box Meta box input data
 */



/*-----------------------------------------------------------------------------------*/
/*  Right Upgrade Metabox
/*-----------------------------------------------------------------------------------*/
function ecf_custom_metabox() {
	add_meta_box( 'ecfbuydiv', __( 'Upgrade to Pro Version' ), 'ecf_upgrade_metabox', 'easycontactform', 'side', 'default' );
	add_meta_box( 'ecfdemodiv', __( 'AMAZING Pro Version DEMO' ), 'ecf_prodemo_metabox', 'easycontactform', 'side', 'default' );
}

add_action( 'do_meta_boxes', 'ecf_custom_metabox' );
add_action( "admin_head", 'ecf_admin_head_script' );
add_action( 'admin_enqueue_scripts', 'ecf_load_script', 10, 1 );
//add_action( 'admin_enqueue_scripts', 'ecf_addons_pointer' );

function ecf_load_script() {
	if ( strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post-new.php' ) || strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post.php' ) ) {
		if ( get_post_type( get_the_ID() ) == 'easycontactform' ) {


			wp_enqueue_style( 'ecf-formbuilder-css' );	
			wp_enqueue_style( 'ecf-formbuilder-vendor-css' );
			wp_enqueue_script( 'jquery-ui-core' ); 	
			wp_enqueue_script( 'jquery-ui-widget' ); 	
			wp_enqueue_script( 'jquery-ui-tabs' );	
			wp_enqueue_script( 'jquery-ui-mouse' );		
			wp_enqueue_script( 'jquery-ui-draggable' ); 
			wp_enqueue_script( 'jquery-ui-droppable' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'ecf-formbuilder-core' );
			wp_enqueue_script( 'ecf-formbuilder-js' );
			wp_enqueue_script( 'ecf-metascript', plugins_url( 'js/metabox/metabox.js' , __FILE__ ), false, ECF_VERSION );
			wp_enqueue_script( 'ecf-ibutton-js', plugins_url( 'js/jquery/jquery.ibutton.js' , __FILE__ ) );
			wp_enqueue_style( 'ecf-ibutton-css', plugins_url( 'css/ibutton.css' , __FILE__ ), false, ECF_VERSION );
			wp_enqueue_style( 'ecf-metacss', plugins_url( 'css/metabox.css' , __FILE__ ), false, '' );
			wp_enqueue_style( 'ecf-sldr' );
			wp_enqueue_style( 'ecf-tabulous' );
			wp_enqueue_style( 'ecf-colorpicker' );
			wp_enqueue_style( 'ecf-bootstrap-css' );
			wp_enqueue_style( 'ecf-introcss' );
			wp_enqueue_script( 'ecf-introjs' );
			wp_enqueue_script( 'ecf-bootstrap-js' );
			wp_enqueue_script( 'ecf-colorpickerjs' );
			wp_enqueue_script( 'jquery-ui-slider' );
			wp_enqueue_script( 'jquery-effects-highlight' );
			wp_enqueue_script( 'ecf-no-toggle', plugins_url( 'js/metabox/no-toggle.js' , __FILE__ ), array('jquery'), 1, true ); // @since 1.0.25
        	wp_enqueue_script('ecf-no-toggle');

			//add_action('admin_footer', 'ecf_scroll' );	
			add_action('admin_footer', 'ecf_upgrade_popup' );
			easycform_pointer_header();
			
			// @since 1.0.13
			if( has_filter( 'ecf_addons_enqueue' ) ) {
				apply_filters( 'ecf_addons_enqueue', '' );
				}
			
			}
		}
}

function ecf_admin_head_script () {
	if ( strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post-new.php' ) || strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post.php' ) ) {
		if ( get_post_type( get_the_ID() ) == 'easycontactform' ) {

			?>
			<style type="text/css" media="screen">
			#minor-publishing { display: none !important; }
			#ecf_email_auto_response_ifr { height: 270px !important;}
			@media only screen and (min-width: 1150px) {	
		    	#side-sortables.fixed { position: fixed; top: 55px; right: 20px; width: 280px; }
				}	
            </style>


			<script type="text/javascript">
			/* Javascript/jQuery Code Here */
			
			var servMax = '<?php echo  substr(ini_get( 'upload_max_filesize' ), 0, -1); ?>';
			
			jQuery(document).ready(function($) {
				
			jQuery('#wp-ecf_email_auto_response-media-buttons a').not('#insert-media-button').hide();
				
			// Scroll to Top 
		/*	jQuery(document).on( 'scroll', function(){
				if (jQuery(window).scrollTop() > 700) {
					jQuery('.ecf-scroll-top-wrapper').addClass('show');
					} else {
						jQuery('.ecf-scroll-top-wrapper').removeClass('show');
					}
				
				});
 
    		jQuery('.ecf-scroll-top-wrapper').on('click', ecfPosition); 	*/
				
		    var ecfpos = $('#side-sortables').offset();
		    
			$(window).scroll(function(){
			   if($(window).scrollTop() > ecfpos.top)
			    {
				$('#side-sortables').addClass('fixed');
			    	} 
			    	else 
			    		{
						$('#side-sortables').removeClass('fixed');
			    		}    
		    	});
				
			$('#ecf_meta_admin_email_maxup').keyup(function() {
				if ( parseInt($(this).val()) > parseInt(servMax) ) {
					alert('You can\'t use value greater than '+servMax+'' );
						$(this).val('');
						return false;
					}
				});
				
			$("#ecf_meta_fileex").keypress(function( e ) {
				if(e.which === 32)
        			return false;
				});
				
			$(function(){
    			$('#ecf_meta_fileex').on('paste', function(e){
        			var e = $(this);
        			setTimeout(function(){
            			e.val( $.trim(e.val()).replace(/ /g, "\r\n"));
        					}, 0);
    				});
				});
				
			$('#tahandle').click(function() {
				$('#ecf_meta_fileex').attr('readonly',! this.checked)
				});
				
			var links = jQuery('.ecftabcon li a');
			var tabcont = jQuery("#tabs_container");
			
			jQuery(".ecfdefaulttab").trigger("click");
			
			jQuery('.ecftabcon li a').on('click', function () {
				jQuery(tabcont).hide();
				jQuery(".tabloader").css("height", "300").addClass("tbloader");
				jQuery(tabcont).find("tr").hide();
				jQuery(tabcont).find("."+jQuery(this).attr("id")+"").fadeIn(500, function() {
					jQuery(tabcont).fadeIn("slow");
					jQuery(".tabloader").css("height", "auto").removeClass("tbloader");
					});				
				
                links.removeClass('tabulous_active');
                jQuery(this).addClass('tabulous_active');

								
				});	
				
					
				jQuery(function(){
      				fb = new Formbuilder({
        				selector: '.fb-main',
       			 	bootstrapData:
					[
	<?php if ( trim ( get_post_meta( get_the_ID(), 'ecf_formbuilder_format', true ) ) ) {
			
				$phpArray = json_decode( trim ( get_post_meta( get_the_ID(), 'ecf_formbuilder_format', true ) ), true);
				
				foreach ($phpArray as $key => $value) {
    				foreach ($value as $k => $v) {
						
						if ( $v['field_type'] == 'attachment' ) {
							$attchHandle = '$( "#ecf-field-attachment" ).prop( "disabled", true ).attr("disabled","disabled");';
							}
						
       					echo json_encode($v).',';
    					}
					}
		} else {
			echo '
          {
            "label": "Name",
            "field_type": "name",
			"icons": "fa-user",
			"iconpos": "prepend",
            "required": true,
            "field_options": {"size":"medium"},
            "cid": "c1"
          },
          {
            "label": "Email",
            "field_type": "email",
			"icons": "fa-envelope-o",
			"iconpos": "prepend",
            "required": true,
            "field_options": {"size":"medium"},
            "cid": "c2"
          },
          {
            "label": "Subject",
            "field_type": "text",
			"icons": "fa-asterisk",
			"iconpos": "prepend",
            "required": true,
            "field_options": {"size":"medium"},
            "cid": "c3"
          },
          {
            "label": "Message",
            "field_type": "message",
			"icons": "fa-comment",
			"iconpos": "prepend",
            "required": true,
            "field_options": {"size":"large"},
            "cid": "c4"
          },';
		}?>
		]
      				});

      				fb.on('save', function(payload){
		  				jQuery("#ecf_formbuilder_format").text('');
						jQuery("#ecf_formbuilder_format").text(payload);

      					});		
					<?php if ( isset( $attchHandle ) ) { echo $attchHandle; } ?>		
    			});
			});
                
             </script>  
                    
              <?php
              }
		}
} 
 
 
function ecf_add_meta_box( $meta_box )
{
    if ( !is_array( $meta_box ) ) return false;
    
    // Create a callback function
    $callback = create_function( '$post,$meta_box', 'ecf_create_meta_box( $post, $meta_box["args"] );' );
    add_meta_box( $meta_box['id'], $meta_box['title'], $callback, $meta_box['page'], $meta_box['context'], $meta_box['priority'], $meta_box );
}

/**
 * Create content for a custom Meta Box
 *
 * @param array $meta_box Meta box input data
 */
function ecf_create_meta_box( $post, $meta_box )
{
	
    if ( !is_array( $meta_box ) ) return false;
    
    if ( isset( $meta_box['description'] ) && $meta_box['description'] != '' ){
    	echo '<p>'. $meta_box['description'] .'</p>';
    }
	
    if ( isset( $meta_box['istabbed'] ) && $meta_box['istabbed'] != '' ){
		
		// @since 1.0.13
		$paneltab = '<li><a class="tabulous_active ecfdefaulttab" id="email" href="#tabs-1" title="">Email</a></li><li><a id="layout" href="#tabs-2" title="">Layout & Styles</a></li><li><a id="misc" href="#tabs-3" title="">Miscellaneous</a></li><li><a id="adv" href="#tabs-4" title="">Advanced</a></li>';
		
		echo '<div id="ecftabs"><ul class="ecftabcon">';
		
		if( has_filter( 'ecf_panel_tab' ) ) {
			echo apply_filters( 'ecf_panel_tab', $paneltab );
			} else {
				echo $paneltab;
				}
        echo '</ul><div class="tabloader"><div id="tabs_container">';

	}
	
	wp_nonce_field( basename( __FILE__ ), 'ecf_meta_box_nonce' );
	echo '<table class="form-table ecf-metabox-table">';
 
	foreach ( $meta_box['fields'] as $field ){
		// Get current post meta data
		$meta = get_post_meta( $post->ID, $field['id'], true );
		if ( isset( $field['isfull'] ) && $field['isfull'] == 'yes' ) {
			$isfull = '';
		} else {
			$isfull = '<th><label for="'. $field['id'] .'"><strong>'. $field['name'] .'<br></strong><span>'. $field['desc'] .'</span></label>'.( isset( $field['needmargin'] ) && $field['needmargin'] ? $field['needmargin'] : '' ) .'</th>';	
		}
		echo '<tr class="'. $field['id'] .' '. ( isset( $field['group'] ) && $field['group'] ? $field['group'] : '' ) .' '. ( isset( $field['isselector'] ) && $field['isselector'] ? $field['isselector'] : '' ) .' '. ( isset( $field['extragrp'] ) && $field['extragrp'] ? $field['extragrp'].'-fields' : '' ) .'">'.$isfull.''; // @since 1.0.13
		
		switch( $field['type'] ){
			
			case 'text':
			
			if ( isset( $field['needlefttext'] ) && $field['needlefttext'] ) {
				$max = '<span style="font-size:12px; font-style:italic;">( Default server Max Limit is : '.ini_get( 'upload_max_filesize' ). ' )</span>';
				$txtw = 'style="text-align: center !important; width: 40px !important; margin-right: 3px !important;"';
				$in = substr(ini_get( 'upload_max_filesize' ), -1).'&nbsp;&nbsp;';
				} else {
					$max = null;
					$txtw = null;
					$in = null;
					}
					
				echo '<td><input '.$txtw.' type="text" name="ecf_meta['. $field['id'] .']" id="'. $field['id'] .'" value="'. ($meta ? $meta : $field['std']) .'" size="30" />'.$in.$max.'</td>';
				break;	
					
			case 'textarea':
			
			echo '<td>';
			if ( isset( $field['nthick'] ) && $field['nthick'] ) { $isd = 'readonly'; $tb = '<input id="tahandle" type="checkbox" />'; } else {$tb = null; $isd = null;}
 	echo '<textarea style="width: 100% !important; vertical-align:top !important;" name="ecf_meta['. $field['id'] .']" id="'. $field['id'] .'" type="'. $field['type'] .'" cols="45" rows="7" '.$isd.'>'.( $meta != '' ? esc_textarea( $meta ) : $field['std'] ).'</textarea>';
    echo $tb;
    echo '</div></td>';
		
				break;
		
			case 'customsize':
			
			    echo '<td>';
                echo '<div id="cscontw"><strong>Width</strong> <input style="margin-right:5px !important; margin-left:3px; width:43px !important; float:none !important;" name="ecf_meta['. $field['id'] .'_'.$field['width'].']" id="'. $field['id'] .'_w" type="text" value="' .(get_post_meta($post->ID, 'ecf_cp_thumbsize_'. $field['width'] .'', true) ? get_post_meta($post->ID, 'ecf_cp_thumbsize_'. $field['width'] .'', true) : $field['stdw']).'" />  ' .$field['pixopr']. '</div>

<span id="cssep" style="border-right:solid 1px #CCC;margin-left:9px; margin-right:10px !important; "></span>
 	<div id="csconth"><strong>Height</strong> <input style="margin-left:3px; margin-right:5px !important; width:43px !important; float:none !important;" name="ecf_meta['. $field['id'] .'_'.$field['height'].']" id="'. $field['id'] .'_h" type="text" value="' .(get_post_meta($post->ID, 'ecf_cp_thumbsize_'. $field['height'] .'', true) ? get_post_meta($post->ID, 'ecf_cp_thumbsize_'. $field['height'] .'', true) : $field['stdh']).'" /> ' .$field['pixopr']. '';
			    echo '</div></td>';
			    break;
		
		
			case 'select':
				echo'<td><select class="ecfmetaselect" name="ecf_meta['. $field['id'] .']" id="'. $field['id'] .'">';
				foreach ( $field['options'] as $key => $option ){
					
					if ( $field['needkey'] ) {
						$tval = $key; 
					} else {
						$tval = $option;
						}
										
					echo '<option value="' . $tval . '"';
					if ( $meta ){ 
						if ( $meta == $tval ) echo ' selected="selected"'; 
					} else {
						if ( $field['std'] == $tval ) echo ' selected="selected"';
					}
					echo'>'. $option .'</option>';
				}
				echo'</select></td>';
				
				break;

	
			case 'slider': 
			echo '<td>';
	?>	
    
				  <script type="text/javascript">
				  /*<![CDATA[*/
				  
				 jQuery(document).ready(function($) { 
				  
/* Slider init */

	
        jQuery( '#<?php echo $field['id']; ?>_slider' ).slider({
            range: 'min',
            min: <?php echo $field['min']; ?>,
            max: <?php echo $field['max']; ?>,
			<?php if ( $field['usestep'] == '1' ) { ?>
			step: <?php echo $field['step']; ?>,
			<?php } ?>
            value: '<?php if ( $meta != "") { echo $meta; } else { echo $field['std']; } ?>',
            slide: function( event, ui ) {
                jQuery( "#<?php echo $field['id']; ?>" ).val( ui.value );
            	}
        	});

				  
				  });				

				  /*]]>*/
                  </script>   
    
    <?php echo '<span class="ecf_metaslider"><span id="'.$field['id'].'_slider" ></span><input class="pixoprval" name="ecf_meta['.$field['id'].']" id="'.$field['id'].'" type="text" value="'.( $meta != "" ? $meta : $field['std'] ).'" /><span id="pixopr">'.$field['pixopr'].'</span></span>';
			

				echo '</td>';
			    break;
					
				
			case 'radio':
				echo '<td>';
				
				if ( ecf_check_browser_version_admin( get_the_ID() ) != 'ie8' ) {
					foreach ( $field['options'] as $key => $option ){
						echo '<input id="'. $key .'" type="radio" name="ecf_meta['. $field['id'] .']" value="'. $key .'" class="css-checkbox"';
						if ( $meta ){
							if ( $meta == $key ) echo ' checked="checked"'; 
							} else {
								if ( $field['std'] == $key ) echo ' checked="checked"';
								}
								echo ' /><label for="'. $key .'" class="css-label">'. $option .'</label> ';
								}
							}
							
				else {
					foreach ( $field['options'] as $key => $option ){
						echo '<label class="radio-label"><input type="radio" name="ecf_meta['. $field['id'] .']" value="'. $key .'" class="radio"';
						if ( $meta ){
							if ( $meta == $key ) echo ' checked="checked"';
							} else {
								if ( $field['std'] == $key ) echo ' checked="checked"';
								}
								echo ' /> '. $option .'</label> ';
								}
							}							
												
				echo '</td>';
				
				break;
				
				
			case 'radioredirect':
				echo '<td>';
				if ( ecf_check_browser_version_admin( get_the_ID() ) != 'ie8' ) {
					foreach ( $field['options'] as $key => $option ){
						echo '<input id="'. $key .'" type="radio" name="ecf_meta['. $field['id'] .'][]" value="'. $key .'" class="css-checkbox"';
						if ( $meta ){
							if ( $meta[0] == $key ) echo ' checked="checked"'; 
							} else {
								if ( $field['std'] == $key ) echo ' checked="checked"';
								}
								echo ' /><label for="'. $key .'" class="css-label">'. $option .'</label> ';
								}
							}
							
				else {
					foreach ( $field['options'] as $key => $option ){
						echo '<label class="radio-label"><input type="radio" name="ecf_meta['. $field['id'] .'][]" value="'. $key .'" class="radio"';
						if ( $meta ){
							if ( $meta[0] == $key ) echo ' checked="checked"';
							} else {
								if ( $field['std'] == $key ) echo ' checked="checked"';
								}
								echo ' /> '. $option .'</label> ';
								}
							}							
												
				echo '<br><span style="margin-right: 10px !important;">Text</span><input style="margin-top:20px !important; margin-bottom:10px !important; width: 80% !important;" type="text" name="ecf_meta['. $field['id'] .'][]" id="'. $field['id'] .'_text" value="'. ($meta ? $meta[1] : $field['txt']) .'" size="30" /><br><span style="margin-right: 12px !important;">URL</span><input style="margin-bottom:10px !important; width: 80% !important;" type="text" name="ecf_meta['. $field['id'] .'][]" id="'. $field['id'] .'_url" value="'. ($meta ? $meta[2] : $field['url']) .'" size="30" /></td>';
				
				break;
				
				
			case 'checkbox':
			    echo '<td>';
			    $val = '';
                if ( $meta ) {
                    if ( $meta == 'on' ) $val = ' checked="checked"';
                } else {
                    if ( $field['std'] == 'on' ) $val = ' checked="checked"';
                }

                echo '<input type="hidden" name="ecf_meta['. $field['id'] .']" value="off" />
                <input class="ecfswitch" type="checkbox" id="'. $field['id'] .'" name="ecf_meta['. $field['id'] .']" value="on"'. $val .' />';
			    echo '</td>';
			    break;	
				

			case 'color':

			
				?>
				  <script type="text/javascript">
				  /*<![CDATA[*/
				  
				 jQuery(document).ready(function($) { 
				  
				 jQuery('#<?php echo $field['id']; ?>_picker').children('div').css('backgroundColor', '<?php echo ($meta ? $meta : $field['std']); ?>');    
				 jQuery('#<?php echo $field['id']; ?>_picker').ColorPicker({
					color: '<?php echo ($meta ? $meta : $field['std']); ?>',
					onShow: function (colpkr) {
						jQuery(colpkr).fadeIn(500);
						return false;
					},
					onHide: function (colpkr) {
						jQuery(colpkr).fadeOut(500);
						return false;
					},
					onChange: function (hsb, hex, rgb) {
						//jQuery(this).css('border','1px solid red');
						jQuery('#<?php echo $field['id']; ?>_picker').children('div').css('backgroundColor', '#' + hex);						
						jQuery('#<?php echo $field['id']; ?>_picker').next('input').attr('value','#' + hex);
					}
				  });
				  
				  });				

				  /*]]>*/
                  </script>   
                
                <?php
			

			    echo '<td>';
				echo'<div id="'. $field['id'] .'_picker" class="colorSelector"><div></div></div>
				<input style="margin-left:10px; width:75px !important;" name="ecf_meta['. $field['id'] .']" id="'. $field['id'] .'" type="text" value="'.($meta ? $meta : $field['std']).'" />';
                echo '</td>';
			    break;
				
				
			case 'tinymce':
			
				echo '<td>';	
				wp_editor( ($meta ? $meta : $field['std']) , 'ecf_email_auto_response', array(
				'textarea_name' => 'ecf_meta['. $field['id'] .']',
				'media_buttons' => true,
				'textarea_rows' => 15,
				'wpautop' => true
				 ) );
				echo '</td>';	
				
				break;
				
				
			case 'formbuilder':
			
			echo '<td>';
			
	echo '<div style="height:130px;" id="fbloader" class="tbloader"></div>'; // @since 1.0.25	
 	echo '<div style="display:none;" class="fb-main"></div>';
	echo '<textarea style="width: 100% !important; vertical-align:top !important; display: none;" name="ecf_meta['. $field['id'] .']" id="'. $field['id'] .'" type="'. $field['type'] .'" cols="45" rows="7">'.( $meta != '' ? esc_textarea( $meta ) : $field['std'] ).'</textarea>';
    
    echo '</div></td>';
					?>
                <script type="text/javascript">
                jQuery(document).ready(function($) {
					jQuery(".ecfdefaulttab").trigger("click");
					});
             </script>
                <?php
		
				break;		
			
				
			case 'separator':
			    echo '<td class="menuseparator">';
			    echo '</td>';
			    break;	
					
	
/*-----------------------------------------------------------------------------------*/	
		}
		
		// @since 1.0.13
		if ( has_filter( 'ecf_addons_fields_control' ) ) {
			echo apply_filters( 'ecf_addons_fields_control', $meta, $field, $post->ID );
			}
		
		echo '</tr>';
	}
 
	echo '</table>';
	
    if ( isset( $meta_box['istabbed'] ) && $meta_box['istabbed'] != '' ){
    	echo '</div></div><!--END CON--></div><!--END TAB-->';
    }
	
}

/*-----------------------------------------------------------------------------------*/
/*	Register related Scripts and Styles
/*-----------------------------------------------------------------------------------*/

	// SELECT MEDIA METABOX
add_action( 'add_meta_boxes', 'ecf_metabox_work' );
function ecf_metabox_work(){


// Form Builder

// Config 	

	    $meta_box = array(
		'id' => 'ecf_meta_formbuilder',
		'title' =>  __( 'Form Builder', 'easycform' ),
		'description' => __( '<span class="ecf-introjs"><a href="javascript:void(0);" onclick="startIntro();"><span class="ecf-intro-help"></span>Click Here to learn How to create your first Form</a></span><br /><br />You can add / remove, edit or order any elements with this form builder to fit to your needs.<br /><div class="ecfinfobox">Need Pro Version Features without <i>Full Upgrade</i>? <a href="'.admin_url( 'edit.php?post_type=easycontactform&page=ecf-addons' ).'">Check Available Addons here</a></div>', 'easycform' ),
		'page' => 'easycontactform',
		'context' => 'normal',	
		'istabbed' => '',
		'priority' => 'default',
		'fields' => array(
					
			array(
					'name' => __( '', 'easycform' ),
					'desc' => __( '', 'easycform' ),
					'id' => 'ecf_formbuilder_format',
					'type' => 'formbuilder',		
					'isfull' => 'yes',
					'std' => '{"fields":[{"label":"Name","field_type":"name","icons":"fa-user","iconpos":"prepend","required":true,"field_options":{"size":"medium"},"cid":"c1"},{"label":"Email","field_type":"email","icons":"fa-envelope-o","iconpos":"prepend","required":true,"field_options":{"size":"medium"},"cid":"c2"},{"label":"Subject","field_type":"text","icons":"fa-asterisk","iconpos":"prepend","required":true,"field_options":{"size":"medium"},"cid":"c3"},{"label":"Message","field_type":"message","icons":"fa-comment","iconpos":"prepend","required":true,"field_options":{"size":"large"},"cid":"c4"}]}',
					),	

		
			)
	);
    ecf_add_meta_box( $meta_box );


// Config 	

	    $meta_box = array(
		'id' => 'ecf_meta_settings',
		'title' =>  __( 'Settings', 'easycform' ),
		'description' => __( '', 'easycform' ),
		'page' => 'easycontactform',
		'context' => 'normal',
		'istabbed' => 'yes',	
		'priority' => 'default',
		'fields' => array(
		
		
			// Email Settings
			
			array(
					'name' => __( 'Email Options', 'easycform' ),
					'desc' => __( '' ),
					'id' => 'ecf_meta_separator_email',
					'type' => 'separator',
					'group' => 'email',
					),
			
			
			array(
					'name' => __( 'Email Recipient', 'easycform' ),
					'desc' => __( 'You can change this email with other email that you want. When use Department field, email recipients will use the email that you set in the email field for each department.', 'easycform' ),
					'id' => 'ecf_meta_admin_email',
					'type' => 'text',	
					'group' => 'email',	
					'std' => get_bloginfo( 'admin_email' )
					),
					
			array(
					'name' => __( 'Email Format<span class="ecf_pro_only"></span>', 'easycform' ),
					'desc' => __( 'You can set email format to suit your needs.', 'easycform' ),
					'id' => 'ecf_meta_email_format',
					'type' => 'radio',
					'group' => 'email',	
					'options' => array (	
										'html'=> 'HTML',	
										'plain'=> 'Plain Text'),	
					'std' => 'html',
					),
					
			array(
					'name' => __( 'Email Header', 'easycform' ),
					'desc' => __( 'This text used as the title for your incoming email. This should probably be your site name.', 'easycform' ),
					'id' => 'ecf_meta_admin_email_header',
					'type' => 'text',	
					'group' => 'email',	
					'std' => 'Email from '.get_bloginfo('name')
					),
					
			array(
					'name' => __( 'Display Date/Time, User agent & sender IP Address<span class="ecf_pro_only"></span>', 'easycform' ),
					'desc' => __( 'If ON, all information above will show in your incoming email footer.', 'easycform' ),
					'id' => 'ecf_meta_admin_email_addinfo',
					'type' => 'checkbox',	
					'group' => 'email',	
					'std' => 'on'
					),
					
			array(
					'name' => __( 'Save an Email Attachment?<span class="ecf_pro_only"></span>', 'easycform' ),
					'desc' => __( 'If ON, an email attachment will be stored on the server. You can locate the files in /wp-content/uploads/ directory.', 'easycform' ),
					'id' => 'ecf_meta_saveattch',
					'type' => 'checkbox',
					'group' => 'email',
					'std' => 'off'
					),
					
			array(
					'name' => __( 'Allow Multiple Upload Attachment?<span class="ecf_pro_only"></span>', 'easycform' ),
					'desc' => __( 'If ON, sender can upload multiple files at once.', 'easycform' ),
					'id' => 'ecf_meta_multiattach',
					'type' => 'checkbox',
					'group' => 'email',
					'std' => 'on'
					),
					
			array(
					'name' => __( 'Action After email is Sent', 'easycform' ),
					'desc' => __( 'You can set what action to sender when email is sent.', 'easycform' ),
					'id' => 'ecf_email_action_on_sent',
					'type' => 'radioredirect',
					'group' => 'email',
					'needkey' => 'yes',
					'options' => array (	
										'text'=> ' Display text',
										'redirect'=> 'Redirect to the Page'),	
					'std' => 'text',
					'txt' => 'Your Message Submitted Successfully',
					'url' => 'http://',
					'trgt' => 'off',
					'needmargin' => '<br><br><br><br><br>',
					),
					
			array(
					'name' => __( 'Email Auto Responder', 'easycform' ),
					'desc' => __( '<span style="font-style: italic; font-size:12px; color: #F40043;">All features below only available in PRO VERSION</span>' ),
					'id' => 'ecf_meta_separator_autores',
					'type' => 'separator',
					'group' => 'email',
					),
					
			array(
					'name' => __( 'Enable Auto Responder?', 'easycform' ),
					'desc' => __( 'If ON, sender will receive an email response from you. Use field below to create your own response content.', 'easycform' ),
					'id' => 'ecf_email_isauto_response',
					'type' => 'checkbox',
					'group' => 'email',		
					'std' => 'on'
					),
					
			array(
					'name' => __( 'Auto Response From Email', 'easycform' ),
					'desc' => __( 'Email to send Auto Response from. For example no-reply@ghozylab.com', 'easycform' ),
					'id' => 'ecf_email_auto_response_from',
					'type' => 'text',
					'group' => 'email',		
					'std' => ''
					),
					
			array(
					'name' => __( 'Auto Response From Name', 'easycform' ),
					'desc' => __( 'This should probably be your site name.', 'easycform' ),
					'id' => 'ecf_email_auto_response_name',
					'type' => 'text',
					'group' => 'email',		
					'std' => get_bloginfo('name')
					),
					
					
			array(
					'name' => __( 'Auto Response Content', 'easycform' ),
					'desc' => __( 'Sends an automated reply to incoming messages.<br>Available template tags:<br><ul class="ecf-tipslist">
    <li>{name} - The sender name</li>
    <li>{email} - The sender email address</li>
	<li>{message} - The sender message</li>
    <li>{date_time} - The date/time when an email sent</li>
</ul', 'easycform' ),
					'id' => 'ecf_email_auto_response',
					'type' => 'tinymce',
					'group' => 'email',	
					'std' => 'Dear {name},

Thank you for contacting us, we will reply via ( {email} ) as soon as possible starting from {date_time}

&nbsp;

Best Regard,
<em>'.get_bloginfo('name').'</em>',
					),
					
			array(
					'name' => __( 'Captcha Settings', 'easycform' ),
					'desc' => __( '<span style="font-style: italic; font-size:12px; color: #F40043;">All features below only available in PRO VERSION</span>' ),
					'id' => 'ecf_meta_separator_form_cap',
					'type' => 'separator',
					'group' => 'misc',
					),
					
					
			array(
					'name' => __( 'Use Captcha', 'easycform' ),
					'desc' => __( 'We recommend you to enable this option to to stop spam email. Register for free <a href="https://www.google.com/recaptcha/admin" target="_blank">here</a>.', 'easycform' ),
					'id' => 'ecf_meta_use_captcha',
					'type' => 'checkbox',
					'group' => 'misc',			
					'std' => 'off'
					),
					
					
			array(
					'name' => __( 'Captcha Style', 'easycform' ),
					'desc' => __( 'There are available three style that you can choose.', 'easycform' ),
					'id' => 'ecf_meta_captcha_style',
					'type' => 'radio',
					'group' => 'misc',
					'needkey' => 'yes',
					'options' => array (	
										'v2'=> 'New reCAPTCHA',
										'v1'=> 'Old reCAPTCHA',	
										'simple'=> 'Simple'),	
					'std' => 'v2',
					),
					
			array(
					'name' => __( 'Captcha Themes', 'easycform' ),
					'desc' => __( 'Select theme to customizing the Look and Feel of reCAPTCHA.', 'easycform' ),
					'id' => 'ecf_meta_captcha_themes',
					'type' => 'select',	
					'group' => 'misc',
					'needkey' => 'yes',		
					'options' => array (
										'light'=> 'Light ( New reCAPTCHA Only )',	
										'dark'=> 'Dark ( New reCAPTCHA Only ) ',
										'clean'=> 'Clean ( Old reCAPTCHA Only )',	
										'white'=> 'White ( Old reCAPTCHA Only )',
										'red'=> 'Red ( Old reCAPTCHA Only )',
										'blackglass'=> 'Black Glass ( Old reCAPTCHA Only )'),
					'std' => 'light',
					),
					
					
			array(
					'name' => __( 'Captcha Public key', 'easycform' ),
					'desc' => __( 'Make sure to enter Captcha Secret key correctly to avoid email delivery failure.', 'easycform' ),
					'id' => 'ecf_meta_captcha_pub',
					'type' => 'text',
					'group' => 'misc',		
					'std' => ''
					),
					
			array(
					'name' => __( 'Captcha Secret key', 'easycform' ),
					'desc' => __( 'It\'s very important to enter Captcha Secret key correctly to avoid email delivery failure.', 'easycform' ),
					'id' => 'ecf_meta_captcha_skey',
					'type' => 'text',
					'group' => 'misc',		
					'std' => ''
					),
					
			array(
					'name' => __( 'Captcha Label', 'easycform' ),
					'desc' => __( 'Label on top of Captcha. For example : Enter characters below:<br />type none for no text.', 'easycform' ),
					'id' => 'ecf_meta_captcha_label',
					'type' => 'text',
					'group' => 'misc',		
					'std' => 'Enter characters below:'
					),
						
		
			// Form Layout	
			
			array(
					'name' => __( 'Form Layout', 'easycform' ),
					'desc' => __( '<span style="font-style: italic; font-size:12px; color: #F40043;">All features below only available in PRO VERSION</span>' ),
					'id' => 'ecf_meta_separator_form_layout',
					'type' => 'separator',
					'group' => 'layout',
					),
					
			array(
					'name' => __( 'Form Width', 'easycform' ),
					'desc' => __( 'You can easily set the form width with this option. Default : 550px', 'easycform' ),
					'id' => 'ecf_meta_form_width',
					'type' => 'slider',
					'std' => '550',
					'max' => '1024',
					'min' => '300',
					'step' => '10',
					'usestep' => '1',
					'pixopr' => 'px',
					'group' => 'layout',
					),
					
			array(
					'name' => __( 'Background Color', 'easycform' ),
					'desc' => __( 'Set the background color for your form here. Default: #ffffff', 'easycform' ),
					'id' => 'ecf_meta_form_back_col',
					'type' => 'color',
					'std' => '#ffffff',
					'group' => 'layout',
					),
					
			array(
					'name' => __( 'Form Border', 'easycform' ),
					'desc' => __( 'Set the form border size here. Default : 1px', 'easycform' ),
					'id' => 'ecf_meta_form_border',
					'type' => 'slider',
					'std' => '1',
					'max' => '10',
					'min' => '0',
					'step' => '1',
					'usestep' => '1',
					'pixopr' => 'px',
					'group' => 'layout',
					),
						
			array(
					'name' => __( 'Border Color', 'easycform' ),
					'desc' => __( 'Set the color of border here. Default: #d6d6d6', 'easycform' ),
					'id' => 'ecf_meta_form_border_col',
					'type' => 'color',
					'std' => '#d6d6d6',
					'group' => 'layout',
					),
					
			array(
					'name' => __( 'Use Shadow?', 'easycform' ),
					'desc' => __( 'If ON, the shadow will show around the form.', 'easycform' ),
					'id' => 'ecf_meta_form_isshadow',
					'type' => 'checkbox',
					'group' => 'layout',		
					'std' => 'on'
					),	
					
			array(
					'name' => __( 'Shadow Color', 'easycform' ),
					'desc' => __( 'Set the color of shadow here. Default: #383838', 'easycform' ),
					'id' => 'ecf_meta_form_shadow_col',
					'type' => 'color',
					'std' => '#383838',
					'group' => 'layout',
					),
					
			array(
					'name' => __( 'Label /Title Color', 'easycform' ),
					'desc' => __( 'Set the color of form label / title here. Default: #666666', 'easycform' ),
					'id' => 'ecf_meta_form_text_col',
					'type' => 'color',
					'std' => '#666666',
					'group' => 'layout',
					'needmargin' => '<br><br>',
					),
				
			array(
					'name' => __( 'Header Area', 'easycform' ),
					'desc' => __( '' ),
					'id' => 'ecf_meta_separator_form_header',
					'type' => 'separator',
					'group' => 'layout',
					),
					
			array(
					'name' => __( 'Header Color', 'easycform' ),
					'desc' => __( 'You can set header color here. Default: #F8F8F8', 'easycform' ),
					'id' => 'ecf_meta_form_header_col',
					'type' => 'color',
					'std' => '#F8F8F8',
					'group' => 'layout',
					),
					
					
			array(
					'name' => __( 'Show Form Title', 'easycform' ),
					'desc' => __( 'If ON, the title will appear on the form header.', 'easycform' ),
					'id' => 'ecf_meta_form_istitle',
					'type' => 'checkbox',
					'group' => 'layout',		
					'std' => 'on'
					),	
					
			array(
					'name' => __( 'Form Title', 'easycform' ),
					'desc' => __( 'With this option the sender will easily determine the form type. For example ; Contact Us or Feedback. You can type none if you want to hide the form header.', 'easycform' ),
					'id' => 'ecf_meta_form_header_txt',
					'type' => 'text',
					'group' => 'layout',		
					'std' => ''
					),
					
			array(
					'name' => __( 'Header Title Color', 'easycform' ),
					'desc' => __( 'You can set title color here. Default: #232323', 'easycform' ),
					'id' => 'ecf_meta_form_title_col',
					'type' => 'color',
					'std' => '#232323',
					'group' => 'layout',
					'needmargin' => '<br><br>',
					),
					
			array(
					'name' => __( 'Form Elements', 'easycform' ),
					'desc' => __( '' ),
					'id' => 'ecf_meta_separator_form_content_el',
					'type' => 'separator',
					'group' => 'layout',
					),
						
			array(
					'name' => __( 'Fields Border Color on Hover & Focus', 'easycform' ),
					'desc' => __( 'Set the border color for each field on hover & focus. Default: #2da5da', 'easycform' ),
					'id' => 'ecf_meta_form_fields_br_col',
					'type' => 'color',
					'std' => '#2da5da',
					'group' => 'layout',
					),
					
			array(
					'name' => __( 'Fields Background Color', 'easycform' ),
					'desc' => __( 'Set the background color for each field. Default: #ffffff', 'easycform' ),
					'id' => 'ecf_meta_form_fields_bk_col',
					'type' => 'color',
					'std' => '#ffffff',
					'group' => 'layout',
					),
					
			array(
					'name' => __( 'Button Color', 'easycform' ),
					'desc' => __( 'Set the button color to fit your need. Default: #2DA5DA', 'easycform' ),
					'id' => 'ecf_meta_form_fields_btn_col',
					'type' => 'color',
					'std' => '#2DA5DA',
					'group' => 'layout',
					),
					
			array(
					'name' => __( 'Button Text', 'easycform' ),
					'desc' => __( 'You can change default SEND for submit button text.', 'easycform' ),
					'id' => 'ecf_meta_form_fields_btn_txt',
					'type' => 'text',
					'group' => 'layout',		
					'std' => 'SEND'
					),
					
			array(
					'name' => __( 'Button Loading Style', 'easycform' ),
					'desc' => __( 'Select the loading animation for your submit button. Default: slide-down', 'easycform' ),
					'id' => 'ecf_meta_form_fields_btn_anim',
					'type' => 'select',	
					'group' => 'layout',
					'needkey' => 'yes',		
					'options' => array (
										'expand-left'=> 'Expand Left',	
										'expand-right'=> 'Expand Right',
										'expand-up'=> 'Expand Up',	
										'zoom-in'=> 'Zoom In',
										'zoom-out'=> 'Zoom Out',
										'slide-left'=> 'Slide Left',
										'slide-right'=> 'Slide Right',
										'slide-up'=> 'Slide Up',
										'slide-down'=> 'Slide Down',										
										),
					'std' => 'slide-down',
					),
					
			array(
					'name' => __( 'Required Error Message', 'easycform' ),
					'desc' => __( 'You can customize required error message here or<br />type none for no message.', 'easycform' ),
					'id' => 'ecf_meta_form_err_msg',
					'type' => 'text',	
					'group' => 'layout',
					'std' => 'This field is required'
					),
					
			array(
					'name' => __( 'Advanced Settings', 'easycform' ),
					'desc' => __( '<span style="font-style: italic; font-size:12px; color: #F40043;">All features below only available in PRO VERSION</span>' ),
					'id' => 'ecf_meta_separator_form_adv',
					'type' => 'separator',
					'group' => 'adv',
					),
					
			array(
					'name' => __( 'Attachment Size Limit', 'easycform' ),
					'desc' => __( 'For best performance we recommend you to set less than 2M even though your server provides a limit of '.ini_get( 'upload_max_filesize' ).'. Set to 0 if you want to use default server Max Limit.', 'easycform' ),
					'id' => 'ecf_meta_admin_email_maxup',
					'type' => 'text',	
					'needlefttext' => 'y',
					'group' => 'adv',	
					'std' => '0'
					),
					
			array(
					'name' => __( 'Accepted Filetypes', 'easycform' ),
					'desc' => __( 'Currently, sender permitted to upload the following file types from your contact form. You can add another Filetypes and make sure the format should like this : NAME:MIME Type ( for example html:text/html ) and don\'t forget to add on newline.<br><br> - Complete list <a href="http://en.wikipedia.org/wiki/Internet_media_type" target="_blank">here</a>', 'easycform' ),
					'id' => 'ecf_meta_fileex',
					'type' => 'textarea',
					'nthick' => '1',
					'std' => 'txt:text/plain
css:text/css
gif:image/gif
png:image/x-png
jpeg:image/jpeg
jpg:image/jpeg
JPG:image/jpeg
jpe:image/jpeg
TIFF:image/tiff
tiff:image/tiff
tif:image/tiff
TIF:image/tiff
bmp:image/x-ms-bmp
BMP:image/x-ms-bmp
ai:application/postscript
eps:application/postscript
ps:application/postscript
rtf:application/rtf
pdf:application/pdf
doc:application/msword
docx:application/msword
xls:application/vnd.ms-excel
xlsx:application/vnd.ms-excel
zip:application/zip
rar:application/rar
wav:audio/wav
mp3:audio/mp3
ppt:application/vnd.ms-powerpoint
aar:application/sb-replay
sce:application/sb-scenario',
					'group' => 'adv',
					),	
					
					
			array(
					'name' => __( 'Custom CSS', 'easycform' ),
					'desc' => __( 'Want to add any custom CSS code? Put in here, and the rest is taken care of. This overrides the default stylesheets.<br>For example: body {
    background-color: #E6E6E6;
}', 'easycform' ),
					'id' => 'ecf_meta_customcss',
					'type' => 'textarea',
					'std' => '',
					'group' => 'adv',
					),	
					
					
			array(
					'name' => __( 'Custom JS', 'easycform' ),
					'desc' => __( 'Want to add any custom JS code? Put in here, and the rest is taken care of.<br>For example: alert(\'Hello World!\');', 'easycform' ),
					'id' => 'ecf_meta_customjs',
					'type' => 'textarea',
					'std' => '',
					'group' => 'adv',
					),	
				
				
		
			)
	);
	
	// @since 1.0.13
	if( has_filter( 'ecf_addons_metabox' ) ) {
		$meta_box = apply_filters( 'ecf_addons_metabox', $meta_box );
		} else {
			$meta_box = $meta_box;
			}
	
    ecf_add_meta_box( $meta_box );
	
}

//-----------------------------------------------------------------------------------------------------------------

/**
 * Save custom Meta Box
 *
 * @param int $post_id The post ID
 */
function ecf_save_meta_box( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;
	
	if ( !isset( $_POST['ecf_meta'] ) || !isset( $_POST['ecf_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['ecf_meta_box_nonce'], basename( __FILE__ ) ) )
		return;
	
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) ) return;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) ) return;
	}
			
		// save data
		foreach( $_POST['ecf_meta'] as $key => $val ) {
			delete_post_meta( $post_id, $key );
			add_post_meta( $post_id, $key, $_POST['ecf_meta'][$key], true ); 
		}
}
add_action( 'save_post', 'ecf_save_meta_box' );


function ecf_scroll() {

    echo '<div class="ecf-scroll-top-wrapper">
    		<span class="ecf-scroll-top-inner">
        		<i class="ecfa"></i>
    			</span>
			</div>';
	}
	
	
function ecf_upgrade_popup() {
	
echo '<!-- Modal -->
<div class="modal fade" id="myModalupgrade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Pricing Table</h4>
            </div>
            <div class="modal-body" style="background-color: #f5f5f5;">
            
           
            <div class="row flat"> <!-- Content Start -->
            
            
              <div class="col-lg-3 col-md-3 col-xs-6">
                <ul class="plan plan1">
                    <li class="plan-name">
                        Pro
                    </li>
                    <li class="plan-price">
                        <strong>$'.ECF_PRO.'</strong>
                    </li>
                    <li>
                        <strong>1 site</strong>
                    </li>
                    <li class="plan-action">
                        <a href="http://ghozylab.com/plugins/ordernow.php?order=ecfpro&utm_source=contactform&utm_medium=orderfromeditor&utm_campaign=orderfromeditor" target="_blank" class="btn btn-danger btn-lg">BUY NOW</a>
                    </li>
                </ul>
            </div> 
            
              <div class="col-lg-3 col-md-3 col-xs-6"><span class="featured"></span>
                <ul class="plan plan1">
                    <li class="plan-name">
                        Pro+
                    </li>
                    <li class="plan-price">
                        <strong>$'.ECF_PROPLUS.'</strong>
                    </li>
                    <li>
                        <strong>3 sites</strong>
                    </li>
                    <li class="plan-action">
                        <a href="http://ghozylab.com/plugins/ordernow.php?order=ecfproplus&utm_source=contactform&utm_medium=orderfromeditor&utm_campaign=orderfromeditor" target="_blank" class="btn btn-danger btn-lg">BUY NOW</a>
                    </li>
                </ul>
            </div> 
            
              <div class="col-lg-3 col-md-3 col-xs-6">
                <ul class="plan plan1">
                    <li class="plan-name">
                        Pro++
                    </li>
                    <li class="plan-price">
                        <strong>$'.ECF_PROPLUSPLUS.'</strong>
                    </li>
                    <li>
                        <strong>5 sites</strong>
                    </li>
                    <li class="plan-action">
                        <a href="http://ghozylab.com/plugins/ordernow.php?order=ecfproplusplus&utm_source=contactform&utm_medium=orderfromeditor&utm_campaign=orderfromeditor" target="_blank" class="btn btn-danger btn-lg">BUY NOW</a>
                    </li>
                </ul>
            </div> 
            
              <div class="col-lg-3 col-md-3 col-xs-6">
                <ul class="plan plan1">
                    <li class="plan-name">
                        Developer
                    </li>
                    <li class="plan-price">
                        <strong>$'.ECF_DEV.'</strong>
                    </li>
                    <li>
                        <strong>15 sites</strong>
                    </li>
                    <li class="plan-action">
                        <a href="http://ghozylab.com/plugins/ordernow.php?order=ecfdev&utm_source=contactform&utm_medium=orderfromeditor&utm_campaign=orderfromeditor" target="_blank" class="btn btn-danger btn-lg">BUY NOW</a>
                    </li>
                </ul>
            </div> 
            
            
            </div><!-- Content End  --> 
            
            </div>
        </div>
    </div>
</div>
    
<!--  END HTML (to Trigger Modal) -->';	
	
	
}



?>