/* Page ScrollToTop */
function ecfPosition() {
    verticalOffset = typeof(verticalOffset) != 'undefined' ? verticalOffset : 0;
    element = jQuery('body');
    offset = element.offset();
    offsetTop = offset.top;
    jQuery('html, body').animate({scrollTop: offsetTop}, 700, 'linear');
	}
	
	
jQuery(document).ready(function($){	
	
			 // Upgrade Popup
 			$('#ecfprcngtableclr').on( 'click', function() {
				
				$("#myModalupgrade").modal({
					keyboard: false,
					backdrop: 'static'
					});
					return false;
					
				});	
				
});	