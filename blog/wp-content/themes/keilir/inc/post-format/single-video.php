<?php
	$options = array(
		'disable_icons' => of_get_option('disable_icons', false),
		'disable_post_header' => of_get_option('disable_post_header', false),
		);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-video"><?php 

		$video_embed = get_post_meta($post->ID, '_format_video_embed', true);

		if (0 === strpos($video_embed, 'http')) {
			global $wp_embed;
			$content = $wp_embed->run_shortcode('[embed]' . $video_embed . '[/embed]' ); 
			$content = preg_replace('#\<iframe(.*?)\ssrc\=\"(.*?)\"(.*?)\>#i', '<iframe$1 src="$2?wmode=opaque"$3>', $content);
			$content = preg_replace('#\<iframe(.*?)\ssrc\=\"(.*?)\?(.*?)\?(.*?)\"(.*?)\>#i', '<iframe$1 src="$2?$3&$4"$5>', $content);			
		    echo $content;
		}else{
			echo get_post_meta($post->ID, '_format_video_embed', true);
		}

 		?>
	</div>

	<div class="entry-container">
		<?php if(!$options['disable_icons']){ ?>
			<?php get_template_part('inc/post-format/post-icons') ?>
		<?php } ?>
		<div class="entry-content">
			<h1 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
			<div class="post-meta">
				<?php get_template_part( 'inc/meta-top' ); ?>
			</div>			
			<?php the_content(); ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-links"><h4>' . __( 'Pages:', 'bluth' ).'</h4>', 'after' => '</div>', 'pagelink' => '<span>%</span>', )); ?>
		</div><!-- .entry-content -->

	</div><!-- .entry-container -->
	<?php if(!of_get_option('disable_footer_post')){ ?>
	<footer class="entry-meta clearfix">
		<?php get_template_part( 'inc/meta-bottom' ); ?>
	</footer><!-- .entry-meta -->
	<?php } ?>
</article><!-- #post-<?php the_ID(); ?> -->