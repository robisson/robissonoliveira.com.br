<?php
	$options = array(
		'disable_icons' => of_get_option('disable_icons', false),
		'disable_post_header' => of_get_option('disable_post_header', false),
		);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( $images = get_children(array('post_parent' => get_the_ID(), 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC'))){ ?>
	<div class="entry-image entry-slider">
		<div class="entry-slides<?php echo ((count($images) > 1) ? ' nivo-slider' : '') ?>">
			<?php 

			$image_version = (of_get_option('show_full_image') ? 'full' : 'gallery-large');
			$i = 1;
			foreach( $images as $image ){ ?>
				<a href="<?php the_permalink(); ?>"<?php echo ($i !== 1) ? ' style="display:none"' : '' ?> title="<?php printf( esc_attr__('Permalink to %s', 'bluth'), the_title_attribute('echo=0') ); ?>" rel="bookmark">
					<?php echo wp_get_attachment_image($image->ID, $image_version) ?>
				</a>
			<?php $i++; } ?>
		</div>
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
			<?php the_content('Continue reading...'); ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-links"><h4>' . __( 'Pages:', 'bluth' ).'</h4>', 'after' => '</div>', 'pagelink' => '<span>%</span>', )); ?>
		</div><!-- .entry-content -->
	</div><!-- .entry-container -->
	<?php if(!of_get_option('disable_footer_post')){ ?>
	<footer class="entry-meta clearfix">
		<?php get_template_part( 'inc/meta-bottom' ); ?>
	</footer><!-- .entry-meta -->
	<?php } ?>

</article><!-- #post-<?php the_ID(); ?> -->