<?php


	#
	#	CREATE CUSTOM OPTION FRAMEWORK SETUP
	#
	function optionsframework_page() {
		settings_errors(); ?>

		<div id="optionsframework-wrap" class="wrap">
			<div id="optionsframework-header"><h2>Keilir<small> - Theme Options</small></h2></div>
		    <?php screen_icon( 'themes' ); ?>
		    <h2 class="nav-tab-wrapper">
		        <?php echo optionsframework_tabs(); ?>
		    </h2>

		    <div id="optionsframework-metabox" class="metabox-holder">
			    <div id="optionsframework" class="postbox">
					<form action="options.php" method="post">
					<?php settings_fields( 'optionsframework' ); ?>
					<?php optionsframework_fields(); /* Settings */ ?>
					<div id="optionsframework-submit">
						<input type="submit" class="button-primary" name="update" value="<?php esc_attr_e( 'Save Options', 'options_framework_theme' ); ?>" />
						<input type="submit" class="reset-button button-secondary" name="reset" value="<?php esc_attr_e( 'Restore Defaults', 'options_framework_theme' ); ?>" onclick="return confirm( '<?php print esc_js( __( 'Click OK to reset. Any theme settings will be lost!', 'options_framework_theme' ) ); ?>' );" />
						<div class="clear"></div>
					</div>
					</form>
				</div> <!-- / #container -->
			</div>
			<?php do_action( 'optionsframework_after' ); ?>
		</div> <!-- / .wrap -->
		
	<?php
	}
	



	/*  Loads the Options Panel  */
	if ( !function_exists( 'optionsframework_init' ) ) {

		define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/inc/theme-options/' );
		require_once dirname( __FILE__ ) . '/theme-options/options-framework.php';

		/*  Add custom script to theme options  */
		add_action('optionsframework_custom_scripts', 'optionsframework_custom_scripts');
	}
		
	function optionsframework_custom_scripts(){ 
		echo '<script type="text/javascript" src="'.OPTIONS_FRAMEWORK_DIRECTORY.'js/theme-options.js"></script>';
		echo '<link rel="stylesheet" type="text/css" href="'.OPTIONS_FRAMEWORK_DIRECTORY.'css/theme-options.css">';
		echo '<link rel="stylesheet" type="text/css" href="'.get_template_directory_uri().'/assets/css/fontello.css">';
	}
	/* 
	 * This is an example of how to override a default filter
	 * for 'textarea' sanitization and $allowedposttags + embed and script.
	 */
	add_action('admin_init','optionscheck_change_santiziation', 100);
	 
	function optionscheck_change_santiziation() {
	    remove_filter( 'of_sanitize_textarea', 'of_sanitize_textarea' );
	    add_filter( 'of_sanitize_textarea', create_function('$input', 'return $input;') );
	}
