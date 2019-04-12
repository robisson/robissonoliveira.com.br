<?php
/*
Plugin Name: Bluthemes Author Widget
Description: Show authors of your blog
Author: Bluthemes
Version: 1
Author URI: http://www.bluthemes.com/
*/
class blu_author extends WP_Widget {

	function blu_author(){
		$widget_ops = array('classname' => 'blu_author', 'description' => 'Disaply the blogs authors' );
		$this->WP_Widget('blu_author', 'Bluthemes - Author', $widget_ops);
	}

	function form($instance){

		$instance = wp_parse_args((array)$instance, array(
			'title' => '',
			'author_layout' => 'multi',
			'single_author_id' => '',
		));
		
		$title = strip_tags($instance['title']);
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'bluth_admin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
	  	<p>
	    	<label for="<?php echo $this->get_field_id('author_layout'); ?>">Layout (single author or multiple)</label><br>
	    	<select class="author_layout" id="<?php echo $this->get_field_id('author_layout'); ?>" name="<?php echo $this->get_field_name('author_layout'); ?>" style="width: 100%;">
	      		<option value="multi" <?php selected($instance['author_layout'], 'multi') ?>>Show a list of authors</option>
		      	<option value="single" <?php selected($instance['author_layout'], 'single') ?>>Show a single author</option>
	    	</select>
  		</p>
  		<div id="blu_author_single" data-field-id="<?php echo $this->get_field_id('author_layout'); ?>"<?php echo $instance['author_layout'] == 'multi' ? ' class="hidden"' : '' ?>>
  			<hr>
  			<p><strong>Options for single author layout</strong></p>
			<p>
				<label for="<?php echo $this->get_field_id('single_author_id'); ?>">Select Author:</label><br />
		    	<select id="<?php echo $this->get_field_id('single_author_id'); ?>" name="<?php echo $this->get_field_name('single_author_id'); ?>" style="width: 100%;"><?php 

					$users = get_users( array('who' => 'authors') );

					foreach($users as $user){

						echo '<option value="'.$user->ID.'"'.($instance['single_author_id'] == $user->ID ? ' selected="selected"' : '').'>'.$user->user_nicename.'</option>';
					}
				?>
				</select>
			</p>
  		</div>
  		<script type="text/javascript">
  			jQuery(function($){
  				$('.author_layout').unbind('change').change(function(){
  					console.log(1);
  					if($(this).find('option:selected').val() == 'single'){
  						$('#blu_author_single[data-field-id="'+$(this).attr('id')+'"]').removeClass('hidden');
  					}else{
  						$('#blu_author_single[data-field-id="'+$(this).attr('id')+'"]').addClass('hidden');
  					}
  				});
  			});
  		</script><?php
	}

	function update($new_instance, $old_instance){

		$instance = $old_instance;
		$instance['title'] 				= strip_tags($new_instance['title']);
		$instance['author_layout']    	= in_array($new_instance['author_layout'], array('multi','single')) ? $new_instance['author_layout'] : 'multi';
		$instance['single_author_id'] 	= is_numeric($new_instance['single_author_id']) ? $new_instance['single_author_id'] : 0;

		return $instance;
	}

	function widget($args, $instance){

		wp_enqueue_style('blu-widgets-author', get_template_directory_uri().'/framework/widgets/assets/blu-author.css');		

		extract($args);

		$title 	= apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);

		# If there was a problem saving the user id, change to multi layout
		if(!is_numeric($instance['single_author_id']) or $instance['single_author_id'] == 0){
			$instance['author_layout'] = 'multi';
		}

		# Start Widget
		echo $before_widget;
		echo !empty($title) ? $before_title.$title.$after_title : '';

		?>
		<div class="widget-body author-layout-<?php echo $instance['author_layout'] ?>"><?php
	
			switch($instance['author_layout']){
				case 'single':

					$author = get_user_by('id', $instance['single_author_id']);

					# Author Bio
					$author_bio =  of_get_option('author_bio_'.$author->ID);

						if(empty($author_bio)){
							$author_bio = get_the_author_meta('description', $author->ID);
						}

					# Background Image	
					$image_bg_uri = of_get_option('author_box_image_'.$author->ID);

					if(!empty($image_bg_uri)){
						echo '<img src="'.esc_url($image_bg_uri).'" />';
					}

					echo '<div class="blu_author_body'.(empty($image_bg_uri) ? ' blu_author_no_bg' : '').'">';

						# Avatar
						echo '<div class="blu_author_img"><img src="'.blu_get_avatar($author->ID, 'small').'"></div>';
						
						echo '<div class="blu_author_bio">';
							echo '<h3><a href="'. esc_url($author->user_url).'" title="'.sprintf( __('All posts by %s', 'bluthemes'), $author->display_name ).'">'.$author->display_name.'</a></h3>';
							echo empty($author_bio) ? '' : '<p>'.$author_bio.'</p>'; 
	   

							$social_header = array(
								'facebook'    => array('title' => 'Facebook', 'icon' => 'icon-facebook-1', 'link' => of_get_option('author_facebook_'.$author->ID)),
								'twitter'     => array('title' => 'Twitter', 'icon' => 'icon-twitter-1', 'link' => of_get_option('author_twitter_'.$author->ID)),
								'googleplus'  => array('title' => 'Google+', 'icon' => 'icon-google', 'link' => of_get_option('author_google_'.$author->ID)),
								'tumblr'      => array('title' => 'Tumblr', 'icon' => 'icon-tumblr-2', 'link' => of_get_option('author_tumblr_'.$author->ID)),
								'linkedin'    => array('title' => 'Linkedin', 'icon' => 'icon-linkedin-1', 'link' => of_get_option('author_linkedin_'.$author->ID)),
								'instagram'   => array('title' => 'Instagram', 'icon' => 'icon-instagram-2', 'link' => of_get_option('author_instagram_'.$author->ID)),
								'dribbble'    => array('title' => 'Dribbble', 'icon' => 'icon-dribbble-1', 'link' => of_get_option('author_dribbble_'.$author->ID)),
								'pinterest'   => array('title' => 'Pinterest', 'icon' => 'icon-pinterest-1', 'link' => of_get_option('author_pinterest_'.$author->ID)),
								'youtube'     => array('title' => 'Youtube', 'icon' => 'icon-youtube', 'link' => of_get_option('author_youtube_'.$author->ID)),
							);
							
							$social_links = '';	
							foreach ($social_header as $key => $value) {
								if(!empty($value['link'])){
									$social_links .= '<li><a href="'.$value['link'].'" title="'.$value['title'].'" class="blu_'.$key.'_background_hover blu_'.$key.'_border_hover" target="_blank"><i class="'.$value['icon'].'"></i></a></li>';
								}
							}

							if(!empty($social_links)){
								echo '<div class="social-links-wrap">';
									echo '<ul class="social-links clearfix">';
										echo $social_links;
									echo '</ul>';
								echo '</div>';
							} 

						echo '</div>';
					echo '</div>';
					break;

				case 'multi':
				default:

					$authors = get_users(array('who' => 'authors'));

					foreach($authors as $author){

						$latest_post = get_posts(array(
						    'author'=>$author->ID,
						    'orderby'=>'date',
						    'order'=>'desc',
						    'numberposts'=>1
						));

					echo '<article>';

						echo '<div class="author-image'.($latest_post ? '' : ' author-image-no-article').'">';
							echo '<img class="avatar avatar-96 photo" src="'.blu_get_avatar($author->ID, 'small').'">';

	            			if($latest_post){


	            				$post_format = get_post_format($latest_post[0]->ID);

									if(empty($post_format)){
										$post_format = 'standard';
									}

								if($post_format == 'status'){
									if(get_post_meta( $latest_post[0]->ID, 'bluth_facebook_status', true )){
										$post_format = 'facebook';
									}else{
										$post_format = 'twitter';
									}
								}
									
								$icon_defaults = array(
									'facebook' => 'icon-facebook-1',
									'twitter' => 'icon-twitter-1',
									'audio' => 'icon-volume-up',
									'gallery' => 'icon-picture',
									'image' => 'icon-picture-1',
									'link' => 'icon-link',
									'quote' => 'icon-quote-left',
									'video' => 'icon-videocam',
									'standard' => 'icon-calendar-3'
								);

	            				$post_image = get_the_post_thumbnail( $latest_post[0]->ID, 'thumbnail', array('class' => 'author-image-article') );

	            				if($post_image){
									echo $post_image;
	            				}else{
	            					echo '<div class="author-image-article author-image-placeholder">';
										echo '<i class="'.of_get_option($post_format.'_icon', $icon_defaults[$post_format]).'"></i>';
	            					echo '</div>';
	            				}
							}
						echo '</div>';

						echo '<div class="author-meta bl-middle-align">';	
	            			if($latest_post){
								echo '<a href="'.esc_url(get_permalink($latest_post[0]->ID)).'" title="'.sprintf( __('Continue reading: %s', 'bluthemes'), get_the_title($latest_post[0]->ID) ).'">';
									echo '<h4>'.blu_truncate( get_the_title($latest_post[0]->ID), 40, ' ').'</h4>';
								echo '</a>';
	            			}
							echo '<a class="text-muted" href="'.get_author_posts_url($author->ID).'" title="'.sprintf( __('All posts by %s', 'bluthemes'), $author->display_name ).'">'.$author->display_name.'</a>';
						echo '</div>';

					echo '</article>';
					}
					
					break;
			} ?>
		</div><?php

		echo $after_widget;
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("blu_author");') );