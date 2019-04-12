<?php
global $util;

if(isset($_POST)){
if($util->post('reset_all_options')!='')
{
	c_init_my_options();
	$util->success_option_msg('All Options Restored to Defaults');

}else if($util->post('Save_general_options')!='')
{
	c_save_redirection_general_options();
	$util->success_option_msg('General Options Saved!');

}else if($util->post('save_history_options')!='')
{
	c_save_redirection_history_options();
	$util->success_option_msg('History Options Saved!');
}
else if($util->post('clear_history')!='')
{
	c_clear_redirection_history();
	$util->success_option_msg('History Cleared!');
}
else if($util->post('save_404_options')!='')
{
	c_save_404_redirection_options();
	$util->success_option_msg('404 Redirection Options Saved!');
}
else if($util->post('clear_all_404')!='')
{
	c_clear_all_404();
	$util->success_option_msg('All Discovered 404 Pages Cleared!');
}
else if($util->post('save_data_options')!='')
{
	c_save_keep_data();
	$util->success_option_msg('Data Options Saved!');
}
else if($util->post('optimize_tables')!='')
{
	c_optimize_tables();
	$util->success_option_msg('Data Tables Optimized!');
}
else if($util->post('save_all_options'))
{
	c_save_redirection_general_options();
	c_save_redirection_history_options();
	c_save_404_redirection_options();
	c_save_keep_data();

	$util->success_option_msg('All options saved!');

}






if($util->there_is_cache()!='')
$util->info_option_msg("You have a cache plugin installed <b>'" . $util->there_is_cache() . "'</b>, you have to clear cache after any changes to get the changes reflected immediately! ");
}

$options= $util->get_my_options();

?>
<form method="POST">
<h3>General Options<hr></h3>

<table class="cform" align="center" width="100%">
	<tr><td>
	Plugin Status:
	<?php
		$drop = new dropdown('plugin_status');
		$drop->add('Enabled','1');
		$drop->add('Disabled','0');
		$drop->dropdown_print();
		$drop->select($options['plugin_status']);
	?>

	</td></tr>

	<tr><td>
	<?php $check = new checkoption('redirect_control_panel',$options['redirect_control_panel']); ?>
	Do not redirect control panel links (This will be usefull when making wrong expressions that may cause an infinit redirection loop).
	<br/>
	<?php $check = new checkoption('show_redirect_box',$options['show_redirect_box']); ?>
	Show redirect box in posts & pages edit page (Important to set up redirection for posts and pages easily).

	<br/>
	<?php $check = new checkoption('reflect_modifications',$options['reflect_modifications']); ?>
	Reflect any modifications in the post permalink to all redirection links (Mostly Recommended).


<script type="text/javascript">

</script>
	</td></tr>

</table>
	<br/><input style="margin-left:5px" class="button-primary" type="submit" value="Save General Options" name="Save_general_options">


<br/><br/>
<h3>Redirection History Options<hr></h3>
<table class="cform" align="center" width="100%">
	<tr><td>
	Redirection History Status:
	<?php
		$drop = new dropdown('history_status');
		$drop->add('Enabled','1');
		$drop->add('Disabled','0');
		$drop->dropdown_print();
		$drop->select($options['history_status']);
	?>

	</td></tr>
		<tr><td>
	Redirection History Limit:
	<?php
		$drop = new dropdown('history_limit');
		$drop->add('7 days','7');
		$drop->add('1 month','30');
		$drop->add('2 months','60');
		$drop->add('3 months','90');
		$drop->dropdown_print();
		$drop->select($options['history_limit']);
	?>

	</td></tr>

</table>
<br/>
<input style="margin-left:5px" class="button-primary" type="submit" value="Save History Options" name="save_history_options">
<input style="margin-left:5px" class="button-primary" type="submit" value="Clear History" name="clear_history">

<br/><br/>
<h3>404 Error Pages Options<hr></h3>


<table class="cform" align="center" width="100%">
	<tr><td>
	404 Error Pages Discovery:
	<?php
		$drop = new dropdown('p404_discovery_status');
		$drop->add('Enabled','1');
		$drop->add('Disabled','0');
		$drop->dropdown_print();
		$drop->select($options['p404_discovery_status']);
	?>

	</td></tr>

	<tr><td>
	Unknown 404 Redirection Status:
		<?php
		$drop = new dropdown('p404_status');
		$drop->add('Enabled','1');
		$drop->add('Disabled','2');
		$drop->dropdown_print();
		$drop->select($options['p404_status']);
		?>
	</td></tr>

	<tr><td>
	Redirect All Unknown 404 Pages to: <input type="text" name="redirect_to" id="redirect_to" size="30" value="<?php echo $options['p404_redirect_to']?>">
	</td></tr>

</table>
<br/>
<input style="margin-left:5px" class="button-primary" type="submit" value="Save 404 Redirection Options" name="save_404_options">
<input style="margin-left:5px" class="button-primary" type="submit" value="Clear All Discovered 404 Pages" name="clear_all_404">





<br/><br/>
<h3>Redrection Data Options<hr></h3>
<table class="cform" align="center" width="100%">
	<tr><td>
	<?php $check = new checkoption('keep_data',$options['keep_data'],'1'); ?>
	Keep redirection data after uninstall the plugin, this will be useful when you install it later.
	</td></tr>
</table>
<br/>
<input style="margin-left:5px" class="button-primary" type="submit" value="Save Data Options" name="save_data_options">
<input style="margin-left:5px" class="button-primary" type="submit" value="Optimize Data Tables" name="optimize_tables">
<br/><br/><br/>
<hr>
<input style="margin-left:5px" class="button-primary" type="submit" value="Save All Options" name="save_all_options">
<input style="margin-left:5px" class="button-primary" type="submit" value="Restore Default Settings" name="reset_all_options">

</form>