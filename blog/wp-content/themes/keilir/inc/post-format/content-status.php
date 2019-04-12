<?php
	$options = array(
		'disable_icons' => of_get_option('disable_icons', false),
		'disable_post_header' => of_get_option('disable_post_header', false),
		);

	$facebook = get_post_meta( $post->ID, 'bluth_facebook_status', true );
	$twitter = get_post_meta( $post->ID, 'bluth_twitter_status', true );

	$status = '';
	if($facebook){
		$status = str_replace( '&#039;', '\'', html_entity_decode($facebook));
		$status_class = 'format-facebook';
	}elseif($twitter){
		$status = str_replace( '&#039;', '\'', html_entity_decode($twitter));
		$status_class = 'format-twitter';
	}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class($status_class); ?>>

	<?php if ( has_post_thumbnail() ) { ?>
	<div class="entry-image">
		<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'bluth'), the_title_attribute('echo=0') ); ?>" rel="bookmark">
			<?php echo bl_post_thumbnail(get_post_thumbnail_id(), of_get_option('lazy_load', false)); ?>
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
			<div class="status-content">
			<?php echo $status; ?>			
			</div>
		</div><!-- .entry-content -->
	</div><!-- .entry-container -->
</article><!-- #post-<?php the_ID(); ?> -->