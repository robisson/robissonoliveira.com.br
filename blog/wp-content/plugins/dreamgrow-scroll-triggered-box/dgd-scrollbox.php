<?php
/**
Plugin Name: Scroll Triggered Box
Plugin URI: http://www.dreamgrow.com/dreamgrow-scroll-triggered-box/
Description: Scroll Triggered Box
Version: 2.3
Author: Dreamgrow Digital
Author URI: http://www.dreamgrow.com
License: GPL2
*/

if(!defined('DGDSCROLLBOXTYPE'))
    define('DGDSCROLLBOXTYPE', 'dgd_scrollbox');        // DO NOT TOUCH!

if(!defined('DGDSCROLLBOX_VERSION'))
    define('DGDSCROLLBOX_VERSION', '2.3');

require_once(plugin_dir_path(__FILE__).'dgd-scrollbox-helper.class.php');

class DgdScrollbox {
    public $html=array();
    private $output='html';
    private $post_id;
    private $post_title;
    private $permalink;

    public function __construct() {
        add_action('init', array($this, 'create_dgd_scrollbox_post_type') );
        add_action('wp_enqueue_scripts', array($this, 'enqueue_style_n_script') );
        add_shortcode('close-button', array($this, 'close_button') );
        add_action('wp_ajax_dgd_stb_form_process', array($this, 'dgd_stb_form_process'));
        add_action('wp_ajax_nopriv_dgd_stb_form_process', array($this, 'dgd_stb_form_process'));
        add_action('wp_footer',  array($this, 'do_footer'), 100);
        add_action('widgets_init', array($this, 'scrollbox_widgets_init'), 15);
        add_filter('the_content', array($this, 'add_widget_to_scrollbox_content'));
        if(is_admin()) {
            require_once(plugin_dir_path(__FILE__).'dgd-scrollbox-admin.class.php');
            new DgdScrollboxAdmin();
            register_activation_hook(__FILE__, array('DgdScrollboxAdmin', 'install') );
            register_deactivation_hook(__FILE__, array('DgdScrollboxAdmin', 'uninstall') );
        } 
    }

    function create_dgd_scrollbox_post_type() {
      register_post_type( DGDSCROLLBOXTYPE,
        array(
          'labels' => array(
            'name' => __( 'Scrollboxes' ),
            'singular_name' => __( 'Scrollbox' ),
            'add_new' => __( 'Add New Scrollbox'),
            'all_items' => __('All Scrollboxes'),
            'add_new_item' => __( 'Add new Scrollbox'),
            'edit_item' => __( 'Modify Scrollbox'),
            'not_found' => __( 'No Scrollboxes found' ),
          ),
          'public' => true,
          'hierarchical' => true,
          'has_archive' => false,
          'exclude_from_search' => true,
          'show_in_nav_menus' => false,
          'show_in_menu' => true,
          'show_ui' => true,
          'menu_icon' => 'dashicons-welcome-comments',
          'capability_type' => 'page', // to use custom template???
          'supports' =>array('title', 'editor'),
        )
      );
    }

    public function get_original_post_id() {
        global $wp_version, $post;
        if(version_compare($wp_version, '3.1', '>=')) {
            // get_queried_object() is available from WP 3.1.0
            $post1=get_queried_object();
            if (is_object($post1) && isset($post1->ID)) {
                $this->post_id = $post1->ID;
                $this->post_title = $post1->post_title;
            } else {
                // get_queried_object() does not work for first page if WP<4.1
                $this->post_id = $post->ID;
                $this->post_title = $post->post_title;                
            }
        } else {
            $this->post_id = $post->ID;
            $this->post_title = $post->post_title;
        }
    }

    public function scrollbox_widgets_init() {
        register_sidebar( array(
            'name' => 'Scrollbox',
            'id' => DGDSCROLLBOXTYPE.'_1',
            'description' => 'Dreamgrow scroll triggered box',
            'before_widget' => '<div>',
            'after_widget' => '</div>',
            'before_title' => '<h2 class="rounded">',
            'after_title' => '</h2>',
        ) );
    }

    public function dgd_stb_form_process() {
        $nonce = $_POST['stbNonce'];        
        if (!wp_verify_nonce($nonce, 'dgd_stb_nonce')) {
            die (json_encode(array('html'=>'Sorry, but you must reload this page!', 'status'=>'500')));
        }
        
        $box_id=(int)str_replace( DGDSCROLLBOXTYPE.'-', '', $_POST['Box']);
        $box_id = filter_var($box_id, FILTER_VALIDATE_INT);
        $meta=get_post_meta($box_id, 'dgd_stb', true );
        if(!isset($meta['thankyou'])) {
            $meta['thankyou']=DgdScrollboxHelper::$dgd_stb_meta_default['thankyou'];
            $meta['thankyou'].='. Box post: '.$_POST['box'].', id:'.$box_id;
        }

        if(isset($_POST['email'])) {
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        } else if (isset($_POST['Email'])) {
            $email = filter_var($_POST['Email'], FILTER_VALIDATE_EMAIL);
        } else {
            $email = $meta['receiver_email'];
        }

        $emailTo = $meta['receiver_email'] ? $meta['receiver_email'] : get_option('admin_email');
        $subject = __('Dgd Scrollbox submit on ' . get_bloginfo('name'));
        $body="Submitted values:\n";
        foreach($_POST as $name=>$value) {
            if(!in_array($name, array('action', 'stbNonce', 'submitted', 'Screen_size'))) { 
                $body.=htmlspecialchars($name).': '.htmlspecialchars($value)."\n"; 
            }
        }
        $body.="===============================\n";
        $body.='Submitted from IP: '.$_SERVER['REMOTE_ADDR']."\n";
        $body.='Used browser: '.$_SERVER['HTTP_USER_AGENT']."\n";
        $body.='Screen size: '.$_POST['Screen_size']."\n";

        $headers = 'From: ' . $emailTo . "\r\n" . 'Reply-To: ' . $email;

        wp_mail($emailTo, $subject, $body, $headers);
        die(json_encode(array('html'=>$meta['thankyou'], 'status'=>'200')));
    }

    private function get_widget_content() {
        if(is_active_sidebar(DGDSCROLLBOXTYPE.'_1')) { 
            ob_start();
            dynamic_sidebar(DGDSCROLLBOXTYPE.'_1'); 
            return ob_get_clean();
        }
        return '';
    }

    public function add_widget_to_scrollbox_content($content) {
        // This is needed for preview
        global $post;
        if ($post->post_type == DGDSCROLLBOXTYPE) {
            $meta=get_post_meta( $post->ID, 'dgd_stb', true );
            if (isset($meta['widget_enabled'])) {
                $content.=$this->get_widget_content();
            }
        }
        return $content;
    }

    private function get_html() {
        $output='';
        $widget_content=$this->get_widget_content();

        foreach($this->html as $box) {
            $output.='<div class="dgd_stb_box '.$box['theme'].'" id="'.$box['id'].'">';
            if(isset($box['show_close_button'])) $output.='<a class="dgd_stb_box_close dgd_stb_box_x" href="javascript:void(0);"> </a>';
            $output.=do_shortcode($box['html']);
            if($box['widget_enabled'] == '1') $output.=$widget_content;
            $output.='</div>'."\n";
            $output.="\n";
        }
        return $output;
    }

    private function get_scrollboxes() {
        $active_pop_ups=$this->get_matching_popups();
        $js=array();
        $widget_enabled = '0';
        if(count($active_pop_ups)>0) {
            foreach($active_pop_ups as $pop_up) {
                $meta=get_post_meta( $pop_up->ID, 'dgd_stb', true );

                // set and unset some parameters
                if(isset($meta['receiver_email']) && $meta['receiver_email']) {
                    $meta['receiver_email']='1';
                } else {
                    $meta['receiver_email']='0';
                }
                
                if(isset($meta['widget_enabled'])) {
                    $widget_enabled = '1';
                } else {
                    $widget_enabled = '0';
                }

                $meta['id'] = DGDSCROLLBOXTYPE.'-'.$pop_up->ID;
                $meta['mode'] = 'stb';
                $meta['voff'] = 0;
                $meta['hoff'] = 0;

                // post_content does not add <p> tags, must use wpautop function for that
                $html=wpautop($pop_up->post_content);
                if (isset($meta['migrated_no_css'])) {
                    $html='<div id="scrolltriggered">'.$html.'</div>';
                } 
                if($this->output=='js') {
                    $meta['html']=$html;
                } else {
                    $this->html[]=array(
                        'id'=>$meta['id'],
                        'show_close_button'=>true,
                        'theme'=>$meta['theme'],
                        'widget_enabled'=>$widget_enabled,
                        'html'=>$html,
                        // 'tabid'=>(isset($meta['tabid']) ? $meta['tabid'] : null),
                        );
                }

                if(isset($meta['tab'])) {
                    $meta['tabid']=DGDSCROLLBOXTYPE.'-'.$pop_up->ID.'-tab';
                    $tabmeta=array(
                        'id'=>$meta['tabid'],
                        'parentid'=>$meta['id'],
                        'trigger' => array(
                            'action'=>'tab',
                            ),
                        'height' => 'auto',
                        'width' => 'auto',
                        'vpos'=> (($meta['vpos']=='center' && $meta['hpos']=='center')? 'bottom' : $meta['vpos']),
                        'hpos'=> $meta['hpos'],
                        'theme' => $meta['theme'],
                        'jsCss' => array (
                            'margin' => '0',
                            'backgroundImageUrl' => '',
                            'backgroundColor' => $meta['jsCss']['backgroundColor'],
                            'boxShadow' => '0px',
                            'borderColor' => $meta['jsCss']['borderColor'],
                            'borderWidth' => ($meta['jsCss']['borderWidth']=='0px' ? '0px' : '1px'),
                            'borderRadius' => '',
                            ),
                        'transition' => array (
                            'effect' => $meta['transition']['effect'],
                            'from' => $meta['transition']['from'],
                            'speed' => $meta['transition']['speed'],
                            ),
                        'lightbox' => 0,
                        'closeImageUrl' => '',
                        'hide_mobile' => (isset($meta['hide_mobile']) ? '1' : null),
                        'submit_auto_close' => 0,
                        'delay_auto_close' => 0,
                        'hide_submitted' => 0,
                        'cookieLifetime' => -1,     // Tab will be always available
                        'receiver_email' => $meta['receiver_email'],
                        'thankyou' => $meta['thankyou'],
                        'widget_enabled' => '0',
                    );
                    $this->html[]=array(
                        'id'=>$meta['tabid'],
                        'show_close_buttone'=>false,
                        'theme'=>$meta['theme'],
                        'widget_enabled'=>'0',
                        'html'=>$meta['tabhtml'],
                    );
                    $js[]=$tabmeta;
                }
                unset($meta['tabhtml']);
                $js[]=$meta;
            }
        } 
        return $js;
    }

    /**
        Returns array of popup page objects + meta info
    */
    private function get_matching_popups() {
        $active_pop_ups=array();

        $tags=DgdScrollboxHelper::get_all_tags_ids($this->post_id);
        $categories=wp_get_post_categories( $this->post_id );
        $pop_ups = get_pages( array('post_type'=>DGDSCROLLBOXTYPE, 'post_status' => 'publish')); 
        
        // compare pop-up definition with page, 
        foreach($pop_ups as $pop_up) {

            // get showing options
            $show_on=get_post_meta($pop_up->ID, 'dgd_stb_show', true);
            $hide_on=get_post_meta($pop_up->ID, 'dgd_stb_hide', true);
            $meta=get_post_meta($pop_up->ID, 'dgd_stb', true);

            $popupcookie=$meta['cookieLifetime'];
            $clientcookie=(isset($_COOKIE[DGDSCROLLBOXTYPE.'-'.$pop_up->ID]) ? $_COOKIE[DGDSCROLLBOXTYPE.'-'.$pop_up->ID]*1 : -1);
            if ($clientcookie=='9000' && !isset($meta['tab'])) {
                // closed for ever
                continue;
            }

            // Exclusion list, has higher priority than inclusion list
            if (
                (isset($show_on['admin_only']) && !current_user_can('manage_options')) ||
                (!isset($show_on['frontpage']) && is_front_page()) ||   // is on front page but front page is not chosed
                (!isset($show_on['postspage']) && ($this->post_id == get_option('page_for_posts'))) ||
                ($popupcookie>-1 && $clientcookie>-1 && $popupcookie>=$clientcookie && !isset($meta['tab'])) // client already has same or shorter cookie than server, skip
               ) {
                continue;
               } 

            // Inclusion list
            if (
                ( 
                 (isset($show_on['post_types']) && in_array(get_post_type($this->post_id), array_keys($show_on['post_types']))) ||
                 (isset($show_on['frontpage']) && is_front_page()) ||
                 (isset($show_on['postspage']) && ($this->post_id == get_option('page_for_posts'))) ||
                 (isset($show_on['error404']) && ($this->post_id==0)) ||
                 (isset($show_on['selected_pages']) && in_array($this->post_id, array_values($show_on['selected_pages']) )) ||
                 (isset($show_on['categories']) && is_array($categories) && count(array_intersect($show_on['categories'], $categories))>0) ||
                 (isset($show_on['tags']) && is_array($tags) && count(array_intersect($show_on['tags'], $tags))>0)
                )  &&  (
                 !(isset($hide_on['selected_pages']) && in_array($this->post_id, array_values($hide_on['selected_pages']) )) &&
                 !(isset($hide_on['categories']) && is_array($categories) && count(array_intersect($hide_on['categories'], $categories))>0) &&
                 !(isset($hide_on['tags']) && is_array($tags) && count(array_intersect($hide_on['tags'], $tags))>0)
                )
               ) {
                    $active_pop_ups[]=$pop_up;
               } 
        }

        return $active_pop_ups;
    }

    public function do_footer() {
        // using HTML output gives better compatibility with other plugins
        echo "\n<!--     ===== START Dreamgrow Scroll Triggered Box ".DGDSCROLLBOX_VERSION." =====   -->\n\n";
        echo '<div class="dgd_overlay"></div>'."\n";
        echo $this->get_html();
        echo "\n<!--     ===== END OF Dreamgrow Scroll Triggered Box ".DGDSCROLLBOX_VERSION." =====   -->\n\n";
    }

    public function enqueue_style_n_script() {
        global $wp_version;

        wp_enqueue_style( 'dgd-scrollbox-plugin-core', plugins_url( 'css/style.css', __FILE__ ), array(), DGDSCROLLBOX_VERSION );  
        wp_enqueue_script( 'dgd-scrollbox-plugin', plugins_url( 'js/script.js', __FILE__ ), array('jquery'), DGDSCROLLBOX_VERSION, false );

        $this->get_original_post_id();

        $image='';
        $thumbnail=false;
        if(has_post_thumbnail($this->post_id)) {
            $image = wp_get_attachment_image_src(get_post_thumbnail_id($this->post_id), 'full');
            $thumbnail=$image[0];
        } 

        $scrollboxes_array=$this->get_scrollboxes();

        $data = array(
            'ajaxurl' => parse_url(admin_url('admin-ajax.php'), PHP_URL_PATH),
            'nonce' => wp_create_nonce('dgd_stb_nonce'),
            'debug' => (current_user_can('manage_options') ? '1' : ''),
            'permalink' => get_permalink($this->post_id),
            'title' => $this->post_title,
            'thumbnail' => $thumbnail,
            'scripthost' => parse_url(plugins_url('/',  __FILE__), PHP_URL_PATH), 
        );

        if(version_compare($wp_version, '3.3', '>=')) {
            // WP=3.3 or newer
            $data['scrollboxes']= $scrollboxes_array;
        } else {
            // WP<3.3 does not support multi-dimensional arrays in wp_localize_script
            // so we add $DGD.scrollboxes separately, without wp_localize_script encoding help
            $data['l10n_print_after'] = '$DGD.scrollboxes = ' . json_encode( $scrollboxes_array ) . ';';
        }
        wp_localize_script('dgd-scrollbox-plugin', '$DGD', $data);
    }

    public function close_button($atts) {
        $text='Close';
        if(is_array($atts) and isset($atts['text'])) {
            $text=$atts['text'];
        }
        return '<a class="dgd_stb_box_close_button">'.$text.'</a>';
    }

}

new DgdScrollbox();