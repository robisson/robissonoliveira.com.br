<?php
/*
Plugin Name: bl google ads
Description: display your ads here
Author: Ivar Rafn
Version: 1
Author URI: http://www.bluth.is/
*/
class bl_google_ads extends WP_Widget{


  function bl_google_ads(){
    $widget_ops = array('classname' => 'bl_google_ads', 'description' => 'Displays google ads' );
    $this->WP_Widget('bl_google_ads', 'Bluth Google Ads', $widget_ops);
  }
 
  function form($instance){

    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = !empty($instance['title']) ? $instance['title'] : '';
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>">Title (optional)</label><br>
      <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>">
    </p>
    <strong>Instructions</strong>
    <ol>
      <li>Set up your Google Ads information in the Theme Options</li>
      <li>Put this widget where you want the ads to appear</li>
    </ol><?php
  }
 
  function update($new_instance, $old_instance){
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    return $instance;
  }
 
  function widget($args, $instance){

    extract($args, EXTR_SKIP);
    $title = apply_filters('widget_title', $instance['title'] );

    echo $before_widget;
    echo !empty($title) ? $before_title . $title . $after_title : ''; ?>
      <div class="widget-body"><?php

        $publisher_id = of_get_option('google_publisher_id', false);
        $ad_unit_id   = of_get_option('google_ad_unit_id', false);

        if(!$publisher_id or !$ad_unit_id){
          echo 'Please set up your Google Ads information in the Theme Options > Advertising';
        }else{
          blu_get_google_ads(); 
        }
      ?>
      </div><?php
    echo $after_widget;
  }
}
add_action( 'widgets_init', create_function('', 'return register_widget("bl_google_ads");') );