<?php


	// Meta box
	add_action( 'add_meta_boxes', 'cd_layout_meta' );  
	function cd_layout_meta()  
	{  
	    add_meta_box( 'bluth_post_meta_social', 'Social Status', 'bluth_post_meta_social', 'post', 'normal', 'high' );   
	    add_meta_box( 'bluth_post_meta', 'Page Layout', 'bluth_post_meta', 'post', 'normal', 'high' );   
	    add_meta_box( 'bluth_page_meta', 'Page Options', 'bluth_page_meta', 'page', 'side', 'high' );   
	}  
  
	function bluth_post_meta( $post )  
	{  
	    $layout = get_post_meta( $post->ID, 'bluth_post_layout', true );  
	      
	    if( empty( $layout ) ) $layout = 'right_side';  
	      
	    $dir = get_template_directory_uri().'/assets/img/';  
	    wp_nonce_field( 'save_post_layout', 'layout_nonce' );  
	    ?>  
	    <fieldset class="clearfix">  
	    <div class="cd-layout"> 
	        <input type="radio" id="sidebar-left" name="bluth_post_layout" value="left_side" <?php checked( $layout, 'left_side' ); ?> /> 
	        <label for="sidebar-left"> 
	            <img src="<?php echo $dir; ?>2cl.png" alt="sidebar then content" /> 
	            <span>Sidebar on the left</span> 
	        </label> 
	    </div> 
	    <div class="cd-layout"> 
	        <input type="radio" id="sidebar-default" name="bluth_post_layout" value="single_column" <?php checked( $layout, 'single_column' ); ?> /> 
	        <label for="sidebar-default"> 
	            <img src="<?php echo $dir; ?>1col.png" alt="Use the Default Sidebar" /> 
	            <span>Single column</span> 
	        </label> 
	    </div> 
	    <div class="cd-layout"> 
	        <input type="radio" id="sidebar-right" name="bluth_post_layout" value="right_side" <?php checked( $layout, 'right_side' ); ?> /> 
	        <label for="sidebar-right"> 
	            <img src="<?php echo $dir; ?>2cr.png" alt="content then sidebar" /> 
	            <span>Sidebar on the right</span> 
	        </label> 
	    </div> 
	    </fieldset> 
	    <?php 
	} 
	function bluth_post_meta_social( $post )  
	{  
	    $facebook_status = get_post_meta( $post->ID, 'bluth_facebook_status', true );  
	    $twitter_status = get_post_meta( $post->ID, 'bluth_twitter_status', true );  

	    wp_nonce_field( 'save_post_layout', 'layout_nonce' );  
	    ?>  
	    <fieldset class="clearfix bl_status_show"> 
		    <div class="cd-layout" style="width:45%;"> 
		        <label for="facebook-status"> 
		            <h4 style="border-bottom:1px solid #cccccc; margin:25px 0; padding-bottom:10px; width:95%;">Facebook embedded status ( <a href="https://developers.facebook.com/docs/plugins/embedded-posts/" target="_blank">How do I do this?</a> )</h4>  
		        </label> 
		    	<textarea id="facebook-status" name="bluth_facebook_status" value="" style="width:95%"><?php echo $facebook_status; ?></textarea>
		    </div>
		    <div class="cd-layout" style="width:45%;"> 
		        <label for="twitter-status"> 
		            <h4 style="border-bottom:1px solid #cccccc; margin:25px 0; padding-bottom:10px; width:95%;">Twitter embedded status ( <a href="https://dev.twitter.com/docs/embedded-tweets" target="_blank">How do I do this?</a> )</h4>
		        </label> 
		    	<textarea id="twitter-status" name="bluth_twitter_status" value="" style="width:95%"><?php echo $twitter_status; ?></textarea>
		    </div>
		</fieldset> 	    
	    <?php 
	} 
  
	function bluth_page_meta( $post )  
	{  
	    global $post;  
	    $values = get_post_custom( $post->ID );  
	    $bluth_page_hide_author = isset( $values['bluth_page_hide_author'][0] ) ? esc_attr( $values['bluth_page_hide_author'][0] ) : '';  
	    $bluth_page_hide_title 	= isset( $values['bluth_page_hide_title'][0] ) ? esc_attr( $values['bluth_page_hide_title'][0] ) : '';  
	      
	    wp_nonce_field( 'bluth_page_meta_nounce', 'page_meta_nounce' ); 
	    ?> 
	    <p> 
	        <input type="checkbox" id="bluth_page_hide_author" name="bluth_page_hide_author" <?php checked( $bluth_page_hide_author, 'on' ); ?> />  
	        <label for="bluth_page_hide_author">Hide author footer</label>  
	    </p> 
	    <p> 
	        <input type="checkbox" id="bluth_page_hide_title" name="bluth_page_hide_title" <?php checked( $bluth_page_hide_title, 'on' ); ?> />  
	        <label for="bluth_page_hide_title">Hide page title</label>  
	    </p>  
	    <?php      
	}  

	 
	add_action( 'save_post', 'bluth_post_meta_save' ); 
	function bluth_post_meta_save( $id ) 
	{ 
	    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return; 
	    if( !isset( $_POST['layout_nonce'] ) || !wp_verify_nonce( $_POST['layout_nonce'], 'save_post_layout' ) ) return; 
	    if( !current_user_can( 'edit_post' ) ) return; 
	     
	    if( isset( $_POST['bluth_post_layout'] ) ) 
	        update_post_meta( $id, 'bluth_post_layout', esc_attr( strip_tags( $_POST['bluth_post_layout'] ) ) ); 

	    if( isset( $_POST['bluth_facebook_status'] ) ) 
	        update_post_meta( $id, 'bluth_facebook_status', esc_attr(  $_POST['bluth_facebook_status'] ) );

	    if( isset( $_POST['bluth_twitter_status'] ) ) 
	        update_post_meta( $id, 'bluth_twitter_status', esc_attr( $_POST['bluth_twitter_status'] ) );

	} 


	add_action( 'save_post', 'bluth_page_meta_save' ); 
	function bluth_page_meta_save( $id ) 
	{ 
	    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return; 
	    if( !isset( $_POST['page_meta_nounce'] ) || !wp_verify_nonce( $_POST['page_meta_nounce'], 'bluth_page_meta_nounce' ) ) return; 
	    if( !current_user_can( 'edit_post' ) ) return; 
	   
	 	$chk = isset( $_POST['bluth_page_hide_author'] ) ? 'on' : 'off';  
	    update_post_meta( $id, 'bluth_page_hide_author', $chk ); 

	 	$chk = isset( $_POST['bluth_page_hide_title'] ) ? 'on' : 'off';  
	    update_post_meta( $id, 'bluth_page_hide_title', $chk );  
	}  

	add_action( 'admin_print_styles-post.php', 'cd_layout_enqueue' );  
	add_action( 'admin_print_styles-post-new.php', 'cd_layout_enqueue' );  
	function cd_layout_enqueue()  
	{  
	    wp_enqueue_style( 'cdlayout-style', get_template_directory_uri() . '/assets/css/style-admin.css', array(), NULL, 'all' );   
	}  