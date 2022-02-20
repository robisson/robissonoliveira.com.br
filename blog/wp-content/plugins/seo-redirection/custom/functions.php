<?php
// custom functions
if(!function_exists("SEOR_make_absolute_url")) {
	function SEOR_make_absolute_url($url)
	{
		if(substr($url,0,1)=='/')
		{
			$url = site_url() . $url;
		}
		return $url;
	}
}
if(!function_exists("c_init_my_options")) {
function c_init_my_options()
{ 
        global $util;	
	
	$options=$util->get_my_options();
	
	$options['plugin_status']= '1';
	$options['redirection_base']= site_url();
	$options['redirect_control_panel']= '1';	
	$options['show_redirect_box']= '1';
	$options['reflect_modifications']= '1';
	$options['history_status']= '1';
	$options['history_limit']= '30';
	$options['p404_discovery_status']= '1';
	$options['p404_redirect_to']= site_url();
	$options['p404_status']= '2';
	$options['keep_data']= '1';	
	
	$util->update_my_options($options);
}}



if(!function_exists("c_save_redirection_general_options")) {
function c_save_redirection_general_options()
{ global $util;	
	$util->update_post_option('plugin_status');
	$util->update_post_option('redirect_control_panel');
	$util->update_post_option('show_redirect_box');
	$util->update_post_option('reflect_modifications');
	
}}

//----------------------------------------------------------

if(!function_exists("c_save_redirection_history_options")) {
function c_save_redirection_history_options()
{ global $util;	
	$util->update_post_option('history_status');
	$util->update_post_option('history_limit');
	
}}

//----------------------------------------------------------

if(!function_exists("c_save_404_redirection_options")) {
function c_save_404_redirection_options()
{ global $util;	
		
 	$util->update_post_option('p404_discovery_status');
	$util->update_post_option('p404_status');
	$util->update_option('p404_redirect_to',$util->post('redirect_to'));
}}

//------------------------------------------------------------

if(!function_exists("c_clear_redirection_history")) {
function c_clear_redirection_history()
{
	global $wpdb,$table_prefix;
	$table_name = $table_prefix . 'WP_SEO_Redirection_LOG'; 
	$wpdb->query(" TRUNCATE TABLE  $table_name ");

}}

//------------------------------------------------------------

if(!function_exists("c_clear_all_404")) {
function c_clear_all_404()
{
	global $wpdb,$table_prefix;
	$table_name = $table_prefix . 'WP_SEO_404_links'; 
	$wpdb->query(" TRUNCATE TABLE  $table_name ");

}}
	
//------------------------------------------------------------

if(!function_exists("c_save_keep_data")){
function c_save_keep_data()
{
	global $util;	
	$util->update_post_option('keep_data');

}}

//------------------------------------------------------------

if(!function_exists("c_optimize_tables")){
function c_optimize_tables()
{
	global $wpdb,$table_prefix;
	$table_name1 = $table_prefix . 'WP_SEO_404_links';
	$table_name2 = $table_prefix . 'WP_SEO_Redirection';
	$table_name3 = $table_prefix . 'WP_SEO_Redirection_LOG';
        $table_name4 = $table_prefix . 'WP_SEO_Cache';
	$wpdb->query(" OPTIMIZE TABLE  $table_name1,$table_name2,$table_name3,$table_name4 ");
}}

