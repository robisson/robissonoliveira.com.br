<?php
	/**
	 * The template for displaying any single post.
	 *
	 */
	$post_layout = get_post_meta( $post->ID, 'bluth_post_layout', true );

	$layout = of_get_option('side_bar', 'right_side');

	$layout = (empty($post_layout)) ? $layout : $post_layout;
	

	// set content width
	$content_class = 'span9';
	if($layout == 'single_column' or $layout == 'single'){
		$content_class = 'span10 offset1';
	}

	get_header();

	// Advert above content
	$ad_content_placement 	= of_get_option('ad_content_placement', array('home' => true,'pages' => true,'posts' => true));
	$ad_content_mode 		= of_get_option('ad_content_mode', 'none');
	$ad_content_box 		= of_get_option('ad_content_box', true);	
	$ad_content_padding 	= of_get_option('ad_content_padding', true);

	if($ad_content_mode != 'none' and $ad_content_placement['posts'] == true){
		echo '<div class="above_content'.(($ad_content_box) ? ' box' : '').(($ad_content_padding) ? ' pad15' : '').'">';
			if($ad_content_mode == 'image'){
				echo '<a href="'.of_get_option('ad_content_image_link').'" target="_blank"><img src="'.of_get_option('ad_content_image').'"></a>';
			}elseif($ad_content_mode == 'html'){
				echo apply_filters('shortcode_filter',do_shortcode(of_get_option('ad_content_code')));
			}
		echo '</div>';
	}
?>
	<div id="primary" class="row layout_<?php echo $layout ?>">

		<div id="content" class="<?php echo $content_class; ?>" role="main">

			<?php if ( have_posts() ){ ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'inc/post-format/single', get_post_format() ); ?>

					<?php if(get_previous_post() or get_next_post()){ ?>
					<div class="single-pagination box clearfix"><?php
						if(get_previous_post()){ ?>
							<span class="nav-previous" style="float:left;">
								<?php previous_post_link( '%link', '<span><i class="icon-left-open"></i>'.__('Older', 'bluth').'</span><br/><div class="post-title hidden-tablet hidden-phone">%title</div>' ); ?>
							</span><?php
						} 
						if(get_next_post()){ ?>
							<span class="nav-next" style="float:right;">
								<?php next_post_link( '%link', '<span>'.__('Newer', 'bluth').'<i class="icon-right-open"></i></span><br/><div class="post-title hidden-tablet hidden-phone">%title</div>' ); ?>
							</span><?php
						} ?>
					</div>
					<?php } ?>

					<?php 
					// show related posts by tag
					if(!of_get_option('disable_related_posts')){ 
					 	get_template_part( 'inc/related-posts' );
					} 
					endwhile; // OK, let's stop the post loop once we've displayed it 

					// If comments are open load up the default comment template provided by Wordpress
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}					
				
				 }else{ // Well, if there are no posts to display and loop through, let's apologize to the reader (also your 404 error) ?>
				
				<article class="post error">
					<h1 class="404"><?php _e('Page not found', 'bluth'); ?></h1>
				</article>

			<?php } // OK, I think that takes care of both scenarios (having a post or not having a post to show) ?>

		</div><!-- #content .site-content -->

		<?php if($layout == 'right_side' or $layout == 'left_side'){ ?>
		<aside id="side-bar" class="span3">
				<?php get_sidebar(); ?>
		</aside>
		<?php } ?>			
	</div><!-- #primary .content-area -->
<?php get_footer(); // This fxn gets the footer.php file and renders it ?>
