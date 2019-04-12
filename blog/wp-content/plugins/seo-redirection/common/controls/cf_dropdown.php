<?php
/*
Author: Fakhri Alsadi
Date: 16-7-2010
Contact: www.clogica.com   info@clogica.com    mobile: +972599322252
*/

///@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
///@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
////  class dropdown     @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
/*
A simple class to create Drop down lists easily using PHP

----------------------------------------------------------
example:
----------------------------------------------------------
$drop = new dropdown('gendar');
$drop->add('mail','mail');`	
$drop->add('femail','femail');
$drop->dropdown_print();
$drop->select('femail');


//////////////////////////////

$drop = new dropdown('gendar');
$drop->data_bind('data_status');
$drop->dropdown_print();



*/
////@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

if(!class_exists('dropdown')){
class dropdown{

var $name='drop';
var $options='';
var $class='';
var $onchange='';

function dropdown($str,$class='',$onchange='')
	{
	$this->name=$str;
	
	if($class!='')
	$this->class=$class;
	
	if($onchange!='')
	$this->onchange=$onchange;
	
	}
		
function add($name,$value)
	{
	$this->options=$this->options. "<option value='$value'>$name</option>";
	}	
	
function dropdown_print()
	{
		if($this->onchange == '')
		echo "<select size='1' name='" . $this->name. "' id='" . $this->name. "' >" . $this->options . "</select>";
			else
		echo "<select size='1' name='" . $this->name. "' id='" . $this->name. "'  onchange='" . $this->onchange . "' >" . $this->options . "</select>";
	}	
		
function select($str)
	{
		echo "<script>document.getElementById('" . $this->name . "').value='".$str."'</script>";

	}	
	
function data_bind($tbl,$name="name",$id="id",$where="",$order="",$limit="")
	{
		global $wpdb;
		$res = $wpdb->get_results("select $id,$name from PREFIX_$tbl $where $order $limit ", ARRAY_A);
		foreach ( $res as $ar){ 
			$this->add($ar[1],$ar[0]);
		}

		
	}		

}}





?>