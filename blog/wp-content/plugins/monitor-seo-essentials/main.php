<?php
/*
Plugin Name: Dashboard Social Stats
Description: Allows the user to monitor Facebook shares, backlinks, and Alexa rank from the Wordpress dashboard.
Author: Graeme Boy
Author URI: http://www.graemeboy.com
Plugin URI: http://www.graemeboy.com
Version: 3.0
*/
add_action('wp_dashboard_setup', 'dss_exe_widget');
function dss_exe_widget() {
	wp_add_dashboard_widget( 'dss_dashboard_widget', 'Dashboard Social Stats', 'dss_dashboard_widget' );
}

function dss_dashboard_widget() {
	dss_create_widget();
}

function dss_get_alexa($url) {
	$response = simplexml_load_file('http://data.alexa.com/data?cli=10&dat=snbamz&url=' . $url);
	$pop = $response->SD[1]->POPULARITY;
	$rank = $pop['TEXT'];
	return number_format(intval($rank));
}

function dss_get_google_backlinks($url) {
	$page = @file_get_contents('http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=site:' . $url);
	if (!empty($page)) {
		$data = json_decode($page);
	}
	$data = $data->responseData->cursor;
	$num_backlinks = $data->estimatedResultCount;
	return number_format(intval($num_backlinks));
}

function dss_get_facebook_shares($url) {
	$page = @file_get_contents('http://graph.facebook.com/' . $url);
	if (!empty($page)) {
		$data = json_decode($page);
	}
	$num_shares = $data->shares;
	return number_format(intval($num_shares));
}

function dss_get_all_google_backlinks() {
	$posts_array = get_posts();
	$num_backlinks = dss_get_google_backlinks(home_url()); // total number of shares, will increment.
	foreach ($posts_array as $post) {
		$num_backlinks += dss_get_google_backlinks(get_permalink($post->ID));
	}
	return $num_backlinks;
}

function dss_get_all_facebook() {
	$posts_array = get_posts();
	$num_shares = dss_get_facebook_shares(home_url()); // total number of shares, will increment.
	foreach ($posts_array as $post) {
		$num_shares += dss_get_facebook_shares(get_permalink($post->ID));
	}
	return $num_shares;
}

function dss_create_widget() {
	$url = home_url();
	$alexa_rank = dss_get_alexa($url);
	$fb_shares = dss_get_all_facebook();
	$google_backlinks_homepage = dss_get_google_backlinks(home_url());
	$total_google_backlinks = dss_get_all_google_backlinks();
    ?>
    <table id="dss_table" class="widefat">
		<tr>
			<th>Alexa Rank</th>
			<td><?php echo $alexa_rank; ?></td>
		</tr>
		<tr>
			<th>Total Facebook Shares</th>
			<td><?php echo $fb_shares; ?></td>
		</tr>
		<tr>
			<th>Homepage Backlinks</th>
			<td><?php echo $google_backlinks_homepage; ?></td>
		</tr>
		<tr>
			<th>Total Backlinks</th>
			<td><?php echo $total_google_backlinks; ?></td>
		</tr>
    </table>
    <style type="text/css">
    	#dss_table td {
    		vertical-align: middle;
    	}
    </style>
    <?php
}
?>