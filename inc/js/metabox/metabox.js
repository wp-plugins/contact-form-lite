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


/* IntroJS */
      function startIntro(){
		 
        var intro = introJs();
          intro.setOptions({
            steps: [
              {
                element: '#title',
                intro: "First, enter your Form title here."
              },				  
			  {  
                element: '.fb-tab-content',
                intro: "Insert the field you choose into your form."
              },
              {
                element: '.response-field-name',
                intro: "You can click on here to edit your field, for example edit the placeholder, icon etc. You also can drag & drop to re-order the field.",
                position: 'right'
              },
              {
                element: '#ecf_meta_settings',
                intro: 'Finally, you can adjust the options below to fit your needs.',
                position: 'right'
              },
              {
                element: '#publish',
                intro: "When you are done, you can save the form and put the form into your post/page using <b>Easy Contact Form</b> Shortcode Manager.",
                position: 'bottom'
              },
              {
                element: '#ecfbuydiv',
                intro: "Upgrade to <b>Pro Version</b> which gives you a tons of awesome features!",
                position: 'left'
              },			  
              {
                element: '#ecfdemodiv',
                intro: "Or you can see the <b>DEMO</b> first before you buy.",
                position: 'left'
              },		  
			  
            ]
          });
		  
			intro.setOption('tooltipPosition', 'auto');
			intro.setOption('positionPrecedence', ['left', 'right', 'bottom', 'top'])
            intro.start();
			
			intro.oncomplete(function() {
				jQuery('#side-sortables').css({position: 'fixed'});
			});
			
			intro.onchange(function() {
				jQuery('#side-sortables').css({position: 'relative'});
			});
			
      }