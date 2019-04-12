<?php
$orig_post = $post;
global $post;

	$tags = wp_get_post_tags($post->ID);
	$categories = get_the_category();

if($tags or $categories){

	$args = array(
	  'post__not_in' => array($post->ID),
	  'posts_per_page'=>4,
	  'tax_query' => array(
	        array(
	            'taxonomy' => 'post_format',
	            'field' => 'slug',
	            'terms' => array( 'post-format-link','post-format-quote','post-format-status' ),
	            'operator' => 'NOT IN'
	        )
	    )
	);


	$cat_ids = array();
	if($categories){
		foreach($categories as $category) $cat_ids[] = $category->cat_ID;
	}

	$tag_ids = array();
	if($tags){
		foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
	}

	$related_first = of_get_option('related_posts_first');

	if($related_first == 'category'){

		$args['category__in'] = $cat_ids;
		$my_query = new wp_query($args);

		if(!$my_query->have_posts() and !empty($tags)){
			unset($args['category__in']);
			$args['tag__in'] = $tag_ids;
			$my_query = new wp_query($args);
		}

	}else{

		$args['tag__in'] = $tag_ids;
		$my_query = new wp_query($args);

		if(!$my_query->have_posts() and !empty($tags)){
			unset($args['tag__in']);
			$args['category__in'] = $cat_ids;
			$my_query = new wp_query($args);
		}
	}



if($my_query->have_posts()){

    echo '<div id="related-posts" class="row-fluid">';

    while($my_query->have_posts()){

    $my_query->the_post(); 
    $post_format = get_post_format();

	$heading = '<h4><a href="'.get_permalink().'" rel="bookmark" title="'.get_the_title().'">'.get_the_title().'</a></h4>';

	echo '<div class="span3 box related-'.(empty($post_format) ? 'standard' : $post_format).'">';
	switch ($post_format) {

		case 'video':
			echo '<div class="related-header">';


		$video_embed = get_post_meta($post->ID, '_format_video_embed', true);

		if (0 === strpos($video_embed, 'http')) {
			global $wp_embed;
			$content = $wp_embed->run_shortcode('[embed]' . $video_embed . '[/embed]' ); 
			$content = preg_replace('#\<iframe(.*?)\ssrc\=\"(.*?)\"(.*?)\>#i', '<iframe$1 src="$2?wmode=opaque"$3>', $content);
			$content = preg_replace('#\<iframe(.*?)\ssrc\=\"(.*?)\?(.*?)\?(.*?)\"(.*?)\>#i', '<iframe$1 src="$2?$3&$4"$5>', $content);			
		    echo $content;
		}else{
			echo get_post_meta($post->ID, '_format_video_embed', true);
		}

			echo '</div>';
			echo '<div class="related-content">';
			echo $heading;
		break;
		case 'audio':
			echo '<div class="related-header">';
			echo get_post_meta($post->ID, '_format_audio_embed', true);
			echo '</div>';
			echo '<div class="related-content">';
			echo $heading;
		break;
		case 'gallery':
		default:
			if($images = get_children(array('post_parent' => get_the_ID(), 'post_type' => 'attachment', 'post_mime_type' => 'image' ))){ 
			
			$image = current($images);
				echo '<div class="related-header">';
				echo '<a class="related-image" href="'.get_permalink().'" rel="bookmark">'.wp_get_attachment_image($image->ID, 'small').'</a>';
				echo '</div>';
				echo '<div class="related-content">';
				echo $heading;
			}elseif(has_post_thumbnail($post->ID)){
				echo '<div class="related-header">';
				echo '<a class="related-image" href="'.get_permalink().'" rel="bookmark">'.the_post_thumbnail('small').'</a>';
				echo '</div>';
				echo '<div class="related-content">';
				echo $heading;
			}else{

				echo '<div class="related-content">';
				echo $heading;
	    	 	$exerpt = get_the_excerpt(); 
	    	 	if(strlen($exerpt) > 255){
	    	 		echo '<p>'.substr($exerpt, 0, 255).'...</p>';
	    	 	}else{
	    	 		echo '<p>'.$exerpt.'</p>';
	    	 	}
			} 
			
		break;
	}
	?>
	    </div>
	    <div class="related-footer">
		    <i class="icon-clock"></i>&nbsp;<?php the_time('M j, Y') ?>
	    </div>
	</div>
    <?php }
    echo '</div>';
}
}
$post = $orig_post;
wp_reset_query();