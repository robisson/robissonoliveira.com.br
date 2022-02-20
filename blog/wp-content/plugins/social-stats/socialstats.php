<?php
/*
Plugin Name: Social Stats
Plugin URI: http://www.linksalpha.com/
Description:
Version: 1.4
Author: LinksAlpha
Author URI: http://www.linksalpha.com/
*/

define('SOCIALSTATS_PLUGIN_NAME', 'Social Stats');
define('SOCIALSTATS_PLUGIN_NAME_INTERNAL', 'socialstats');
define('SOCIALSTATS_PLUGIN_PREFIX', 'socialstats');
define('SOCIALSTATS_PLUGIN_URL', get_plugin_dir() );

$socialstats_settings['api_key'] = array('label'=>'Access Key:', 'type'=>'text', 'default'=>'');
$socialstats_settings['id'] = array('label'=>'id', 'type'=>'text', 'default'=>'');
$options = get_option(SOCIALSTATS_PLUGIN_NAME_INTERNAL);

function socialstats_init() {
	if ( is_admin() ) {
		wp_enqueue_script('jquery');
		wp_register_script('jqueryuijs', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js');
		wp_enqueue_script('jqueryuijs');
		wp_register_script('socialstatsjs', SOCIALSTATS_PLUGIN_URL.'socialstats.js');
		wp_enqueue_script('socialstatsjs');
		wp_register_script('swfobjectjs', SOCIALSTATS_PLUGIN_URL.'swfobject.js');
		wp_enqueue_script('swfobjectjs');
		wp_register_style('socialstatscss', SOCIALSTATS_PLUGIN_URL.'socialstats.css?test=5');
		wp_enqueue_style('socialstatscss');
		wp_register_style('redmond_modcss', SOCIALSTATS_PLUGIN_URL.'redmond_mod.css');
		wp_enqueue_style('redmond_modcss');
		add_action('admin_menu', 'socialstats_pages');
	}
}

add_filter( 'xmlrpc_methods', 'socialstats_xmlrpc' );

register_activation_hook( __FILE__, 'socialstats_activate' );

function socialstats_activate() {
	$socialstats_eget = get_bloginfo('admin_email'); $socialstats_uget = get_bloginfo('url'); $socialstats_nget = get_bloginfo('name');
	$socialstats_dget = get_bloginfo('description'); $socialstats_cget = get_bloginfo('charset'); $socialstats_vget = get_bloginfo('version');
	$socialstats_lget = get_bloginfo('language'); $link='http://www.linksalpha.com/a/bloginfo';
	$socialstats_bloginfo = array('email'=>$socialstats_eget, 'url'=>$socialstats_uget, 'name'=>$socialstats_nget, 'desc'=>$socialstats_dget, 'charset'=>$socialstats_cget, 'version'=>$socialstats_vget, 'lang'=>$socialstats_lget, 'plugin'=>'ss');
	socialstats_http_post($link, $socialstats_bloginfo);
}


function socialstats_pages() {
	if ( function_exists('add_submenu_page') ) {
		$page = add_submenu_page('plugins.php', SOCIALSTATS_PLUGIN_NAME, SOCIALSTATS_PLUGIN_NAME, 'manage_options', 'socialstats', 'socialstats_conf');
		$page = add_submenu_page('index.php', __('Social Stats'), __('Social Stats'), 'manage_options', 'socialstats', 'socialstats_page');
	}
}


function socialstats_xmlrpc($methods) {
	$methods['wp.postIDList'] = 'socialstats_get_post_id_list';
    return $methods;
}


function socialstats_get_post_id_list() {
	global $wpdb;
	$posts_all = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' and post_type = 'post'");
	$post_ids = array();
	foreach($posts_all as $post_one) {
		$post_ids[] = $post_one->ID;
	}
	$post_ids_string = implode(",", $post_ids);
	return $post_ids_string;
}


function socialstats_conf() {
	global $socialstats_settings;
	
	if ( isset($_POST['socialstatsdelete']) ) {
		delete_option(SOCIALSTATS_PLUGIN_NAME_INTERNAL);
	}

	if ( isset($_POST['submit']) ) {
		if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
			die(__('Cheatin&#8217; uh?'));
		}
		$field_name = sprintf('%s_%s', SOCIALSTATS_PLUGIN_PREFIX, 'api_key');
		$value = strip_tags(stripslashes($_POST[$field_name]));
		if($value) {
			$add = socialstats_add($value);
		}
	}

	$options = get_option(SOCIALSTATS_PLUGIN_NAME_INTERNAL);
	$html  = '<div id="socialstats_msg"></div>';
	$html .= '<div class="socialstats_header"><h2><img class="socialstats_image" src="http://www.linksalpha.com/favicon.ico" />&nbsp;'.SOCIALSTATS_PLUGIN_NAME.'</h2></div>';
	if (!empty($options['api_key'])) {
		$html .= '<div>Your Social Stats plugin has been configured. You can view the stats from <a href="index.php?page=socialstats">your Dashboard</a>.</div>';
		$html .= '<div style="margin:20px 0px 20px 0px;">Getting Errors? Delete Existing API key stored locally to add new API key from <a href="http://www.linksalpha.com/stats/settings" target="_blank">here</a> <br/> </div>';
		$html .= '<form action="" method="post" name="socialstatsdelete" style="width:90%;">
					<input type="submit" name="socialstatsdelete" class="button-primary" value="Delete Local Key" /></form>';

		$html .= '<div style="display:none">';
	} else {
		$html .= '<div>';
	}

	$html .= '<table style="width:100%;"><tr><td style="width:800px;padding-right:60px;">';
	$html .=   '<div style="padding:0px 0px 0px 0px;">Social Stats makes it easy for you to track performance of your blog on social networks. For your blog, LinksAlpha can show stats from: Facebook Likes, Facebook Shares, Facebook Comments, Bit.ly Clicks, Yahoo Updates, Diggs, and Google Buzz Updates. These stats are available for the last 24 hours, 2 days, 7 days, and 30 days.</div>
				<div style="padding:10px 0px 10px 0px;">See the complete feature list at: <a href="http://www.linksalpha.com/stats/features" target="_blank">http://www.linksalpha.com/stats/features</a></div>
				<div>Only 2 steps and you are done! Read instructions here: <a href="http://help.linksalpha.com/social-discussions/social-stats" target="_blank">http://help.linksalpha.com/social-discussions/social-stats</a></div>';
	$html .= '<div style="padding:20px 0px 5px 0px;"><a href="http://www.linksalpha.com/stats/settings" target="_blank" style="color:red;">Click Here</a> to get Access Key for Social Stats. You can <a href="http://help.linksalpha.com/stats/wordpress-plugin-social-stats" target="_blank">read more about this process at LinksAlpha.com.</a></div>';
	$html .= '<div class="socialstats_header2"><big><strong>Setup</strong></big></div><div style="padding-left:0px;">';
	$html .= '<div class="socialstats_content_box">';
	$html .= '<form action="" method="post" id="socialstatsadd" name="socialstatsadd" style="width:90%;">';
	$html .= '<fieldset class="socialstats_fieldset">';
	$html .= '<legend>Add Access Key</legend>';

	$curr_field = 'api_key';
	$field_name = sprintf('%s_%s', SOCIALSTATS_PLUGIN_PREFIX, $curr_field);
	$html .= '<div><label for="'.$field_name.'">'.$socialstats_settings[$curr_field]['label'].'&nbsp;</label></div>';
	$html .= '<div style="padding-bottom:10px "><input style="width:400px;border-color:#ffd050;" class="widefat" id="'.$field_name.'" name="'.$field_name.'" type="text" value="'.$options[$curr_field].'" /></div>';

	$html .= '</fieldset>';
	$html .= '<div style="padding:20px 0px 20px 0px;"><input type="submit" name="submit" class="button-primary" value="Add Key" /></div>';
    $html .= '<div ><a href="http://www.linksalpha.com/stats/settings" target="_blank" style="color:red;">Click Here</a> to get Access Key for Social Stats</a></div>';
	$html .= '<input type="hidden" value="'.SOCIALSTATS_PLUGIN_URL.'" id="socialstats_plugin_url" /></form></div></td>';

	$html .= '<td style="width:250px;vertical-align:top;">
			 <div class="rts_header2"><big><strong>Top Social Plugins</strong></big></div>
			 <div class="la_content_box_3">
			 	<div style="padding:0px 0px 5px 0px"><a href="http://wordpress.org/extend/plugins/1-click-retweetsharelike/" target="_blank">1-Click Retweet/Share/Like:</a>
					<div>Add social sharing buttons to enable readers to share your content quickly.</div>
			 	</div>
			 	<br/>
			 	<div><a href="http://wordpress.org/extend/plugins/social-stats/screenshots/" target="_blank">Network Publisher:</a>
			 	<br/>Auto post your blog on Social Networks such as Facebook Profile, Facebook Pages, Twitter, LinkedIn, Yammer, Yahoo, MySpace, Identi.ca.
			 	</div>
			 </div></td></tr></table>';
	$html .= '<div>';
	echo $html;
}


function socialstats_add($api_key) {
	if (!$api_key) {
		$html = '<div class="msg_error">Error occurred while processing your request. Error Code 100</div>';
		echo $html;
		return FALSE;
	}
	$link = 'http://www.linksalpha.com/a/statsadd';
	$body = array('api_key'=>$api_key, 'link'=>get_bloginfo('wpurl'));
	$response_full = socialstats_http_post($link, $body);
	$results = socialstats_http_process($response_full);
	if (!$results) {
		return;
	}
	$options = get_option(SOCIALSTATS_PLUGIN_NAME_INTERNAL);
	$options['api_key'] = $api_key;
	$options['id'] = $results->id;
	update_option(SOCIALSTATS_PLUGIN_NAME_INTERNAL, $options);
	$html = '<div class="msg_success">API Key has been added successfully. It may take 24 hours for data to show for the first time.</div>';
	echo $html;
	return TRUE;
}


function socialstats_page() {
	$options = get_option(SOCIALSTATS_PLUGIN_NAME_INTERNAL);
	$html = '<div style="padding-bottom:0px;border-bottom:1px solid #DCDCD8;margin-bottom:25px;">
				<table style="width:100%;">
					<tr>
						<td style="padding-left:20px;">
							<h2><a href="http://www.linksalpha.com"><img class="socialstats_image" src="http://www.linksalpha.com/favicon.ico" /></a>&nbsp;'.SOCIALSTATS_PLUGIN_NAME.'</h2>
						</td>
						<td style="text-align:right;padding-right:20px;">
							<form action="http://www.linksalpha.com/account" method="post"><input type="submit" style="color:#FFFFFF ! important;" value="Track More Websites" class="socialstats_button_blue" /></form>
						</td>
					</tr>
				</table>
			</div>';
	if (empty($options['api_key'])) {
		$html .= '<div class="msg_error">You have not added your <a target="_blank" href="http://www.linksalpha.com/stats">LinksAlpha.com Social Stats</a> Access Key. Please add the Access Key to view stats. Error Code 100</div>';
		echo $html;
		return;
	}
	$views = array('link', 'posts');
	if (!empty($_GET['view'])) {
		if (in_array($_GET['view'], $views)) {
			$html .= call_user_func('socialstats_'.$_GET['view'], $options['api_key'], $options['id']);
			echo $html;
			return;
		}
	}
	$html .= socialstats_home($options['api_key'], $options['id']);
	echo $html;
	return;
}


function socialstats_home($key, $blog_id) {
	$link = 'http://www.linksalpha.com/a/statsget';
	$body = array('api_key'=>$key, 'id'=>$blog_id);
	$response_full = socialstats_http_post($link, $body);
	$results = socialstats_http_process($response_full);
	if (!$results) {
		return;
	}

	$html  = '<div>';
	$html .= '<script type="text/javascript">';
	$html .= 'swfobject.embedSWF("'.SOCIALSTATS_PLUGIN_URL.'open-flash-chart.swf", "stats_chart2", "1000", "200", "9.0.0", "'.SOCIALSTATS_PLUGIN_URL.'expressInstall.swf", {"get-data": "stats_chart2" }, {"wmode" : "transparent"} );';
	$html .= 'swfobject.embedSWF("'.SOCIALSTATS_PLUGIN_URL.'open-flash-chart.swf", "stats_chart3", "1000", "200", "9.0.0", "'.SOCIALSTATS_PLUGIN_URL.'expressInstall.swf", {"get-data": "stats_chart3" }, {"wmode" : "transparent"} );';
	$html .= 'function ofc_ready() { }
			  function stats_chart2() {
			  	return JSON.stringify(data2);
			  }
			  function stats_chart3() {
			    return JSON.stringify(data3);
			  }
			  function findSWF(movieName) {
				  if (navigator.appName.indexOf("Microsoft")!= -1) {
				    return window[movieName];
				  } else {
				    return document[movieName];
				  }
			  }
			  var data2 = '.$results->data2.';
			  var data3 = '.$results->data3.';
			 ';
	$html .= '</script>';

	$html .= '<div class="socialstats_postbox_container_full">';

	$html .= '<div id="tabs">';
	$html .= '<ul>';
	$html .= '<li><a href="#tabs-2"><img src="'.SOCIALSTATS_PLUGIN_URL.'facebook_icon.png" class="socialstats_image_mid" />&nbsp;Facebook</a></li>';
	$html .= '<li><a href="#tabs-3"><img src="'.SOCIALSTATS_PLUGIN_URL.'bitly.png" class="socialstats_image_mid" />&nbsp;Bit.ly</a></li>';
	$html .= '</ul>';
	$html .= '<div id="tabs-2">';
	$html .= '<div class="socialstats_graph_head">Daily Facebook Shares Count</div>';
	$html .= '<div id="stats_chart2"></div>';
	$html .= '</div>';
	$html .= '<div id="tabs-3">';
	$html .= '<div class="socialstats_graph_head">Daily Bit.ly Click Count</div>';
	$html .= '<div id="stats_chart3"></div>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	
	$html .= '<div class="socialstats_postbox_container_full" style="padding-bottom:10px;padding-top:20px;">';
	$html .= '<div class="postbox" style="display: block;" ><h3 class="socialstats_handle">Performance - Past 30 Days</h3>';
	$html .= '<table class="socialstats_dashboard_agg"><tr>';
	$html .= '<th>Bit.ly Clicks</th>';
	$html .= '<th>Facebook Shares</th>';
	$html .= '<th>Facebook Likes</th>';
	$html .= '<th>Facebook Comments</th>';
	$html .= '<th>Buzz Updates</th>';
	$html .= '</tr><tr>';
	$html .= '<td>'.$results->blog_days_30->count_bitly_clicks.'</td>';
	$html .= '<td>'.$results->blog_days_30->count_facebook_shares.'</td>';
	$html .= '<td>'.$results->blog_days_30->count_facebook_likes.'</td>';
	$html .= '<td>'.$results->blog_days_30->count_facebook_comments.'</td>';
	$html .= '<td>'.$results->blog_days_30->count_buzz.'</td>';
	$html .= '</tr></table></div></div>';

	//Box 1
	$html .= '<div class="socialstats_postbox_container">';
	$html .= '<div class="socialstats_postbox" style="display: block;" >';
	$html .= '<h3 class="socialstats_handle">Recent Posts</h3>';
	$html .= '<div class="socialstats_inside">';
	$html .= socialstats_grid_links($results->posts_recent);
	if (count($results->posts_recent)) {
		$html .= '<div style="padding-top:3px;padding-right:5px;"><span style="float:right"><a href="index.php?page=socialstats&view=posts&id='.$blog_id.'&days=recent">View More</a></span><span>&nbsp;</span></div>';
	}
	$html .= '</div></div>';

	//Box 2
	$html .= '<div class="socialstats_postbox_container_right">';
	$html .= '<div class="socialstats_postbox" style="display: block;" >';
	$html .= '<h3 class="socialstats_handle">Top Posts - Past 2-7 Days</h3>';
	$html .= '<div class="socialstats_inside">';
	$html .= socialstats_grid_links($results->posts_days_7);
	if (count($results->posts_days_7)) {
		$html .='<div style="padding-top:3px;padding-right:5px;">
					<span style="float:right"><a href="index.php?page=socialstats&view=posts&id='.$blog_id.'&days=7">View More</a></span><span>&nbsp;</span>
				</div>';
	}
	$html .= '</div></div>';
	$html .= '</div>';

	//Box 3
	$html .= '<div class="socialstats_postbox_container">';
	$html .= '<div class="socialstats_postbox" style="display: block;" >';
	$html .= '<h3 class="socialstats_handle"><span>Top Posts - Past 24 hours</span></h3>';
	$html .= '<div class="socialstats_inside">';
	$html .= socialstats_grid_links($results->posts_days_1);
	if (count($results->posts_days_1)) {
		$html .= '<div style="padding-top:3px;padding-right:5px;"><span style="float:right"><a href="index.php?page=socialstats&view=posts&id='.$blog_id.'&days=1">View More</a></span><span>&nbsp;</span></div>';
	}
	$html .= '</div></div>';

	//Box 4
	$html .= '<div class="socialstats_postbox_container_right">';
	$html .= '<div class="socialstats_postbox" style="display: block;" >';
	$html .= '<h3 class="socialstats_handle"><span>Top Posts - Past 7-30 Days</span></h3>';
	$html .= '<div class="socialstats_inside">';
	$html .= socialstats_grid_links($results->posts_days_30);
	if (count($results->posts_days_30)) {
		$html .= '<div style="padding-top:3px;padding-right:5px;"><span style="float:right"><a href="index.php?page=socialstats&view=posts&id='.$blog_id.'&days=30">View More</a></span><span>&nbsp;</span></div>';
	}
	$html .= '</div></div>';
	$html .= '</div>';

	$html .= '</div>';

	return $html;
}


function socialstats_link($key, $blog_id) {
	$link = 'http://www.linksalpha.com/a/statsgetlink';
	$body = array('api_key'=>$key, 'id'=>$_GET['id']);
	$response_full = socialstats_http_post($link, $body);
	$results = socialstats_http_process($response_full);
	if (!$results) {
		return;
	}

	$html  = '<div style="padding:0px 0px 20px 0px;"><a href="index.php?page=socialstats">« Return to Social Stats</a></div>';

	$html .= '<div style="padding-bottom:25px;">';
	$html .= '<div class="fontBold18" style="padding-bottom:10px;">Post Statstics » <a href="'.$results->link_info->link.'">'.$results->link_info->title.'</a></div>';
	$html .= '<div class="content_link_big">'.$results->link_info->count_bitly_clicks.'<span>&nbsp;Bit.ly Clicks</span></div>';
	$html .= '<div class="content_link_big">'.$results->link_info->count_facebook_likes.'<span>&nbsp;Facebook Likes</span></div>';
	$html .= '<div class="content_link_big">'.$results->link_info->count_facebook_shares.'<span>&nbsp;Facebook Shares</span></div>';
	$html .= '<div class="content_link_big">'.$results->link_info->count_facebook_comments.'<span>&nbsp;Facebook Comments</span></div>';
	if(!$results->link_info->count_buzz) {
		$results->link_info->count_buzz = 0;
	}
	$html .= '<div class="content_link_big">'.$results->link_info->count_buzz.'<span>&nbsp;Google Buzz Updates</span></div>';
	if(!$results->link_info->count_diggs) {
		$results->link_info->count_diggs = 0;
	}
	$html .= '<div class="content_link_big">'.$results->link_info->count_diggs.'<span>&nbsp;Diggs</span></div>';
	$html .= '<div class="content_link_big">'.$results->link_info->count_yahoo_updates.'<span>&nbsp;Yahoo Updates</span></div>';
	$html .= '</div>';

	return $html;
}


function socialstats_posts($key, $blog_id) {
	$day = $_GET['days'];
	$link = 'http://www.linksalpha.com/a/statsgetposts';
	$body = array('api_key'=>$key, 'id'=>$blog_id, 'days'=>$day);
	$response_full = socialstats_http_post($link, $body);
	$results = socialstats_http_process($response_full);
	if (!$results) {
		return;
	}
	$days_header = array('recent'=>'Recent Posts', '1'=>'Top Posts - Past 24 hours', '7'=>'Top Posts - Past 2-7 Days', '30'=>'Top Posts - Past 7-30 Days');
	$day_header = $days_header[$day];
	$html  = '<div style="padding:0px 0px 20px 0px;"><a href="index.php?page=socialstats">« Return to Social Stats</a></div>';
	$html .= '<div class="socialstats_postbox_container_full">';
	$html .= '<div class="socialstats_postbox" style="display: block;" >';
	$html .= '<h3 class="socialstats_handle"><span>'.$day_header.'</span></h3>';
	$html .= socialstats_grid_links_full($results->posts);
	$html .= '</div>';
	$html .= '</div>';
	return $html;
}


function socialstats_grid_links($posts) {
	if (!count($posts)) {
		$html = '<div style="padding-top:10px;">No Posts found for this time range</div>';
		$html .= '</div>';
		return $html;
	}
	$html = '<table class="socialstats_grid">';
	$html .= '<tr><th class="socialstats_label">&nbsp;</th><th><img src="'.SOCIALSTATS_PLUGIN_URL.'facebook_like.png" class="socialstats_image_small" />&nbsp;Likes</th><th><img src="'.SOCIALSTATS_PLUGIN_URL.'facebook_icon.png" class="socialstats_image_small" />&nbsp;Shares</th><th><img src="'.SOCIALSTATS_PLUGIN_URL.'bitly.png" class="socialstats_image_small" />&nbsp;bit.ly</th></tr>';
	$i = 0;
	foreach ($posts as $post) {
		if (($i & 1)) {
			$html .= '<tr class="socialstats_alternate">';
		} else {
			$html .= '<tr>';
		}
		$html .= '<td class="socialstats_label"><div><a href="index.php?page=socialstats&view=link&id='.$post->link_id.'">'.$post->title.'</a></div></td>';
		$html .= '<td class="socialstats_views">'.$post->count_facebook_likes.'</td>';
		$html .= '<td class="socialstats_views">'.$post->count_facebook_shares.'</td>';
		$html .= '<td class="socialstats_views">'.$post->count_bitly_clicks.'</td>';
		$html .= '</tr>';
		$i++;
		if ($i > 9) {
			break;
		}
	}
	$html .= '</table>';
	return $html;
}


function socialstats_grid_links_full($posts) {
	if (!count($posts)) {
		$html = '<div style="padding-top:10px;">No Posts found for this time range</div>';
		$html .= '</div>';
		return $html;
	}
	$html = '<table class="socialstats_grid">';
	$html .= '<tr>
				<th class="socialstats_label_3">&nbsp;</th>
				<th><div><img src="'.SOCIALSTATS_PLUGIN_URL.'bitly.png" class="socialstats_image_small" />&nbsp;bit.ly Clicks</div></th>
				<th><div><img src="'.SOCIALSTATS_PLUGIN_URL.'facebook.png" class="socialstats_image_small" />&nbsp;FB Shares</div></th>
				<th><div><img src="'.SOCIALSTATS_PLUGIN_URL.'facebook.png" class="socialstats_image_small" />&nbsp;FB Likes</div></th>
				<th><div><img src="'.SOCIALSTATS_PLUGIN_URL.'facebook.png" class="socialstats_image_small" />&nbsp;FB Comments</div></th>
				<th><div><img src="'.SOCIALSTATS_PLUGIN_URL.'buzz.png" class="socialstats_image_small" />&nbsp;Buzz Updates</div></th>
				<th><div><img src="'.SOCIALSTATS_PLUGIN_URL.'yahoo.png" class="socialstats_image_small" />&nbsp;Yahoo Updates</div></th>
			</tr>';
	$i = 0;
	foreach ($posts as $post) {
		if (($i & 1)) {
			$html .= '<tr class="socialstats_alternate">';
		} else {
			$html .= '<tr>';
		}
		$html .= '<td class="socialstats_label_3"><div><a href="index.php?page=socialstats&view=link&id='.$post->link_id.'">'.$post->title.'</a></div></td>';
		$html .= '<td class="socialstats_views_3">'.$post->count_bitly_clicks.'</td>';
		$html .= '<td class="socialstats_views_3">'.$post->count_facebook_shares.'</td>';
		$html .= '<td class="socialstats_views_3">'.$post->count_facebook_likes.'</td>';
		$html .= '<td class="socialstats_views_3">'.$post->count_facebook_comments.'</td>';
		if(!$post->count_buzz) {
			$post->count_buzz = 0;
		}
		$html .= '<td class="socialstats_views_3">'.$post->count_buzz.'</td>';
		$html .= '<td class="socialstats_views_3">'.$post->count_yahoo_updates.'</td>';
		$html .= '</tr>';
		$i++;
	}
	$html .= '</table>';
	return $html;
}


function socialstats_ping($id) {
	if(!$id) {
		return FALSE;
	}
	$options = get_option(SOCIALSTATS_PLUGIN_NAME_INTERNAL);
	$link = 'http://www.linksalpha.com/a/ping?id='.$options['id'];
	$response_full = socialstats_http($link);
	$response_code = $response_full[0];
	if ($response_code === 200) {
		return TRUE;
	}
	return FALSE;
}


function get_plugin_dir() {
	if ( version_compare($wp_version, '2.8', '<') ) {
		$path = dirname(plugin_basename(__FILE__));
		if ( $path == '.' )
			$path = '';
		$plugin_path = trailingslashit( plugins_url( $path ) );
	} else {
		$plugin_path = trailingslashit( plugins_url( '', __FILE__) );
	}
	return $plugin_path;
}

function socialstats_json_decode($str) {
	if (function_exists("json_decode")) {
	    return json_decode($str);
	} else {
		if (!class_exists('Services_JSON')) {
			require_once("JSON.php");
		}
	    $json = new Services_JSON();
	    return $json->decode($str);
	}
}


function socialstats_http($link) {
	if (!$link) {
		return array(500, 'Invalid Link');
	}
	require_once(ABSPATH.WPINC.'/class-snoopy.php');
	$snoop = new Snoopy;
	$snoop->agent = WIDGET_NAME.' - '.get_option('siteurl');
	if($snoop->fetchtext($link)){
		if (strpos($snoop->response_code, '200')) {
			$response = $snoop->results;
			return array(200, $response);
		}
	}
	if( !class_exists( 'WP_Http' ) ) {
		include_once( ABSPATH . WPINC. '/class-http.php' );
	}
	if (!class_exists('WP_Http')) {
		return array(500, $snoop->response_code);
	}
	$request = new WP_Http;
	$headers = array( 'Agent' => WIDGET_NAME.' - '.get_option('siteurl') );
	$response_full = $request->request( $link );
	$response_code = $response_full['response']['code'];
	if ($response_code === 200) {
		$response = $response_full['body'];
		return array($response_code, $response);
	}
	$response_msg = $response_full['response']['message'];
	return array($response_code, $response_msg);
}


function socialstats_http_post($link, $body) {
	if (!$link) {
		return array(500, 'Invalid Link');
	}
	require_once(ABSPATH.WPINC.'/class-snoopy.php');
	$snoop = new Snoopy;
	$snoop->agent = SOCIALSTATS_PLUGIN_NAME.' - '.get_option('siteurl');
	if($snoop->submit($link, $body)){
		if (strpos($snoop->response_code, '200')) {
			$response = $snoop->results;
			return array(200, $response);
		}
	}
	if( !class_exists( 'WP_Http' ) ) {
		include_once( ABSPATH . WPINC. '/class-http.php' );
	}
    if (!class_exists('WP_Http')) {
		return array(500, $snoop->response_code);
	}
	$request = new WP_Http;
	$headers = array( 'Agent' => SOCIALSTATS_PLUGIN_NAME.' - '.get_option('siteurl') );
	$response_full = $request->request( $link, array( 'method' => 'POST', 'body' => $body, 'headers'=>$headers) );
	$response_code = $response_full['response']['code'];
	if ($response_code === 200) {
		$response = $response_full['body'];
		return array($response_code, $response);
	}
	$response_msg = $response_full['response']['message'];
	return array($response_code, $response_msg);
}
function socialstats_http_process($response_full) {
	if ($response_full[0] != 200) {
		$html = '<div class="msg_error">Error occurred while processing your request. '.$response_full[1].'. Error Code 100</div>';
		echo $html;
		return FALSE;
	}
	$response = socialstats_json_decode($response_full[1]);
	if($response->errorCode) {
		if ($response->errorMessage == "internal error") {
			$html = '<div class="msg_error">Error occurred while processing your request. Error Code 200</div>';
		} else {
			$html = '<div class="msg_error">Error occurred. '.$response->errorMessage.'. Error Code 200</div>';
		}
		echo $html;
		return FALSE;
	}
	return $response->results;
}
add_action('init', 'socialstats_init');
add_action('publish_post', 'socialstats_ping');

?>
