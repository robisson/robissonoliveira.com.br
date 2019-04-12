var ofw_frame = false;
(function($) {
	$(document).ready(function() {


		function optionsframework_add_file(event, selector) {
		
			var upload = $(".uploaded-file");
			var $el = $(this);

			event.preventDefault();


			// If the media frame already exists, reopen it.
			if(!ofw_frame){
				ofw_frame = wp.media({
					title: $el.data('choose'),
					multiple: false,
					library: {
		                type: 'image'
		            },
					button: {
						text: $el.data('update'),
						close: false
					}
				});
			}

			ofw_frame.open();

			$('.media-button-select').unbind().click(function(){

				var attachment = ofw_frame.state().get('selection').first();
				ofw_frame.close();

				selector.find('.upload').val(attachment.attributes.url);
				if ( attachment.attributes.type == 'image' ) {
					selector.find('.screenshot').empty().hide().append('<img src="' + attachment.attributes.url + '"><a class="remove-image">Remove</a>').slideDown('fast');
				}
				selector.find('.upload-button').unbind().addClass('remove-file').removeClass('upload-button').val(optionsframework_l10n.remove);
				selector.find('.of-background-properties').slideDown();
				selector.find('.remove-image, .remove-file').on('click', function() {
					optionsframework_remove_file( $(this).parents('.section') );
				});

			});	

		}
        
		function optionsframework_remove_file(selector) {
			selector.find('.remove-image').hide();
			selector.find('.upload').val('');
			selector.find('.of-background-properties').hide();
			selector.find('.screenshot').slideUp();
			selector.find('.remove-file').unbind().addClass('upload-button').removeClass('remove-file').val(optionsframework_l10n.upload);
			// We don't display the upload button if .upload-notice is present
			// This means the user doesn't have the WordPress 3.5 Media Library Support
			if ( $('.section-upload .upload-notice').length > 0 ) {
				$('.upload-button').remove();
			}
			selector.find('.upload-button').on('click', function(event) {
				optionsframework_add_file(event, $(this).parents('.section'));
			});
		}
		
		$('.remove-image, .remove-file').on('click', function() {
			optionsframework_remove_file( $(this).parents('.section') );
        });
        
        $('.upload-button').click( function( event ) {
        	optionsframework_add_file(event, $(this).parents('.section'));
        });
        
    });
	
})(jQuery);