<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 */

function optionsframework_option_name() {

	// This gets the theme name from the stylesheet
	$themename = get_option( 'stylesheet' );
	$themename = preg_replace("/\W/", "_", strtolower($themename) );

	$optionsframework_settings = get_option( 'optionsframework' );
	$optionsframework_settings['id'] = $themename;
	update_option( 'optionsframework', $optionsframework_settings );
}

add_thickbox();

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'bluth'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */

function optionsframework_options() {


	$background_mode = array(
		'image' => __('Image', 'bluth'),
		'pattern' => __('Pattern', 'bluth'),
		'color' => __('Solid Color', 'bluth')
	);

	$font_sizes = array(
		'10px' => '10px',
		'12px' => '12px',
		'14px' => '14px',
		'16px' => '16px',
		'18px' => '18px',
		'20px' => '20px',
		'22px' => '22px',
		'24px' => '24px',
		'26px' => '26px',
		'28px' => '28px',
		'30px' => '30px',
		'32px' => '32px',
		'34px' => '34px',
		'36px' => '36px',
		'38px' => '38px',
		'40px' => '40px',
		'42px' => '42px',
		'44px' => '44px',
		'46px' => '46px',
		'48px' => '48px'
	);



	// If using image radio buttons, define a directory path
	$imagepath =  get_template_directory_uri() . '/assets/img/';

	$options = array();


	$options[] = array(
		'name' => __('Theme Options', 'bluth'),
		'type' => 'heading');


	$options[] = array(
		'name' => __('Background', 'bluth'),
		'desc' => __('What kind of background do you want?', 'bluth'),
		'id' => 'background_mode',
		'std' => 'image',
		'type' => 'radio',
		'options' => $background_mode);

	$options[] = array(
		'name' => __('Background Image', 'bluth'),
		'desc' => __('Upload your background image here. Recommended size is 1400x875 px or 1.6 to 1 ratio.', 'bluth'),
		'id' => 'background_image',
		'std' => get_template_directory_uri() . '/assets/img/bg.jpg',
		'class' => 'background_image',
		'type' => 'upload');

	$options[] = array(
		'name' => __('Show stripe overlay', 'bluth'),
		'desc' => __('Uncheck this to remove the stripe overlay that covers the background image', 'bluth'),
		'id' => 'show_stripe',
		'std' => '1',
		'class' => 'background_image',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Select a background pattern', 'bluth'),
		'desc' => __('Select a background pattern from the list or upload your own below.', 'bluth'),
		'id' => "background_pattern",
		'std' => "brick_wall.jpg",
		'type' => "images",
		'class' => "hide background_pattern",
		'options' => array(
			'az_subtle.png' => $imagepath . '/pattern/sample/az_subtle_50.png',
			'cloth_alike.png' => $imagepath . '/pattern/sample/cloth_alike_50.png',
			'cream_pixels.png' => $imagepath . '/pattern/sample/cream_pixels_50.png',
			'gray_jean.png' => $imagepath . '/pattern/sample/gray_jean_50.png',
			'grid.png' => $imagepath . '/pattern/sample/grid_50.png',
			'light_noise_diagonal.png' => $imagepath . '/pattern/sample/light_noise_diagonal_50.png',
			'light_paper.png' => $imagepath . '/pattern/sample/light_paper_50.png',
			'noise_lines.png' => $imagepath . '/pattern/sample/noise_lines_50.png',
			'pw_pattern.png' => $imagepath . '/pattern/sample/pw_pattern_50.png',
			'shattered.png' => $imagepath . '/pattern/sample/shattered_50.png',
			'squairy_light.png' => $imagepath . '/pattern/sample/squairy_light_50.png',
			'striped_lens.png' => $imagepath . '/pattern/sample/striped_lens_50.png',
			'textured_paper.png' => $imagepath .'/pattern/sample/textured_paper_50.png')
	);

	$options[] = array(
		'name' => __('Upload Pattern', 'bluth'),
		'desc' => __('Upload a new pattern here. If this feature is used it will overwrite the selection above.', 'bluth'),
		'id' => 'background_pattern_custom',
		'class' => 'background_pattern',
		'type' => 'upload');

	$options[] = array(
		'name' => __('Background Color', 'bluth'),
		'desc' => __('Select the background color', 'bluth'),
		'id' => 'background_color',
		'std' => '#E9F0F4',
		'class' => "hide background_color",
		'type' => 'color' );

	$options[] = array(
		'name' => __('Facebook App Id', 'bluth'),
		'desc' => __('Insert you Facebook app id here. If you don\'t have one for your webpage you can create it <a target="_blank" href="https://developers.facebook.com/apps">here</a>', 'bluth'),
		'id' => 'facebook_app_id',
		'type' => 'text');

	$options[] = array(
		'name' => __('Enable Facebook comments for posts', 'bluth'),
		'desc' => __('Check this to use Facebook comments instead of regular wordpress comments for posts. Requires a Facebook app id in the field above.', 'bluth'),
		'id' => 'facebook_comments',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Disable Responsive CSS', 'bluth'),
		'desc' => __('Check this to disable responsive css. Responsive css enable the webpage to adapt to every screen size allowing mobile users to browse the website more easily.', 'bluth'),
		'id' => 'disable_responsive',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Layout', 'bluth'),
		'desc' => __('Select the layout you want, left sidebar, right sidebar or no sidebar. Default: Right sidebar', 'bluth'),
		'id' => "side_bar",
		'std' => "right_side",
		'type' => "images",
		'options' => array(
			'single' => $imagepath . '1col.png',
			'left_side' => $imagepath . '2cl.png',
			'right_side' => $imagepath . '2cr.png')
	);

	$options[] = array(
		'name' => __('Right-to-Left Language', 'bluth'),
		'desc' => __('Check this if your language is written in a Right-to-Left direction', 'bluth'),
		'id' => 'enable_rtl',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Footer text', 'bluth'),
		'desc' => __('{year} will be replaced with the current year', 'bluth'),
		'id' => 'footer_text',
		'std' => 'Copyright &copy; {year} · Theme design by the Bluthemes · www.bluthemes.com',
		'type' => 'text');

	$options[] = array(
		'name' => __('Google Analytics', 'bluth'),
		'desc' => __('Add your Google Analytics tracking code here. Google Analytics is a free web analytics service more info here: <a href="http://www.google.com/analytics/">Google Analytics</a>', 'bluth'),
		'id' => 'google_analytics',
		'std' => '',
		'type' => 'textarea');

	$options[] = array(
		'name' => __('SEO plugin support', 'bluth'),
		'desc' => __('Check this to give an SEO plugin control of meta description, title and open graph tags.', 'bluth'),
		'id' => 'seo_plugin',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Fluid footer', 'bluth'),
		'desc' => __('By default the footer uses a responsive grid system with a percentage based width, by checking this the footer makes every widget fluid so large items in the footer will work properly.', 'bluth'),
		'id' => 'footer_fluid',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Show tagline under site name', 'bluth'),
		'desc' => __('Shows the tagline under the site name if there is no logo', 'bluth'),
		'id' => 'show_tagline',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Disable color bars', 'bluth'),
		'desc' => __('Check the color bars you want to remove', 'bluth'),
		'id' => 'remove_color_stripes',
		'std' => array(
			'header_bars' 	=> '0',
			'footer_bars' 	=> '0',
		),
		'type' => 'multicheck',
		'options' => array(
			'header_bars' 	=> 'Remove header color bars',
			'footer_bars' 	=> 'Remove footer color bars',
		));

	$options[] = array(
		'name' => __('Color bar first column', 'bluth'),
		'desc' => __('Select a color for the first column in the color bar', 'bluth'),
		'id' => 'color_bar_1',
		'std' => '#F69087',
		'type' => 'color' );

	$options[] = array(
		'name' => __('Color bar second column', 'bluth'),
		'desc' => __('Select a color for the second column in the color bar', 'bluth'),
		'id' => 'color_bar_2',
		'std' => '#85CCB1',
		'type' => 'color' );

	$options[] = array(
		'name' => __('Color bar third column', 'bluth'),
		'desc' => __('Select a color for the third column in the color bar', 'bluth'),
		'id' => 'color_bar_3',
		'std' => '#85A9B3',
		'type' => 'color' );

	$options[] = array(
		'name' => __('Color bar fourth column', 'bluth'),
		'desc' => __('Select a color for the fourth column in the color bar', 'bluth'),
		'id' => 'color_bar_4',
		'std' => '#B0CB7A',
		'type' => 'color' );

		$options[] = array(
		'name' => 'Move JavaScript to the footer',
		'desc' => 'When enabled the JavaScript files will be moved to the footer of the page and will increase performance on the website. WARNING: this can break some plugins, make sure to test the page after enabling.',
		'id' => 'scripts_to_footer',
		'std' => '0',
		'type' => 'checkbox');




	$options[] = array(
		'name' => 'Header',
		'type' => 'heading');

	$options[] = array(
		'name' => __('Logo', 'bluth'),
		'desc' => __('Upload your logo here. Remove the image to show the name of the website in text instead.', 'bluth'),
		'id' => 'logo',
		'type' => 'upload');

	$options[] = array(
		'name' => __('Favicon', 'bluth'),
		'desc' => __('Upload a favicon. Favicons are the icons that appear in the tabs of the browser and left of the address bar. (16x16 pixels)', 'bluth'),
		'id' => 'favicon',
		'type' => 'upload');
	$options[] = array(
		'name' => __('Show search in header', 'bluth'),
		'desc' => __('Uncheck this to remove the search input from the header', 'bluth'),
		'id' => 'show_search_header',
		'std' => '1',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Disable Sticky Header', 'bluth'),
		'desc' => __('Check this to disable the sticky header feature. (The header won\'t stay fixed at the top of the window when you scroll down)', 'bluth'),
		'id' => 'disable_fixed_header',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => 'Custom content in the menu',
		'desc' => 'Here you can add shortcodes or HTML content to add buttons or anything else to the menu. <br><br>Example:<br> [button url="http://www.example.com" style="primary" size="default" icon="check"]Buy Now![/button]',
		'id' => 'custom_menu_content',
		'std' => '',
		'type' => 'textarea');



	$options[] = array(
		'name' => __('Posts & Pages', 'bluth'),
		'type' => 'heading');

	$options[] = array(
		'name' => __('Front Page layout', 'bluth'),
		'desc' => __('Select the kind of layout you want on the front page. List is a compact version with an image on the left side. Default has a large image on top.', 'bluth'),
		'id' => 'index_layout',
		'std' => 'default',
		'type' => 'select',
		'options' => array(
			'default' 	=> 'Default',
			'list' 		=> 'List',
			'list-excerpt' 		=> 'List w/Excerpt',
		));


	$options[] = array(
		'name' => __('Archives layout', 'bluth'),
		'desc' => __('Select the kind of layout you want on the archives. These include pages like category, tags, monthly archives etc.', 'bluth'),
		'id' => 'archive_layout',
		'std' => 'default',
		'type' => 'select',
		'options' => array(
			'default' 	=> 'Default',
			'list' 		=> 'List',
			'list-excerpt' 		=> 'List w/Excerpt',
		));

	$options[] = array(
		'name' => __('Sticky Post layout', 'bluth'),
		'desc' => __('Select the kind of layout you want for sticky posts. With this you can make the sticky post appear in a different layout than the other posts.', 'bluth'),
		'id' => 'sticky_layout',
		'std' => 'default',
		'type' => 'select',
		'options' => array(
			'default' 	=> 'Default',
			'list' 		=> 'List',
			'list-excerpt' 		=> 'List w/Excerpt',
		));

	$options[] = array(
		'name' => 'Hide "No Comments" text when there are no comments available',
		'desc' => 'If there are no comments on a post this option will remove the No Comments text',
		'id' => 'hide_no_comments',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => 'Enable Lazy Image loading',
		'desc' => 'Lazy Load delays loading of images. Images outside of viewport are not loaded until user scrolls to them. Using Lazy Load on long web pages will make the page load faster. In some cases it can also help to reduce server load.',
		'id' => 'lazy_load',
		'std' => '1',
		'type' => 'checkbox');

	$options[] = array(
		'name' => 'Show Facebook like button in the footer of each post',
		'desc' => 'Enable this to render a like button in the footer of each post on the homepage and inside the post itself',
		'id' => 'enable_fb_like_footer',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Show full version of featured images (not cropped)', 'bluth'),
		'desc' => __('Check this to make the featured image, that sits on top of the post, render the full version of the image so the height will be dynamic not fixed as the cropped version is.', 'bluth'),
		'id' => 'show_full_image',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Center align title and post meta', 'bluth'),
		'desc' => __('Check this to center align the post title and the meta data below it (date, category tags etc.)', 'bluth'),
		'id' => 'center_align_post',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Disable share buttons in the post footer', 'bluth'),
		'desc' => __('Check this to remove "Share story" and all the share buttons in the posts footer.', 'bluth'),
		'id' => 'disable_share_story',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Disable the meta footer for posts', 'bluth'),
		'desc' => __('Check this to remove the footer with the author image and share buttons from all posts', 'bluth'),
		'id' => 'disable_footer_post',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Disable the meta footer for pages', 'bluth'),
		'desc' => __('Check this to remove the footer with the author image and share buttons from all pages', 'bluth'),
		'id' => 'disable_footer_page',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Disable hover zoom effect on post images', 'bluth'),
		'desc' => __('Check this to remove the zoom effect when the mouse pointer is hovered above a post image', 'bluth'),
		'id' => 'disable_img_hover',
		'std' => '0',
		'type' => 'checkbox');




	$options[] = array(
		'name' => __('Enable posts excerpt (post summary)', 'bluth'),
		'desc' => __('Check this to only show the post excerpt or the summary of a post in the browse page. The default behavior is to show the whole post but you can provide a cut-off point by adding the <a href="http://codex.wordpress.org/Customizing_the_Read_More" target="_blank">More</a> tag.', 'bluth'),
		'id' => 'enable_excerpt',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Exerpt Length', 'bluth'),
		'desc' => __('How many words would you like to show in the post summary. Default: 55 words', 'bluth'),
		'id' => 'excerpt_length',
		'std' => '55',
		'class' => 'hide',
		'type' => 'text');

	$options[] = array(
		'name' => __('Continue Reading link as a button', 'bluth'),
		'desc' => __('Check this to make the continue reading link a button. You can style this button in the Custom CSS by using a rule like this: .more-link{ background: red!important }', 'bluth'),
		'id' => 'continue_reading_button',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Show Continue Reading link', 'bluth'),
		'desc' => __('Uncheck this to hide the Continue Reading link that appears below the post conent.', 'bluth'),
		'id' => 'show_continue_reading',
		'std' => '1',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Disable Post Header', 'bluth'),
		'desc' => __('Check this to disable the header that renders above each post. Note: this will also remove the post icon for each post.', 'bluth'),
		'id' => 'disable_post_header',
		'std' => '0',
		'type' => 'checkbox');



	$options[] = array(
		'name' => __('Disable Related Posts', 'bluth'),
		'desc' => __('Related articles are show below each post when you view it. Check this to disable that feature.', 'bluth'),
		'id' => 'disable_related_posts',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Related Posts by Tag or Category first', 'bluth'),
		'desc' => __('If "Category" is selected the theme will search for related posts by category and if none are found tags will be used to find related posts. If you choose Tags it will work exactly the opposite way.', 'bluth'),
		'id' => 'related_posts_first',
		'std' => 'category',
		'type' => 'select',
		'options' => array(
			'category' 	=> 'Category',
			'tags' 		=> 'Tags',
		));

		

	$options[] = array(
		'name' => __('Post footer share buttons', 'bluth'),
		'desc' => __('Select the sharing options you want to display in the post footer.', 'bluth'),
		'id' => 'footer_share_options',
		'std' => array(
			'facebook' 		=> '1',
			'googleplus' 	=> '1',
			'twitter' 		=> '1',
			'reddit' 		=> '1',
			'linkedin' 		=> '1',
			'delicious' 	=> '1',
			'email' 		=> '1',
		),
		'type' => 'multicheck',
		'options' => array(
			'facebook' 		=> 'Facebook',
			'googleplus' 	=> 'Google+',
			'twitter' 		=> 'Twitter',
			'reddit' 		=> 'Reddit',
			'linkedin' 		=> 'Linkedin',
			'delicious' 	=> 'Delicious',
			'email' 		=> 'Email',
		));

	$options[] = array(
		'name' => 'Disable "X time ago" date format for comments',
		'desc' => 'Comments dates are shown with a format like "2 days ago", check this to disable that and enable the default WP date format',
		'id' => 'disable_timeago',
		'std' => '0',
		'type' => 'checkbox');



	#
	#	USERS
	#
	$options[] = array(
		'name' => 'Authors',
		'type' => 'heading');


		$social_sites = array(
			'facebook'    => 'Facebook',
			'twitter'     => 'Twitter',
			'google'      => 'Google+',
			'tumblr'      => 'Tumblr',
			'linkedin'    => 'Linkedin',
			'instagram'   => 'Instagram',
			'dribbble'    => 'Dribbble',
			'pinterest'   => 'Pinterest',
			'youtube'     => 'Youtube',
		);

		$users = get_users( array('who' => 'authors') );
		foreach($users as $user){
			
			$options[] = array(
				'name' => 'Author: '.$user->user_nicename,
				'type' => 'info');

				$options[] = array(
					'name' => 'Author Cover for '.$user->user_nicename,
					'desc' => 'Upload a cover for the author box',
					'id' => 'author_box_image_'.$user->ID,
					'type' => 'upload');

				$options[] = array(
					'name' => 'Author Box Avatar',
					'desc' => 'Upload a custom avatar for the author box (will use gravatar if nothing is set) (120x120)',
					'id' => 'author_box_avatar_'.$user->ID,
					'type' => 'upload');

				$options[] = array(
					'name' => 'Author Bio',
					'desc' => 'Share a little biographical information, this will overwrite the default bio field from the WordPress user settings.',
					'id' => 'author_bio_'.$user->ID,
					'std' => '',
					'type' => 'textarea');

				$options[] = array(
					'name' => '',
					'desc' => 'The social media links below will be used in the author widget, in the author info box below articles and more. (Optional)',
					'id' => 'cust_not',
					'type' => 'description_box');

				foreach ($social_sites as $key => $value) {

					$options[] = array(
						'name' => $value,
						'desc' => $value.' URL for this '.$user->user_nicename,
						'id' => 'author_'.$key.'_'.$user->ID,
						'std' => '',
						'type' => 'text');
				}
		}










	$options[] = array(
		'name' => __('Icons', 'bluth'),
		'type' => 'heading');

	$options[] = array(
		'name' => __('Disable Post Icons', 'bluth'),
		'desc' => __('Check this to disable the post icons', 'bluth'),
		'id' => 'disable_icons',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Images or Icons', 'bluth'),
		'desc' => __('You can upload an icon for each post type or select an icon from the included icon set.', 'bluth'),
		'id' => 'icon_media',
		'std' => 'icons',
		'type' => 'radio',
		'options' => array('icons' => 'Select icons from the icon set', 'images' => 'Upload my own image icons (40x40 pixels)'));

	$options[] = array(
		'name' => __('Post Icons', 'bluth'),
		'desc' => __('Do you want different icons per post format or category?', 'bluth'),
		'id' => 'icon_mode',
		'std' => 'post_format',
		'type' => 'radio',
		'options' => array('post_format' => 'Post Format', 'category' => 'Category'));

	$options_categories_obj = get_categories();

	foreach ($options_categories_obj as $category) {
		
		$options_categories[$category->cat_ID] = $category->cat_name;

		$options[] = array(
			'name' => __(ucfirst ($category->cat_name).' Icon', 'bluth'),
			'desc' => __('Select a post icon for the category: '.ucfirst ($category->cat_name), 'bluth'),
			'id' => 'cat_'.$category->cat_ID.'_icon',
			'std' => 'icon-calendar-3',
			'class' => 'post_icon_edit hide icon_mode_cat icon_media_icon',
			'type' => 'text');

		$options[] = array(
			'name' => __(ucfirst ($category->cat_name).' Image', 'bluth'),
			'desc' => __('Upload an image icon for the category: '.ucfirst ($category->cat_name), 'bluth'),
			'class' => 'hide icon_mode_cat icon_media_image',
			'id' => 'cat_'.$category->cat_ID.'_image',
			'type' => 'upload');

		$options[] = array(
			'name' => __(ucfirst ($category->cat_name).' Post Color', 'bluth'),
			'desc' => __('Select the color for the '.ucfirst ($category->cat_name).' category post icon and links', 'bluth'),
			'id' => 'cat_'.$category->cat_ID.'_post_color',
			'class' => 'hide icon_mode_cat',
			'std' => '#F69087',
			'type' => 'color' );		
	}


	$options[] = array(
		'name' => __('Standard Post Icon', 'bluth'),
		'desc' => __('Select an icon for the standard post type. (Default: icon-calendar-3)', 'bluth'),
		'id' => 'standard_icon',
		'std' => 'icon-calendar-3',
		'class' => 'post_icon_edit hide icon_mode_post_format icon_media_icon',
		'type' => 'text');

	$options[] = array(
		'name' => __('Standard Post Icon', 'bluth'),
		'desc' => __('Upload an image icon for Standard Posts', 'bluth'),
		'class' => 'hide icon_mode_post_format icon_media_image',
		'id' => 'standard_image',
		'type' => 'upload');

	$options[] = array(
		'name' => __('Standard Post Color', 'bluth'),
		'desc' => __('Select the color for the standard post icon and links', 'bluth'),
		'id' => 'standard_post_color',
		'std' => '#F69087',
		'class' => 'hide icon_mode_post_format',
		'type' => 'color' );

	$options[] = array(
		'name' => __('Gallery Post Icon', 'bluth'),
		'desc' => __('Select an icon for the gallery post type. (Default: icon-picture)', 'bluth'),
		'id' => 'gallery_icon',
		'std' => 'icon-picture',
		'class' => 'post_icon_edit hide icon_mode_post_format icon_media_icon',
		'type' => 'text');

	$options[] = array(
		'name' => __('Gallery Post Icon', 'bluth'),
		'desc' => __('Upload an image icon for Gallery Posts', 'bluth'),
		'class' => 'hide icon_mode_post_format icon_media_image',
		'id' => 'gallery_image',
		'type' => 'upload');

	$options[] = array(
		'name' => __('Gallery Post Color', 'bluth'),
		'desc' => __('Select the color for the gallery post icon and links', 'bluth'),
		'id' => 'gallery_post_color',
		'std' => '#4782A6',
		'class' => 'hide icon_mode_post_format',
		'type' => 'color' );
	
	$options[] = array(
		'name' => __('Image Post Icon', 'bluth'),
		'desc' => __('Select an icon for the image post type. (Default: icon-picture-1)', 'bluth'),
		'id' => 'image_icon',
		'std' => 'icon-picture-1',
		'class' => 'post_icon_edit hide icon_mode_post_format icon_media_icon',
		'type' => 'text');

	$options[] = array(
		'name' => __('Image Post Icon', 'bluth'),
		'desc' => __('Upload an image icon for Image Posts', 'bluth'),
		'class' => 'hide icon_mode_post_format icon_media_image',
		'id' => 'image_image',
		'type' => 'upload');

	$options[] = array(
		'name' => __('Image Post Color', 'bluth'),
		'desc' => __('Select the color for the image post icon and links', 'bluth'),
		'id' => 'image_post_color',
		'std' => '#B0CB7A',
		'class' => 'hide icon_mode_post_format',
		'type' => 'color' );

	$options[] = array(
		'name' => __('Link Post Icon', 'bluth'),
		'desc' => __('Select an icon for the link post type. (Default: icon-link)', 'bluth'),
		'id' => 'link_icon',
		'std' => 'icon-link',
		'class' => 'post_icon_edit hide icon_mode_post_format icon_media_icon',
		'type' => 'text');

	$options[] = array(
		'name' => __('Link Post Icon', 'bluth'),
		'desc' => __('Upload an image icon for Link Posts', 'bluth'),
		'class' => 'hide icon_mode_post_format icon_media_image',
		'id' => 'link_image',
		'type' => 'upload');

	$options[] = array(
		'name' => __('Link Post Color', 'bluth'),
		'desc' => __('Select the color for the link post icon and links', 'bluth'),
		'id' => 'link_post_color',
		'std' => '#9664B5',
		'class' => 'hide icon_mode_post_format',
		'type' => 'color' );

	$options[] = array(
		'name' => __('Quote Post Icon', 'bluth'),
		'desc' => __('Select an icon for the quote post type. (Default: icon-quote-left)', 'bluth'),
		'id' => 'quote_icon',
		'std' => 'icon-quote-left',
		'class' => 'post_icon_edit hide icon_mode_post_format icon_media_icon',
		'type' => 'text');

	$options[] = array(
		'name' => __('Quote Post Icon', 'bluth'),
		'desc' => __('Upload an image icon for Quote Posts', 'bluth'),
		'class' => 'hide icon_mode_post_format icon_media_image',
		'id' => 'quote_image',
		'type' => 'upload');

	$options[] = array(
		'name' => __('Quote Post Color', 'bluth'),
		'desc' => __('Select the color for the quote post icon and links', 'bluth'),
		'id' => 'quote_post_color',
		'std' => '#85A9B3',
		'class' => 'hide icon_mode_post_format',
		'type' => 'color' );

	$options[] = array(
		'name' => __('Audio Post Icon', 'bluth'),
		'desc' => __('Select an icon for the audio post type. (Default: icon-volume-up)', 'bluth'),
		'id' => 'audio_icon',
		'std' => 'icon-volume-up',
		'class' => 'post_icon_edit hide icon_mode_post_format icon_media_icon',
		'type' => 'text');

	$options[] = array(
		'name' => __('Audio Post Icon', 'bluth'),
		'desc' => __('Upload an image icon for Audio Posts', 'bluth'),
		'class' => 'hide icon_mode_post_format icon_media_image',
		'id' => 'audio_image',
		'type' => 'upload');

	$options[] = array(
		'name' => __('Audio Post Color', 'bluth'),
		'desc' => __('Select the color for the audio post icon and links', 'bluth'),
		'id' => 'audio_post_color',
		'std' => '#EF7336',
		'class' => 'hide icon_mode_post_format',
		'type' => 'color' );	

	$options[] = array(
		'name' => __('Video Post Icon', 'bluth'),
		'desc' => __('Select an icon for the video post type. (Default: icon-videocam)', 'bluth'),
		'id' => 'video_icon',
		'std' => 'icon-videocam',
		'class' => 'post_icon_edit hide icon_mode_post_format icon_media_icon',
		'type' => 'text');

	$options[] = array(
		'name' => __('Video Post Icon', 'bluth'),
		'desc' => __('Upload an image icon for Video Posts', 'bluth'),
		'class' => 'hide icon_mode_post_format icon_media_image',
		'id' => 'video_image',
		'type' => 'upload');

	$options[] = array(
		'name' => __('Video Post Color', 'bluth'),
		'desc' => __('Select the color for the video post icon and links', 'bluth'),
		'id' => 'video_post_color',
		'std' => '#85CCB1',
		'class' => 'hide icon_mode_post_format',
		'type' => 'color' );	

	$options[] = array(
		'name' => __('Facebook Status Post Icon', 'bluth'),
		'desc' => __('Select an icon for the facebook status post type. (Default: icon-facebook-1)', 'bluth'),
		'id' => 'facebook_icon',
		'std' => 'icon-facebook-1',
		'class' => 'post_icon_edit hide icon_mode_post_format icon_media_icon',
		'type' => 'text');

	$options[] = array(
		'name' => __('Facebook Status Post Icon', 'bluth'),
		'desc' => __('Upload an image icon for Facebook Status Posts', 'bluth'),
		'class' => 'hide icon_mode_post_format icon_media_image',
		'id' => 'facebook_image',
		'type' => 'upload');

	$options[] = array(
		'name' => __('Facebook Status Post Color', 'bluth'),
		'desc' => __('Select the color for the Facebook status post icon and links', 'bluth'),
		'id' => 'facebook_post_color',
		'std' => '#49659F',
		'class' => 'hide icon_mode_post_format',
		'type' => 'color' );	

	$options[] = array(
		'name' => __('Twitter Status Post Icon', 'bluth'),
		'desc' => __('Select an icon for the Twitter status post type. (Default: icon-twitter-1)', 'bluth'),
		'id' => 'twitter_icon',
		'std' => 'icon-twitter-1',
		'class' => 'post_icon_edit hide icon_mode_post_format icon_media_icon',
		'type' => 'text');

	$options[] = array(
		'name' => __('Twitter status Post Icon', 'bluth'),
		'desc' => __('Upload an image icon for Twitter status Posts', 'bluth'),
		'class' => 'hide icon_mode_post_format icon_media_image',
		'id' => 'twitter_image',
		'type' => 'upload');

	$options[] = array(
		'name' => __('Twitter status Post Color', 'bluth'),
		'desc' => __('Select the color for the Twitter status post icon and links', 'bluth'),
		'id' => 'twitter_post_color',
		'std' => '#00ACED',
		'class' => 'hide icon_mode_post_format',
		'type' => 'color' );	

	$options[] = array(
		'name' => __('Sticky Post Icon', 'bluth'),
		'desc' => __('Select an icon for sticky posts. (Default: icon-pin)', 'bluth'),
		'id' => 'sticky_icon',
		'std' => 'icon-pin',
		'class' => 'hide post_icon_edit icon_media_icon icon_mode_post_format icon_mode_cat',
		'type' => 'text');

	$options[] = array(
		'name' => __('Sticky Post Icon', 'bluth'),
		'desc' => __('Upload an image icon for Sticky Posts', 'bluth'),
		'class' => 'hide icon_media_image icon_mode_post_format icon_mode_cat',
		'id' => 'sticky_image',
		'type' => 'upload');

	$options[] = array(
		'name' => __('Sticky Post Color', 'bluth'),
		'desc' => __('Select the color for the sticky post icon and links', 'bluth'),
		'id' => 'sticky_post_color',
		'std' => '#585756',
		'type' => 'color' );




	$options[] = array(
		'name' => __('Colors & Fonts', 'bluth'),
		'type' => 'heading');


	$options[] = array(
		'name' => __('Heading font', 'bluth'),
		'desc' => __('Select a font type for all heading', 'bluth'),
		'id' => 'heading_font',
		'std' => 'Crete+Round:400,400italic',
		'type' => 'text');

	$options[] = array(
		'name' => __('Main font', 'bluth'),
		'desc' => __('Select a font type for normal text', 'bluth'),
		'id' => 'text_font',
		'std' => 'Lato:400,700,400italic',
		'type' => 'text');

	$options[] = array(
		'name' => __('Menu links font', 'bluth'),
		'desc' => __('Select a font type for the menu items in the header', 'bluth'),
		'id' => 'menu_font',
		'std' => 'Lato:400,700,400italic',
		'type' => 'text');

	$options[] = array(
		'name' => __('Brand font', 'bluth'),
		'desc' => __('Select a font type for the brand. If you use text instead of a logo in your header this setting changes the font family for that text.', 'bluth'),
		'id' => 'brand_font',
		'std' => 'Lato:400,700,400italic',
		'type' => 'text');

	$options[] = array(
		'name' => __('Body font size', 'bluth'),
		'desc' => __('Select the size for the body in other words the default font size if no other specific size is set. (default: 14px)', 'bluth'),
		'id' => 'size_body',
		'std' => '14px',
		'type' => 'select',
		'class' => 'mini',
		'options' => $font_sizes
	);

	$options[] = array(
		'name' => __('Post title font size', 'bluth'),
		'desc' => __('Select the size for the post title. (default: 38px)', 'bluth'),
		'id' => 'size_post_title',
		'std' => '38px',
		'type' => 'select',
		'class' => 'mini',
		'options' => $font_sizes
	);

	$options[] = array(
		'name' => __('Post text font size', 'bluth'),
		'desc' => __('Select the size for the post text. (default: 18px)', 'bluth'),
		'id' => 'size_post_text',
		'std' => '18px',
		'type' => 'select',
		'class' => 'mini',
		'options' => $font_sizes
	);

	$options[] = array(
		'name' => __('Page title font size', 'bluth'),
		'desc' => __('Select the size for page titles. (default: 38px)', 'bluth'),
		'id' => 'size_page_title',
		'std' => '38px',
		'type' => 'select',
		'class' => 'mini',
		'options' => $font_sizes
	);

	$options[] = array(
		'name' => __('Page text font size', 'bluth'),
		'desc' => __('Select the size for page text. (default: 16px)', 'bluth'),
		'id' => 'size_page_text',
		'std' => '18px',
		'type' => 'select',
		'class' => 'mini',
		'options' => $font_sizes
	);

	$options[] = array(
		'name' => __('Top Header Color', 'bluth'),
		'desc' => __('Select the color for the top header that includes the menu and logo', 'bluth'),
		'id' => 'header_color',
		'std' => '#2E3641',
		'type' => 'color' );

	$options[] = array(
		'name' => __('Top Header Font Color', 'bluth'),
		'desc' => __('Select the color for the top header menu links', 'bluth'),
		'id' => 'header_font_color',
		'std' => '#ffffff',
		'type' => 'color' );

	$options[] = array(
		'name' => __('Post Header Color', 'bluth'),
		'desc' => __('Select the color for the top header of each post', 'bluth'),
		'id' => 'post_header_color',
		'std' => '#2E3641',
		'type' => 'color' );

	$options[] = array(
		'name' => __('Archives Header Color', 'bluth'),
		'desc' => __('Select the color for the archive header. This header only appears if the layout is set to List in Posts & Pages.', 'bluth'),
		'id' => 'archive_header_color',
		'std' => '#2E3641',
		'type' => 'color' );

	$options[] = array(
		'name' => __('Archives Header Font Color', 'bluth'),
		'desc' => __('Select the font color for the archive header. This header only appears if the layout is set to List in Posts & Pages.', 'bluth'),
		'id' => 'archive_header_font_color',
		'std' => '#FFFFFF',
		'type' => 'color' );

	$options[] = array(
		'name' => __('Archives paragraph Font Color', 'bluth'),
		'desc' => __('Select the font color for the archive paragraph below the heading. This can be the category description or tag description.', 'bluth'),
		'id' => 'archive_paragraph_font_color',
		'std' => '#999999',
		'type' => 'color' );

	$options[] = array(
		'name' => __('Widget Header Color', 'bluth'),
		'desc' => __('Select the default color for the top header of each widget', 'bluth'),
		'id' => 'widget_header_color',
		'std' => '#2E3641',
		'type' => 'color' );

	$options[] = array(
		'name' => __('Widget Header Font Color', 'bluth'),
		'desc' => __('Select the color for the heading in each widget', 'bluth'),
		'id' => 'widget_header_font_color',
		'std' => '#ffffff',
		'type' => 'color' );

	$options[] = array(
		'name' => __('Footer Color', 'bluth'),
		'desc' => __('Select the default color for the footer', 'bluth'),
		'id' => 'footer_color',
		'std' => '#2E3641',
		'type' => 'color' );



	$options[] = array(
		'name' => __('Advertising', 'bluth'),
		'type' => 'heading');


		$options[] = array(
			'name' => __('Google Ads', 'bluth_admin'),
			'type' => 'info');

			$options[] = array(
				'name' => __('Google Publisher ID', 'bluth_admin'),
				'desc' => __('Found in the top right corner of your <a href="https://www.google.com/adsense/" target="_blank">adsense account</a>.', 'bluth_admin'),
				'id' => 'google_publisher_id',
				'std' => '',
				'type' => 'text');

			$options[] = array(
				'name' => __('Google Ad unit ID', 'bluth_admin'),
				'desc' => __('Found in your Ad Units area under <strong>ID</strong> <a href="https://www.google.com/adsense/app#myads-springboard" target="_blank">here</a>.', 'bluth_admin'),
				'id' => 'google_ad_unit_id',
				'std' => '',
				'type' => 'text');

		$options[] = array(
			'name' => __('Ad spot #1 - Above the header.', 'bluth'),
			'desc' => __('Select what kind of ad you want added above the top menu. <a target="_blank" href="http://www.bluth.is/wordpress/keilir/wp-content/uploads/2013/07/ad_above_example.png">See Example</a>', 'bluth'),
			'id' => 'ad_header_mode',
			'std' => 'none',
			'type' => 'radio',
			'options' => array(
				'none' => __('None', 'bluth'),
				'html' => __('Shortcode or HTML code like Adsense', 'bluth'),
				'image' => __('Image with a link', 'bluth')
			));

		$options[] = array(
			'name' => __('Add Shortcode or HTML code here', 'bluth'),
			'desc' => __('Insert a shortcode provided by this theme or any plugin. You can also add advertising code from any provider or use plain html. To add Adsense just paste the embed code here that they provide and save.', 'bluth'),
			'id' => 'ad_header_code',
			'class' => 'hide ad_header_code',
			'std' => '',
			'type' => 'textarea');

		$options[] = array(
			'name' => __('Upload Image', 'bluth'),
			'desc' => __('Upload an image to add above the header menu and add a link for it in the input box below', 'bluth'),
			'id' => 'ad_header_image',
			'class' => 'hide ad_header_image',
			'type' => 'upload');

		$options[] = array(
			'name' => __('Image link', 'bluth'),
			'desc' => __('Add a link to the image', 'bluth'),
			'id' => 'ad_header_image_link',
			'class' => 'hide ad_header_image',
			'std' => '',
			'type' => 'text');


		$options[] = array(
			'name' => __('Ad spot #2 - Between posts', 'bluth'),
			'desc' => __('Here you can add advertising between posts. <a target="_blank" href="http://www.bluth.is/wordpress/keilir/wp-content/uploads/2013/07/ad_posts_example.png">See Example</a>', 'bluth'),
			'id' => 'ad_posts_mode',
			'std' => 'none',
			'type' => 'radio',
			'options' => array(
				'none' => __('None', 'bluth'),
				'html' => __('Shortcode or HTML code like Adsense', 'bluth'),
				'image' => __('Image with a link', 'bluth')
			));

		$options[] = array(
			'name' => __('Add Shortcode or HTML code here', 'bluth'),
			'desc' => __('Insert a shortcode provided by this theme or any plugin. You can also add advertising code from any provider or use plain html. To add Adsense just paste the embed code here that they provide and save.', 'bluth'),
			'id' => 'ad_posts_code',
			'class' => 'hide ad_posts_code',
			'std' => '',
			'type' => 'textarea');

		$options[] = array(
			'name' => __('Upload Image', 'bluth'),
			'desc' => __('Upload an image to add between posts and add a link for it in the input box below', 'bluth'),
			'id' => 'ad_posts_image',
			'class' => 'hide ad_posts_image',
			'type' => 'upload');

		$options[] = array(
			'name' => __('Image link', 'bluth'),
			'desc' => __('Add a link to the image', 'bluth'),
			'id' => 'ad_posts_image_link',
			'class' => 'hide ad_posts_image',
			'std' => '',
			'type' => 'text');	

		$options[] = array(
			'name' => __('Display Frequency', 'bluth'),
			'desc' => __('How often do you want the ad to appear?', 'bluth'),
			'id' => 'ad_posts_frequency',
			'std' => 'one',
			'type' => 'select',
			'class' => 'mini hide ad_posts_options', //mini, tiny, small
			'options' => array(
				'1' => __('Between every post', 'bluth'),
				'2' => __('Every 2th posts', 'bluth'),
				'3' => __('Every 3th post', 'bluth'),
				'4' => __('Every 4th post', 'bluth'),
				'5' => __('Every 5th post', 'bluth')
			));

		$options[] = array(
			'name' => __('Add white background', 'bluth'),
			'desc' => __('Check this to wrap the ad content in a white box', 'bluth'),
			'id' => 'ad_posts_box',
			'std' => '1',
			'class' => 'hide ad_posts_options',
			'type' => 'checkbox');



		$options[] = array(
			'name' => __('Ad spot #3 - Above the content.', 'bluth'),
			'desc' => __('Select what kind of ad you want added above the main container. <a target="_blank" href="http://www.bluth.is/wordpress/keilir/wp-content/uploads/2013/07/ad_content_example.png">See Example</a>', 'bluth'),
			'id' => 'ad_content_mode',
			'std' => 'none',
			'type' => 'radio',
			'options' => array(
				'none' => __('None', 'bluth'),
				'html' => __('Shortcode or HTML code like Adsense', 'bluth'),
				'image' => __('Image with a link', 'bluth')
			));

		$options[] = array(
			'name' => __('Add Shortcode or HTML code here', 'bluth'),
			'desc' => __('Insert a shortcode provided by this theme or any plugin. You can also add advertising code from any provider or use plain html. To add Adsense just paste the embed code here that they provide and save.', 'bluth'),
			'id' => 'ad_content_code',
			'class' => 'hide ad_content_code',
			'std' => '',
			'type' => 'textarea');

		$options[] = array(
			'name' => __('Upload Image', 'bluth'),
			'desc' => __('Upload an image to add above the header menu and add a link for it in the input box below', 'bluth'),
			'id' => 'ad_content_image',
			'class' => 'hide ad_content_image',
			'type' => 'upload');

		$options[] = array(
			'name' => __('Image link', 'bluth'),
			'desc' => __('Add a link to the image', 'bluth'),
			'id' => 'ad_content_image_link',
			'class' => 'hide ad_content_image',
			'std' => '',
			'type' => 'text');

		$options[] = array(
			'name' => __('Add white background', 'bluth'),
			'desc' => __('Check this to wrap the ad content in a white box', 'bluth'),
			'id' => 'ad_content_box',
			'std' => '1',
			'class' => 'hide ad_content_options',
			'type' => 'checkbox');

		$options[] = array(
			'name' => __('Add padding', 'bluth'),
			'desc' => __('Add padding to the banner container', 'bluth'),
			'id' => 'ad_content_padding',
			'class' => 'hide ad_content_options',
			'std' => '1',
			'type' => 'checkbox');

		$options[] = array(
			'name' => __('Banner placement', 'bluth'),
			'desc' => __('Where do you want the banner to appear?', 'bluth'),
			'id' => 'ad_content_placement',
			'class' => 'hide ad_content_options',
			'std' => array(
				'home' => '1',
				'pages' => '1',
				'posts' => '1'
			),
			'type' => 'multicheck',
			'options' => array(
				'home' => __('Frontpage', 'bluth'),
				'pages' => __('Pages', 'bluth'),
				'posts' => __('Posts', 'bluth')
			));



	$options[] = array(
		'name' => __('Custom CSS', 'bluth'),
		'type' => 'heading');

		$options[] = array(
			'name' => __('Add Custom CSS rules here', 'bluth'),
			'desc' => __('Here you can overwrite specific css rules if you want to customize your theme a little. Write into this box just like you would do in a regular css file. Example: body{ color: #444; }', 'bluth'),
			'id' => 'custom_css',
			'class' => 'custom_css',
			'std' => '',
			'type' => 'textarea');



	return $options;
}