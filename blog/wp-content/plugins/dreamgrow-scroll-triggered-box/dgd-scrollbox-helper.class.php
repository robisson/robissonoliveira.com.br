<?php

Class DgdScrollboxHelper {
    public static $defaults_old = array(
        'cookie_life' => 30,
        'trigger_height' => 80,
        'trigger_element' => '',
        'width' => 300,
        'position' => 'right',
        'include_css' => 1,
        'hide_mobile' => 1,
        'show_admin' => 1,
        'show' => array(
            'page' => 1,
            'post' => 1,
            'frontpage' => 1,
            'postspage' => 1,
        ),
        'theme' => 'default',
        'social' => array(
            'facebook' => 0,
            'twitter' => 0,
            'google' => 0,
            'pinterest' => 0,
            'stumbleupon' => 0,
            'linkedin' => 0,
        ),
        'receiver_email' => '',
    );

    public static $dgd_stb_meta_default = array (
        'trigger' => array(
            'action'=>'scroll', // (scroll|delay|scroll_delay|element)
            'scroll' => 50,     // % of page must be scrolled to trigger
            'delaytime' => 0,       // time in seconds to wait to trigger
            'element' =>'',     // element shown to trigger
            ),
        'height' => 'auto',
        'width' => 350,
        'vpos' => 'bottom',
        'hpos' => 'right',
        'include_css' => '1',
        'theme' => 'default',
        'jsCss' => array (
            'padding' => '10',
            'margin' => '10',
            'backgroundImageUrl' => '',
            'backgroundColor' => '',
            'boxShadow' => '',
            'borderColor' => '',
            'borderWidth' => '0px',
            'borderRadius' => '',
            ),
        'transition' => array (
            'effect' => 'slide',
            'from' => 'b',
            'speed' => 400,
            ),
        'social' => array(
            'facebook' => 0,
            'twitter' => 0,
            'google' => 0,
            'pinterest' => 0,
            'stumbleupon' => 0,
            'linkedin' => 0
        ),
        'lightbox' => array(
            'enabled' => null,
            'color' => '#000000',
            'opacity' => '0.7',
            'blur' => 2
        ),
        'closeImageUrl' => '',
        'hide_mobile' => '1',
        'submit_auto_close' => 5,
        'delay_auto_close' => 40,
        'hide_submitted' => '1',
        'cookieLifetime' => 1,     // days
        'receiver_email' => false,
        'thankyou' => 'You are subscribed. Thank You!',
        'widget_enabled' => '1',
        'tabhtml'=>'Subscribe!',
        'tab'=>'1',
        'keep_open' => null
    );

    public static $dgd_stb_show_meta_default = array(
        'frontpage' => '1',
        'postspage' => '1',
        'error404' => null,     // use null for isset function returning false
        'selected_pages' => array(),
        'categories' => array(),
        'tags' => array(),
        'post_types' => array ('page'=>1, 'post'=>1),
        'admin_only' => null,
    );


    public static $pop_up_variables = array(
        'height'=>'int', 
        'width'=>'int', 
        'padding'=>'int', 
        'margin'=>'int',
        'trigger'=>'int', 
        'delaytime'=>'int', 
        'scroll'=>'int', 
        //  'clicks'=>'int', 
        'showtime'=>'int', 
        'vpos'=>'text', 
        'hpos'=>'text', 
        'trans'=>'text', 
        'speed'=>'int', 
        'dir'=>'text', 
        'bgcolor'=>'text',
        'bordercolor'=>'text',
        'borderwidth'=>'int',
        'borderradius'=>'int',
        'shadow'=>'int',
        'cookie'=>'int',
        'bgimage'=>'text',
        'closeimage'=>'text',
        );

    public static function get_all_categories_ids($post_id=false) {
        if($post_id) {
            $categories=wp_get_post_categories($post_id);
        } else {
            $categories=get_categories();
        }
        $new_array=array();
        foreach($categories as $category) {
            $new_array[]=$category->cat_ID*1;
        }    
        return $new_array;
    }

    public static function get_all_tags_ids($post_id=false) {
        if($post_id) {
            $tags=wp_get_post_tags($post_id);
        } else {
            $tags = get_tags();
        }
        $new_array=array();
        foreach($tags as $tag) {
            $new_array[]=$tag->term_id;
        }    
        return $new_array;
    }

	public static function has_wpml() {
	    return function_exists('icl_object_id');
	}


    public static function get_default_language() {
        if(self::has_wpml()) {
            global $sitepress;
            return $sitepress->get_default_language();
        } 
        return false; // does not have wpml
    }


    public static function get_all_languages() {
        if(function_exists('icl_get_languages')){
            return array_keys(icl_get_languages());
        }
        return array();
    }


    public static function get_translation_languages() {
        $all_languages=self::get_all_languages();
        if(!empty($all_languages)) {
            $default = self::get_default_language();
            return array_diff($all_languages, array($default));
        }
        return array();
    }


    // 		$trid = translate($translation->product_id, $product->product_id, $translation->lang, 'product');
    public static function translate($translated_post_id, $original_post_id, $lang, $post_type, $is_post = true) {
        if(self::has_wpml()) {
            include_once( WP_PLUGIN_DIR . '/sitepress-multilingual-cms/inc/wpml-api.php' );
            // Get trid of original post    
            $element_type = $is_post ? 'post_' . $post_type : 'tax_'.$post_type;
            $trid = wpml_get_content_trid($element_type, $original_post_id);

            // Get default language
            $default_lang = self::get_default_language();

            // Associate original post and translated post
            global $wpdb;
            $wpdb->update(
                $wpdb->prefix . 'icl_translations', 
                array('trid' => $trid, 'language_code' => $lang, 'source_language_code' => $default_lang), 
                array('element_id' => $translated_post_id, 'element_type' => $element_type)
            );

            return $trid;
        }
        return false;
    }

    public static function sampleHtml($sample=0) {
        return '<h5>Sign up for our Newsletter</h5>
    <ul>
        <li>Fresh trends</li>
        <li>Cases and examples</li>
        <li>Research and statistics</li>
    </ul>
    <p>Enter your email and stay on top of things,</p>
    <form action="#" class="stbContactForm" method="post">
        <input type="text" name="Email" id="email" value="" /><input type="submit" name="Submit" class="stb-submit" value="Subscribe" />
    </form>
    <p class="stbMsgArea"></p>';
    }

}


// Set sample HTML for back and front
$sampleHtml = DgdScrollboxHelper::sampleHtml();