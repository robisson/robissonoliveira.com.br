<?php
/*
Author: Fakhri Alsadi
Date: 16-7-2010
Contact: www.clogica.com   info@clogica.com    mobile: +972599322252
*/

///@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
///@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
////  class checkoption     @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
/*
A simple class to create checkbox options     

----------------------------------------------------------
example:
----------------------------------------------------------
$check = new checkoption('redirect_control_panel');
$check->check($options['redirect_control_panel'],'1')

or 

$check = new checkoption('redirect_control_panel',$options['redirect_control_panel'],'1');

*/


if(!class_exists('checkoption')){
class checkoption{

var $name;


function checkoption($name,$check=null,$value='1')
{
	
	$this->name=$name;	
	echo '<input type="checkbox" name="' . $name . '"  id="' . $name . '"  value="1">';	
	
	if(isset($check))
	$this->check($check,$value);
	
}

//---------------------------------------

function check($check,$value)
	{
		if($check==$value)
		echo "<script type='text/javascript'>document.getElementById('" . $this->name . "').checked=true;</script>";
		else
		echo "<script type='text/javascript'>document.getElementById('" . $this->name . "').checked=false;</script>";
	}	


}}



?>