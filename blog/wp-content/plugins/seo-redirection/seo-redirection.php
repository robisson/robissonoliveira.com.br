<?php
/*
Plugin Name: SEO Redirection
Plugin URI: http://www.clogica.com/product/seo-redirection-premium-wordpress-plugin
Description: By this plugin you can manage all your website redirection types easily.
Author: Fakhri Alsadi
Version: 3.7
Author URI: http://www.clogica.com
*/

require_once ('common/controls.php');
require_once ('custom/controls.php');
require_once ('custom/controls/cf.SR_redirect_cache.class.php');

if(!defined('WP_SEO_REDIRECTION_OPTIONS'))
{
	define( 'WP_SEO_REDIRECTION_OPTIONS', 'wp-seo-redirection-group' );
}

if(!defined('WP_SEO_REDIRECTION_VERSION'))
{
	define( 'WP_SEO_REDIRECTION_VERSION', '3.5'); 
}

$util= new clogica_util_1();
$util->init(WP_SEO_REDIRECTION_OPTIONS, __FILE__);

add_action('admin_enqueue_scripts', 'WPSR_header_code');
add_action('admin_menu', 'WPSR_admin_menu');
add_action('wp', 'WPSR_redirect',1);
add_action( 'save_post', 'WPSR_get_post_redirection');
add_action( 'add_meta_boxes', 'adding_WPSR_custom_meta_boxes', 10, 3 );
add_action( 'admin_head', 'WPSR_check_default_permalink' );
add_action( 'plugins_loaded', 'WPSR_upgrade' );

register_activation_hook( __FILE__ , 'WPSR_upgrade' );
register_uninstall_hook( __FILE__ , 'WPSR_uninstall' ); 

/////////////////////////////////////////////////////////////////////////

function adding_WPSR_custom_meta_boxes() {
	global $util;
	if($util->get_option_value('show_redirect_box')=='1'){

    	$screens = array( 'post', 'page' );
    
    	foreach ( $screens as $screen ) {
    
    		add_meta_box(
    			'WPSR_meta_box',
    			__( 'SEO Redirection'),
    			'WPSR_render_meta_box',
    			$screen
    		);
    	}

	}
}


function WPSR_render_meta_box($post)
{
global $wpdb,$table_prefix,$util ;
$table_name = $table_prefix . 'WP_SEO_Redirection';

	if(get_post_status()!='auto-draft')
	{
		$permalink="";
		if (in_array($post->post_status, array('draft', 'pending'))) {
		list( $permalink, $postname ) = get_sample_permalink( $post->ID);
    		$permalink = str_replace( '%postname%', $postname, $permalink );

		} else {
		    	
				$permalink = get_permalink($post->ID);
		}

		$permalink = $util->make_relative_url($permalink);

		$postID=$post->ID;
		


$theurl = $wpdb->get_row($wpdb->prepare(" select redirect_to,redirect_from from $table_name where postID=%d  ",$postID));

$urlredirect_to='';
   if($wpdb->num_rows>0)
     $urlredirect_to=$theurl->redirect_to;

if($urlredirect_to !='' && $theurl->redirect_from != $permalink )
	{
	// the post_name field changed!
	$wpdb->query($wpdb->prepare(" update $table_name set redirect_from=%s  where postID=%d ",$permalink,$postID));
	if($util->get_option_value('reflect_modifications')=='1'){
		$wpdb->query($wpdb->prepare(" update $table_name set redirect_to=%s  where redirect_to=%s ",$permalink,$theurl->redirect_from));
		$util->info_option_msg('<b>SEO Redirection</b> has detected a change in Permalink, this will be reflected to the redirection records!');
	}
	//-------------------------------------------
	}

echo '
<table border="0" width="100%" cellpadding="2">
	<tr>
		<td width="99%"><input onchange="redirect_check_click()" type="checkbox" name="redirect_check"  id="redirect_check" value="ON">
		Redirect&nbsp;<font color="#008000">' . $permalink . '</font><input type="hidden" name="wp_seo_redirection_url_from" value="' . $permalink . '"></td>
	</tr>
</table>
<div id="redirect_frame">
<table border="0" width="100%" cellpadding="2">
	<tr>
		<td><b>Redirect to</b> <input type="text" name="wp_seo_redirection_url" id="wp_seo_redirection_url" value="' . $urlredirect_to .  '" size="62"></td>
	</tr>
	<tr>
		<td>
		<ul>
			<li>To make a redirection, put the full <b>URL</b> including <b>\'http://\'</b> in the text field above and then click the button <b>Update</b>.</li>
			<li>If you have a caching plugin installed, clear cache to reflect the
			changes immediately.</li>

			<li>To remove the redirection, just uncheck the check box above and then click the button <b>Update</b>.</li>
		</ul>
		</td>
	</tr>
</table>
</div>';

echo "

<script type='text/javascript'>
function WSR_check_status(x)
{
	if(x==0)
	{
		document.getElementById('redirect_check').checked=false;
		document.getElementById('redirect_frame').style.display = 'none';
		document.getElementById('wp_seo_redirection_url').value='';
	}else
	{
	   	document.getElementById('redirect_check').checked=true;
	   	document.getElementById('redirect_frame').style.display= 'block';
	}

}

function redirect_check_click()
{
	if(document.getElementById('redirect_check').checked)
	WSR_check_status(1);
	else
	WSR_check_status(0);
}
</script>
";

	if($urlredirect_to =='')
	echo "<script type='text/javascript'>WSR_check_status(0);</script>";
	else
	echo "<script type='text/javascript'>WSR_check_status(1);</script>";


	}else
	{
		echo 'You can not make a redirection for the new posts before saving them.';
	}
}


//--------------------------------------------------------------------------------------------


    function WPSR_check_default_permalink()
    {
       global $util,$wp_rewrite;
       
       $file= get_home_path() . "/.htaccess";
       $filestr ="";
       $begin_marker = "# BEGIN WordPress";
       $end_marker = "# END WordPress";
       $content="ErrorDocument 404 /index.php?error=404";
       $findword = "ErrorDocument 404";
       
       if($wp_rewrite->permalink_structure =='')
       {
        
        if(file_exists($file)){
            
           $f = @fopen( $file, 'r+' );
           $filestr = @fread($f , filesize($file)); 
           
           if (strpos($filestr , $findword) === false)
            {
               if (strpos($filestr , $begin_marker) === false)
                    {
                        $filestr = $begin_marker . PHP_EOL . $content . PHP_EOL . $end_marker . PHP_EOL . $filestr ;
                        fwrite($f ,  $filestr); 
                        fclose($f);
                    }
                    else
                    {
                        fclose($f);
                        $f = fopen($file, "w");
                        $n=strpos($filestr , $begin_marker) + strlen('# BEGIN WordPress');;
                        $div1= substr($filestr,0,$n);
                        $div2= substr($filestr,($n+1),strlen($filestr));
                        $filestr = $div1 . PHP_EOL . $content . PHP_EOL . $div2;
                        fwrite($f ,  $filestr); 
                        fclose($f);
                        
                    }
            }
            
        }else
        {
          
          $filestr = $begin_marker . PHP_EOL . $content . PHP_EOL . $end_marker ;
          if($f = @fopen( $file, 'w' )){
            fwrite($f ,  $filestr); 
            fclose($f);
            $util->warning_option_msg('SEO Redirection: The <b>.htaccess</b> has been created!');
            }
            else
            {
            $util->warning_option_msg('SEO Redirection: Could not create the file <b>.htaccess</b>!');
            }
        }
       
       }
       
    }

//--------------------------------------------------------------------------------------------

//------------------------------------------------------------------------

function  WPSR_get_post_redirection($post_id)
{

global $wpdb,$util,$table_prefix ;
$table_name = $table_prefix . 'WP_SEO_Redirection';

// Autosave
if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        return;
// AJAX
if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) 
        return;
// Post revision
if ( false !== wp_is_post_revision( $post_id ) )
        return;

$redirect_from=$util->post('wp_seo_redirection_url_from');
$redirect_to=$util->post('wp_seo_redirection_url');

if($redirect_to!=''){


	$wpdb->get_results($wpdb->prepare("select ID from $table_name where postID=%d ",$post_id));

	if ($wpdb->num_rows > 0) {

	$sql = $wpdb->prepare("update $table_name set redirect_to=%s,redirect_from=%s,redirect_type='301',url_type=2 where postID=%d",$redirect_to,$redirect_from,$post_id);
		$wpdb->query($sql);

	}else
	{
		$wpdb->query($wpdb->prepare("delete from $table_name where redirect_from=%s",$redirect_from));
		$sql = $wpdb->prepare("insert into $table_name(redirect_from,redirect_to,redirect_type,url_type,postID) values (%s,%s,'301',2,%d) ",$redirect_from,$redirect_to,$post_id);
		$wpdb->query($sql);
	}


	}else
	{
	$wpdb->query($wpdb->prepare("delete from $table_name where postID=%d",$post_id));
	}

	$SR_redirect_cache = new free_SR_redirect_cache();
	$SR_redirect_cache->free_cache();
}



//-------------------------------------------------------------

function WPSR_log_404_redirection($link)
{
	global $wpdb,$table_prefix,$util ;
	$table_name = $table_prefix . 'WP_SEO_404_links';
    
	$referrer=$util->get_ref();
	$ip=$util->get_visitor_IP();
	$country=$util->get_visitor_country();
	$os=$util->get_visitor_OS();
	$browser=$util->get_visitor_Browser();

	if($os!='Unknown' || $browser!='Unknown'){
		$wpdb->query($wpdb->prepare(" insert IGNORE into $table_name(ctime,link,referrer,ip,country,os,browser) values(NOW(),%s,%s,%s,%s,%s,%s) ",$link,$referrer,$ip,$country,$os,$browser));
	}
}


//-------------------------------------------------------------

function WPSR_log_redirection_history($rID,$postID, $rfrom, $rto, $rtype,$rsrc)
{
	global $wpdb,$table_prefix,$util ;
	$table_name = $table_prefix . 'WP_SEO_Redirection_LOG';

	$referrer=$util->get_ref();
	$ip=$util->get_visitor_IP();
	$country=$util->get_visitor_country();
	$os=$util->get_visitor_OS();
	$browser=$util->get_visitor_Browser();

	$wpdb->query($wpdb->prepare(" insert into $table_name(rID,postID,rfrom,rto,rtype,rsrc,ctime,referrer,ip,country,os,browser) values(%d ,%d,%s,%s,%s,%s,NOW(),%s,%s,%s,%s,%s) ",$rID,$postID,$rfrom,$rto,$rtype,$rsrc,$referrer,$ip,$country,$os,$browser));
    
    $limit= $util->get_option_value('history_limit');
    
    $expdate = date('Y-n-j', time() - (intval($limit) * 24 * 60 * 60));
    $wpdb->query("delete FROM $table_name WHERE date_format(date(ctime),'%Y-%m-%d') < date_format(date('$expdate'),'%Y-%m-%d')");

}

//-------------------------------------------------------------

function WPSR_make_redirect($redirect_to,$redirect_type,$redirect_from,$obj='')
{
	global $util,$post;

        if($redirect_to == $redirect_from || !$util->is_valid_url($redirect_to))
        return 0;

	if(is_object($obj) && $obj->redirect_to_type=='Folder' && $obj->redirect_to_folder_settings=='2' ){

		if($obj->redirect_from_type=='Folder')
		{

			if($obj->redirect_from_folder_settings=='2' || $obj->redirect_from_folder_settings=='3')
			{
				if(strlen($redirect_from) > strlen($obj->redirect_from))
				{
					$difference=substr($redirect_from,intval(strlen($obj->redirect_from)-strlen($redirect_from)));
					$redirect_to = $redirect_to . $difference;
				}
			}

		}
		else if ($obj->redirect_from_type=='Regex')
		{
			$page=substr(strrchr($redirect_from, "/"), 1);
			$redirect_to = $redirect_to . '/' . $page;
		}

	}

	$rID=0;
	$rsrc='404';
	$postID=0;

	if(is_object($obj))
	{
		$rID=$obj->ID;
		$postID=$obj->postID;
		if($obj->url_type==1)
		$rsrc='Custom';
		else if($obj->url_type==2)
		$rsrc='Post';

	}

    if($util->get_option_value('history_status')=='1'){
	WPSR_log_redirection_history($rID,$postID, $redirect_from, $redirect_to, $redirect_type,$rsrc);
    }

	$redirect_to = $util->make_absolute_url($redirect_to);


	if(is_singular())
	{
		$SR_redirect_cache = new free_SR_redirect_cache();
		$SR_redirect_cache->add_redirect($post->ID,1,$redirect_from,$redirect_to,$redirect_type);
	}

	if($redirect_type=='301')
	{
  		header ('HTTP/1.1 301 Moved Permanently');
		header ("Location: " . $redirect_to);
		exit();
	}
	else if($redirect_type=='307')
	{
		header ('HTTP/1.0 307 Temporary Redirect');
		header ("Location: " . $redirect_to);
		exit();
	}
	else if($redirect_type=='302')
	{
		header ("Location: " . $redirect_to);
		exit();
	}

}


//-------------------------------------------------------------

function WPSR_redirect()
{
global $wpdb,$post,$table_prefix,$util ;

if($util->get_option_value('plugin_status')=='1'){

	$table_name = $table_prefix . 'WP_SEO_Redirection';
	$permalink= urldecode($util->get_current_relative_url());
	if(substr($permalink,0,1)==":")
	{
		$first_slash=stripos($permalink,"/");
		$permalink = substr($permalink,$first_slash,strlen($permalink)-$first_slash);
	}
	$post_cache_result="";
	$SR_redirect_cache = new free_SR_redirect_cache();

	if(is_singular())
	{
		$post_cache_result=$SR_redirect_cache->redirect_cached($post->ID);
	}
	if($post_cache_result == 'not_redirected')
	{
		return 0;
	}

        
        
	$permalink_alternative="";
	if(substr($permalink,-1)=='/')
	{
		$permalink_alternative = substr($permalink,0,intval(strlen($permalink)-1));
	}else
	{
		$permalink_alternative = $permalink . '/';
	}

	//$permalink_options = "(redirect_from='$permalink' or redirect_from='$permalink_alternative' )";
	//$permalink_regex_options = "('$permalink' regexp regex or '$permalink_alternative'  regexp regex )";

$permalink_options = $wpdb->prepare("(redirect_from=%s or redirect_from=%s )", $permalink, $permalink_alternative);
$permalink_regex_options = $wpdb->prepare("(%s regexp regex or %s regexp regex )", $permalink, $permalink_alternative);

		if (($util->get_option_value('redirect_control_panel')!='1') || ($util->get_option_value('redirect_control_panel')=='1' && !preg_match('/^' . str_replace('/','\/', get_admin_url()) . '/i', $permalink) && !preg_match('/^' . str_replace('/','\/', site_url()) . '\/wp-login.php/i', $permalink))){


		$theurl = $wpdb->get_row(" select * from $table_name where enabled=1 and regex='' and $permalink_options  ");

			if($wpdb->num_rows>0 && $theurl->redirect_to!=''){
                            WPSR_make_redirect($theurl->redirect_to,$theurl->redirect_type,$permalink,$theurl);
			}

		$theurl = $wpdb->get_row(" select * from $table_name where enabled=1 and regex<>'' and $permalink_regex_options order by LENGTH(regex) desc ");
			if($wpdb->num_rows>0 && $theurl->redirect_to!=''){
			WPSR_make_redirect($theurl->redirect_to,$theurl->redirect_type,$permalink,$theurl);
			}


			if(is_404())
			{

				 if($util->get_option_value('p404_discovery_status')=='1'){
				 WPSR_log_404_redirection($permalink);
				 }

				$options= $util->get_my_options();
				if($options['p404_status']=='1'){

					 WPSR_make_redirect($options['p404_redirect_to'],'301',$permalink);

				}
			}
		}


	if(is_singular() && $post_cache_result == 'not_found')
	{
		$SR_redirect_cache->add_redirect($post->ID,0,'','',0);
	}

}
}

//---------------------------------------------------------------

function WPSR_header_code()
{

	if( is_admin() && array_key_exists('page',$_GET) && $_GET['page']=='seo-redirection.php' ) {
	wp_register_style( 'c_admin_css_common', WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)) . '/common/' . "style.css" );
	wp_register_style( 'c_admin_css_custom', WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)) . '/custom/' . "style.css" );
	wp_enqueue_script('jquery');
	wp_enqueue_style('c_admin_css_common');
	wp_enqueue_style('c_admin_css_custom');
	}
}

//---------------------------------------------------------------

function WPSR_admin_menu() {
	add_options_page('SEO Redirection', 'SEO Redirection', 'manage_options', basename(__FILE__), 'WPSR_options_menu'  );
}

//---------------------------------------------------------------
function WPSR_options_menu() {
global $util;

	if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}


	if($util->get_option_value('plugin_status')!='1')
		$util->info_option_msg('SEO Redirection is disabled now, you can go to option tab and enable it!');

	echo '<div class="wrap"><h2>SEO Redirection Free</h2><br/>';
        
        if(is_multisite())
        {
            
            echo '<div class="error" id="message"><p></p><div class="warning_icon"></div>This version does not support Multisite WordPress installation, you may face troubles like losing redirects when adding new sites to your network, the premium version supports multisite well (<a target="_blank" href="http://www.clogica.com/product/seo-redirection-premium-wordpress-plugin">
http://www.clogica.com/product/seo-redirection-premium-wordpress-plugin</a>) <p></p></div>';
            
        }

	$mytabs = new phptab();

	$mytabs->set_ignore_parameter(array('del','search','page_num','add','edit','page404'));
	$mytabs->add_file_tab('cutom','Custom Redirects','option_page_custome_redirection.php','file');
	$mytabs->add_file_tab('posts','Post Redirects','option_page_post_redirection_list.php','file');
	$mytabs->add_file_tab('404','404 Error Links','option_page_404.php','file');
	$mytabs->add_file_tab('history','History','option_page_history.php','file');
	$mytabs->add_file_tab('goptions','Options','option_page_goptions.php','file');
	$mytabs->add_file_tab('premium','<span style="color:brown;"><b>&#9658; Premium Features</b></span>','premium.php','file');
	$mytabs->run();
   
}


function WPSR_upgrade(){

	$util= new clogica_util_1();
        $util->init(WP_SEO_REDIRECTION_OPTIONS, __FILE__);
	
		
	if($util->get_option_value('plugin_version')!= WP_SEO_REDIRECTION_VERSION)
	{
		WPSR_install();
		$util->update_option('plugin_version', WP_SEO_REDIRECTION_VERSION);
	}
}

//-----------------------------------------------------
function WPSR_install(){
        global $wpdb,$table_prefix ;
		
        $util= new clogica_util_1();
        $util->init(WP_SEO_REDIRECTION_OPTIONS, __FILE__);
	
	$options=get_option(WP_SEO_REDIRECTION_OPTIONS);
	if(!is_array($options))
	{
		add_option(WP_SEO_REDIRECTION_OPTIONS);
		$options= array();
	}
	
	
	    if(!array_key_exists('plugin_status',$options))
			$options['plugin_status']= '1';
	
		if(!array_key_exists('redirection_base',$options))
       		$options['redirection_base']= site_url();
	
        if(!array_key_exists('redirect_control_panel',$options))
			$options['redirect_control_panel']= '1';
	
        if(!array_key_exists('show_redirect_box',$options))
			$options['show_redirect_box']= '1';
	
		if(!array_key_exists('reflect_modifications',$options))
			$options['reflect_modifications']= '1';
	
        if(!array_key_exists('history_status',$options))
			$options['history_status']= '1';
	
        if(!array_key_exists('history_limit',$options))
			$options['history_limit']= '30';
	
        if(!array_key_exists('p404_discovery_status',$options))
			$options['p404_discovery_status']= '1';
	
        if(!array_key_exists('p404_redirect_to',$options))
			$options['p404_redirect_to']= site_url();
	
        if(!array_key_exists('p404_status',$options))
			$options['p404_status']= '2';
	
        if(!array_key_exists('keep_data',$options))
			$options['keep_data']= '1';	
	
    	update_option(WP_SEO_REDIRECTION_OPTIONS,$options);
	

	$table_name = $table_prefix . 'WP_SEO_Redirection';
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			$sql = "
                  CREATE TABLE IF NOT EXISTS `$table_name` (
                  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
                  `enabled` int(1) NOT NULL DEFAULT '1',
                  `redirect_from` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                  `redirect_from_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                  `redirect_from_folder_settings` int(1) NOT NULL,
                  `redirect_from_subfolders` int(1) NOT NULL DEFAULT '1',
                  `redirect_to` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                  `redirect_to_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                  `redirect_to_folder_settings` int(1) NOT NULL DEFAULT '1',
                  `regex` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                  `redirect_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                  `url_type` int(2) NOT NULL DEFAULT 1,
                  `postID` int(11) unsigned DEFAULT NULL,
                  PRIMARY KEY (`ID`),
                  UNIQUE KEY `redirect_from` (`redirect_from`)
                )ENGINE = MyISAM ;";
			$wpdb->query($sql); 
		}else
		{
                    //check if Innodb convert it to myisam.                
                    $status = $wpdb->get_row("SHOW TABLE STATUS WHERE Name = '$table_name'");
                    if($status->Engine == 'InnoDB')
                    {
                        $wpdb->query("alter table $table_name engine = MyISAM;");
                    }

                    // if the table exists
                    $redirects = $wpdb->get_results(" select redirect_from,redirect_to,ID from $table_name; ");
                    foreach ($redirects as $redirect)
                    {
                            $redirect_from=$util->make_relative_url($redirect->redirect_from);
                            $redirect_to=$util->make_relative_url($redirect->redirect_to);
                            $ID=$redirect->ID;
                            $wpdb->query(" update $table_name set  redirect_from='$redirect_from',redirect_to='$redirect_to'  where ID=$ID ");
                    }

		}

	$table_name = $table_prefix . 'WP_SEO_Cache';
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql = "
    		CREATE TABLE IF NOT EXISTS `$table_name` (
              `ID` int(11) unsigned NOT NULL,
              `is_redirected` int(1) unsigned NOT NULL,
              `redirect_from` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `redirect_to` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `redirect_type` int(3) unsigned NOT NULL DEFAULT 301,
              PRIMARY KEY (`ID`)
            ) ENGINE = MyISAM ;
			";
		$wpdb->query($sql);
	}else
        {
            //check if Innodb convert it to myisam.                
            $status = $wpdb->get_row("SHOW TABLE STATUS WHERE Name = '$table_name'");
            if($status->Engine == 'InnoDB')
            {
                $wpdb->query("alter table $table_name engine = MyISAM;");
            }
        }
        
               
        if($wpdb->get_var(" SELECT count(*) as cnt FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_NAME = '$table_name'
			AND table_schema = DATABASE()
                        AND COLUMN_NAME = 'redirect_from' ") == '0') {

                $sql="
                ALTER TABLE $table_name
                ADD COLUMN `redirect_from` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
            ";
                $wpdb->query($sql);
            }


	$table_name = $table_prefix . 'WP_SEO_404_links';
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			$sql = "
			CREATE TABLE IF NOT EXISTS `$table_name` (
              `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `ctime` datetime NOT NULL,
              `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `referrer` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `ip` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
              `country` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
              `os` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
              `browser` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
              PRIMARY KEY (`ID`),
              UNIQUE KEY `link` (`link`)
            ) ENGINE = MyISAM ;
			";
			$wpdb->query($sql);
		}else
                {
                     //check if Innodb convert it to myisam.                
                    $status = $wpdb->get_row("SHOW TABLE STATUS WHERE Name = '$table_name'");
                    if($status->Engine == 'InnoDB')
                    {
                        $wpdb->query("alter table $table_name engine = MyISAM;");
                    }
                }
		
		
		$table_name = $table_prefix . 'WP_SEO_Redirection_LOG';
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			$sql = "
    		CREATE TABLE IF NOT EXISTS `$table_name` (
              `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `rID` int(11) unsigned DEFAULT NULL,
              `postID` int(11) unsigned DEFAULT NULL,
              `ctime` datetime NOT NULL,
              `rfrom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `rto` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `rtype` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `rsrc` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
              `referrer` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `ip` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
              `country` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
              `os` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
              `browser` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
              PRIMARY KEY (`ID`)
            ) ENGINE = MyISAM ;
			";
			
			$wpdb->query($sql);
		}else
                {
                    //check if Innodb convert it to myisam.                
                    $status = $wpdb->get_row("SHOW TABLE STATUS WHERE Name = '$table_name'");
                    if($status->Engine == 'InnoDB')
                    {
                        $wpdb->query("alter table $table_name engine = MyISAM;");
                    }
                }
		
}


//---------------------------------------------------------------


function WPSR_uninstall(){
    global $wpdb,$table_prefix ;
    
    $util= new clogica_util_1();
    $util->init(WP_SEO_REDIRECTION_OPTIONS, __FILE__);

    
    if($util->get_option_value('keep_data')!='1'){
        
        $table_name = $table_prefix . 'WP_SEO_Redirection';
        $wpdb->query($wpdb->prepare(" DROP TABLE %s  ",$table_name));

		$table_name = $table_prefix . 'WP_SEO_Cache';
		$wpdb->query($wpdb->prepare(" DROP TABLE %s  ",$table_name));

        $table_name = $table_prefix . 'WP_SEO_404_links';
		$wpdb->query($wpdb->prepare(" DROP TABLE %s  ",$table_name));
        
        $table_name = $table_prefix . 'WP_SEO_Redirection_LOG';
		$wpdb->query($wpdb->prepare(" DROP TABLE %s  ",$table_name));
        
        
		$util->delete_my_options();    
    }
	
	

}

//---------------------------------------------------------------

?>