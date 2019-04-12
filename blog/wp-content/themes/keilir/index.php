<?php
/**
 * The template for displaying the home/index page.
 * This template will also be called in any case where the Wordpress engine 
 * doesn't know which template to use (e.g. 404 error)
 */

$layout = of_get_option('side_bar');
$layout = (empty($layout)) ? 'right_side' : $layout;
$ad_posts_mode = of_get_option('ad_posts_mode', 'none');
$ad_posts_frequency = of_get_option('ad_posts_frequency', 'none');
$ad_posts_box = of_get_option('ad_posts_box', true);

get_header(); ?>


<?php
	// Advert above content
	$ad_content_placement 	= of_get_option('ad_content_placement', array('home' => true,'pages' => true,'posts' => true));
	$ad_content_mode 		= of_get_option('ad_content_mode', 'none');
	$ad_content_box 		= of_get_option('ad_content_box', true);
	$ad_content_padding 	= of_get_option('ad_content_padding', true);
	$posts_layout 			= of_get_option('index_layout', 'default');
	$posts_layout 			= (is_archive()) ? of_get_option('archive_layout', 'default') : $posts_layout;

	if($ad_content_mode != 'none' and $ad_content_placement['home'] == true and is_home()){
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
		<div id="content" class="<?php echo ($layout == 'single') ? 'span10 offset1' : 'span9'; ?>" role="main">
		<?php		
			if(is_archive() and in_array($posts_layout, array('list','list-excerpt')) ){

				echo '<header class="entry-header"><h1>';
				if( is_day() ){
					printf( __( 'Daily Archives: %s', 'bluth' ), get_the_date() );
				}
				elseif( is_month() ){
					printf( __( 'Monthly Archives: %s', 'bluth' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'bluth' ) ) );
				}
				elseif( is_year() ){
					printf( __( 'Yearly Archives: %s', 'bluth' ), get_the_date( _x( 'Y', 'yearly archives date format', 'bluth' ) ) );
				}
				elseif(is_category()){ 
					printf( __( 'Category: %s', 'bluth' ), single_cat_title( '', false ) ); ?><?php
				}
				elseif(is_tag()){ 
					printf( __( 'Tag Archives: %s', 'bluth' ), single_tag_title( '', false ) ); ?><?php
				}						
				else{
					_e( 'Archives', 'bluth' );
				}
				echo '</h1>';
				if(is_category()){ ?>
					<p><?php echo strip_tags(category_description()); ?></p><?php
				}
				elseif(is_tag()){ ?>
					<p><?php echo strip_tags(tag_description()); ?></p><?php
				}
				echo '</header>';
			}

			if( have_posts() ){ 

				$i = 1;
				while ( have_posts() ){

					the_post(); 

					$selected_layout = $posts_layout;

					$sticky_layout = of_get_option('sticky_layout', 'default');

						if(is_sticky()){
							$selected_layout = $sticky_layout;
						}

					if(in_array($selected_layout, array('list','list-excerpt')) ){
						get_template_part( 'inc/post-format/content-list' );
					}else{
						get_template_part( 'inc/post-format/content', get_post_format() );
					}


					// advertising between posts
					if($ad_posts_mode != 'none'){

						// take into account ad frequency
						if (($i % $ad_posts_frequency) == 0){

							switch ($ad_posts_mode) {
								case 'image':
									echo '<div class="'.(($ad_posts_box) ? 'box' : '').' between_posts"><a target="_blank" href="'.of_get_option('ad_posts_image_link').'"><img src="'.of_get_option('ad_posts_image').'"></a></div>';
								break;
								case 'html':
									echo '<div class="'.(($ad_posts_box) ? 'box' : '').' between_posts">'.apply_filters('shortcode_filter',do_shortcode(of_get_option('ad_posts_code'))).'</div>';
								break;
							}
						}
					}
					$i++;
				}

			}else{ ?>
			<article class="type-page box">
				<h1 class="title"><?php _e('Post not found', 'bluth'); ?></h1>
				<p class="lead"><?php _e('We could not find that post you were looking for.', 'bluth'); ?></p>
				<br>
				<h3><?php _e('Try searching', 'bluth') ?></h3>
				<?php echo get_search_form(); ?>
				<?php get_template_part( 'inc/recent-posts' ); ?>				
			</article>
			<?php } 

			kriesi_pagination(); 

			?> 
		</div><!-- #content -->
		<?php if($layout == 'right_side' or $layout == 'left_side'){ ?>
		<aside id="side-bar" class="span3">
				<?php get_sidebar(); ?>
		</aside>
		<?php } ?>				
	</div><!-- #primary -->
<?php get_footer(); ?>