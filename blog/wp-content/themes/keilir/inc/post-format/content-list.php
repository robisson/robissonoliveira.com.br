<?php
$layout = is_archive() ? of_get_option('archive_layout', 'list') : of_get_option('index_layout', 'list');

	if(is_sticky()){
		$layout = of_get_option('sticky_layout', 'list');
	}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('frontpage-list'); ?>>

	<div class="entry-container clearfix">
		<?php if ( has_post_thumbnail() ) { ?>
		<div class="entry-image">
			<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'bluth'), the_title_attribute('echo=0') ); ?>" rel="bookmark">
				<?php echo bl_post_thumbnail(get_post_thumbnail_id(), of_get_option('lazy_load', false), 'small'); ?>
			</a>
		</div>
		<?php } ?>		
		<div class="entry-content">
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<div class="post-meta">
				<?php get_template_part('inc/meta-top'); ?>
			</div><?php
				if($layout == 'list-excerpt'){
					the_excerpt();
				}
			?>
		</div><!-- .entry-content -->
	</div><!-- .entry-container -->

</article><!-- #post-<?php the_ID(); ?> -->