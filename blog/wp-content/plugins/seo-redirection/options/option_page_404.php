<?php

global $wpdb,$table_prefix,$util;

if($util->post('redirect_to')!='')
	{
		 global $util;	
		
		$util->update_post_option('p404_status');
		$util->update_option('p404_redirect_to',$util->make_relative_url($util->post('redirect_to')));
		$util->success_option_msg('Options Saved!');	
		
		if($util->there_is_cache()!='') 
$util->info_option_msg("You have a cache plugin installed <b>'" . $util->there_is_cache() . "'</b>, you have to clear cache after any changes to get the changes reflected immediately! ");
	
	}
	
$options= $util->get_my_options();
$rlink=$util->get_current_parameters(array('del','search','page_num','add','edit'));


if($util->get_option_value('p404_discovery_status')!='1')
		$util->info_option_msg("404 error pages discovery property is disabled now!, you can re-enable it from options tab.");


?>

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

<h3>New Discovered 404 links<hr></h3>
<div class="link_buttons">
<table border="0" width="100%">
	<tr>
		<td align="left">
		<input onkeyup="if (event.keyCode == 13) go_search();" style="height: 30px;" id="search" type="text" name="search" value="<?php echo htmlentities($util->get('search'))?>" size="40">
		<a onclick="go_search()" href="#"><div class="search_link">Search</div></a> 
		<a href="<?php echo $util->get_current_parameters('search')?>"><div class="see_link">Show All</div></a>
		</td>
	</tr>
</table>
</div>
<?php

	$table_name = $table_prefix . 'WP_SEO_404_links'; 
	
	$grid = new datagrid();
	$grid->set_data_source($table_name);
	$grid->add_select_field('ID');
	$grid->add_select_field('link');
	$grid->add_select_field('referrer');

	$grid->set_order(" ID desc ");
	
	if($util->get('search')!='')
	{
		$search=$util->get('search');
		
		$grid->set_filter(" link like '%%$search%%' or ctime like '%%$search%%'
		or referrer like '%%$search%%'   or country like '%%$search%%'   or ip like '%%$search%%'
		or os like '%%$search%%' or browser like '%%$search%%'
		 ");
	}
	
	$grid->set_table_attr('width','100%');
	$grid->set_col_attr(1,'width','140px');
	$grid->set_col_attr(1,'align','center');
	$grid->set_col_attr(3,'width','20px'); 
	$grid->set_col_attr(3,'align','center'); 
	$grid->set_col_attr(4,'align','center');
	$grid->set_col_attr(5,'align','center');
	$grid->set_col_attr(5,'width','130px');  
	$grid->set_col_attr(6,'width','75px');  
	$grid->set_col_attr(6,'align','center');
	$grid->set_col_attr(7,'align','center');
	$grid->set_col_attr(8,'align','center');
	$grid->set_col_attr(8,'width','20px');
	$grid->set_col_attr(2,'style','padding-left: 5px');
	$grid->add_data_col('ctime','Discovered');
	//$grid->add_html_col("<a target='_blank' title='{db_link}' href='{db_link}'><span class='link'></span></a>{db_link}",'Link');
	$grid->add_php_col(' echo " <a target=\'_blank\' href=\'" . SEOR_make_absolute_url($db_link) ."\'> {$db_link}</a>" ;','Link');
	$grid->add_php_col('if($db_referrer !="") echo "<a target=\'_blank\' title=\'$db_referrer\' href=\'$db_referrer\'><span class=\'link\'></span></a>" ;','Ref');
	$grid->add_data_col('ip','IP');
	$grid->add_data_col('country','Country');	
	$grid->add_data_col('os','OS');
	$grid->add_data_col('browser','Browser');

	$grid->add_template_col('redirect_link','?page=' . $_GET['page'] . '&tab=cutom&add=1&page404={db_ID}','Redirect');
	
	$grid->run();
	
	
		
?>
<div>* Too many 404 errors? <a target="_blank" href="http://www.clogica.com/kb/too-many-404-errors.htm">click here to see why?</a></div>
<br/><br/>
<form method="POST">
<h3>Unknown 404 Links Redirection<hr></h3>	
 
<table class="cform" width="100%">
	<tr>
		<td class="labelxx">Unknown 404 Redirection Status:</td>
		<td>
		<?php
		$drop = new dropdown('p404_status');
		$drop->add('Enabled','1');	
		$drop->add('Disabled','2');
		$drop->dropdown_print();
		$drop->select($options['p404_status']);
		?>
		</td>
	</tr>
	<tr>
	<td  class="labelxx">
	Redirect Unknown 404 Pages to:
	</td>
	<td>
	<input type="text" name="redirect_to" id="redirect_to" size="30" value="<?php echo $options['p404_redirect_to']?>">
	</td>
	</tr>
</table>		
	
	
<br/>
<input  class="button-primary" type="submit" value="  Update Options  " name="Save_Options">
</form>