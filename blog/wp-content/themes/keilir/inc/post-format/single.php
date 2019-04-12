<?php
	$options = array(
		'disable_icons' => of_get_option('disable_icons', false),
		'disable_post_header' => of_get_option('disable_post_header', false),
		'standard_icon' => of_get_option('standard_icon', false),
		);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( has_post_thumbnail() ) { ?>
	<div class="entry-image">
		<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'bluth'), the_title_attribute('echo=0') ); ?>" rel="bookmark">
			<?php 
				$image_version = (of_get_option('show_full_image') ? 'full' : 'gallery-large');
				the_post_thumbnail($image_version);
			?>
		</a>
	</div>
	<div class="entry-container">
		<?php if(!$options['disable_icons']){ ?>
			<?php get_template_part('inc/post-format/post-icons') ?>	
		<?php } ?>
	<?php }else{ ?>
		<?php if(!$options['disable_post_header']){ ?>
		<header class="entry-header">
			<?php if(!$options['disable_icons']){ ?>
				<?php get_template_part('inc/post-format/post-icons') ?>
			<?php } ?>
		</header><!-- .entry-header -->
		<?php } ?>
	<div class="entry-container">
	<?php } ?>
		<div class="entry-content">
			<h1 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
			<div class="post-meta">
				<?php get_template_part( 'inc/meta-top' ); ?>
			</div>
			<?php the_content(__('Continue reading...', 'bluth')); ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-links"><h4>' . __( 'Pages:', 'bluth' ).'</h4>', 'after' => '</div>', 'pagelink' => '<span>%</span>', )); ?>
		</div><!-- .entry-content -->

	</div><!-- .entry-container -->
	<?php if(!of_get_option('disable_footer_post')){ ?>
	<footer class="entry-meta clearfix">
		<?php get_template_part( 'inc/meta-bottom' ); ?>
	</footer><!-- .entry-meta -->
	<?php } ?>
</article><!-- #post-<?php the_ID(); ?> -->



