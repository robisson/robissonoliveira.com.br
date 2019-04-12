function bl_open_uploader(element, class_name){

	$that = jQuery(element);
    wp.media.editor.send.attachment = function(props, attachment){

    	$that.prev().val(attachment.url);

    	jQuery('.'+class_name + ' > img').remove();
    	jQuery('.'+class_name).prepend('<img src="'+attachment.url+'" style="width:100%">');
    }

    wp.media.editor.open(this);

    return false;
}
jQuery(function() {


	jQuery('.cf-nav a').click(function(){
		jQuery('#bluth_post_meta_social').hide();
	});
	jQuery('.cf-nav a[href="#post-format-status"]').click(function(){
		if(jQuery('#content').val() == ''){
			tinyMCE.activeEditor.selection.setContent('Status Post');
		}
		jQuery('#bluth_post_meta_social').show();
	});
	if(!jQuery('.cf-nav a[href="#post-format-status"]').hasClass('current')){
		jQuery('#bluth_post_meta_social').hide();
	}
});