<?php 

global $wpdb,$table_prefix,$util;
$table_name = $table_prefix . 'WP_SEO_Redirection';

	if($util->get('del')!='')
	{
		$delid=intval($util->get('del'));
		$wpdb->query(" delete from $table_name where ID='$delid' ");	
		
		
		if($util->there_is_cache()!='') 
		$util->info_option_msg("You have a cache plugin installed <b>'" . $util->there_is_cache() . "'</b>, you have to clear cache after any changes to get the changes reflected immediately! ");

		$SR_redirect_cache = new free_SR_redirect_cache();
		$SR_redirect_cache->free_cache();
	}
	
	$rlink=$util->get_current_parameters(array('del','search','page_num','add','edit'));
	
?>
<br/>

<script type="text/javascript">

//---------------------------------------------------------

function go_search(){
var sword = document.getElementById('search').value;
	if(sword!=''){
		window.location = "<?php echo $rlink?>&search=" + sword ;
	}else
	{
		alert('Please input any search words!');
		document.getElementById('search').focus();
	}
	
}

</script>

<div class="link_buttons">
<table border="0" width="100%">
	<tr>
		<td align="left">
		<input onkeyup="if (event.keyCode == 13) go_search();" style="height: 30px;" id="search" type="text" name="search" value="<?php echo htmlentities($util->get('search','title'))?>" size="40">
		<a onclick="go_search()" href="#"><div class="search_link">Search</div></a> 
		<a href="<?php echo $util->get_current_parameters('search')?>"><div class="see_link">Show All</div></a>
		</td>
	</tr>
</table>
</div>
<?php

	$grid = new datagrid();
	$grid->set_data_source($table_name);
	$grid->add_select_field('ID');
	$grid->add_select_field('postID');
	$grid->add_select_field('redirect_from');
	$grid->add_select_field('redirect_from_type');
	$grid->add_select_field('redirect_to');
	$grid->add_select_field('redirect_to_type');
	$grid->set_table_attr('width','100%');
	$grid->set_col_attr(5,'width','50px');
	$grid->set_col_attr(5,'width','50px','header');
	$grid->set_col_attr(1,'width','20px','header');
	$grid->set_col_attr(2,'width','20px','header');
	$grid->set_order(" ID desc ");
	
	$grid->set_filter("url_type=2");
	
	if($util->get('search')!='')
	{
		$search=$util->get('search');
		$grid->set_filter("url_type!=1 and (redirect_from like '%%$search%%' or redirect_to like '%%$search%%' or redirect_type like '%%$search%%'  )");
	}
	
	
	$grid->add_template_col('del', $util->get_current_parameters('del') . '&del={db_ID}','Del');
	$grid->add_template_col('go_link','post.php?post={db_postID}&action=edit','Post');
	//$grid->add_data_col('redirect_from','Redirect from');
	//$grid->add_data_col('redirect_to','Redirect to');
	$grid->add_php_col(' echo "<div class=\'{$db_redirect_from_type}_background_{$db_enabled}\'><a target=\'_blank\' href=\'" . SEOR_make_absolute_url($db_redirect_from) ."\'>{$db_redirect_from}</a></div>" ;','Redirect from ');
	$grid->add_php_col(' echo "<div class=\'{$db_redirect_to_type}_background_{$db_enabled}\'><a target=\'_blank\' href=\'" . SEOR_make_absolute_url($db_redirect_to) ."\'>{$db_redirect_to}</a></div>"; ','Redirect to ');
	$grid->add_data_col('redirect_type','Type');
	
	$grid->run();
	
		

?>
<b>Note</b>: To add a redirection for any post or page , use custom redirection or go to the edit page, you will find the redirection box, use it to set your redirection.<br/>