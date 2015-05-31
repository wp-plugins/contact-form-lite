<?php

if ( ! defined( 'ABSPATH' ) ) exit;


function easycform_pricing_init() {
    $easycform_featured_init = add_submenu_page('edit.php?post_type=easycontactform', 'Pricing & compare tables', __('UPGRADE to PRO', 'easycform'), 'edit_posts', 'easycform_comparison', 'easycform_pricing_table');
}
add_action( 'admin_menu', 'easycform_pricing_init' );

function easycform_put_compare_style() {
	if ( is_admin() && isset( $_GET['page'] ) && $_GET['page'] == 'easycform_comparison' ){
		wp_enqueue_style( 'ecf-pricing-css' );
		}
}
add_action( 'admin_enqueue_scripts', 'easycform_put_compare_style' );


function easycform_pricing_table() {

?>
<!-- DC Pricing Tables:3 Start -->

    <div class="wrap">
        <div id="icon-edit" class="icon32 icon32-posts-easymedia"><br /></div>
        <h2><?php _e('Comparison', 'easycform'); ?></h2>   
  <div class="tsc_pricingtable03 tsc_pt3_style1" style="margin-bottom:110px; height:750px;">
    <div class="caption_column">
      <ul>
        <li class="header_row_1 align_center radius5_topleft"><?php easycform_share(); ?></li>
        <li class="header_row_2">
          <h2 class="caption"><?php echo ECF_ITEM_NAME; ?></h2>
        </li> 
        <li class="row_style_2"><span>License</span></li>
        <li class="row_style_4"><span>Unlimited Form</span></li>
        <li class="row_style_2"><span>100% Responsive</span></li>       
        <li class="row_style_4"><span>Unlimited colors and layout</span></li>
        <li class="row_style_2"><span>HTML/Plain Email Format</span></li>
        <li class="row_style_2"><span>SMTP email Authentication</span></li>
        <li class="row_style_4"><span>Redirect after Email Sent</span></li>
        <li class="row_style_2"><span>350+ Icons</span></li>
        <li class="row_style_4"><span>Custom CSS</span></li>
        <li class="row_style_2"><span>Custom JS</span></li>
        <li class="row_style_4"><span>Email Auto Responder</span></li>
        <li class="row_style_4"><span>Support Department Field</span></li>
        <li class="row_style_4"><span>Support Email Attachment</span></li>
        <li class="row_style_2"><span>File Type Control</span></li>
        <li class="row_style_4"><span>File Size Control</span></li>
        <li class="row_style_2"><span>Support CaptCHA</span></li>
        <li class="row_style_4"><span>Support Fields Validation</span></li>
        <li class="row_style_2"><span>Submissions Report</span></li>
        <li class="row_style_4"><span>Form Analytics</span></li>
        <li class="row_style_2"><span>WP Multisite</span></li>
        <li class="row_style_4"><span>Support</span></li>
        <li class="row_style_2"><span>Update</span></li>
        <li class="footer_row"></li>
      </ul>
    </div>
    <div class="column_1">
      <ul>
        <li class="header_row_1 align_center">
          <h2 class="col1">Lite</h2>
        </li>
        <li class="header_row_2 align_center">
          <h1 class="col1">Free</h1>
        </li>
        <li class="row_style_1 align_center">None</li>
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_1 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_2 align_center"><span class="pricing_no"></span></li>  
        <li class="row_style_1 align_center"><span class="pricing_no"></span></li>  
        <li class="row_style_3 align_center"><span class="pricing_no"></span></li> 
        <li class="row_style_1 align_center"><span class="pricing_yes"></span></li>      
        <li class="row_style_3 align_center"><span class="pricing_no"></span></li>
        <li class="row_style_1 align_center"><span class="pricing_no"></span></li>
        <li class="row_style_3 align_center"><span class="pricing_no"></span></li>        
        <li class="row_style_1 align_center"><span class="pricing_no"></span></li>        
        <li class="row_style_3 align_center"><span class="pricing_no"></span></li>
        <li class="row_style_1 align_center"><span class="pricing_no"></span></li>
        <li class="row_style_3 align_center"><span class="pricing_no"></span></li>            
        <li class="row_style_1 align_center"><span class="pricing_no"></span></li>
        <li class="row_style_3 align_center"><span class="pricing_no"></span></li>
        <li class="row_style_1 align_center"><span class="pricing_no"></span></li>
        <li class="row_style_3 align_center"><span class="pricing_no"></span></li>   
        <li class="row_style_1 align_center"><span class="pricing_no"></span></li>
        <li class="row_style_3 align_center"><span class="pricing_yes"></span></li>      
        <li class="row_style_1 align_center"><span>none</span></li>
        <li class="row_style_3 align_center"><span class="pricing_yes"></span></li>
         
        <li class="footer_row"></li>
      </ul>
    </div>
    
    <div class="column_2">
      <ul>
        <li class="header_row_1 align_center">
          <h2 class="col2">Pro</h2>
        </li>
        <li class="header_row_2 align_center">
          <h1 class="col2">$<span><?php echo ECF_PRO; ?></span></h1>
        </li>
        <li class="row_style_2 align_center"><span style="font-weight: bold; color:#F77448; font-size:14px;">1 Site</span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>        
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li> 
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li> 
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li> 
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>  
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>  
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_2 align_center"><span>1 Month</span></li>
        <li class="row_style_4 align_center"><span>1 Year</span></li>
        <li class="footer_row"><a target="_blank" href="http://ghozylab.com/plugins/ordernow.php?order=ecfpro&utm_source=contactform&utm_medium=pricingpage&utm_campaign=pricingpage" class="tsc_buttons2 red">Upgrade Now</a></li>
      </ul>
    </div>    
    
    <div class="column_2">
      <ul>
        <li class="header_row_1 align_center">
          <h2 class="col2">Pro+</h2>
        </li>
        <li class="header_row_2 align_center">
          <h1 class="col2">$<span><?php echo ECF_PROPLUS; ?></span></h1>
        </li>
        <li class="row_style_2 align_center"><span style="font-weight: bold; color:#F77448; font-size:14px;">3 Sites</span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>        
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li> 
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li> 
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li> 
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>  
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>  
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_2 align_center"><span>1 Month</span></li>
        <li class="row_style_4 align_center"><span>1 Year</span></li>
        <li class="footer_row"><a target="_blank" href="http://ghozylab.com/plugins/ordernow.php?order=ecfproplus&utm_source=contactform&utm_medium=pricingpage&utm_campaign=pricingpage" class="tsc_buttons2 red">Upgrade Now</a></li>
      </ul>
    </div>
    <div class="column_2">
      <ul>
        <li class="header_row_1 align_center">
          <h2 class="col2">Pro++</h2>
        </li>
        <li class="header_row_2 align_center">
          <h1 class="col2">$<span><?php echo ECF_PROPLUSPLUS; ?></span></h1>
        </li>
        <li class="row_style_2 align_center"><span style="font-weight: bold; color:#F77448; font-size:14px;">5 Sites</span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>        
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li> 
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li> 
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li> 
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>  
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>  
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_2 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_4 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_2 align_center"><span>6 Month</span></li>
        <li class="row_style_4 align_center"><span>1 Year</span></li>
        <li class="footer_row"><a target="_blank" href="http://ghozylab.com/plugins/ordernow.php?order=ecfproplusplus&utm_source=contactform&utm_medium=pricingpage&utm_campaign=pricingpage" class="tsc_buttons2 red">Upgrade Now</a></li>
      </ul>
    </div>    
     <div class="column_4">
      <ul>
        <li class="header_row_1 align_center">
          <h2 class="col2">Developer</h2>
        </li>
        <li class="header_row_2 align_center">
          <h1 class="col2">$<span><?php echo ECF_DEV; ?></span></h1>
        </li>
        <li class="row_style_1 align_center"><span style="font-weight: bold; color: #F77448; font-size:14px;">15 Sites</span></li>
        <li class="row_style_3 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_1 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_3 align_center"><span class="pricing_yes"></span></li>        
        <li class="row_style_1 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_3 align_center"><span class="pricing_yes"></span></li> 
        <li class="row_style_1 align_center"><span class="pricing_yes"></span></li>        
        <li class="row_style_3 align_center"><span class="pricing_yes"></span></li> 
        <li class="row_style_1 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_3 align_center"><span class="pricing_yes"></span></li>  
        <li class="row_style_1 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_3 align_center"><span class="pricing_yes"></span></li> 
        <li class="row_style_1 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_3 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_1 align_center"><span class="pricing_yes"></span></li>  
        <li class="row_style_3 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_1 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_3 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_1 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_3 align_center"><span class="pricing_yes"></span></li>
        <li class="row_style_1 align_center"><span>1 year</span></li>
        <li class="row_style_3 align_center"><span>1 year</span></li>
        <li class="footer_row"><a target="_blank" href="http://ghozylab.com/plugins/ordernow.php?order=ecfdev&utm_source=contactform&utm_medium=pricingpage&utm_campaign=pricingpage" class="tsc_buttons2 red">Upgrade Now</a></li>
      </ul>
    </div>   
    
    
    </div>
  </div>
<!-- DC Pricing Tables:3 End -->
<div class="tsc_clear"></div> <!-- line break/clear line -->
<?php


}