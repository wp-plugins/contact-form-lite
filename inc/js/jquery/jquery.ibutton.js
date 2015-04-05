jQuery(document).ready(function($) {
	
			jQuery("input[type=checkbox].ecfswitch").each(function() {
				// Insert switch
				jQuery(this).before('<span class="ecfswitch"><span class="background" /><span class="ecfmask" /></span>');
				 //Hide checkbox
				jQuery(this).hide();
				if (!jQuery(this)[0].checked) jQuery(this).prev().find(".background").css({left: "-49px"});
				if (jQuery(this)[0].checked) jQuery(this).prev().find(".background").css({left: "-2px"});	
			});
			// Toggle switch when clicked
			jQuery("span.ecfswitch").click(function() {
				// Slide switch off
				if (jQuery(this).next()[0].checked) {
					jQuery(this).find(".background").animate({left: "-49px"}, 200);
				// Slide switch on
				} else {
					jQuery(this).find(".background").animate({left: "-2px"}, 200);
				}
				// Toggle state of checkbox
				jQuery('#').attr('checked', true);
				jQuery(this).next()[0].checked = !jQuery(this).next()[0].checked;
												
			});
			
			// Toggle switch when clicked
			jQuery("#comtarget").on('click', function(){
				 var checked = jQuery(this).attr('checked');
				// Toggle state of checkbox
				if (checked) {
					jQuery(this).val('_blank');
				} else {
					jQuery(this).val('_self');
					}
												
			});
			
			
});