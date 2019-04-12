<?php
/*
Plugin Name: bl flickr
Description: Displays flickr images in a nice box carousel
Author: Ivar Rafn
Version: 1
Author URI: http://www.bluth.is/
*/

class bl_flickr extends WP_Widget
{ 
	
	function bl_flickr() {

	    $widget_ops = array('classname' => 'bl_flickr', 'description' => 'Displays recent instagram images in a widget' );
	    $this->WP_Widget('bl_flickr', 'Bluth Flickr', $widget_ops);		
	}
	
	function form($instance) {
		
		$defaults = array( 'title' => 'Flickr Widget', 'num_img' => '8', 'user_id' => '42373292@N00' ); // Default Values
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

	  <strong>Instructions</strong> 
	  <p>You need to get the user id from your Flickr account.</p>
	  <ol>
	    <li>Attain your user id using <a href="http://idgettr.com/" target="_blank">this tool</a></li>
	    <li>Copy the user id</li>
	    <li>Paste it in the input box below</li>
	  </ol>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
        <p>
			<label for="<?php echo $this->get_field_id( 'user_id' ); ?>">Flickr User ID:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'user_id' ); ?>" name="<?php echo $this->get_field_name( 'user_id' ); ?>" value="<?php echo $instance['user_id']; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'num_img' ); ?>">Number of Photos (max:10)</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'num_img' ); ?>" name="<?php echo $this->get_field_name( 'num_img' ); ?>" value="<?php echo $instance['num_img']; ?>" />
		</p>
		<?php
	}
	
	function update( $new_instance, $old_instance ) {  
		
		$instance = $old_instance; 
		$instance['title'] 		= strip_tags( $new_instance['title'] );
		$instance['user_id'] 	= strip_tags( $new_instance['user_id'] );
		$instance['num_img'] 	= strip_tags( $new_instance['num_img'] );
		return $instance;
	}	

	function widget($args, $instance) {
		extract($args);
		$title 		= apply_filters('widget_title', $instance['title']);
		$user_id 	= $instance['user_id'];
		$num_img 	= $instance['num_img'];
		
		echo $before_widget;
		echo !empty($title) ? '<h3 class="widget-head"><i class="icon-flickr-1"></i> '.$title.'</h3>' : '';
		echo '<div id="flickr_tab" class="clearfix">';
		echo '<script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count='.$num_img.'&display=latest&size=s&layout=x&source=user&user='.$user_id.'"></script>';
		echo '</div>';
		echo $after_widget;
	}

}
add_action( 'widgets_init', create_function('', 'return register_widget("bl_flickr");') );
?>