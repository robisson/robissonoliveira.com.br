<?php
	$categories = get_the_category();
	$separator = ' ';
	$output = '';
	if($categories){
		
		$output .= '<li><i class="icon-folder-1"></i> ';
		foreach($categories as $category) {
			$output .= '<a href="'.get_category_link( $category->term_id ).'" title="' .__('View all posts in', 'bluth'). ' '. esc_attr( $category->name).'">'.$category->cat_name.'</a>'.$separator;
		}
		$output .= '</li>';
	}
?>
<ul>
	<li><i class="icon-clock-1"></i> <time class="entry-date" datetime="<?php echo get_the_date( 'c' ); ?>"><?php echo get_the_date(); ?></time></li>
	<?php echo trim($output, $separator); ?>
	<?php 

	$hide_no_comments = of_get_option('hide_no_comments', false);

	$comments_count = wp_count_comments(get_the_ID());

	if($comments_count->approved == 0 and $hide_no_comments){

	}else{ ?>

		<li><i class="icon-comment-1"></i> <a class="comment_count" data-post="<?php echo the_ID() ?>" href="<?php the_permalink(); ?>#comments"><?php comments_number( __('No Comments', 'bluth'), '1 '.__('Comment', 'bluth'), '% '.__('Comments', 'bluth') ); ?></a></li><?php
	}

	if(has_tag()){
		the_tags('<li><i class="icon-tag-1"></i> ',', ','</li>'); 
	} ?>
</ul>
