<?php

class blu_posts extends WP_Widget {

	function blu_posts() {
		$this->WP_Widget('blu_posts', 'Bluthemes - Posts Widget', array('classname' => 'blu_posts', 'description' => 'Loads posts'));
	}

	function form($instance) {	


		wp_enqueue_script('suggest');

		$instance = wp_parse_args((array)$instance, array(
			'title' => '',
			'sub_title' => '',
			'post_count' => 3,
			'categories' => array(),

			'display_excerpt' => true,
			'loadmorebutton' => true,
			'tag_posts' => '',
			'orderby' => 'date',
			'order' => 'desc'
		));

		extract($instance);
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('sub_title'); ?>"><?php _e('Sub-Title:', 'bluthemes'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('sub_title'); ?>" name="<?php echo $this->get_field_name('sub_title'); ?>" type="text" value="<?php echo $sub_title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('post_count'); ?>"><?php _e('Number of posts to load:', 'bluthemes'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('post_count'); ?>" name="<?php echo $this->get_field_name('post_count'); ?>" type="text" value="<?php echo $post_count; ?>" />
		</p>


		<!-- CATEGORIES -->
		<p>
			<label for="<?php echo $this->get_field_id('categories'); ?>">
				<?php _e('Only Show Categories:', 'bluthemes'); ?>
				<small><?php _e('Hold CTRL for multiple, leave empty for all categories', 'bluth_admin'); ?></small>
			</label>
			<select class="widefat" id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>[]" multiple><?php 


				$all_categories = get_categories();

				foreach($all_categories as $category) {
				 	echo '<option value="'.$category->term_id.'" '.(in_array($category->term_id, $cat_posts) ? ' selected="selected"' : '').'>'.$category->name.'</option>';
				} ?>
			</select>
		</p>





		<p>
			<label for="<?php echo $this->get_field_id('sortby'); ?>"><?php _e( 'Sort by:' ); ?></label>
			<select name="<?php echo $this->get_field_name('sortby'); ?>" id="<?php echo $this->get_field_id('sortby'); ?>" class="widefat">
				<option value="post_title"<?php selected( $instance['sortby'], 'post_title' ); ?>><?php _e('Page title'); ?></option>
				<option value="menu_order"<?php selected( $instance['sortby'], 'menu_order' ); ?>><?php _e('Page order'); ?></option>
				<option value="ID"<?php selected( $instance['sortby'], 'ID' ); ?>><?php _e( 'Page ID' ); ?></option>
			</select>
		</p>











		<?php



	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( in_array( $new_instance['sortby'], array( 'post_title', 'menu_order', 'ID' ) ) ) {
			$instance['sortby'] = $new_instance['sortby'];
		} else {
			$instance['sortby'] = 'menu_order';
		}

		$instance['exclude'] = strip_tags( $new_instance['exclude'] );

		return $instance;
	}

	// widget display
	function widget($args, $instance) {
		/* ... */
	}
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("wp_my_plugin");'));