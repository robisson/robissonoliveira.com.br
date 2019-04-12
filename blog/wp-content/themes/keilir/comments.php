<?php

    if ( post_password_required() )
        return;

    if(of_get_option('facebook_app_id') and of_get_option('facebook_comments')){
        echo '<div id="comments" class="comments-area comments-area-facebook">';
        echo '<div class="fb-comments" data-href="'.get_permalink().'" data-width="470" data-num-posts="10"></div>';
        echo '</div>';
    }else{
?>
    <div id="comments" class="comments-area">
     
        <?php if(have_comments()){ ?>
            <h3 class="comments-title">
                <i class="icon-comment-empty"></i>&nbsp;
                <?php comments_number( __('No Comments', 'bluth'), '1 '.__('Comment'), '%'.__('Comments', 'bluth') ); ?>
            </h3>
     
            <ol class="commentlist">
                <?php wp_list_comments( array( 'callback' => 'keilir_comment' ) ); ?>
            </ol><!-- .commentlist -->
     
            <?php 
            // are there comments to navigate through? If so, show navigation
            if(get_comment_pages_count() > 1 && get_option('page_comments')){  ?>
                <nav role="navigation" id="comment-nav-below" class="site-navigation comment-navigation clearfix">
                    <div class="nav-previous"><i class="icon-left-open"></i>&nbsp;<?php echo get_previous_comments_link( __( 'Older Comments', 'bluth' ) ); ?></div>
                    <div class="nav-next"><?php echo get_next_comments_link( __( 'Newer Comments', 'bluth' ) ); ?>&nbsp;<i class="icon-right-open"></i></div>
                </nav><!-- #comment-nav-below .site-navigation .comment-navigation -->
            <?php } ?>
        <?php } // have_comments() ?>
     

        <?php if ( !comments_open() ){ ?>
            <h4 class="nocomments text-center"><?php _e( 'Comments are closed.', 'bluth' ); ?></h4>
        <?php } ?>

     
        <?php comment_form(); ?>

    </div><!-- #comments .comments-area -->
<?php } ?> 