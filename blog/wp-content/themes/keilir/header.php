<!DOCTYPE html>
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<!-- 	This file is part of a WordPress theme for sale at ThemeForest.net.
		See: http://themeforest.net/item/keilir-responsive-wordpress-blog-theme/4893662
		Copyright 2013 Bluthemes 	-->
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<?php
	// check if Yoast SEO plugin is active
	if ( of_get_option('seo_plugin') ){
		echo '<title>';
		wp_title('');
		echo '</title>';
	}else{

		global $page, $paged;

		// add title tag
		echo '<title>';
		bloginfo('name');
		wp_title(' - ', true, 'left');
		echo '</title>';

		if (is_single() || is_page() ){ 
			if ( have_posts() ) {
				while ( have_posts() ){ 
					the_post();
					echo '<meta name="description" content="';
					echo str_replace('[&hellip;] Continue reading...', '', strip_tags( get_the_excerpt() ));
					echo '" />';
				} 
			}
		}elseif(is_home()){ 
				echo '<meta name="description" content="';
				bloginfo('description');
				echo '" />';
		}
	}
?>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/html5.js" type="text/javascript"></script>
<![endif]-->

<?php

//render google tracking code if present
$google_analytics = of_get_option('google_analytics', false);
if($google_analytics){
	echo (strpos($google_analytics, '<script') === false) ? '<script>'.of_get_option('google_analytics').'</script>' : of_get_option('google_analytics');
}

wp_head(); 

?>
</head>
<body <?php body_class(); ?>>
<?php 

	// Facebook Javascript SDK
	if(of_get_option('facebook_app_id')){ ?>
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=<?php echo of_get_option('facebook_app_id'); ?>";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	<?php }	

	// Advert above header
	$ad_header_mode = of_get_option('ad_header_mode', 'none');

	if($ad_header_mode != 'none'){
		echo '<div class="above_header">';
			if($ad_header_mode == 'image'){
				echo '<a href="'.of_get_option('ad_header_image_link').'" target="_blank"><img src="'.of_get_option('ad_header_image').'"></a>';
			}elseif($ad_header_mode == 'html'){
				echo apply_filters('shortcode_filter',do_shortcode(of_get_option('ad_header_code')));
			}
		echo '</div>';
	}
?>
<div id="page" class="site">
	<?php do_action( 'before' ); ?>
	<header id="masthead" role="banner">
		<?php 
		$remove_color_stripes = of_get_option('remove_color_stripes', array('header_bars' => 0));
		if($remove_color_stripes['header_bars'] != '1'){ ?>
		<div class="top-color clearfix">
			<div class="first_col"></div>
			<div class="second_col"></div>
			<div class="third_col"></div>
			<div class="fourth_col"></div>
		</div>
		<?php } ?>
		<div class="container">

			<div class="navbar navbar-inverse">
			  <div class="navbar-inner">
			    <!-- Responsive Navbar Part 1: Button for triggering responsive navbar (not covered in tutorial). Include responsive CSS to utilize. -->
			    <button data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar" type="button"><i class="icon-menu-1"></i></button>
				<?php 
				$logo = of_get_option('logo', '' );
				if (!empty($logo)) 
				{ 
					?><a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><img src="<?php echo $logo; ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"></a><?php 
				}else
				{ 
					if(of_get_option('show_tagline')){ 
						?><a class="brand brand-text brand-tagline" href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><span><?php bloginfo( 'name' ); ?></span><p><?php bloginfo( 'description' ); ?></p></a><?php
					}else
					{ 
						?><a class="brand brand-text" href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><span><?php bloginfo( 'name' ); ?></span></a><?php 
					} 
				} 

				if ( has_nav_menu( 'primary' ) ) {
					wp_nav_menu( array( 
						'container' => 'div',
						'container_class' => 'nav-collapse collapse',
						'theme_location' => 'primary',
						'menu_class' => 'nav',
						'walker' => new Bootstrap_Walker(),									
						) );
				}



				if(of_get_option('show_search_header')){ ?>
					<div class="bl_search nav-collapse collapse">
						<?php echo get_search_form(); ?>
					</div><?php
				}

				// custom menu content
				$custom_menu_content = of_get_option('custom_menu_content', false);

				if($custom_menu_content){
					echo '<div class="custom_menu_content">';
					echo apply_filters('shortcode_filter', do_shortcode($custom_menu_content));
					echo '</div>';
				} ?>
			  </div><!-- /.navbar-inner -->
			</div>

		</div>
	</header><!-- #masthead .site-header -->
	<div id="main" class="container">