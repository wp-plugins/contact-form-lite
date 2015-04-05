jQuery(document).ready(function($) {
	
			FormList = jQuery('#ecftinymce_select_form');

// END LOAD MEDIA

	jQuery("body").delegate("#ecf_shortcode_button","click",function(){	
		
			mg_H = 300;
			mg_W = 550;
			FormList.find('option').remove();
			jQuery("<option/>").val(0).text('Loading...').appendTo(FormList);
			
		setTimeout(function() {
			tb_show( 'Shortcode Generator', '#TB_inline?height='+mg_H+'&width='+mg_W+'&inlineId=ecfmodal' );
			jQuery('#TB_window').css("height", mg_H);
			jQuery('#TB_window').css("width", mg_W);
			jQuery('#TB_window').css("top", ((jQuery(window).height() - mg_H) / 6) + 'px');
			jQuery('#TB_window').css("left", ((jQuery(window).width() - mg_W) / 4) + 'px');
			jQuery('#TB_window').css("margin-top", ((jQuery(window).height() - mg_H) / 6) + 'px');
			jQuery('#TB_window').css("margin-left", ((jQuery(window).width() - mg_W) / 4) + 'px');
			jQuery("#TB_window").css('height','auto');
			jQuery("#TB_ajaxContent").css('height','auto');
			jQuery("select#ecftinymce_select_form").val("select");
			
			//load ajax to grab form list ( we need this methode to avoid conflict in media editor with another plugin )
			grabform();
			

		}, 300);	
		
	});
	
	// add the shortcode to the post editor
	jQuery('#ecf_insert_scrt').on("click", function () {

		if ( jQuery( "#ecftinymce_select_form" ).val() != 'select' ) {
		
			var sccode;
			sccode = "[easy-contactform id="+jQuery( "#ecftinymce_select_form option:selected" ).val()+"]";
		
			if( jQuery('#wp-content-editor-container > textarea').is(':visible') ) {
				var val = jQuery('#wp-content-editor-container > textarea').val() + sccode;
				jQuery('#wp-content-editor-container > textarea').val(val);	
				}
				else {
				tinyMCE.activeEditor.execCommand('mceInsertContent', 0, sccode);
					}

			tb_remove();
			}
			else {
				alert('Please select form first!');
				//tb_remove();
				}
		});	
		
		
function grabform() {
	
			jQuery.ajax({
			url: ajaxurl,
			data:{
				'action': 'ecf_grab_form_list_ajax',
				'grabform': 'yes'
			},
			dataType: 'JSON',
			type: 'POST',
			success:function(response){
				FormList.find('option').remove();
				jQuery("<option/>").val('select').text('- Select Form -').appendTo(FormList);
				jQuery.each(response, function(i, option)
				{
					jQuery("<option/>").val(option.val).text(option.title).appendTo(FormList);
				});
			},
			error: function(errorThrown){
			   jQuery("<option/>").val('select').text('- Select Form -').appendTo(FormList);
			}
			
		}); // End Grab	
		
}
		
		
});