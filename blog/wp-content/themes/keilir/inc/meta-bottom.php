<div class="up_arrow"></div>
<div class="pull-left author-meta">
	<?php echo get_avatar( get_the_author_meta( 'ID' ) , 55 ); ?>
	<h4 class="bl_popover"  data-placement="top" data-trigger="hover" data-title="<?php echo esc_html( get_the_author() ) ?>" data-content="<?php echo get_the_author_meta('description', get_the_author_meta('ID')); ?>">
		<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) ?>"><?php echo esc_html( get_the_author() ) ?></a>
	</h4>
</div>
<?php 

	$share_url = urlencode(get_permalink());


	if(!of_get_option('disable_share_story')){

		$share_title = urlencode(html_entity_decode(get_the_title(), ENT_COMPAT, 'UTF-8'));

		$footer_share_options = of_get_option('footer_share_options');
		?>
		<div class="pull-right share-story-container">
			<small class="muted pull-left"><?php _e('Share story', 'bluth'); ?></small>
			<ul class="share-story">
				<?php if($footer_share_options === false or $footer_share_options['facebook'] == '1'){ ?><li><a class="tips" data-title="Facebook" href="javascript:void(0);" onClick="social_share('http://www.facebook.com/sharer.php?u=<?php echo $share_url; ?>&amp;t=<?php echo $share_title; ?>');"><i class="icon-facebook-1"></i></a></li><?php } ?>
				<?php if($footer_share_options === false or $footer_share_options['googleplus'] == '1'){ ?><li><a class="tips" data-title="Google+" href="javascript:void(0);" onClick="social_share('https://plus.google.com/share?url=<?php echo $share_url; ?>');"><i class="icon-gplus-1"></i></a></li><?php } ?>
				<?php if($footer_share_options === false or $footer_share_options['twitter'] == '1'){ ?><li><a class="tips" data-title="Twitter" href="javascript:void(0);" onClick="social_share('http://twitter.com/intent/tweet?url=<?php echo $share_url; ?>');"><i class="icon-twitter-1"></i></a></li><?php } ?>
				<?php if($footer_share_options === false or $footer_share_options['reddit'] == '1'){ ?><li><a class="tips" data-title="Reddit" href="javascript:void(0);" onClick="social_share('http://www.reddit.com/submit?url=<?php echo $share_url; ?>&amp;title=<?php echo $share_title ?>');"><i class="icon-reddit"></i></a></li><?php } ?>
				<?php if($footer_share_options === false or $footer_share_options['linkedin'] == '1'){ ?><li><a class="tips" data-title="Linkedin" href="javascript:void(0);" onClick="social_share('http://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo $share_url; ?>&amp;title=<?php echo $share_title; ?>');"><i class="icon-linkedin-1"></i></a></li><?php } ?>
				<?php if($footer_share_options === false or $footer_share_options['delicious'] == '1'){ ?><li><a class="tips" data-title="Delicious" href="javascript:void(0);" onClick="social_share('http://www.delicious.com/post?v=2&amp;url=<?php echo $share_url; ?>&amp;notes=&amp;tags=&amp;title=<?php echo $share_title; ?>');"><i class="icon-delicious"></i></a></li><?php } ?>
				<?php if($footer_share_options === false or $footer_share_options['email'] == '1'){ ?><li><a class="tips" data-title="Email" href="javascript:void(0);" onClick="social_share('mailto:?subject=<?php echo $share_title;?>&amp;body=<?php echo $share_url ?>');"><i class="icon-mail-1"></i></a></li><?php } ?>
			</ul>
		</div><?php
	} 

	$fb_app_id = of_get_option('facebook_app_id', false);

	// Facebook like button in post footer
	if(of_get_option('enable_fb_like_footer')){
		echo '<div class="post-footer-like"><iframe src="//www.facebook.com/plugins/like.php?href='.$share_url.'&amp;width=90&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;share=false&amp;height=21'.( ($fb_app_id) ? '&amp;appId=1377189725849154' : '' ).'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:21px;" allowTransparency="true"></iframe></div>';
	}

?>