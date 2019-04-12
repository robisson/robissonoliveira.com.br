<?php
/*
Plugin Name: bl Instagram
Description: Box with your recent tweets
Author: Ivar Rafn
Version: 1
Author URI: http://www.bluth.is/
*/
class bl_instagram extends WP_Widget
{
  function bl_instagram(){
    $widget_ops = array('classname' => 'bl_instagram', 'description' => 'Displays recent instagram images in a widget. Recommended for the sidebar.' );
    $this->WP_Widget('bl_instagram', 'Bluth Instagram', $widget_ops);
  }
 
  function form($instance){

    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    
    $title        = !empty($instance['title']) ? $instance['title'] : '';
    $access_token = !empty($instance['access_token']) ? $instance['access_token'] : '';
    $user_id      = !empty($instance['user_id']) ? $instance['user_id'] : '';

  ?>
  <p>
    <label for="<?php echo $this->get_field_id('title'); ?>">Title</label><br>
    <input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>">
  </p>
  <strong>Instructions</strong>
  <p>You need to authenticate yourself to our instagram app to get an access token to retrieve your images and display them on your page.</p>
  <ol>
    <li>Attain your access token and user id <a href="https://api.instagram.com/oauth/authorize/?client_id=e802ca96dd27470f9ef0271bc9f0e6a3&redirect_uri=http://www.bluth.is/&response_type=code" target="_blank">by clicking here</a></li>
    <li>Copy the access token and user id</li>
    <li>Paste them in the input box below</li>
  </ol>
  <p>Not recommended in the footer area because of sizing issues.</p>
    <p>
    <label for="<?php echo $this->get_field_id('access_token'); ?>">Accee token</label><br>
    <input class="widefat" type="text" id="<?php echo $this->get_field_id('access_token'); ?>" name="<?php echo $this->get_field_name('access_token'); ?>" value="<?php echo $access_token; ?>">
  </p>
    <p>
    <label for="<?php echo $this->get_field_id('user_id'); ?>">User id</label><br>
    <input class="widefat" type="text" id="<?php echo $this->get_field_id('user_id'); ?>" name="<?php echo $this->get_field_name('user_id'); ?>" value="<?php echo $user_id; ?>">
  </p>
  <?php
  }
 
  function update($new_instance, $old_instance){

    $instance = $old_instance;
    $instance['title']          = strip_tags($new_instance['title']);
    $instance['access_token']   = strip_tags($new_instance['access_token']);
    $instance['user_id']        = strip_tags($new_instance['user_id']);
    return $instance;
  }
 
  function widget($args, $instance){

    extract($args, EXTR_SKIP);

	echo $before_widget;
    ?>
	  <?php echo !empty($instance['title']) ? '<h3 class="widget-head"><i class="icon-instagram-filled"></i> '.$instance['title'].'</h3>' : '' ?>
      <div class="widget-body">
	    <?php

		    	if(!function_exists('curl_version')){
					echo '<div class="pad15 alert alert-error" style="margin:0"><h4>cURL extension needs to be enabled on your server.</h4>';
					echo '<p>Try removing the \';\' from extension=php_curl.dll in php.ini. If that doesn\'t work contact your hosting provider and let them help you out.</p></div>';
		    	}
	    		elseif(empty($instance['user_id'])){
					echo '<div class="pad15 alert alert-error" style="margin:0"><h4>Instagram user id not set</h4>';
					echo '<p>Please add your user id in the input field for the widget</p></div>';  			
	    		}
	    		elseif(empty($instance['access_token'])){
					echo '<div class="pad15 alert alert-error" style="margin:0"><h4>Instagram access token not set</h4>';
					echo '<p>Please add your access token in the input field for the widget</p></div>';     			
	    		}else{

				    if(($cache = wp_cache_get('bl_instagram', 'bluth')) === false){

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_TIMEOUT, 20);

						// get recent instagram posts
						curl_setopt($ch, CURLOPT_URL, "https://api.instagram.com/v1/users/".$instance['user_id']."/media/recent/?access_token=".$instance['access_token']);
						$posts_data = curl_exec($ch);

						// get follows, following and total images
						curl_setopt($ch, CURLOPT_URL, "https://api.instagram.com/v1/users/".$instance['user_id']."?access_token=".$instance['access_token']);
						$user_data = curl_exec($ch);
						
						if(curl_errno($ch)){
							$curl_error = '<div class="pad15 alert alert-error" style="margin:0"><h4>Error</h4><p>'.curl_error($ch).'</p></div>'; 						    
						}

						curl_close($ch);

						$posts 	= json_decode($posts_data);
						$user 	= json_decode($user_data);

						if(!isset($posts->meta->error_message) or !isset($user->meta->error_message)){
							// save to cache for 6 hours
					        wp_cache_set( 'bl_instagram', array('posts' => $posts_data, 'user' => $user_data), 'bluth', 21600);
						}else{
							wp_cache_delete( 'bl_instagram', 'bluth' );
						}

				    }else{
						
						$posts 	= json_decode($cache['posts']);
						$user 	= json_decode($cache['user']);
				    }


						$interactions = array();
						if(isset($curl_error)){
							echo $curl_error;
						}
						elseif(isset($posts->meta->error_message)){
							echo '<div class="pad15 alert alert-error" style="margin:0"><h4>Error</h4>';
							echo '<p>'.$posts->meta->error_message.'</p></div>';     
						}
						else if(isset($user->meta->error_message)){
							echo '<div class="pad15 alert alert-error" style="margin:0"><h4>Error</h4>';
							echo '<p>'.$user->meta->error_message.'</p></div>';
						}
						else{ ?>
						<ul class="instagram-header">	
							<li>
								<small class="muted"><?php _e('Followers', 'bluth'); ?></small>
								<p><?php echo $user->data->counts->followed_by ?></p>
							</li>	
							<li>
								<small class="muted"><?php _e('Following', 'bluth'); ?></small>
								<p><?php echo $user->data->counts->follows ?></p>
							</li>	
							<li>
								<small class="muted"><?php _e('Images', 'bluth'); ?></small>
								<p><?php echo $user->data->counts->media ?></p>
							</li>
						</ul>	
						<div class="instagram-images-container">
						<ul class="instagram-images clearfix">
						<?php foreach ($posts->data as $post) { ?>

							<li>
								<img src="<?php echo $post->images->low_resolution->url ?>">
							</li>

						<?php
							$interactions[] = array('likes' => $post->likes->count, 'comments' => $post->comments->count, 'link' => $post->link);
						 } ?>
						</ul>
						</div>
						<ul class="instagram-interactions">
							<li class="instagram-comments"><i class="icon-comment-1"></i> <span><?php echo $interactions[0]['comments'] ?></span></li>
							<li class="instagram-likes"><i class="icon-heart-1"></i> <span><?php echo $interactions[0]['likes'] ?></span></li>
						</ul>
						<div class="instagram_link visible-desktop"><a href="<?php echo $interactions[0]['link'] ?>" target="_blank"><i class="icon-link"></i></a></div>
						<div class="left_arrow instagram_arrow visible-desktop"><i class="icon-left-open-1"></i></div>
						<div class="right_arrow instagram_arrow visible-desktop"><i class="icon-right-open-1"></i></div>
						<?php } ?>
			<?php } ?>
      </div>
      <?php if(isset($interactions)){ ?>
      <script type="text/javascript">
      		var instagram_array = {<?php 
      			$i = 1;
      			foreach ($interactions as $image) {
      				echo '"image_'.$i.'": {"likes": '.$image['likes'].',"comments": '.$image['comments'].',"link": "'.$image['link'].'"},';
      				$i++;
      			}
      		?>},
      		total_images = <?php echo count($interactions); ?>,
      		li_width = jQuery('.instagram-images > li').first().width(),
      		li_index = 1;

      		jQuery('.instagram_arrow').click(function(){

      			if(jQuery(this).hasClass('left_arrow') && li_index > 1){

	      				jQuery('.instagram-images').animate({left: '+='+li_width}, 150, 'swing');
	      				li_index--;

	      				jQuery('.instagram-interactions .instagram-comments span').fadeOut('fast', function(){
	      					jQuery(this).text(instagram_array['image_'+li_index].comments).fadeIn('fast');
	      				});

	      				jQuery('.instagram-interactions .instagram-likes span').fadeOut('fast', function(){
	      					jQuery(this).text(instagram_array['image_'+li_index].likes).fadeIn('fast');
	      				});
	      				jQuery('.instagram_link a').attr('href', instagram_array['image_'+li_index].link)

      			}else if(jQuery(this).hasClass('right_arrow') && li_index < total_images){

	      				jQuery('.instagram-images').animate({left: '-='+li_width}, 150, 'swing');
	      				li_index++;

	      				jQuery('.instagram-interactions .instagram-comments span').fadeOut('fast', function(){
	      					jQuery(this).text(instagram_array['image_'+li_index].comments).fadeIn('fast');
	      				});
	      				jQuery('.instagram-interactions .instagram-likes span').fadeOut('fast', function(){
	      					jQuery(this).text(instagram_array['image_'+li_index].likes).fadeIn('fast');
	      				});
	      				jQuery('.instagram_link a').attr('href', instagram_array['image_'+li_index].link)
      			}
      		});
      </script>
    <?php }
	echo $after_widget;
  }
}
add_action( 'widgets_init', create_function('', 'return register_widget("bl_instagram");') );