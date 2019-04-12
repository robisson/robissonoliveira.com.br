<?php 
	/* Template name: Page: Left sidebar */ 
	$hide_author = get_post_meta( $post->ID, 'bluth_page_hide_author', 'off' );
	$hide_title = get_post_meta( $post->ID, 'bluth_page_hide_title', 'off' );

	get_header(); 

	// Advert above content
	$ad_content_placement 	= of_get_option('ad_content_placement', array('home' => true,'pages' => true,'posts' => true));
	$ad_content_mode 		= of_get_option('ad_content_mode', 'none');
	$ad_content_box 		= of_get_option('ad_content_box', true);	
	$ad_content_padding 	= of_get_option('ad_content_padding', true);	

	if($ad_content_mode != 'none' and $ad_content_placement['pages'] == true){
		echo '<div class="above_content'.(($ad_content_box) ? ' box' : '').(($ad_content_padding) ? ' pad15' : '').'">';
			if($ad_content_mode == 'image'){
				echo '<a href="'.of_get_option('ad_content_image_link').'" target="_blank"><img src="'.of_get_option('ad_content_image').'"></a>';
			}elseif($ad_content_mode == 'html'){
				echo apply_filters('shortcode_filter',do_shortcode(of_get_option('ad_content_code')));
			}
		echo '</div>';
	}
	
?>
	<div id="primary" class="row layout_left_side">

		<div id="content" class="span9" role="main">

			<?php if(have_posts()){ 
			
				while ( have_posts() ) : the_post(); 
				?>
				<div class="box">
				<article class="type-page">

					<?php if ( has_post_thumbnail() ) { ?>
					<div class="entry-image">
						<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'bluth'), the_title_attribute('echo=0') ); ?>" rel="bookmark">
							<?php the_post_thumbnail('full'); ?>
						</a>
					</div>
					<?php } ?>
					
					<?php if($hide_title != 'on'){ ?>
					<h1 class="title"><?php the_title(); ?></h1>
					<?php } ?>
					<div class="the-content">
						<?php the_content(); ?>
						<?php wp_link_pages(); ?>
					</div><!-- the-content -->
				</article>
				<?php if(!of_get_option('disable_footer_page') and $hide_author != 'on'){ ?>
				<footer class="entry-meta clearfix">
					<?php get_template_part( 'inc/meta-bottom' ); ?>
				</footer><!-- .entry-meta -->					
				<?php } ?>
				</div>

				<?php if(comments_open()){ ?>
					<?php comments_template( '', true ); ?>
				<?php } ?>

				<?php endwhile; ?>
			<?php }else{ ?>
				
			<article class="type-page box">
				<h1 class="title"><?php _e('Post not found', 'bluth'); ?></h1>
				<p class="lead"><?php _e('We could not find that post you were looking for.', 'bluth'); ?></p>
				<br>
				<h3><?php _e('Try searching', 'bluth') ?></h3>
				<?php echo get_search_form(); ?>
				<?php get_template_part( 'inc/recent-posts' ); ?>				
			</article>

			<?php } ?>

		</div><!-- #content .site-content -->

		<aside id="side-bar" class="span3">
				<?php get_sidebar(); ?>
		</aside>
	</div><!-- #primary .content-area -->

<?php get_footer(); ?>