<?php
/*
Plugin Name: bl Socialbox
Description: Box with links to social sites like facebook and google+
Author: Ivar Rafn
Version: 1
Author URI: http://www.bluth.is/
*/
class bl_socialbox extends WP_Widget
{
  function bl_socialbox(){
    $widget_ops = array('classname' => 'bl_socialbox', 'description' => 'Displays links to social networks in a stylish manner' );
    $this->WP_Widget('bl_socialbox', 'Bluth Socialbox', $widget_ops);
  }
 
  function form($instance){

    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    

    $title 			= !empty($instance['title']) ? $instance['title'] : '';
    $facebook 		= !empty($instance['facebook']) ? $instance['facebook'] : '';
    $twitter 		= !empty($instance['twitter']) ? $instance['twitter'] : '';
    $googleplus 	= !empty($instance['googleplus']) ? $instance['googleplus'] : '';
    $linkedin 		= !empty($instance['linkedin']) ? $instance['linkedin'] : '';
    $youtube 		= !empty($instance['youtube']) ? $instance['youtube'] : '';
    $rss 			= !empty($instance['rss']) ? $instance['rss'] : '';
    $flickr 		= !empty($instance['flickr']) ? $instance['flickr'] : '';
    $vimeo 			= !empty($instance['vimeo']) ? $instance['vimeo'] : '';
    $pinterest 		= !empty($instance['pinterest']) ? $instance['pinterest'] : '';
    $dribbble 		= !empty($instance['dribbble']) ? $instance['dribbble'] : '';
    $tumblr 		= !empty($instance['tumblr']) ? $instance['tumblr'] : '';
    $instagram 		= !empty($instance['instagram']) ? $instance['instagram'] : '';
    $soundcloud 	= !empty($instance['soundcloud']) ? $instance['soundcloud'] : '';
	?>
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>">Title</label><br>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>">
	</p>
	<hr style="border:none;height:1px;background:#BFBFBF">
	<p>Insert the URL's to your social networks</p>
	<p>
		<label for="<?php echo $this->get_field_id('facebook'); ?>">Facebook</label><br>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id('facebook'); ?>" name="<?php echo $this->get_field_name('facebook'); ?>" value="<?php echo $facebook; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('twitter'); ?>">Twitter</label><br>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id('twitter'); ?>" name="<?php echo $this->get_field_name('twitter'); ?>" value="<?php echo $twitter; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('googleplus'); ?>">Google+</label><br>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id('googleplus'); ?>" name="<?php echo $this->get_field_name('googleplus'); ?>" value="<?php echo $googleplus; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('linkedin'); ?>">LinkedIn</label><br>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id('linkedin'); ?>" name="<?php echo $this->get_field_name('linkedin'); ?>" value="<?php echo $linkedin; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('youtube'); ?>">Youtube</label><br>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id('youtube'); ?>" name="<?php echo $this->get_field_name('youtube'); ?>" value="<?php echo $youtube; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('rss'); ?>">RSS</label><br>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id('rss'); ?>" name="<?php echo $this->get_field_name('rss'); ?>" value="<?php echo $rss; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('flickr'); ?>">Flickr</label><br>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id('flickr'); ?>" name="<?php echo $this->get_field_name('flickr'); ?>" value="<?php echo $flickr; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('vimeo'); ?>">Vimeo</label><br>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id('vimeo'); ?>" name="<?php echo $this->get_field_name('vimeo'); ?>" value="<?php echo $vimeo; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('pinterest'); ?>">Pinterest</label><br>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id('pinterest'); ?>" name="<?php echo $this->get_field_name('pinterest'); ?>" value="<?php echo $pinterest; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('dribbble'); ?>">Dribbble</label><br>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id('dribbble'); ?>" name="<?php echo $this->get_field_name('dribbble'); ?>" value="<?php echo $dribbble; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('tumblr'); ?>">Tumblr</label><br>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id('tumblr'); ?>" name="<?php echo $this->get_field_name('tumblr'); ?>" value="<?php echo $tumblr; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('instagram'); ?>">Instagram</label><br>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id('instagram'); ?>" name="<?php echo $this->get_field_name('instagram'); ?>" value="<?php echo $instagram; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('soundcloud'); ?>">Soundcloud</label><br>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id('soundcloud'); ?>" name="<?php echo $this->get_field_name('soundcloud'); ?>" value="<?php echo $soundcloud; ?>">
	</p>

	<?php
  }
 
  function update($new_instance, $old_instance){

    $instance = $old_instance;
    $instance['title'] 			= strip_tags($new_instance['title']);
    $instance['facebook'] 		= esc_url($new_instance['facebook']);
    $instance['twitter'] 		= esc_url($new_instance['twitter']);
    $instance['googleplus'] 	= esc_url($new_instance['googleplus']);
    $instance['linkedin'] 		= esc_url($new_instance['linkedin']);
    $instance['youtube'] 		= esc_url($new_instance['youtube']);
    $instance['rss'] 			= esc_url($new_instance['rss']);
    $instance['flickr'] 		= esc_url($new_instance['flickr']);
    $instance['vimeo'] 			= esc_url($new_instance['vimeo']);
    $instance['pinterest'] 		= esc_url($new_instance['pinterest']);
    $instance['dribbble'] 		= esc_url($new_instance['dribbble']);
    $instance['tumblr'] 		= esc_url($new_instance['tumblr']);
    $instance['instagram'] 		= esc_url($new_instance['instagram']);
    $instance['soundcloud'] 	= esc_url($new_instance['soundcloud']);
    return $instance;
  }
 
  function widget($args, $instance){

    extract($args, EXTR_SKIP);

    echo $before_widget; 
    ?>
  		<?php echo !empty($instance['title']) ? '<h3 class="widget-head">'.$instance['title'].'</h3>' : '' ?>
    	<div class="widget-body">
    		<ul class="clearfix">
	    	<?php echo !empty($instance['facebook']) 	? '<li><a target="_blank" data-title="Facebook" class="tips bl_icon_facebook" href="'.$instance['facebook'].'"><i class="icon-facebook-1"></i></a></li>' : '' ?>
	    	<?php echo !empty($instance['twitter']) 	? '<li><a target="_blank" data-title="Twitter" class="tips bl_icon_twitter" href="'.$instance['twitter'].'"><i class="icon-twitter-1"></i></a></li>' : '' ?>
	    	<?php echo !empty($instance['googleplus']) 	? '<li><a target="_blank" data-title="Google+" class="tips bl_icon_googleplus" href="'.$instance['googleplus'].'"><i class="icon-gplus-1"></i></a></li>' : '' ?>
	    	<?php echo !empty($instance['linkedin']) 	? '<li><a target="_blank" data-title="Linkedin" class="tips bl_icon_linkedin" href="'.$instance['linkedin'].'"><i class="icon-linkedin-1"></i></a></li>' : '' ?>
	    	<?php echo !empty($instance['youtube']) 	? '<li><a target="_blank" data-title="Youtube" class="tips bl_icon_youtube" href="'.$instance['youtube'].'"><i class="icon-youtube"></i></a></li>' : '' ?>
	    	<?php echo !empty($instance['rss']) 		? '<li><a target="_blank" data-title="Rss" class="tips bl_icon_rss" href="'.$instance['rss'].'"><i class="icon-rss-1"></i></a></li>' : '' ?>
	    	<?php echo !empty($instance['flickr']) 		? '<li><a target="_blank" data-title="Flickr" class="tips bl_icon_flickr" href="'.$instance['flickr'].'"><i class="icon-flickr-1"></i></a></li>' : '' ?>
	    	<?php echo !empty($instance['vimeo']) 		? '<li><a target="_blank" data-title="Vimeo" class="tips bl_icon_vimeo" href="'.$instance['vimeo'].'"><i class="icon-vimeo-rect"></i></a></li>' : '' ?>
	    	<?php echo !empty($instance['pinterest']) 	? '<li><a target="_blank" data-title="Pinterest" class="tips bl_icon_pinterest" href="'.$instance['pinterest'].'"><i class="icon-pinterest-circled-1"></i></a></li>' : '' ?>
	    	<?php echo !empty($instance['dribbble']) 	? '<li><a target="_blank" data-title="Dribbble" class="tips bl_icon_dribbble" href="'.$instance['dribbble'].'"><i class="icon-dribbble-1"></i></a></li>' : '' ?>
	    	<?php echo !empty($instance['tumblr']) 		? '<li><a target="_blank" data-title="Tumblr" class="tips bl_icon_tumblr" href="'.$instance['tumblr'].'"><i class="icon-tumblr-rect"></i></a></li>' : '' ?>
	    	<?php echo !empty($instance['instagram']) 	? '<li><a target="_blank" data-title="Instagram" class="tips bl_icon_instagram" href="'.$instance['instagram'].'"><i class="icon-instagram-filled"></i></a></li>' : '' ?>
	    	<?php echo !empty($instance['soundcloud']) 	? '<li><a target="_blank" data-title="Soundcloud" class="tips bl_icon_soundcloud" href="'.$instance['soundcloud'].'"><i class="icon-soundcloud-1"></i></a></li>' : '' ?>
    		</ul>
    	</div>
    <?php
	echo $after_widget;
  }
}
add_action( 'widgets_init', create_function('', 'return register_widget("bl_socialbox");') );