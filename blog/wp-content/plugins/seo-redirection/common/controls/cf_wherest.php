<?php

/*
Author: Fakhri Alsadi
Date: 16-7-2010
Contact: www.clogica.com   info@clogica.com    mobile: +972599322252
*/


///@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

///@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

////  class wherest      @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

/*
A simple class to create Drop down lists easily using PHP
----------------------------------------------------------

example:

----------------------------------------------------------

$wherest = new wherest();

$wherest->add_param("and", "binary tawajoh like '%$msn%' ");

$wherest->add_text( "some text ");

$wherest->get_statment();



*/

////@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

if(!class_exists('wherest')){
class wherest{

var $where='';

function add_param($op , $value)

	{
	if($this->where == '')

		$this->where=" where  " . $value . " ";
	else

		$this->where= $this->where . " " . $op . " " . $value;


	}	

	

	

function add_text($value)
	{
	$this->where=$this->where . $value ;
	}	

	

	

function get_statment()

	{

	return $this->where;

	}	

			



}}









?>