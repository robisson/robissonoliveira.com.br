<?php
	if(is_sticky() and !is_single()){
		$post_icon = of_get_option('sticky_icon', 'icon-pin');
	}

	$post_format = (get_post_format()) ? get_post_format() : 'standard';

	if($post_format == 'status'){
		if(get_post_meta( $post->ID, 'bluth_facebook_status', true )){
			$post_format = 'facebook';
		}else{
			$post_format = 'twitter';
		}
	}

	$icon_media = of_get_option('icon_media', '');

	$icon_defaults = array(
		'facebook' => 'icon-facebook-1',
		'twitter' => 'icon-twitter-1',
		'audio' => 'icon-volume-up',
		'gallery' => 'icon-picture',
		'image' => 'icon-picture-1',
		'link' => 'icon-link',
		'quote' => 'icon-quote-left',
		'video' => 'icon-videocam',
		'standard' => 'icon-calendar-3'
	);


	if(of_get_option('icon_mode') == 'post_format'){
?>
	<div class="post-format-badge<?php echo (!empty($icon_media) ? ' post-format-badge-'.$icon_media : ''); ?> post-format-<?php echo $post_format ?>">
		<i class="<?php echo (isset($post_icon)) ? $post_icon : of_get_option($post_format.'_icon', $icon_defaults[$post_format]); ?>"></i>
	</div>
	<?php }else{
		$category = get_the_category();
	?>
	<div class="post-format-badge<?php echo (!empty($icon_media) ? ' post-format-badge-'.$icon_media : ''); ?> post-format-badge-<?php echo $category[0]->cat_ID ?>">
		<i class="<?php echo (isset($post_icon)) ? $post_icon : of_get_option('cat_'.$category[0]->cat_ID.'_icon', 'icon-calendar-3'); ?>"></i>
	</div>
<?php 
	} 
?>