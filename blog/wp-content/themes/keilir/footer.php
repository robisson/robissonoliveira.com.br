<?php
	//get theme options
	$footer_text = of_get_option('footer_text', '' );
	$footer_color = of_get_option('footer_color', '' );
	$footer_class = '';

	// Footer Color
	if(!empty($footer_color)){

		// determin if dark or light
		$hex = str_replace('#', '', $footer_color);
		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));

		if($r + $g + $b > 255){ //bright background, use dark font
		    $footer_class = ' light_theme';
		}
	}
?>
	</div><!-- #main .site-main -->
	<footer id="colophon" class="site-footer<?php echo $footer_class; ?>" role="contentinfo">
		<?php 
		$remove_color_stripes = of_get_option('remove_color_stripes', array('footer_bars' => 0));
		if($remove_color_stripes['footer_bars'] != '1'){ ?>
		<div class="top-color clearfix">
			<div class="first_col"></div>
			<div class="second_col"></div>
			<div class="third_col"></div>
			<div class="fourth_col"></div>
		</div>
		<?php } ?>
		<div class="container">
			<?php if(of_get_option('footer_fluid')){ ?>
			<ul id="footer-body">
				<?php dynamic_sidebar('footer-widgets'); ?>
			</ul>	
			<?php }else{ ?>
			<div class="row-fluid" id="footer-body">
				<?php dynamic_sidebar('footer-widgets'); ?>
			</div>
			<?php } ?>
		</div><!-- .site-info -->
		<?php if(!empty($footer_text)){?>
		<div class="row-fluid" id="footer-bottom">
			<?php echo str_replace("{year}", date('Y'), $footer_text); ?>
		</div>	
		<?php } ?>
	</footer><!-- #colophon .site-footer -->
</div><!-- #page -->
<?php
	// background image or pattern
	switch (of_get_option('background_mode')) {
		case 'image':
			if(of_get_option('background_image')){

				echo '<div class="bl_background">'.(of_get_option('show_stripe') ? '<div id="stripe"></div>' : ''). '<img src="'.of_get_option('background_image').'"></div>';
			}
		break;
		case 'pattern':
			if( of_get_option('background_pattern_custom') ){

				echo '<div style="background-image: url(\''.of_get_option('background_pattern_custom').'\')" id="background_pattern"></div>';
			
			}elseif (of_get_option('background_pattern')) {

				echo '<div style="background-image: url(\''.get_template_directory_uri() . '/assets/img/pattern/large/'.of_get_option('background_pattern').'\')" id="background_pattern"></div>';
			}
		break;
	}

?>
<?php wp_footer(); ?>
<?php if(!of_get_option('disable_fixed_header')){ ?>
	<script type="text/javascript">

		jQuery(function(){
			if(jQuery('.above_header').length > 0){

				var height = jQuery('.above_header').height();
			}else{
				var height = 0;
			}
			jQuery('#masthead').affix({offset: height});
		});

	</script>
<?php } ?>
</body>
</html>