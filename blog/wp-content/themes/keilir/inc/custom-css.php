<?php
function bluth_custom_css() {

	$css_options = array(
		'background_color' 			=> of_get_option('background_color'),
		'header_color' 				=> of_get_option('header_color'),
		'header_font_color' 		=> of_get_option('header_font_color'),
		'post_header_color' 		=> of_get_option('post_header_color'),
		'widget_header_color' 		=> of_get_option('widget_header_color'),
		'widget_header_font_color' 	=> of_get_option('widget_header_font_color'),
		'footer_color' 				=> of_get_option('footer_color'),
		'custom_css' 				=> html_entity_decode(of_get_option('custom_css')),
		'standard_post_color' 		=> of_get_option('standard_post_color'),
		'gallery_post_color' 		=> of_get_option('gallery_post_color'),
		'image_post_color' 			=> of_get_option('image_post_color'),
		'link_post_color' 			=> of_get_option('link_post_color'),
		'quote_post_color' 			=> of_get_option('quote_post_color'),
		'audio_post_color' 			=> of_get_option('audio_post_color'),
		'video_post_color' 			=> of_get_option('video_post_color'),
		'sticky_post_color' 		=> of_get_option('sticky_post_color'),
		'post_font_size' 			=> of_get_option('post_font_size'),
		'title_font_size' 			=> of_get_option('title_font_size'),
		'center_align_post' 		=> of_get_option('center_align_post'),
		'color_bar_1' 				=> of_get_option('color_bar_1'),
		'color_bar_2' 				=> of_get_option('color_bar_2'),
		'color_bar_3' 				=> of_get_option('color_bar_3'),
		'color_bar_4' 				=> of_get_option('color_bar_4'),
		'disable_img_hover' 		=> of_get_option('disable_img_hover'),
		'facebook_post_color' 		=> of_get_option('facebook_post_color'),
		'twitter_post_color' 		=> of_get_option('twitter_post_color'),
		'size_post_title' 			=> of_get_option('size_post_title'),
		'size_post_text' 			=> of_get_option('size_post_text'),
		'size_body' 				=> of_get_option('size_body'),
		'size_page_title' 			=> of_get_option('size_page_title'),
		'size_page_text' 			=> of_get_option('size_page_text'),
		'archive_header_font_color' => of_get_option('archive_header_font_color'),
		'archive_header_color'		 => of_get_option('archive_header_color'),
		'archive_paragraph_font_color' => of_get_option('archive_paragraph_font_color'),
		);

if(!empty($css_options) or of_get_option('icon_mode') or of_get_option('icon_media') == 'images'){ ?>
<style type="text/css">
	<?php 

	$icon_media = of_get_option('icon_media');

	if(of_get_option('icon_mode') == 'category'){
		$options_categories_obj = get_categories();
		foreach ($options_categories_obj as $category) {

			if($icon_media == 'images' and of_get_option('cat_'.$category->cat_ID.'_image', '') != ''){
				echo '.tab_icon.bl_tab_cat_'.$category->cat_ID.',.post-format-badge.post-format-badge-'.$category->cat_ID.'{ background-image: url("'.of_get_option('cat_'.$category->cat_ID.'_image').'"); }';
			}
			echo '.tab_icon.bl_tab_cat_'.$category->cat_ID.',.post-format-badge.post-format-badge-'.$category->cat_ID.'{ background-color: '.of_get_option('cat_'.$category->cat_ID.'_post_color', '#B0CB7A').'; }';
		}
		if($icon_media == 'images'){
				echo '.tab_icon.bl_tab_cat_1,.post-format-badge.post-format-badge-1{ background-image: url("'.of_get_option('cat_1_image'). '"); }';
		}
	}

	if(!empty($css_options['header_color'])){

		$hex = str_replace('#', '', $css_options['header_color']);
		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));

		if($r + $g + $b > 255){
			echo '.dropdown-menu > li > a:hover, .dropdown-menu > li > a:focus{ background: rgba(0,0,0,0.05); }';
		}else{
			echo '.dropdown-menu > li > a:hover, .dropdown-menu > li > a:focus{ background: rgba(255,255,255,0.05); }';
		}	
	}

	if(of_get_option('icon_media') == 'images'){
		echo '.post-format-badge i,.tab_icon i{ display:none;}';
		echo '.tab_icon, .post-format-badge-images{ background-position: center center; background-repeat: no-repeat; }';
		echo '.sticky .post-format-badge{ background-image: url("'.of_get_option('sticky_image').'")!important; }';
	}

	if(of_get_option('icon_media') == 'images' and of_get_option('icon_mode') == 'post_format'){
		echo '.tab_image, .post-format-image{ background-image: url("'.of_get_option('image_image').'"); }';
		echo '.tab_link, .post-format-link{ background-image: url("'.of_get_option('link_image').'"); }';
		echo '.tab_quote, .post-format-quote{ background-image: url("'.of_get_option('quote_image').'"); }';
		echo '.tab_audio, .post-format-audio{ background-image: url("'.of_get_option('audio_image').'"); }';
		echo '.tab_video, .post-format-video{ background-image: url("'.of_get_option('video_image').'"); }';
		echo '.tab_gallery, .post-format-gallery{ background-image: url("'.of_get_option('gallery_image').'"); }';
		echo '.tab_standard, .post-format-standard{ background-image: url("'.of_get_option('standard_image').'"); }';
		echo '.tab_facebook, .post-format-facebook{ background-image: url("'.of_get_option('facebook_image').'"); }';
		echo '.tab_twitter, .post-format-twitter{ background-image: url("'.of_get_option('twitter_image').'"); }';
	}

	echo (!empty($css_options['background_color'])) ? 'body{ background: '.$css_options['background_color']. '; } ' : '';
	echo (!empty($css_options['post_header_color'])) ? 'header.entry-header{ background: '.$css_options['post_header_color']. '; } ' : '';
	echo (!empty($css_options['header_color'])) ? '.dropdown-menu,header#masthead,.navbar-inverse .navbar-inner{ background: '.$css_options['header_color']. '; } ' : '';
	echo (!empty($css_options['header_font_color'])) ? 'div.navbar-inverse .nav-collapse .nav > li > a, .navbar-inverse .nav-collapse .dropdown-menu a{color:'.$css_options['header_font_color']. '!important; } ' : '';
	echo (!empty($css_options['widget_header_color'])) ? '.widget-head{ background: '.$css_options['widget_header_color']. '; } ' : '';
	echo (!empty($css_options['archive_header_color'])) ? '.archive .entry-header{ background: '.$css_options['archive_header_color']. '; } ' : '';
	echo (!empty($css_options['archive_header_font_color'])) ? '.archive .entry-header h1{ color: '.$css_options['archive_header_font_color']. '; } ' : '';
	echo (!empty($css_options['archive_paragraph_font_color'])) ? '.archive .entry-header p{ color: '.$css_options['archive_paragraph_font_color']. '; } ' : '';
	echo (!empty($css_options['widget_header_font_color'])) ? '.widget-head{color:'.$css_options['widget_header_font_color'].';} ' : '';
	echo (!empty($css_options['footer_color'])) ? 'footer.site-footer{ background:'.$css_options['footer_color'].';} ' : '';
	echo (!empty($css_options['post_font_size'])) ? '.entry-content p{font-size: '.$css_options['post_font_size'] .';}' : '';
	echo (!empty($css_options['title_font_size'])) ? '.entry-title a{font-size: '.$css_options['title_font_size'] .';}' : '';

	if(!empty($css_options['standard_post_color'])){
		echo '.post-format-standard{background-color: '.$css_options['standard_post_color'].'}';
		echo '.tab_standard{background-color:'.$css_options['standard_post_color'].';}';
		echo '.format-standard .entry-content .post-meta ~ * a, .format-standard .entry-content .post-meta a:hover, .format-standard .entry-content .entry-title a:hover{color: '.$css_options['standard_post_color'].';} #related-posts .box.related-standard{border-top: 3px solid '.$css_options['standard_post_color'].';}';
	}
	if(!empty($css_options['gallery_post_color'])){
		echo '.post-format-gallery{background-color: '.$css_options['gallery_post_color'].'}';
		echo '.tab_gallery{background-color:'.$css_options['gallery_post_color'].';}';
		echo '.format-gallery .entry-content .post-meta ~ * a, .format-gallery .entry-content .post-meta a:hover, .format-gallery .entry-content .entry-title a:hover{color: '.$css_options['gallery_post_color'].'; }#related-posts .box.related-gallery{border-top: 3px solid '.$css_options['gallery_post_color'].';}';
	}
	if(!empty($css_options['image_post_color'])){
		echo '.post-format-image{background-color: '.$css_options['image_post_color'].'}';
		echo '.tab_image{background-color:'.$css_options['image_post_color'].';}';
		echo '.format-image .entry-content .post-meta ~ * a, .format-image .entry-content .post-meta a:hover, .format-image .entry-content .entry-title a:hover{color: '.$css_options['image_post_color'].';} #related-posts .box.related-image{border-top: 3px solid '.$css_options['image_post_color'].';}';
	}
	if(!empty($css_options['link_post_color'])){
		echo '.post-format-link{background-color: '.$css_options['link_post_color'].'}';
		echo '.tab_link{background-color:'.$css_options['link_post_color'].';}';
		echo '.format-link .entry-content .post-meta ~ * a, .format-link .entry-content .post-meta a:hover, .format-link .entry-content .entry-title a:hover{color: '.$css_options['link_post_color'].';} #related-posts .box.related-link{border-top: 3px solid '.$css_options['link_post_color'].';}';
	}
	if(!empty($css_options['quote_post_color'])){
		echo '.post-format-quote{background-color: '.$css_options['quote_post_color'].'}';
		echo '.tab_quote{background-color:'.$css_options['quote_post_color'].';}';
		echo '.format-quote .entry-content .post-meta ~ * a, .format-quote .entry-content .post-meta a:hover, .format-quote .entry-content .entry-title a:hover{color: '.$css_options['quote_post_color'].';} #related-posts .box.related-quote{border-top: 3px solid '.$css_options['quote_post_color'].';}';
	}
	if(!empty($css_options['audio_post_color'])){
		echo '.post-format-audio{background-color: '.$css_options['audio_post_color'].'}';
		echo '.tab_audio{background-color:'.$css_options['audio_post_color'].';}';
		echo '.format-audio .entry-content .post-meta ~ * a, .format-audio .entry-content .post-meta a:hover, .format-audio .entry-content .entry-title a:hover{color: '.$css_options['audio_post_color'].';} #related-posts .box.related-audio{border-top: 3px solid '.$css_options['audio_post_color'].';}';
	}
	if(!empty($css_options['video_post_color'])){
		echo '.post-format-video{background-color: '.$css_options['video_post_color'].'}';
		echo '.tab_video{background-color:'.$css_options['video_post_color'].';}';
		echo '.format-video .entry-content .post-meta ~ * a, .format-video .entry-content .post-meta a:hover, .format-video .entry-content .entry-title a:hover{color: '.$css_options['video_post_color'].';} #related-posts .box.related-video{border-top: 3px solid '.$css_options['video_post_color'].';}';
	}
	if(!empty($css_options['facebook_post_color'])){
		echo '.post-format-facebook{background-color: '.$css_options['facebook_post_color'].'}';
		echo '.tab_facebook{background-color:'.$css_options['facebook_post_color'].';}';
		echo '.format-facebook .entry-content .post-meta ~ * a, .format-facebook .entry-content .post-meta a:hover, .format-facebook .entry-content .entry-title a:hover{color: '.$css_options['facebook_post_color'].';} #related-posts .box.related-facebook{border-top: 3px solid '.$css_options['facebook_post_color'].';}';
	}
	if(!empty($css_options['twitter_post_color'])){
		echo '.post-format-twitter{background-color: '.$css_options['twitter_post_color'].'}';
		echo '.tab_twitter{background-color:'.$css_options['twitter_post_color'].';}';
		echo '.format-twitter .entry-content .post-meta ~ * a, .format-twitter .entry-content .post-meta a:hover, .format-twitter .entry-content .entry-title a:hover{color: '.$css_options['twitter_post_color'].';} #related-posts .box.related-twitter{border-top: 3px solid '.$css_options['twitter_post_color'].';}';
	}
	if(!empty($css_options['sticky_post_color'])){
		echo '.sticky .post-format-badge{background-color: '.$css_options['sticky_post_color'].'}';
		echo '.sticky .entry-content .post-meta ~ * a, .sticky .entry-content .post-meta a:hover, .sticky .entry-content .entry-title a:hover{color: '.$css_options['sticky_post_color'].';}';
	}
	if(!empty($css_options['center_align_post'])){
		echo '.entry-title,.post-meta,.status-content{text-align:center;}';
		echo '.status-content iframe{margin:0 auto!important;}';
		echo '.post-meta ul{display: inline-block;}';
		echo '.format-quote .entry-title{text-align:left;}';
	}
	if(!empty($css_options['color_bar_1'])){
		echo '.top-color .first_col{background: '.$css_options['color_bar_1'].';}';
	}
	if(!empty($css_options['color_bar_2'])){
		echo '.top-color .second_col{background: '.$css_options['color_bar_2'].';}';
	}
	if(!empty($css_options['color_bar_3'])){
		echo '.top-color .third_col{background: '.$css_options['color_bar_3'].';}';
	}
	if(!empty($css_options['color_bar_4'])){
		echo '.top-color .fourth_col{background: '.$css_options['color_bar_4'].';}';
	}
	if(!empty($css_options['disable_img_hover'])){
		echo '.entry-image a:hover img{-webkit-transform: none;-moz-transform:none;-ms-transform:none;-o-transform:none;transform:none;}';
	}
	if(!empty($css_options['size_post_title'])){
		echo '.entry-title{font-size: '.$css_options['size_post_title'].';}';
	}
	if(!empty($css_options['size_post_text'])){
		echo '.entry-content p{font-size: '.$css_options['size_post_text'].';}';
	}
	if(!empty($css_options['size_body'])){
		echo 'body{font-size: '.$css_options['size_body'].';}';
	}
	if(!empty($css_options['size_page_title'])){
		echo 'article.type-page .title{font-size: '.$css_options['size_page_title'].';}';
	}
	if(!empty($css_options['size_page_text'])){
		echo '.the-content, .the-content p{font-size: '.$css_options['size_page_text'].';}';
	}
	
	echo (!empty($css_options['custom_css'])) ? $css_options['custom_css'] : '';
	?>
</style>
<?php }

}
add_action( 'wp_head', 'bluth_custom_css', 100 );
?>