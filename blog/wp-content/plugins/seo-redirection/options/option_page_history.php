<?php

global $wpdb,$table_prefix,$util;
$table_name = $table_prefix . 'WP_SEO_Redirection_LOG'; 
$rlink=$util->get_current_parameters(array('del','search','page_num','add','edit'));

if($util->get('del')!=''){	
	if($util->get('del')=='all'){
		c_clear_redirection_history();
	
		if($util->there_is_cache()!='') 
		$util->info_option_msg("You have a cache plugin installed <b>'" . $util->there_is_cache() . "'</b>, you have to clear cache after any changes to get the changes reflected immediately! ");
	}
}

if($util->get_option_value('history_status')!='1')
		$util->info_option_msg("Redirection history property is disabled now!, you can re-enable it from options tab.");

?>

<script type="text/javascript">
//---------------------------------------------------------
function go_search(){
var sword = document.getElementById('search').value;
	if(sword!=''){
		window.location = "<?php echo esc_url($rlink)?>&search=" + sword ;
	}else
	{
		alert('Please input any search words!');
		document.getElementById('search').focus();
	}
	
}

</script>

<h3>Redirection History<hr></h3>
<div class="link_buttons">
<table border="0" width="100%">
	<tr>
		<td width="150"><a href="<?php echo $rlink?>&del=all"><div class="del_link">Clear History</div></a></div></td>
		<td align="right">
		<input onkeyup="if (event.keyCode == 13) go_search();" style="height: 30px;" id="search" type="text" name="search" value="<?php echo htmlentities($util->get('search'),ENT_QUOTES)?>" size="40">
		<a onclick="go_search()" href="#"><div class="search_link">Search</div></a> 
		<a href="<?php echo $util->get_current_parameters('search')?>"><div class="see_link">Show All</div></a>
		</td>
	</tr>
</table>
</div>

<?php
	
	
	$grid = new datagrid();
	$grid->set_data_source($table_name);
	$grid->set_order(" ID desc "); 

	if($util->get('search')!='')
	{
		$search=$util->get('search');
		
		$grid->set_filter(" rfrom like '%%$search%%' or rto like '%%$search%%' or ctime like '%%$search%%'
		or referrer like '%%$search%%'   or country like '%%$search%%'   or ip like '%%$search%%'
		or os like '%%$search%%' or browser like '%%$search%%' or rsrc like '%%$search%%' or rtype like '%%$search%%' 
		 ");
	}
		
	$grid->add_select_field('rID');
	$grid->add_select_field('postID');
	$grid->add_select_field('referrer');
	$grid->add_select_field('country');
	$grid->add_select_field('ip');
	$grid->add_select_field('os');
	$grid->add_select_field('browser');
	$grid->add_select_field('rsrc');
	$grid->add_select_field('rfrom');
	$grid->add_select_field('rto');
	$grid->add_select_field('ctime');
	
	$grid->set_table_attr('width','100%');
	$grid->set_col_attr(1,'width','60px');
	$grid->set_col_attr(3,'width','20px'); 
	$grid->set_col_attr(3,'align','center');
	$grid->set_col_attr(4,'width','20px'); 
	$grid->set_col_attr(4,'align','center');
	$grid->set_col_attr(7,'width','30px'); 
	$grid->set_col_attr(7,'align','center');   
	$grid->set_col_attr(6,'width','75px');  
	$grid->set_col_attr(5,'width','130px');  
	
	$grid->add_php_col('echo date(\'Y-n-j\',strtotime($db_ctime)) . \'<br/>\' .  date(\'H:i:s\',strtotime($db_ctime));  ','Time');
	
	$grid->add_php_col(' echo "<div class=\'arrow_from\'><a target=\'_blank\' href=\'" . SEOR_make_absolute_url($db_rfrom) ."\'>{$db_rfrom}</a></div><div class=\'arrow_to\'><a target=\'_blank\' href=\'" . SEOR_make_absolute_url($db_rto) ."\'>{$db_rto}</a></div>" ;','Redirection');
	$grid->add_data_col('rtype','Type');
	$grid->add_php_col('if($db_referrer !="") echo "<a target=\'_blank\' title=\'$db_referrer\' href=\'$db_referrer\'><span class=\'link\'></span></a>" ;','Ref'); 
	
	$grid->add_html_col('{db_country}<br/>{db_ip}','Address');	
	$grid->add_html_col('{db_os}<br/>{db_browser}','Agent');
	
	$grid->add_php_col('	
	if($db_rsrc==\'404\') echo $db_rsrc;
	if($db_rsrc==\'Custom\') echo "<a target=\'blank\' href=\'?page=wp-seo-redirection.php&edit=$db_rID\'>{$db_rsrc}</a>";
	if($db_rsrc==\'Post\') echo "<a target=\'blank\' href=\'post.php?action=edit&post=$db_postID\'>{$db_rsrc}</a>";
	','Class');

	$grid->run();
	

?>