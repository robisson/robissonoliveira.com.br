<?php
/*
    This class creates admin interface for DreamGrow Scroll Triggered Boxes



*/
require_once(plugin_dir_path(__FILE__).'dgd-page-selector-walker.class.php');


Class DgdScrollboxAdmin {

    protected $stb_disabled_types=array(DGDSCROLLBOXTYPE, 'attachment', 'revision', 'nav_menu_item');

    // popup height
    protected $height_options = array (
        0 => true,
        );

    protected $height_select_options = array('auto'=>'auto', '60'=>'60', '80'=>'80', '100'=>'100', '120'=>'120', '140'=>'140', 
        '160'=>'160', '180'=>'180', '200'=>'200', '220'=>'220', '240'=>'240', 
        '260'=>'260', '280'=>'280', '300'=>'300', '350'=>'350', '400'=>'400', 
        '450'=>'450', '500'=>'500', '550'=>'550', '600'=>'600', '650'=>'650', '700'=>'700', '100%' => '100%');

    // popup width
    protected $width_options = array (
        0 => true,
        );
    protected $width_select_options = array('60'=>'60', '80'=>'80', '100'=>'100', '120'=>'120', '140'=>'140', 
        '160'=>'160', '180'=>'180', '200'=>'200', '220'=>'220', '240'=>'240', 
        '260'=>'260', '280'=>'280', '300'=>'300', '350'=>'350', '400'=>'400', 
        '450'=>'450', '500'=>'500', '550'=>'550', '600'=>'600', '650'=>'650', '700'=>'700', '100%' => '100%');

    protected $trigger_options = array(
        'scroll' => 'Scroll and/or Time delay',
        'element' => 'Scroll to Element',
        'click' => 'Click on Element',
        'mouseover' => 'Mouse over Element',
        );


    protected $delaytime_options = array(
        '0' => '0 sec', '1000' => '1 sec', '3000' => '3 sec', '5000' => '5 sec', '10000' => '10 sec', '20000' => '20 sec',
        '40000' => '40 sec', '60000' => '1 minute', '120000'=> '2 minutes', '180000'=> '3 minutes',
        );

    protected $scroll_options = array(
        '0' => '0 %', '10' => '10 %', '20' => '20 %', '30' => '30 %', '40' => '40 %', '50' => '50 %', '60' => '60 %',
        '70' => '70 %', '80' => '80 %', '90' => '90 %', '99' => '100 %',
        );

    protected $clicks_options = array(
        '1' =>  '1 click', '2' =>  '2 clicks', '3' =>  '3 clicks', '5' =>  '5 clicks', '8' =>  '8 clicks',
        '10' => '10 clicks', '15' => '15 clicks', '20' => '20 clicks', '42' => '42 clicks',
        );

    // how long popup is visible, msec
    protected $delay_auto_close_options = array(
        '0' => 'No auto close', 
        '3' => '3 sec', '5' => '5 sec', '8' => '8 sec', '10' => '10 sec', '20' => '20 sec', 
        '40' => '40 sec', '60' => '1 minute', '120'=> '2 minutes', '180'=> '3 minutes',
        );

    // Vertical positioning options
    protected $vpos_options = array(
        'top'    => 'top',
        'center' => 'center',
        'bottom' => 'bottom',
        );

    // Horisontal positioning options
    protected $hpos_options = array(
        'left'   => 'left',
        'center' => 'center',
        'right'  => 'right',
        );

    // div transition effects 
    protected $trans_options = array(
        'none'  => 'None',
        'fade'  => 'Fade in/out',
        );

    // div slide direction options 
    protected $from_options = array(
        'n' => 'None',
        't' => 'from Top',
        'b' => 'from Bottom',
        'l' => 'from Left',
        'r' => 'from Right',
        );


    // div transition speed, msec
    protected $speed_options = array(
        '0'   => 'None',
        '100' => 'ultra-fast (100ms)', '200' => 'fast (200ms)', '400' => 'medium (400ms)', 
        '600' => 'slow (600ms)', '1000'   => 'ultra-slow (1 s)',
        );

    protected $bgcolor_options = array(
        0 => true,      // all input is good
        );

    protected $bordercolor_options = array(
        0 => true,      // all input is good
        );

    protected $bgimage_options = array(
        0 => true,      // all input is good
        );

    protected $closeimage_options = array(
        0 => true,      // all input is good
        );

    protected $blur_options = array(
        '0'=>'None',
        '1' => '1 px',
        '2' => '2 px',
        '3' => '3 px',
        '4' => '4 px',
        '5' => '5 px'
        );

    protected $opacity_options = array(
        '0' => 'None',
        '0.1' => '10 %', '0.2' => '20 %', '0.3' => '30 %', '0.4' => '40 %', '0.5' => '50 %',
        '0.6' => '60 %', '0.7' => '70 %', '0.8' => '80 %', '0.9' => '90 %', '1' => '100%'
    );

    protected $padding_options = array( '0' => '0px', '1' => '1px', '2' => '2px', '3' => '3px', 
        '4' => '4px', '5' => '5px', '6' => '6px', '7' => '7px', '8' => '8px', '9' => '9px', '10' => '10px', 
        '12' => '12px', '14' => '14px', '16' => '16px', '18' => '18px', '20' => '20px', 
        '25'=>'25px', '30'=>'30px', '35'=>'35px', '40'=>'40px', '45'=>'45px', '50'=>'50px');

    protected $margin_options = array( '0' => '0px', '1' => '1px', '2' => '2px', '3' => '3px', 
        '4' => '4px', '5' => '5px', '6' => '6px', '7' => '7px', '8' => '8px', '9' => '9px', '10' => '10px', 
        '12' => '12px', '14' => '14px', '16' => '16px', '18' => '18px', '20' => '20px', 
        '25'=>'25px', '30'=>'30px', '35'=>'35px', '40'=>'40px', '45'=>'45px', '50'=>'50px');

    protected $borderwidth_options = array( '0px' => '0px', '1px' => '1px', '2px' => '2px', '3px' => '3px', 
        '4px' => '4px', '5px' => '5px', '6px' => '6px', '7px' => '7px', '8px' => '8px', '9px' => '9px', '10px' => '10px', 
        '12px' => '12px', '14px' => '14px', '16px' => '16px', '18px' => '18px', '20px' => '20px', 
        '25px'=>'25px', '30px'=>'30px', '35px'=>'35px', '40px'=>'40px', '45px'=>'45px', '50px'=>'50px');

    protected $borderradius_options = array( '0px' => '0px', '1px' => '1px', '2px' => '2px', '3px' => '3px', 
        '4px' => '4px', '5px' => '5px', '6px' => '6px', '7px' => '7px', '8px' => '8px', '9px' => '9px', '10px' => '10px', 
        '12px' => '12px', '14px' => '14px', '16px' => '16px', '18px' => '18px', '20px' => '20px', 
        '25px'=>'25px', '30px'=>'30px', '35px'=>'35px', '40px'=>'40px', '45px'=>'45px', '50px'=>'50px');

    protected $shadow_options = array( '0px' => '0px', '1px' => '1px', '2px' => '2px', '3px' => '3px', 
        '4px' => '4px', '5px' => '5px', '6px' => '6px', '7px' => '7px', '8px' => '8px', '9px' => '9px', '10px' => '10px', 
        '12px' => '12px', '14px' => '14px', '16px' => '16px', '18px' => '18px', '20px' => '20px', 
        '25px'=>'25px', '30px'=>'30px', '35px'=>'35px', '40px'=>'40px', '45px'=>'45px', '50px'=>'50px');

    protected $submit_auto_close_values = array ('0' => 'no auto close', '0.1'=>'immediately', 
        '1'=>'1 second', '2'=>'2 seconds', '3'=>'3 seconds', '5'=>'5 seconds',
        '10'=>'10 seconds', '20'=>'20 seconds', '60'=>'1 minute',
        );


    protected static $cookie_options=array(
        '-1' =>'Each time',
        '0' => 'Once per session',
        '1' => 'Once per 24 hours',
        '7' => 'Once per week',
        '30'=> 'Once per month',
        '92'=> 'Once per 3 months',
        '183'=>'Once per 6 months',
        '365'=>'Once per year',
        );

    protected $facebook_options=array(
        '' => 'Inactive',
        'standard'=> 'Button',
        'button_count'=>'Button Count',
        'box_count'=>'Box',
        );

    protected $twitter_options=array(
        ''=>'Inactive',   
        'no-count'=>'Button',
        'regular'=>'Button Count',
        'vertical'=>'Box'
    );

    protected $google_options=array(
        ''=>'Inactive',
        'annotation'=>'Button',
        'medium'=>'Button Count',
        'tall'=>'Box',
        );

    protected $linkedin_options=array(
        ''=>'Inactive',
        'none'=>'Button',
        'right'=>'Button Count',
        'top'=>'Box',
    );
    
    protected $stumbleupon_options=array(
        ''=>'Inactive',
        '1'=>'Button',
        '4'=>'Button Count',
        '5'=>'Box',
    );

    protected $pinterest_options=array(
        ''=>'Inactive',
        'none'=>'Button',
        'horizontal'=>'Button Count',
        'vertical'=>'Box',
    );

    protected $active_pages=array();
    protected $active_pages_options=array();

    protected $active_categories=array();
    protected $active_categories_options=array();

    protected $active_tags=array();
    protected $active_tags_options=array();


    protected $available_options = array('active_pages', 'active_categories', 'active_tags', 
        'height', 'width', 'padding', 'margin', 'trigger', 'delaytime', 'scroll', 'clicks', 'showtime', 
        'vpos', 'hpos', 'trans', 'speed', 'dir', 'bgcolor', 'bgimage', 'bordercolor', 'borderwidth', 'borderradius', 
        'shadow', 'cookie', 'closeimage',);

    public function __construct() {
        // register_activation_hook(__FILE__, array($this, 'install') );
        // register_deactivation_hook(__FILE__, array($this, 'uninstall') );
        // wp_register_script( 'dgd-scrollbox-plugin-admin', plugins_url('js/admin.js', __FILE__ ), array('jquery','media-upload'), DGDSCROLLBOX_VERSION, true );
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_style_n_script') );
        add_action('admin_init', array($this, 'dgd_scrollbox_editor') );
        add_action('save_post',  array($this, 'save_dgd_scrollbox_fields'), 10, 2 );
        add_filter('default_content', array($this, 'dgd_scrollbox_default'), 10, 2);
        add_action('admin_menu', array($this, 'register_general_settings_page'));
        add_action('parse_request', array($this, 'control_requests') );
        add_filter('tiny_mce_before_init', array($this, 'tiny_mce_fix'));
        add_filter('admin_footer', array($this, 'display_dgd_scrollbox_preview'));
        // add_filter('admin_footer', array($this, 'display_donate_box'));

        // add_action( 'admin_notices', array($this, 'migrate_from_old_version'));
    }

    function tiny_mce_fix( $init ) {
        global $post;
        // assuming that scrollbox will not contain html copy-pasted from Word etc.
        // $valid_elements = 'div[!id|!name|!class|!style],p[!id|!name|!class|!style],span[!id|!name|!class|!style]';
        $valid_elements = 'div[!id|!name|!class|!style],span[!id|!name|!class|!style]';
        if($post->post_type == DGDSCROLLBOXTYPE) {
            if(isset($init['extended_valid_elements'])) {
                $init['extended_valid_elements'] .= ','.$valid_elements;
            } else {
                $init['extended_valid_elements'] = $valid_elements;
            }
        }
        return $init;
    }

    function dgd_scrollbox_default($content, $post) {
        if($post->post_type == DGDSCROLLBOXTYPE) {
            $content = DgdScrollboxHelper::sampleHtml();
        }
        return $content;
    }

    public function enqueue_admin_style_n_script( $hook_suffix ) {
        global $wp_version;
        // first check that $hook_suffix is appropriate for your admin page
        // http://www.webmaster-source.com/2010/01/08/using-the-wordpress-uploader-in-your-plugin-or-theme/

        // Init $DGD variable for preview

        $data = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('dgd_stb_nonce'),
            'debug' => '1',
            'permalink' => get_home_url(),
            'title' => 'Dreamgrow Scroll Triggered Box Preview',
            'thumbnail' => '',
            'scripthost' => plugins_url('/',  __FILE__), 
        );

        $meta=DgdScrollboxHelper::$dgd_stb_meta_default;
        $meta['id']='dgd_scrollbox-preview';
        $meta['voff']=0;
        $meta['hoff']=0;

        if(version_compare($wp_version, '3.3', '>=')) {
            // WP=3.3 or newer
            $data['scrollboxes']= array($meta);
        } else {
            // WP<3.3 does not support multi-dimensional arrays in wp_localize_script
            // so we add $DGD.scrollboxes separately, without wp_localize_script encoding help
            $data['l10n_print_after'] = '$DGD.scrollboxes = ' . json_encode( array($meta) ) . ';';
        }

        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'dgd-scrollbox-plugin', plugins_url( 'css/adminstyle.css', __FILE__ ), array(), DGDSCROLLBOX_VERSION );  
	    wp_enqueue_style( 'dgd-scrollbox-plugin-core', plugins_url( 'css/style.css', __FILE__ ), array(), DGDSCROLLBOX_VERSION );  
        wp_register_script( 'dgd-scrollbox-plugin-admin', plugins_url('js/admin.js', __FILE__ ), array('jquery'), DGDSCROLLBOX_VERSION, true );
        wp_register_script( 'dgd-scrollbox-plugin', plugins_url( 'js/script.js', __FILE__ ), array('jquery'), DGDSCROLLBOX_VERSION, false );
        wp_register_script( 'dgd-scrollbox-plugin-preview', plugins_url('js/preview.js', __FILE__ ), array('jquery', 'dgd-scrollbox-plugin'), DGDSCROLLBOX_VERSION, true );

        wp_enqueue_script('wp-color-picker' );
        wp_enqueue_script( 'dgd-scrollbox-plugin-admin' );
        wp_enqueue_script( 'dgd-scrollbox-plugin-preview' );

        wp_localize_script('dgd-scrollbox-plugin', '$DGD', $data);
    }


    function dgd_scrollbox_editor() {
        add_meta_box( 'dgd_scrollbox_meta_box', // meta box section html id
            'Pop-up details and customization', // meta box title
            array($this, 'display_dgd_scrollbox_meta_box'), // meta box editor html function
            DGDSCROLLBOXTYPE,             // post_type
            'normal',              // where to display (normal|advanced|side)
            'high'                  // priority among other boxes (high|core|default|low)
        );
        add_meta_box( 'dgd_donate_box', // meta box section html id
            'Donate $10, $20 or $50', // meta box title
            array($this, 'display_donate_box'), // meta box editor html function
            DGDSCROLLBOXTYPE,             // post_type
            'side',              // where to display (normal|advanced|side)
            'default'                  // priority among other boxes (high|core|default|low)
        );

        /*
        add_meta_box( 'dgd_preview_box', // meta box section html id
            'Preview', // meta box title
            array($this, 'display_dgd_scrollbox_preview'), // meta box editor html function
            DGDSCROLLBOXTYPE,             // post_type
            'advanced',              // where to display (normal|advanced|side)
            'low'                  // priority among other boxes (high|core|default|low)
        );
        */
    }

    function register_general_settings_page() {
        // add_submenu_page( 'edit.php?post_type='.DGDSCROLLBOXTYPE, 'General settings', 'General settings', 'edit_plugins', 'dgd_scrollbox_general_settings', array($this, 'general_settings') ); 
        // add_submenu_page( 'edit.php?post_type='.DGDSCROLLBOXTYPE, 'Debug', 'Debug', 'edit_plugins', 'dgd_scrollbox_debug', array($this, 'debug_page') );         
    }

    function general_settings() {
        $version=get_option('stb_version');
        if(isset($version) && $version) {
            echo '<p>You have version '.$version.'</p>';
        }

        $feedback='';
        
        if(isset($_GET['action'])) {
            $action=$_GET['action'];
            switch($action) {
                case 'migrate':
                    $this->migrate_from_old_version(false);
                    break;
                case 'force_migrate':
                    $feedback=$this->migrate_from_old_version(true);
                    break;
                case 'undo_migration':
                    $this->clear_migration_flag();
                    break;
                case 'show_stb_options':
                    $old_meta=get_option('stb_settings', $defaults_old);
                    echo '<p>';
                    var_dump($old_meta);
                    var_dump($version);
                    echo '</p>';
                    break;
            }
            echo $feedback;
        }
        ?>
        <h1>General actions</h1>
        <a href="edit.php?post_type=<?php echo DGDSCROLLBOXTYPE ?>&page=dgd_scrollbox_general_settings&action=force_migrate">Migrate again from old plugin</a><br>
        <a href="edit.php?post_type=<?php echo DGDSCROLLBOXTYPE ?>&page=dgd_scrollbox_general_settings&action=show_stb_options">Show old plugin options</a><br>
        <a href="edit.php?post_type=<?php echo DGDSCROLLBOXTYPE ?>&page=dgd_scrollbox_general_settings&action=undo_migration">Clear migration flag</a><br>
        <?php
    }

    function debug_page() {
        global $sitepress;
        /*
        if(isset($sitepress) && method_exists($sitepress, 'get_active_languages')) {
            foreach($sitepress->get_active_languages() as $code=>$name) {
                echo $code.': '.$name['english_name']."<br>\n";
            }

            
            }        
        } else {
            echo 'WPML not installed';
        }
        */
        echo '<h1>WPML state and active languages</h1>';

        if(function_exists('icl_get_languages')){
            $languages = icl_get_languages();
            echo '<h2>Active languages</h2>';
            foreach($languages as $code=>$langarray) {
                echo $code.': '.$langarray['id'].' '.
                    $langarray['language_code'].' '.
                    $langarray['native_name'].' '.
                    $langarray['translated_name']."<br>\n";
            
            }
            echo '<h2>Are translations enabled</h2>';
            $sitepress_settings=get_option('icl_sitepress_settings');
            if(isset($sitepress_settings['custom_posts_sync_option'][DGDSCROLLBOXTYPE]) 
                && $sitepress_settings['custom_posts_sync_option'][DGDSCROLLBOXTYPE]) {
                echo "Scrollboxes translation enabled<br>\n";
            } else {
                // put wpml-config.xml to plugin root and it will be not configurable any more
                // $sitepress->save_settings(array('custom_posts_sync_option'=>array(DGDSCROLLBOXTYPE=>1)) );
                // echo "Scrollboxes translation re-enabled<br>\n";
            }
        } else {
            echo 'WPML not installed<br>';        
        }

        if(isset($_GET['action'])) {
            echo 'action:'.$_GET['action']."<br>\n";
        }
        echo '<p>---</p>';

    }


    function display_donate_box() {
    ?>
            <!-- div id="donate" -->
            <p>If you like Dreamgrow Scroll Triggered Box. Please help to keep it alive by donating. Every cent counts!</p>

            <img src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif"
                       border="0" alt="PayPal - The safer, easier way to pay online!"
                       onClick="$DGD.paypalSubmit('B4NCTTDR9MEPW');" width="147" height="47" class="dgd_donate_button">
            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif"
                     width="1" height="1">
            <p class="dgd_form_footer">How can you support the developement?</p>

            <p><a href="http://wordpress.org/support/view/plugin-reviews/dreamgrow-scroll-triggered-box?rate=5#postform" target="_blank">Leava a raving review *****</a></p>

            <p style="word-break: break-all">Link to the plugin's home page from your blog:
                <a href="http://www.dreamgrow.com/dreamgrow-scroll-triggered-box/" target="_blank">http://www.dreamgrow.com/dreamgrow-scroll-triggered-box/</a></p>

            <p>Spread the word on <a target="_blank" href="http://twitter.com/intent/tweet/?text=Check%20out%20this%20WordPress%20plugin%3A%20Scroll%20Triggered%20Box&via=Dreamgrow&url=http%3A%2F%2Fwww.dreamgrow.com%2Fdreamgrow-scroll-triggered-box%2F">Twitter</a> or <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?n=4&s=100&p[summary]=Scroll+Triggered+Box+will+boost+your+conversion+rates%21+The+plugin+displays+a+pop-up+box+with+customizable+content.&p[url]=http%3A%2F%2Fwww.dreamgrow.com%2Fdreamgrow-scroll-triggered-box%2F&p[title]=Check+out+this+WordPress+plugin%3A+Scroll+Triggered+Box">Facebook</a></p>
            <!-- /div -->
        <?php
    }


    function display_dgd_scrollbox_preview() {
        // if firefox only?
        ?>
        <iframe id="dgd_preview_frame" style="visibility: hidden"></iframe>
        <?php
    }

    function display_dgd_scrollbox_meta_box( $post ) {
        global $dgd_stb_meta_default, $dgd_stb_show_meta_default, $wp_post_types;

        $dgd_stb=get_post_meta( $post->ID, 'dgd_stb', true );
        $dgd_stb_show=get_post_meta( $post->ID, 'dgd_stb_show', true );
        $dgd_stb_hide=get_post_meta( $post->ID, 'dgd_stb_hide', true );

        if(!$dgd_stb) {
            $dgd_stb=DgdScrollboxHelper::$dgd_stb_meta_default;
        }

        if(!$dgd_stb_show){
            $dgd_stb_show=DgdScrollboxHelper::$dgd_stb_show_meta_default;        
        }

        if($dgd_stb['receiver_email']===false) $dgd_stb['receiver_email']= get_option('admin_email');

        /* Re-define some variables for old version scrollboxes */
        if(!isset($dgd_stb['thankyou'])) $dgd_stb['thankyou']=DgdScrollboxHelper::$dgd_stb_meta_default['thankyou'];
        if(!isset($dgd_stb['tabhtml'])) $dgd_stb['tabhtml']=DgdScrollboxHelper::$dgd_stb_meta_default['tabhtml'];
        if(!isset($dgd_stb['submit_auto_close'])) $dgd_stb['submit_auto_close'] = 5;
        if(!isset($dgd_stb['delay_auto_close'])) $dgd_stb['delay_auto_close'] = 0;


        if(!array_key_exists($dgd_stb['height'], $this->height_select_options)) {
            $this->height_select_options[$dgd_stb['height']]=$dgd_stb['height'];
            ksort($this->height_select_options);
        }

        if(!array_key_exists($dgd_stb['width'], $this->width_select_options)) {
            $this->width_select_options[$dgd_stb['width']]=$dgd_stb['width'];
            ksort($this->width_select_options);
        }

        //  var_dump($dgd_stb_show);
        ?>

        <h1>Where and how to trigger?</h1>
        <table class="dgd_admin">
            <tr>
                <td>Triggering action</td>
                <td>
                    <select name="dgd_stb[trigger][action]">
                        <?php echo $this->populate_options($this->trigger_options, $dgd_stb['trigger']['action']) ?>
                    </select>
                    Scroll: <select name="dgd_stb[trigger][scroll]">
                        <?php echo $this->populate_options($this->scroll_options, $dgd_stb['trigger']['scroll']) ?>
                    </select>
                    Additional time delay: <select name="dgd_stb[trigger][delaytime]">
                        <?php echo $this->populate_options($this->delaytime_options, $dgd_stb['trigger']['delaytime']) ?>
                    </select>

                    Element: <input type="text" name="dgd_stb[trigger][element]" value="<?php echo $dgd_stb['trigger']['element'] ?>"><br>
                </td>
            </tr>
            <tr>
                <td>Placement on screen</td>
                <td>
<table id="dgd_pos_selector">
<tr>
<td><a href="#void0" onClick="$DGD.select2D.choose('top', 'left')"> </a></td>
<td><a href="#void0" onClick="$DGD.select2D.choose('top', 'center')"> </a></td>
<td><a href="#void0" onClick="$DGD.select2D.choose('top', 'right')"> </a></td>
</tr>
<tr>
<td><a href="#void0" onClick="$DGD.select2D.choose('center', 'left')"> </a></td>
<td><a href="#void0" onClick="$DGD.select2D.choose('center', 'center')"> </a></td>
<td><a href="#void0" onClick="$DGD.select2D.choose('center', 'right')"> </a></td>
</tr><tr>
<td><a href="#void0" onClick="$DGD.select2D.choose('bottom', 'left')"> </a></td>
<td><a href="#void0" onClick="$DGD.select2D.choose('bottom', 'center')"> </a></td>
<td><a href="#void0" onClick="$DGD.select2D.choose('bottom', 'right')"> </a></td>
</tr>
</table>
<input type="hidden" name="dgd_stb[vpos]" value="<?php echo $dgd_stb['vpos'] ?>" id="vpos_selector">
<input type="hidden" name="dgd_stb[hpos]" value="<?php echo $dgd_stb['hpos'] ?>" id="hpos_selector">
                </td>
            </tr>
            <tr>
                <td>Popup frequency</td>
                <td>
                    <select name="dgd_stb[cookieLifetime]">
                    <?php echo  $this->populate_options(DgdScrollboxAdmin::$cookie_options, $dgd_stb['cookieLifetime']) ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Hide on mobiles</td>
                <td>
                    <label><input type="checkbox" name="dgd_stb[hide_mobile]" value="1" <?php checked(true, isset($dgd_stb['hide_mobile'])); ?>> Hide on mobiles</label>
                </td>
            </tr>
            <tr>
                <td>Show</td>
                <td>
                    <table>
                    <tr>
                        <td width="25%" style="vertical-align: top">On all following:<br />
                            <label><input name="dgd_stb_show[frontpage]" type="checkbox" value="1" <?php checked(true, isset($dgd_stb_show['frontpage'])); ?>>Frontpage</label><br />
                            <label><input name="dgd_stb_show[postspage]" type="checkbox" value="1" <?php checked(true, isset($dgd_stb_show['postspage'])); ?>>Blog page</label><br />
                            <label><input name="dgd_stb_show[error404]" type="checkbox" value="1" <?php checked(true, isset($dgd_stb_show['error404']));     ?>>Error 404 page</label><br />
                            <?php $this->stb_get_post_types($dgd_stb_show); ?><br /><br />
                            <label><input name="dgd_stb_show[admin_only]" type="checkbox" value="1" <?php checked(true, isset($dgd_stb_show['admin_only'])); ?> class="dgd_checkalert"><span class="dgd_checkalert">Admin only</span></label>
                        </td>
                        <td style="vertical-align: top">Exceptions:<br /> 
                            <ul id="dgd_tabs">
                                <li class="include selected" onClick="$DGD.showTab(this, 'include')">Include</li>
                                <li class="exclude" onClick="$DGD.showTab(this, 'exclude')">Exclude</li>
                            </ul>
                            <div class="dgd_tab_container">
                            <div class="dgd_tab_content include">
                                <div class="thirdcolumn">
                                Pages:<br />
                                    <select name="dgd_stb_show[selected_pages][]" multiple="multiple" size="8">
                                        <?php 
                                        $walker = new Dgd_Page_Selector_Walker(isset($dgd_stb_show['selected_pages'])?$dgd_stb_show['selected_pages']:array());
                                        $options_list= wp_list_pages( array('title_li'=>'', 'post-type'=>'page','sort_column' => 'menu_order, post_title', 'echo'=>0, 'walker'=>$walker));
                                        $options_list=str_replace(array('</li>', "</ul>\n"), '', $options_list);
                                        $options_list=str_replace("<ul class='children'>\n", '    ', $options_list);
                                        echo $options_list;
                                        ?>
                                    </select>
                                </div>
                                <div class="thirdcolumn">
                                Categories:<br />
                                    <select name="dgd_stb_show[categories][]" multiple="multiple" size="8">
                                    <?php 
                                        $categories=get_categories();
                                        $new_array=array();
                                        foreach($categories as $category) {
                                            $new_array[$category->cat_ID]=$category->cat_name;
                                        }
                                    echo $this->populate_options($new_array,  (isset($dgd_stb_show['categories']) ? $dgd_stb_show['categories']: array()));
                                    ?>
                                    </select>
                                </div>
                                <div class="thirdcolumn">Tags:<br />
                                    <select name="dgd_stb_show[tags][]" multiple="multiple" size="8">
                                    <?php
                                    $tags = get_tags();
                                    $new_array=array();
                                    foreach($tags as $tag) {
                                        $new_array[$tag->term_id]=$tag->name;
                                    }
                                    echo $this->populate_options($new_array, (isset($dgd_stb_show['tags']) ? $dgd_stb_show['tags']: array()));
                                    ?>
                                    </select>
                                </div>
                                <div class="dgd_clear"></div>
                            </div>

                            <div class="dgd_tab_content exclude hide">
                                <div class="thirdcolumn">Pages:<br />
                                    <select name="dgd_stb_hide[selected_pages][]" multiple="multiple" size="8">
                                        <?php 
                                        $walker = new Dgd_Page_Selector_Walker(isset($dgd_stb_hide['selected_pages'])?$dgd_stb_hide['selected_pages']:array());
                                        $options_list= wp_list_pages( array('title_li'=>'', 'post-type'=>'page','sort_column' => 'menu_order, post_title', 'echo'=>0, 'walker'=>$walker));
                                        $options_list=str_replace(array('</li>', "</ul>\n"), '', $options_list);
                                        $options_list=str_replace("<ul class='children'>\n", '    ', $options_list);
                                        echo $options_list;
                                        ?>
                                    </select>
                                </div>
                                <div class="thirdcolumn">Categories:<br />
                                    <select name="dgd_stb_hide[categories][]" multiple="multiple" size="8">
                                    <?php 
                                        $categories=get_categories();
                                        $new_array=array();
                                        foreach($categories as $category) {
                                            $new_array[$category->cat_ID]=$category->cat_name;
                                        }
                                    echo $this->populate_options($new_array,  (isset($dgd_stb_hide['categories']) ? $dgd_stb_hide['categories']: array()));
                                    ?>
                                    </select>
                                </div>
                                <div class="thirdcolumn">Tags:<br />
                                    <select name="dgd_stb_hide[tags][]" multiple="multiple" size="8">
                                    <?php
                                    $tags = get_tags();
                                    $new_array=array();
                                    foreach($tags as $tag) {
                                        $new_array[$tag->term_id]=$tag->name;
                                    }
                                    echo $this->populate_options($new_array, (isset($dgd_stb_hide['tags']) ? $dgd_stb_hide['tags']: array()));
                                    ?>
                                    </select>
                                </div>
                                <div class="dgd_clear"></div>
                            </div>
                        </td>
                     </tr>
                     </table>
                </td>
            </tr>
        </table>


        <h1>Actions after form submission</h1>

        <table class="dgd_admin">
        <tbody>
            <tr>
                <td>Send submitted values to email</td>
                <td><input type="text" name="dgd_stb[receiver_email]" value="<?php echo $dgd_stb['receiver_email'] ?>" class="dgd_text_input"><br> (Leave email field empty if you want to use form from third party plugin)</td>
            </tr>
            <tr>
                <td>"Thank you" message</td>
                <td><input type="text" name="dgd_stb[thankyou]" value="<?php echo htmlentities($dgd_stb['thankyou']) ?>" class="dgd_text_input">
                <br />(this field accepts html)
                <?php if(function_exists('icl_get_languages')){ echo '<br />(Please note, that you can change it for each translation separately)';} ?></td>
            </tr>
            <tr>
                <td>Close box automatically after "Thank You" message is shown for</td>
                <td><select name="dgd_stb[submit_auto_close]">
                    <?php
                         echo $this->populate_options($this->submit_auto_close_values, $dgd_stb['submit_auto_close']);

                    ?>
                </select></td>
            </tr>
            <tr>
                <td>Close permanently</td>
                <td>
                    <label><input type="checkbox" name="dgd_stb[hide_submitted]" value="1"<?php checked(true, isset($dgd_stb['hide_submitted'])); ?>>Close box permanently for subscribed user</label>
                </td>
            </tr>
        </tbody>
        </table>

        <h1>Auto close</h1>

        <table class="dgd_admin">
        <tbody>
            <tr>
                <td>Hide box automatically after it has been visible for</td>
                <td>
                    <select name="dgd_stb[delay_auto_close]">
                    <?php echo $this->populate_options($this->delay_auto_close_options, $dgd_stb['delay_auto_close']) ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Keep open</td>
                <td><label><input type="checkbox" name="dgd_stb[keep_open]" value="1" <?php checked(true, isset($dgd_stb['keep_open'])); ?>>Keep box open if user scrolls back up</label></td>
            </tr>
        </tbody>
        </table>

        <h1>Lightbox</h1>
        <table class="dgd_admin">
        <tbody>
            <tr>
                <td>Lightbox</td>
                <td><label><input type="checkbox" name="dgd_stb[lightbox][enabled]" value="1" <?php checked(true, isset($dgd_stb['lightbox']['enabled'])); ?>>Enable page shading with overlay ("Lightbox")</label></td>
            </tr>
            <tr>
                <td>Overlay color</td>
                <td>
                    <input type="text" name="dgd_stb[lightbox][color]" value="<?php echo isset($dgd_stb['lightbox']['color']) ? $dgd_stb['lightbox']['color'] : '#000000' ?>" class="dgd-popup-color-picker" />
                </td>
            </tr>
            <tr>
                <td>Overlay opacity</td>
                <td>
                    <select name="dgd_stb[lightbox][opacity]">
                        <?php echo $this->populate_options($this->opacity_options, isset($dgd_stb['lightbox']['opacity']) ? $dgd_stb['lightbox']['opacity'] : '0.7') ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Page content blur</td>
                <td><label><input type="checkbox" name="dgd_stb[lightbox][blur]" value="2" <?php checked(true, isset($dgd_stb['lightbox']['blur'])); ?>>blur page content (Works on browsers supporting CSS3)</label></td>
            </tr>



        </tbody>
        </table>

        <h1>Scrollbox design</h1>
        <table class="dgd_admin">
            <tr>
                <td>Theme</td>
                <td><select name="dgd_stb[theme]"><?php echo $this->get_templates($dgd_stb['theme']) ?></select></td>
            </tr>

            <tr>
                <td>Widget</td>
                <td><label><input type="checkbox" name="dgd_stb[widget_enabled]" value="1" <?php checked(true, isset($dgd_stb['widget_enabled'])); ?>>Enable Widget area</label></td>
            </tr>
            <tr>
                <td class="dgd_leftcol">Popup box dimensions (px)</td>
                <td>
                    Height: <select name="dgd_stb[height]" id="dgd_stb_height">
                    <?php echo  $this->populate_options($this->height_select_options, $dgd_stb['height']) ?>
                    </select> 
                    Width: <select name="dgd_stb[width]" id="dgd_stb_width">
                    <?php echo  $this->populate_options($this->width_select_options, $dgd_stb['width']) ?>
                    </select>
                    Padding: <select name="dgd_stb[jsCss][padding]">
                    <?php echo  $this->populate_options($this->padding_options, $dgd_stb['jsCss']['padding']) ?>
                    </select>
                    Margin: <select name="dgd_stb[jsCss][margin]">
                    <?php echo  $this->populate_options($this->margin_options, $dgd_stb['jsCss']['margin']) ?>
                    </select>
                    <br />(When using background image, box size will default to image size.)
                    </td>
            </tr>

            <tr>
                <td>Popup style</td>
                <td>
                    Back color <input type="text" name="dgd_stb[jsCss][backgroundColor]" value="<?php echo $dgd_stb['jsCss']['backgroundColor'] ?>" class="dgd-popup-color-picker" />
                    Box shadow <select name="dgd_stb[jsCss][boxShadow]">
                    <?php echo  $this->populate_options($this->shadow_options, $dgd_stb['jsCss']['boxShadow']) ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td>Border</td>
                <td>
                    Color <input type="text" name="dgd_stb[jsCss][borderColor]" value="<?php echo $dgd_stb['jsCss']['borderColor'] ?>" class="dgd-popup-color-picker" />
                    Width  <select name="dgd_stb[jsCss][borderWidth]">
                    <?php echo  $this->populate_options($this->borderwidth_options, $dgd_stb['jsCss']['borderWidth']) ?>
                    </select>
                    Corner radius <select name="dgd_stb[jsCss][borderRadius]">
                    <?php echo  $this->populate_options($this->borderradius_options, $dgd_stb['jsCss']['borderRadius']) ?>
                    </select>

                </td>
            </tr>


            <tr>
            <td>Background Image</td>
            <td><label for="upload_image">
                <input type="text" size="36" name="dgd_stb[jsCss][backgroundImageUrl]" value="<?php echo  $dgd_stb['jsCss']['backgroundImageUrl'] ?>" />
                <input id="upload_bg_image_button" type="button" value="Upload" />
                <br />Enter an URL or upload bacground image.
                </label></td>
            </tr>

            <tr>
            <td>Close button Image</td>
            <td><label for="upload_image">
                <input type="text" size="36" name="dgd_stb[closeImageUrl]" value="<?php echo  $dgd_stb['closeImageUrl'] ?>" />
                <input id="upload_close_image_button" type="button" value="Upload" />
                <br />Enter an URL or upload your custom close button image.
                </label></td>
            </tr>          

            <tr>
                <td>Slide in direction</td>
                <td>
                    <select name="dgd_stb[transition][from]">
                    <?php echo  $this->populate_options($this->from_options, $dgd_stb['transition']['from']) ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Additional effect</td>
                <td>
                    <select name="dgd_stb[transition][effect]">
                    <?php echo  $this->populate_options($this->trans_options, $dgd_stb['transition']['effect']) ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Transition duration</td>
                <td><select name="dgd_stb[transition][speed]">
                    <?php echo  $this->populate_options($this->speed_options, $dgd_stb['transition']['speed']) ?>
                    </select>
                </td>
            </tr>

        </table>
        <h1>Permanent tab</h1>
        <table class="dgd_admin">
            <tr>
                <td>Show tab</td>
                <td>
                    <label><input type="checkbox" name="dgd_stb[tab]" value="1" <?php checked(true, isset($dgd_stb['tab'])); ?>>After Scrollbox closing show tab for reopening</label>
                </td>           
            </tr>
            <tr>
                <td>"Tab" text</td>
                <td><input type="text" name="dgd_stb[tabhtml]" value="<?php echo htmlentities($dgd_stb['tabhtml']) ?>" class="dgd_text_input">
                <br />(this field accepts html)</td>
            </tr>

        </table>


        <h1>Social buttons</h1>
        <table class="dgd_admin">
            <tr>
                <td><?php _e('Social buttons', 'stb'); ?></td>
                <td>
                    <label><select name="dgd_stb[social][facebook]">
                        <?php echo  $this->populate_options($this->facebook_options, $dgd_stb['social']['facebook']) ?>
                    </select> Facebook</label><br />
                    <label><select name="dgd_stb[social][twitter]">
                        <?php echo  $this->populate_options($this->twitter_options, $dgd_stb['social']['twitter']) ?>
                    </select> Twitter</label><br />
                    <label><select name="dgd_stb[social][google]">
                        <?php echo  $this->populate_options($this->google_options, $dgd_stb['social']['google']) ?>
                    </select> Google+</label><br />
                    <label><select name="dgd_stb[social][pinterest]">
                        <?php echo  $this->populate_options($this->pinterest_options, $dgd_stb['social']['pinterest']) ?>
                    </select> Pinterest</label> <small>* Pin it button will only be displayed on the pages that have a featured image.</small><br />
                    <label><select name="dgd_stb[social][stumbleupon]">
                        <?php echo  $this->populate_options($this->stumbleupon_options, $dgd_stb['social']['stumbleupon']) ?>
                    </select> Stumbleupon</label><br />
                    <label><select name="dgd_stb[social][linkedin]">
                        <?php echo  $this->populate_options($this->linkedin_options, $dgd_stb['social']['linkedin']) ?>
                    </select> LinkedIN</label>
                </td>
            </tr>


        </table>

        <?php
    }

    private function populate_options($all_options, $selected_options) {
        $rv='';
        foreach($all_options as $key => $label) {
            $rv.='<option value="'.$key.'" ';
            if(is_array($selected_options) && in_array($key, $selected_options)) {
                    $rv.=' selected="1"';
            } else {
                // BUG! 
                $rv.=@selected( $selected_options, $key, false );
            }
            $rv.='>'.$label.'</option>'."\n";
        }
        return $rv;
    }

    private function dgd_scrollbox_page_selector() {
    
    }

    function stb_get_post_types($options){
        $args = array(
            'public'   => true,
        );
        $post_types = get_post_types($args, 'objects');
        if($post_types) {
            foreach ( $post_types as $post_type ) {
                if(!in_array($post_type->name, $this->stb_disabled_types)) {
                    $id = $post_type->name;
                    $label = $post_type->label;
                    echo '<br/>
                          <label><input name="dgd_stb_show[post_types]['.$id.']" type="checkbox" value="1"'.
                          checked(true, isset($options['post_types'][$id]), false) .'>'. $label .'</label>';
                }
            }
        }

    }

    function get_templates($current) {
        $dir = plugin_dir_path(__FILE__) . 'themes/';
        if ($handle = opendir($dir)) {
            $templates = '';
            while (false !== ($entry = readdir($handle))) {
                if ($entry != '.' && $entry != '..' && is_dir(($dir . $entry))) {
                    $templates .= '<option '.selected($entry, $current).
                        ' value="'.$entry.'">'.
                        ucfirst(str_replace('_', ' ', $entry)).
                        "</option>\n";
                }
            }
            closedir($handle);
            $templates.= '<option value="" '.(!$current ? 'selected="selected"' : '').'>I\'m using my own theme</option>';
            return $templates;
        }
        return false;
    }

    function save_dgd_scrollbox_fields( $post_id, $post ) {
        // Check post type for Dgd Popup
        if ( $post->post_type == DGDSCROLLBOXTYPE ) {
            $this->save_options($post_id, false);
            // Clear cache when saving Scrollbox - otherwise cached pages will show old Scrollbox
            // Hint from http://scratch99.com/wordpress/development/clearing-cache-when-widget-saved/ 
            // it's experimental, but sometimes worth to try??
            /*
            if (function_exists('w3tc_pgcache_flush')) { 
                // W3 Total Cache
                @w3tc_pgcache_flush(); 
            }
            if (function_exists('wp_cache_clean_cache')) {
                // WP Super Cache
                @wp_cache_clean_cache( $GLOBALS['file_prefix'] );
            }
            */
        }
    }

    private function save_options($post_id, $option_name) {   
        if(isset($_POST['dgd_stb']) && is_array($_POST['dgd_stb'])) {
            update_post_meta( $post_id, 'dgd_stb', $_POST['dgd_stb']);
        }

        if(isset($_POST['dgd_stb_show']) && is_array($_POST['dgd_stb_show'])) {
            update_post_meta( $post_id, 'dgd_stb_show', $_POST['dgd_stb_show']);
        } else {
            delete_post_meta( $post_id, 'dgd_stb_show');
        }

        if(isset($_POST['dgd_stb_hide']) && is_array($_POST['dgd_stb_hide'])) {
            update_post_meta( $post_id, 'dgd_stb_hide', $_POST['dgd_stb_hide']);
        } else {
            delete_post_meta( $post_id, 'dgd_stb_hide');        
        }
    
    }

    public function clear_migration_flag() {
        $old_meta=get_option('stb_settings');
        if($old_meta) {
            unset($old_meta['migrated']);
            update_option('stb_settings', $old_meta);
        }
    }

    public static function migrate_from_old_version($force=false) {

        $old_meta=get_option('stb_settings', DgdScrollboxHelper::$defaults_old);
        $feedback='';

        //  var_dump($old_meta);
        if($force || !isset($old_meta['migrated'])) {
            $feedback.='<p>';

            $socialHTML='';
            $meta=DgdScrollboxHelper::$dgd_stb_meta_default;
            $meta_show=DgdScrollboxHelper::$dgd_stb_show_meta_default;
            $meta['migrated']='1.4';

            if(!$old_meta['cookie_life']) {
                $meta['cookieLifetime']='-1';
            } else if ($old_meta['cookie_life']>365) {
                $meta['cookieLifetime']='365';
            } else {
                // find nearest bigger or equal option
                foreach(DgdScrollboxAdmin::$cookie_options as $days=>$label) {
                    if($old_meta['cookie_life']<=(int) $days) {
                        $meta['cookieLifetime']=$days;
                        break 1;
                    }
                }
            }
            $meta['trigger']['scroll']=round($old_meta['trigger_height'], -1);
            $meta['trigger']['element']=$old_meta['trigger_element'];
            $meta['width']=$old_meta['width'];
            $meta['hpos']=($old_meta['position']=='middle' ? 'center' : $old_meta['position'] );
            $meta['include_css']=$old_meta['include_css'];  
            $meta['hide_mobile']=$old_meta['hide_mobile'];  // BUG: implement
            $meta_show['admin_only']=$old_meta['show_admin'];
            foreach($old_meta['show'] as $post_type=>$on) {
                $meta_show['post_types'][$post_type]=1;
            }
            $meta['theme']=$old_meta['theme'];              
            $meta['social']=$old_meta['social'];            
            $meta['receiver_email']=$old_meta['receiver_email']; 
            if(isset($old_meta['include_css'])) {
                $meta['theme']=$old_meta['theme'];
                switch($meta['theme']){
                    case 'default':
                        $meta['jsCss']['backgroundColor']='#ffe14f';
                        $meta['jsCss']['borderWidth']='3';
                        $meta['jsCss']['borderColor']='#ffffff';
                        $meta['jsCss']['margin']='10';
                        break;
                }
            } else {
                $meta['theme']='';
                $meta['migrated_no_css']=1;            
            }

            $html=get_option('stb_html');
            $translations=array();
            $wpml_default_language=DgdScrollboxHelper::get_default_language();
            $default_language_post_id=false;
            $migration_success=false;

            foreach($html as $lan=>$content) {
                // if no mutlilanguage, then $lan='0'
                if(isset($old_meta['include_css'])) {
                    // do only if includecss true
                    // replace id with css, because id's will become into conflict 
                    $content=str_replace(array('id="stbMsgArea"', 'id=\'stbMsgArea\'', 'id=stbMsgArea'), 'class="stbMsgArea"', $content);
                    $content=str_replace(array('id="email"', "id='email'"), 'class="email"', $content);
                    $content=str_replace(array('id="stbContactForm"', 'id=\'stbContactForm\'', 'id=stbContactForm'), 'class="stbContactForm"', $content);
                    $content='<div class="inscroll">'.$content.'</div>';

                } else {
                    // open door for themes support
                    $content=str_replace(array('id="stbMsgArea"', 'id=\'stbMsgArea\'', 'id=stbMsgArea'), 'id="stbMsgArea" class="stbMsgArea"', $content);
                    $content=str_replace(array('id="email"', "id='email'"), 'id="email" class="email"', $content);
                    $content=str_replace(array('id="stbContactForm"', 'id=\'stbContactForm\'', 'id=stbContactForm'), 'id="stbContactForm" class="stbContactForm"', $content);
                    $content='<div id="inscroll"><a class="dgd_stb_box_close dgd_stb_box_x" href="#close" id="closebox">x</a>'.$content.'</div>';
                }

                $args=array(
                    'post_content'=>$content,
                    'post_title'=>'Upgraded Box ('.date('d M H:i').')',
                    'post_status'=>'publish',
                    'post_type'=>DGDSCROLLBOXTYPE,
                    );

                $post_id=wp_insert_post($args, true);
                if($post_id) {
                    update_post_meta( $post_id, 'dgd_stb', $meta);
                    update_post_meta( $post_id, 'dgd_stb_show', $meta_show);
                    if($lan==$wpml_default_language) {
                        $default_language_post_id=$post_id;
                    } else {
                        $translations[$lan]=$post_id;
                    }
                    $feedback.='New post was migrated from old version, language code: '.$lan."<br>\n";
                    $migration_success=true;
                }
            }

            if(!empty($translations) && $default_language_post_id) {
                foreach($translations as $lan=>$post_id) {
                    $success=DgdScrollboxHelper::translate($post_id, $default_language_post_id, $lan, DGDSCROLLBOXTYPE );
                    if($success) {
                        $feedback.="Translation for ".$lan." is ".$success."<br>\n";                        
                    } else {
                        $migration_success=false;
                    }
                }
            } else {
                $feedback.='Single language was used'."<br>\n";
            }
            $feedback.='</p>';
            if($migration_success) {
                $old_meta['migrated']=1;
                update_option('stb_settings', $old_meta);
            }
        } 
        return $feedback;
    }

    public function control_requests($arg) {

        // fires when request hits edit.php?post_type=dgd_scrollbox
        // does not fire on edit.php?post_type='.DGDSCROLLBOXTYPE.'&page=dgd_scrollbox_general_settings;
        //  var_dump($_REQUEST);
        //  die();
    }

    public static function install() {

        $old_version=get_option('stb_version', '1.4');
       
        if(version_compare($old_version, '2.0', '<')) {
            $old_version_settings=get_option('stb_settings');
            if($old_version_settings && !isset($old_version_settings['migrated'])) {
                DgdScrollboxAdmin::migrate_from_old_version(false);
            }        
        }
        
        if(version_compare($old_version, '2.1', '<')) {
            // upgrading to 2.1, fill "thankyou" value with default
            $pop_ups = get_pages( array('post_type'=>DGDSCROLLBOXTYPE, 'post_status' => 'publish'));
            if(is_array($pop_ups)) {
                foreach($pop_ups as $pop_up) {
                    $meta=get_post_meta($pop_up->ID, 'dgd_stb', true);
                    if(!isset($meta['thankyou'])) {
                        $meta['thankyou']=DgdScrollboxHelper::$dgd_stb_meta_default['thankyou'];
                    }
                    update_post_meta( $pop_up->ID, 'dgd_stb', $meta);
                }
            }
        }
        update_option('stb_version', DGDSCROLLBOX_VERSION);
    }

    public static function uninstall() {
        // clean up. delete default options
        // $pop_ups = get_pages( array('post_type'=>DGDSCROLLBOXTYPE));
        //  foreach($pop_ups as $pop_up) {
        //     wp_delete_post($pop_up->ID, true);
        // }
    }
}