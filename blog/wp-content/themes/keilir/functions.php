<?php

	// load language
	load_theme_textdomain( 'bluth', get_template_directory() . '/inc/lang' );

	// Define the version so we can easily replace it throughout the theme
	define( 'KEILIR_VERSION', 2.41 );


	/*  Set the content width based on the theme's design and stylesheet  */
	if ( ! isset( $content_width ) ){
		$content_width = 740; /* pixels */
	}

	/*  Add Rss feed support to Head section  */
	add_theme_support( 'automatic-feed-links' );

	/*  Register main menu for Wordpress use  */
	if(!function_exists('bluth_register_nav_menu')){
		function bluth_register_nav_menu() {
			register_nav_menus( 
				array(
					'primary'	=>	'Primary Menu', // Register the Primary menu
					// Copy and paste the line above right here if you want to make another menu, 
					// just change the 'primary' to another name
				)
			);
		}
	}
	add_action( 'after_setup_theme', 'bluth_register_nav_menu' );

	/*  Add support for the multiple Post Formats  */
	add_theme_support( 'post-formats', array('gallery', 'image', 'link', 'quote', 'audio', 'video', 'status')); 


	include_once('inc/theme-options.php'); // Load Theme Options Panel
	/*  Widgets  */
	include_once('inc/widgets/widgets.php');   // Register widget
	include_once "inc/widgets/bl_tabs.php"; // Tabs: (Recent posts, Recent comments, Tags)
	include_once "inc/widgets/bl_socialbox.php"; // Social network links
	include_once "inc/widgets/bl_tweets.php"; // Display recent tweets
	include_once "inc/widgets/bl_instagram.php"; // Display recent instagram images
	include_once "inc/widgets/bl_newsletter.php"; // Display recent instagram images
	include_once "inc/widgets/bl_likebox.php"; // Display a facebook likebox
	include_once "inc/widgets/bl_flickr.php"; // Display a recent flickr images
	include_once "inc/widgets/bl_html.php"; // Display HTML
	include_once "inc/widgets/bl_author.php"; // Display Author Badge
	include_once "inc/widgets/bl_google_ads.php";
	include_once "inc/widgets/blu_author.php";

	include_once('inc/shortcodes.php'); // Load Shortcodes
	include_once('inc/custom-css.php'); // Load Theme Options Panel
	include_once('inc/meta-box.php'); // Load Meta Boxes
	
	/* Bootstrap type menu  */
	include_once('inc/bootstrap-walker.php');



	/* Enqueue Styles and Scripts  */
	if(!function_exists('keilir_assets')){
		function keilir_assets()  { 

			$protocol 			= is_ssl() ? 'https' : 'http';
			$disable_responsive = of_get_option('disable_responsive', false);

			$enable_rtl 			= of_get_option('enable_rtl', false);
			$lazyload 			= of_get_option('lazy_load', false);

			// add theme css
			wp_enqueue_style( 'keilir-bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', array(), KEILIR_VERSION, 'all' );
			wp_enqueue_style( 'keilir-style', get_stylesheet_uri(), array(), KEILIR_VERSION, 'all' );
			// if disable responsive
			if(!$disable_responsive){
				wp_enqueue_style( 'keilir-responsive', get_template_directory_uri() . '/assets/css/style-responsive.css', array(), KEILIR_VERSION, 'all' );
			}
			// if RTL enabled
			if($enable_rtl){
				wp_enqueue_style( 'keilir-rtl', get_template_directory_uri() . '/assets/css/style-rtl.css', array(), KEILIR_VERSION, 'all' );
			}
			wp_enqueue_style( 'keilir-fontello', get_template_directory_uri() . '/assets/css/fontello.css', array(), KEILIR_VERSION, 'all' );
			wp_enqueue_style( 'keilir-nivo', get_template_directory_uri() . '/assets/css/nivo-slider.css', array(), KEILIR_VERSION, 'all' );
			wp_enqueue_style( 'keilir-magnific', get_template_directory_uri() . '/assets/css/magnific-popup.css', array(), KEILIR_VERSION, 'all' );
			wp_enqueue_style( 'keilir-snippet', get_template_directory_uri() . '/assets/css/jquery.snippet.min.css', array(), KEILIR_VERSION, 'all' );

				
			// add theme scripts
			wp_enqueue_script( 'keilir-jquery-ui', $protocol.'://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js', array('jquery'), KEILIR_VERSION, true );
			wp_enqueue_script( 'keilir-snippet', get_template_directory_uri() . '/assets/js/jquery.snippet.min.js', array('jquery'), KEILIR_VERSION, true );
			wp_enqueue_script( 'keilir-nivo', get_template_directory_uri() . '/assets/js/jquery.nivo.slider.pack.js', array(), KEILIR_VERSION, true );
			wp_enqueue_script( 'keilir-timeago', get_template_directory_uri() . '/assets/js/jquery.timeago.js', array('jquery'), KEILIR_VERSION, true );
			wp_enqueue_script( 'keilir-bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array('jquery'), KEILIR_VERSION, true );
			wp_enqueue_script( 'keilir-magnific', get_template_directory_uri() . '/assets/js/jquery.magnific-popup.js', array('jquery'), KEILIR_VERSION, true );
			
			if($lazyload){
				wp_enqueue_script( 'keilir-lazyload', get_template_directory_uri() . '/assets/js/jquery.lazyload.min.js', array('jquery'), KEILIR_VERSION, true );
			}
			wp_enqueue_script( 'keilir-theme', get_template_directory_uri() . '/assets/js/theme.min.js', array('jquery'), KEILIR_VERSION, true );


			// Localize Script
		    wp_localize_script( 'keilir-theme', 'blu', array( 

		    	// Variable
		    	'site_url' => get_site_url(),
		    	'ajaxurl' => admin_url( 'admin-ajax.php' ),
		    	'fb_comments' => (of_get_option('facebook_comments') ? 'true' : 'false'),
		    	'lazy_load' => (of_get_option('lazy_load') ? 'true' : 'false'),

		    	// Locale
		    	'locale' => array(
			    	'email_input' => __( 'Search...', 'bluth' ),
			    	'comment' => __( 'Comment', 'bluth' ),
			    	'comments' => __( 'Comments', 'bluth' ),
			    	'no_comments' => __( 'No Comments', 'bluth' ),
			    	'no_email_provided' => __( 'No email provided!', 'bluth' ),
			    	'thank_you_for_subscribing' => __( 'Thank you for subscribing!', 'bluth' ),
		    	)
		    ));


			$fonts = array();
			$fonts['heading_font'] 	= of_get_option('heading_font', false);
			$fonts['text_font'] 	= of_get_option('text_font', false);
			$fonts['menu_font'] 	= of_get_option('menu_font', false);
			$fonts['brand_font'] 	= of_get_option('brand_font', false);

			// defaults
			$heading_font 	= 'Crete+Round:400,400italic';
			$text_font 		= 'Lato:400,700,400italic';

			// empty font array
			$font_names 	= array();
			$font_subset 	= array();
			$subset_array 	= array();

			foreach ($fonts as $key => $value) {
				if($value){
					$selected_font = explode('&subset=', $value);
					$font_names[] = str_replace(' ', '+', $selected_font[0]);
					if(count($selected_font) > 1){
						$font_subset = explode(',', $selected_font[1]);
						array_fill_keys($font_subset, $font_subset);
						$subset_array = array_merge($subset_array, $font_subset);
					}
				}
			}
			$subset_array = array_unique($subset_array);

			wp_enqueue_style( 'keilir-googlefonts', $protocol.'://fonts.googleapis.com/css?family='.(!empty($font_names) ? implode('|', $font_names) : $text_font.'|'.$heading_font) . (!empty($subset_array) ? '&subset='.implode(',', $subset_array) : '')  );	

		    if ( is_singular() && get_option( 'thread_comments' ) )
		        wp_enqueue_script( 'comment-reply' );			
		}
	}
	add_action( 'wp_enqueue_scripts', 'keilir_assets' ); // Register this fxn and allow Wordpress to call it automatcally in the header


	/* 
	 * Outputs the selected option panel styles inline into the <head>
	 */
	if(!function_exists('options_typography_styles')){ 
		function options_typography_styles() {

			$output = '';
			$heading_font 		= of_get_option('heading_font', false);
			$text_font 			= of_get_option('text_font', false);
			$menu_font 			= of_get_option('menu_font', false);
			$brand_font 		= of_get_option('brand_font', false);

			if($heading_font){
				$selected_font = explode(':', $heading_font);
				$output .= 'h1,h2,h3,h4,h5{font-family: "'.str_replace('+', ' ', $selected_font[0]).'",serif;} .widget_calendar table > caption{font-family: "'.str_replace('+', ' ', $selected_font[0]).'",serif;} ';
			}

			if($text_font){
				$selected_font = explode(':', $text_font);
				$output .= 'body{font-family: "'.str_replace('+', ' ', $selected_font[0]).'",Helvetica,sans-serif;} ';
			}

			if($menu_font){
				$selected_font = explode(':', $menu_font);
				$output .= '.navbar .nav > li > a, .navbar .brand p{font-family: "'.str_replace('+', ' ', $selected_font[0]).'",Helvetica,sans-serif;} ';
			}

			if($brand_font){
				$selected_font = explode(':', $brand_font);
				$output .= '.navbar .brand-text{font-family: "'.str_replace('+', ' ', $selected_font[0]).'",Helvetica,sans-serif;} ';
			}

		     if ( $output != '' ) {
				$output = "\n<style>\n" . $output . "</style>\n";
				echo $output;
		     }
		}
	}
	add_action('wp_head', 'options_typography_styles');




	/*  Attach a class to linked images' parent anchors  */
	if(!function_exists('give_linked_images_class')){
		function give_linked_images_class($html, $id, $caption, $title, $align, $url, $size, $alt = '' ){
		  $classes = 'lightbox'; // separated by spaces, e.g. 'img image-link'

		  // check if there are already classes assigned to the anchor
		  if ( preg_match('/<a.*? class=".*?">/', $html) ) {
		    $html = preg_replace('/(<a.*? class=".*?)(".*?>)/', '$1 ' . $classes . '$2', $html);
		  } else {
		    $html = preg_replace('/(<a.*?)>/', '$1 class="' . $classes . '" >', $html);
		  }
		  return $html;
		}
	}
	add_filter('image_send_to_editor','give_linked_images_class',10,8);


	/*  Custom Pagination ( thanks to kriesi.at )  */
	if(!function_exists('kriesi_pagination')){ 
		function kriesi_pagination($pages = '', $range = 2){  
		     $showitems = ($range * 2)+1;  

		     global $paged;
		     if(empty($paged)) $paged = 1;

		     if($pages == '')
		     {
		         global $wp_query;
		         $pages = $wp_query->max_num_pages;
		         if(!$pages)
		         {
		             $pages = 1;
		         }
		     }   

		     if(1 != $pages)
		     {
		         echo "<div class='pagination'>";
				echo get_previous_posts_link( '<i class="icon-left-open"></i> '.__('Previous Page', 'bluth') );
		         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a class='box' href='".get_pagenum_link(1)."'>&laquo;</a>";
		         if($paged > 1 && $showitems < $pages) echo "<a class='box' href='".get_pagenum_link($paged - 1)."'>&lsaquo;</a>";

		         for ($i=1; $i <= $pages; $i++)
		         {
		             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
		             {
		                 echo ($paged == $i)? "<span class='current box'>".$i."</span>":"<a href='".get_pagenum_link($i)."' class='inactive box' >".$i."</a>";
		             }
		         }

		         if ($paged < $pages && $showitems < $pages) echo "<a class='box' href='".get_pagenum_link($paged + 1)."'>&rsaquo;</a>";  
		         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a class='box' href='".get_pagenum_link($pages)."'>&raquo;</a>";
				echo get_next_posts_link( __('Next Page', 'bluth').' <i class="icon-right-open"></i>' );
		        echo "</div>\n";
		     }
		}
	}

	/*  Add open graph meta tags  */
	if(!function_exists('add_open_graph_tags')){ 
		function add_open_graph_tags() {

			global $post; 

			if(isset($post->ID)){
				
				$images = get_children(array('post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image' ));
				if(!empty($images)){
					$image = current($images);
					$image = wp_get_attachment_image_src($image->ID, 'gallery-large');
					echo '<meta property="og:image" content="'.(is_array($image) ? $image[0] : $image).'" />';
				}
			}
			?>	
			<meta property="og:type" content="article" />
			<meta property="og:url" content="<?php the_permalink(); ?>" />
			<?php
			if (is_single() || is_page() ){ 
				if ( have_posts() ) {
					while ( have_posts() ){ 
						the_post();
						echo '<meta property="og:description" content="'.mb_substr(strip_tags(get_the_excerpt()), 0, 155).'" />';
						echo '<meta property="og:title" content="';
						bloginfo('name');
						echo ' - ';
						the_title();
						echo '" />';
					} 
				}
			}elseif(is_home()){ 
				echo '<meta property="og:title" content="';
				bloginfo('name');
				wp_title(' - ', true, 'left');
				echo '" />';
				echo '<meta property="og:description" content="'.get_bloginfo('description').'" />';
			}		
			?>	
			<meta property="og:site_name" content="<?php echo get_bloginfo('name'); ?>" />
			<?php 
		}
	}
	// only run this function if the user is not using an seo plugin
	if ( !of_get_option('seo_plugin') ){
		add_action('wp_head', 'add_open_graph_tags',99); 
	}


	/*  Post Thumbnails  */
	if ( function_exists( 'add_image_size' ) ) {

		add_theme_support( 'post-thumbnails' );

		add_image_size( 'gallery-large', 870, 400, true );		// Large Blog Image
		add_image_size( 'standard', 700, 300, true );			// Standard Blog Image
		add_image_size( 'small', 194, 150, true ); 				// Related posts image
		add_image_size( 'mini', 60, 60, true ); 				// sidebar widget image
	}


	/**
	 * Template for comments and pingbacks.
	 * Used as a callback by wp_list_comments() for displaying the comments.
	 */
	if(!function_exists('keilir_comment')){
		function keilir_comment( $comment, $args, $depth ) {
		    $GLOBALS['comment'] = $comment;
		    switch ( $comment->comment_type ) :
		        case 'pingback' :
		        case 'trackback' :
		    ?>
		    <li class="post pingback">
		        <p><?php _e( 'Pingback:', 'bluth' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'bluth' ), ' ' ); ?></p>
		    <?php
		            break;
		        default :
		    ?>
		    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		        <article id="comment-<?php comment_ID(); ?>" class="comment">
		            <div>
		                <div class="comment-author">
		                    <?php echo get_avatar( $comment, 45 ); ?>
		                    <?php printf( __( '%s', 'bluth' ), sprintf( '<cite class="commenter">%s</cite>', get_comment_author_link() ) ); ?>
		                	<small class="muted">&nbsp;&nbsp;•&nbsp;&nbsp;<?php

		                		$disable_timeago = of_get_option('disable_timeago', false);

		                		if($disable_timeago){
		                			echo '<time datetime="'.get_comment_time('c').'">'.get_comment_time(get_option('date_format')).'</time></small>';
		                		}else{
		                			echo '<time class="timeago" datetime="'.get_comment_time('c').'">'.get_comment_time('c').'</time></small>';
		                		}
		                	
		                	if ($comment->user_id == get_queried_object()->post_author){ ?>
		                	&nbsp;&nbsp;<span class="label label-success"><?php _e('Author', 'bluth'); ?></span>
		                	<?php } ?>
		                </div><!-- .comment-author .vcard -->
		                <?php if ( $comment->comment_approved == '0' ) : ?>
		                    <em><?php _e( 'Your comment is awaiting moderation.', 'bluth' ); ?></em>
		                    <br />
		                <?php endif; ?>
		            </div>
		 
		            <div class="comment-content"><?php comment_text(); ?></div>
		 
		            <div class="reply">
		                <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		                <?php edit_comment_link( __( '(Edit)', 'bluth' ), '&nbsp;&nbsp;' ); ?>
		            </div><!-- .reply -->
		        </article><!-- #comment-## -->
		 
		    <?php
		            break;
		    endswitch;
		}
	}

	// add span tag around categories post count
	if(!function_exists('cat_count_span')){ 
		function cat_count_span($links) {
			return str_replace(array('</a> (',')'), array('</a> <span class="badge">','</span>'), $links);
		}
	}
	add_filter('wp_list_categories', 'cat_count_span');

	// add span tag around archives post count
	if(!function_exists('archive_count_no_brackets')){ 
		function archive_count_no_brackets($links) {
		  	return str_replace(array('</a>&nbsp;(',')'), array('</a> <span class="badge">','</span>'), $links);
		}
	}
	add_filter('get_archives_link', 'archive_count_no_brackets');

	// Excerpt read more link
	if(!function_exists('excerpt_read_more_link')){ 
		function excerpt_read_more_link($output) {

		if(of_get_option('show_continue_reading')){
			global $post;
			$class = of_get_option('continue_reading_button') ? ' btn btn-primary' : '';
			$more_link = '<p><a class="more-link'.$class.'" href="'. get_permalink($post->ID) . '"> '.__('Continue reading...', 'bluth').'</a></p>';
		}else{
			$more_link = '';
		}	
		 return $output . $more_link;
		}
	}
	add_filter('excerpt_more', 'excerpt_read_more_link');

	function my_more_link($more_link, $more_link_text) {

		if(of_get_option('show_continue_reading')){
			$class = of_get_option('continue_reading_button') ? 'more-link btn btn-primary' : 'more-link';
			return str_replace('more-link', $class, $more_link);
		}else{
			return '';
		}

	}
	add_filter('the_content_more_link', 'my_more_link', 10, 2);	

	// Excerpt length
	if(!function_exists('new_excerpt_length')){ 
		function new_excerpt_length($length) {
			return of_get_option('excerpt_length', 55);
		}
	}
	add_filter('excerpt_length', 'new_excerpt_length');

	function bluth_favicon() {
		if(of_get_option('favicon')){
			echo '<link rel="Shortcut Icon" type="image/x-icon" href="'.of_get_option('favicon').'" />';
		}
	}
	add_action('wp_head', 'bluth_favicon');

		// MailChimp Widget
	if(!function_exists('blu_ajax_mailchimp')){
		function blu_ajax_mailchimp(){  
			$options = get_option('widget_bl_newsletter');
			foreach($options as $option){
				if(is_array($option) and in_array($_POST['list'], $option)){
					$options = $option;
				}
			}

			if(!isset($options['list_id'])){
				echo json_encode(array("error" => "No mailing list selected"));
				 die();
			}
			if(!isset($options['api_key'])){
				echo json_encode(array("error" => "API key not defined")); 
				die();
			}

			if(!isset($_POST['email'])){ 
				echo json_encode(array("error" => __('No email address provided','bluth'))); 
				die();
			} 

			if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/i", $_POST['email'])) {
				echo json_encode(array("error" => __('Email address is invalid','bluth'))); 
			}

			require_once(get_template_directory().'/inc/mailchimp/MCAPI.class.php');

			$api = new MCAPI($options['api_key']);

			$list_id = $options['list_id'];

			if($api->listSubscribe($list_id, $_POST['email'], '') === true) {
				echo json_encode(array("status" => 'ok'));
			}else{
				echo json_encode(array("error" => 'Error: ' . $api->errorMessage));
			}
		    die();
		} 
	} 
	add_action( 'wp_ajax_blu_ajax_mailchimp', 'blu_ajax_mailchimp' );
	add_action( 'wp_ajax_nopriv_blu_ajax_mailchimp', 'blu_ajax_mailchimp' ); 	

	/* V. 1.9 */


	#
	# 	SOCIAL COUNTER WIDGET
	#
	if(!function_exists('blu_get_url')){
		function blu_get_url($url){
	        $bl_results = @wp_remote_retrieve_body(@wp_remote_get($url, array('timeout' => 18 , 'sslverify' => false)));
	        return $bl_results;
    	}
    }
	if(!function_exists('blu_get_json')){
		function blu_get_json($url){
	        $bl_json = @json_decode(blu_get_url($url), true);
	        return $bl_json;
    	}
	}    
	if(!function_exists('blu_get_social_counter')){
		function blu_get_social_counter($social_service) {

			$return_array = array();

			// Cache the results if they haven't been cached
			if(($cache = get_transient('bl_social_counter')) === false){

				foreach ($social_service as $site => $user_id) {
		
					$bl_counter = 0;

					switch($site){
						case 'facebook':
							$bl_data = blu_get_json("http://graph.facebook.com/$user_id");
		                    if ( is_array($bl_data) and !empty($bl_data['likes']) ) {
		                        $return_array['facebook'] = $bl_data['likes'];
		                    }
						break;
						case 'twitter':
							if(!class_exists('TwitterApiClient')){
								require_once('inc/twitter-api.php');
								$Client = new TwitterApiClient;
								$Client->set_oauth (YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, SOME_ACCESS_KEY, SOME_ACCESS_SECRET);
								try {
			                        $path = 'users/show';
			                        $args = array ('screen_name' => $user_id);
			                        $bl_data = @$Client->call( $path, $args, 'GET' );
			                        if (!empty($bl_data['followers_count'])) {
			                            $return_array['twitter'] = $bl_data['followers_count'];
			                        }
			                    }
			                    catch( TwitterApiException $Ex ){ return $Ex; }
							}
						break;
		                case 'google-plus':
		                    $bl_data = blu_get_json("https://www.googleapis.com/plus/v1/people/$user_id?key=AIzaSyCfgXt3OctK-8566-5oinbCO1vRMGzsTy8");

		                    if (!empty($bl_data['plusOneCount'])) {
		                        $return_array['google-plus'] = $bl_data['plusOneCount'];
		                    }
		                    else if (!empty($bl_data['circledByCount'])) {
		                        $return_array['google-plus'] = $bl_data['circledByCount'];
		                    }

		                break;
		                case 'instagram':
		                    $bl_data = blu_get_url("http://instagram.com/$user_id#");
		                    $pattern = "/followed_by\":(.*?),\"follows\":/";
		                    preg_match($pattern, $bl_data, $matches);
		                    if (!empty($matches[1])) {
		                        $return_array['instagram'] = $matches[1];
		                    }
		                break;
					}
				}
		        set_transient( 'bl_social_counter', $return_array, 60 * 60 * 3);
			}else{
				$return_array = $cache;
			}
			return $return_array;
		}
	}

	/* V. 2.2 */

	# 
	# Get post thumbnail
	# 
	if(!function_exists('bl_post_thumbnail')){
		function bl_post_thumbnail($thumb_id, $lazy_load = false, $size = false){

			if(!$size){
				$size = (of_get_option('show_full_image') ? 'full' : 'gallery-large');
			}

			if($lazy_load){
				$large_image_url = wp_get_attachment_image_src( $thumb_id, $size);
				return wp_get_attachment_image($thumb_id, $size, false, array('class' => 'bl_lazyload', 'src' => 'data:image/png;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7', 'data-original'	=> $large_image_url[0]));
			}else{
				return wp_get_attachment_image($thumb_id, $size, false);
			}
    	}
	}

	#
	# Move scripts to footer if the user requests it
	#
	if(!function_exists('bl_scripts_to_footer')){
		function bl_scripts_to_footer() { 
		   remove_action('wp_head', 'wp_print_scripts'); 
		   remove_action('wp_head', 'wp_print_head_scripts', 9); 
		   remove_action('wp_head', 'wp_enqueue_scripts', 1);
		} 

		if(of_get_option('scripts_to_footer')){
			add_action( 'wp_enqueue_scripts', 'bl_scripts_to_footer' );	
		}
	} 

	/* V. 2.3 */


	#
	# Check if CF-post formats plugin that was used in previous versions is installed and notify the user that he needs to uninstall it
	#
	function bl_show_remove_cf_notice(){
	    echo '<div id="bl_message" class="error">';
	    echo '<p><strong>Important:</strong><br><p>You need to uninstall the plugin CF Post Formats which is no longer needed with this version of Keilir. <a href="' . admin_url( 'plugins.php' ) . '">Go to plugins page</a></p></div>';
	}
	if(defined('CFPF_VERSION')){
		add_action('admin_notices', 'bl_show_remove_cf_notice');
	}


	#
	# If Cf post formats is gone, include our own modified CFPF solution
	#
	if(!defined('CFPF_VERSION')){
		include_once('inc/cf-post-formats/cf-post-formats.php');
	}



	/* V. 2.4 */
	function litemag_admin_assets(){
		wp_enqueue_style( 'cdlayout-style', get_template_directory_uri() . '/assets/css/style-admin.css', array(), NULL, 'all' );   
	}
	add_action( 'admin_enqueue_scripts', 'litemag_admin_assets' );	

	function blu_get_attachment_id_by_url( $url ) {
		// Split the $url into two parts with the wp-content directory as the separator
		$parsed_url = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );
		 
		// Get the host of the current site and the host of the $url, ignoring www
		$this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
		$file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );
		 
		// Return nothing if there aren't any $url parts or if the current host and $url host do not match
		if ( ! isset( $parsed_url[1] ) || empty( $parsed_url[1] ) || ( $this_host != $file_host ) ) {
		return;
		}
		 
		// Now we're going to quickly search the DB for any attachment GUID with a partial path match
		// Example: /uploads/2013/05/test-image.jpg
		global $wpdb;
		 
		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE guid RLIKE %s;", $parsed_url[1] ) );
		 
		// Returns null if no attachment is found
		return $attachment[0];
	}

	function blu_truncate($string, $limit, $break=".", $pad="..."){
	  if(strlen($string) <= $limit) return $string;

	  if(false !== ($breakpoint = strpos($string, $break, $limit))) {
	    if($breakpoint < strlen($string) - 1) {
	      $string = substr($string, 0, $breakpoint) . $pad;
	    }
	  }
	  return $string;
	}


	/**
	 * Get users avatar image URL
	 * @param  int 		$author_id 	User id
	 * @param  string 	$size      	size of the image
	 * @return string            	Image URL
	 */
	function blu_get_avatar($author_id, $size = 80){

		$attachment_id = blu_get_attachment_id_by_url(of_get_option('author_box_avatar_'.$author_id));

			if($attachment_id){
				# Get avatar from theme options
				$avatar = wp_get_attachment_image_src($attachment_id, array($size, $size));
			}

			// If not found get gravatar
			if(!isset($avatar)){
				$avatar = get_avatar($author_id, $size);
		    	preg_match("/src='(.*?)'/i", $avatar, $matches);
		    	return $matches[1];			
			}elseif(is_array($avatar)){
				return $avatar[0];
			}
	}	
	// Google ads
	function blu_get_google_ads(){ 
		global $blu_google_ads_count;
		$blu_google_ads_count++;
		?>
		<div style="text-align: center;">
			<div id="google-ads-<?php echo $blu_google_ads_count; ?>" class="google-ads"></div>
		       
			<script type="text/javascript">

			    /* Calculate the width of available ad space */
			    ad = jQuery('#google-ads-<?php echo $blu_google_ads_count; ?>')[0];
			     
			    if (ad.getBoundingClientRect().width) {
			   	 	adWidth = ad.getBoundingClientRect().width; // for modern browsers
			    } else {
			    	adWidth = ad.offsetWidth; // for old IE
			    }
			     
			    /* Replace ca-pub-XXX with your AdSense Publisher ID */
			    google_ad_client = "<?php echo of_get_option('google_publisher_id', '0'); ?>";
			     
			    /* Replace 1234567890 with the AdSense Ad Slot ID */
			    google_ad_slot = "<?php echo of_get_option('google_ad_unit_id', '0'); ?>";
			    /* Do not change anything after this line */
			    if ( adWidth >= 728 )
			    	google_ad_size = ["728", "90"]; /* Leaderboard 728x90 */
			    else if ( adWidth >= 468 )
			    	google_ad_size = ["468", "60"]; /* Banner (468 x 60) */
			    else if ( adWidth >= 336 )
			    	google_ad_size = ["336", "280"]; /* Large Rectangle (336 x 280) */
			    else if ( adWidth >= 300 )
			    	google_ad_size = ["300", "250"]; /* Medium Rectangle (300 x 250) */
			    else if ( adWidth >= 250 )
			    	google_ad_size = ["250", "250"]; /* Square (250 x 250) */
			    else if ( adWidth >= 200 )
			    	google_ad_size = ["200", "200"]; /* Small Square (200 x 200) */
			    else if ( adWidth >= 180 )
			    	google_ad_size = ["180", "150"]; /* Small Rectangle (180 x 150) */
			    else
			    	google_ad_size = ["125", "125"]; /* Button (125 x 125) */
			     

			    document.write (
			    '<ins class="adsbygoogle" style="display:inline-block;width:'
			    + google_ad_size[0] + 'px;height:'
			    + google_ad_size[1] + 'px" data-ad-client="'
			    + google_ad_client + '" data-ad-slot="'
			    + google_ad_slot + '"></ins>'
			    );
			    (adsbygoogle = window.adsbygoogle || []).push({});

			</script>
			 
			<script async src="http://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js">
			</script>
		</div><?php
	}
