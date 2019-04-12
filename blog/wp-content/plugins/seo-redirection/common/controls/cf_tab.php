<?php
/*
Author: Fakhri Alsadi
Date: 16-7-2010
Contact: www.clogica.com   info@clogica.com    mobile: +972599322252

*/

if(!class_exists('phptab')){
class phptab{

var $tabs;  // each tab has  num,title,content,parameter.
var $parameter = 'tab';
var $ignore_parameters='';


	function phptab($parameter='tab')
	{
		$this->tabs = array();
		$this->$parameter = $parameter;
	}
	
//----------------------------------------------------------------------------	
	
	function add_file_tab($num, $title, $content, $type )
	{
	  $index=$this->tabs_count();
		$this->tabs[$index] = array('num' => $num , 'title'=> $title, 'content'=> $content, 'type' => $type );

	}	

	
//----------------------------------------------------------------------------

	function tabs_count()
	{
	if(is_array($this->tabs))
	return count($this->tabs);
	else
	return 0;
	}	
	
//----------------------------------------------------------------------------

	function set_ignore_parameter($ar)
	{
		if(is_array($ar))
		$this->ignore_parameters =$ar;
		else
		$this->ignore_parameters =array($ar);
	}
	
//----------------------------------------------------------------------------

	function get_ignore_parameter($ar)
	{
		 return $this->ignore_parameters;
	}
	
//----------------------------------------------------------------------------	
	function run()
	{ 
            global $util;
		
		$tab_index= $util->get($this->parameter);
		
		if($tab_index=='')
		$tab_index=$this->tabs[0]['num'];
		
		$options_path='';
		if(is_array($this->ignore_parameters))
		{
		$ignore=array_merge(array($this->parameter),$this->ignore_parameters);
		$options_path= $util->get_current_parameters($ignore);
		}else
		{
		$options_path= $util->get_current_parameters($this->parameter);
		}
		
		
		$num_index=-1;
		echo '<ul class="tabs">';
				
		for($i=0;$i<$this->tabs_count();$i++)
		{
			if($this->tabs[$i]['num']==$tab_index){
			echo '<li class="active"><a href="' . $options_path . '&' . $this->parameter .'=' . $this->tabs[$i]['num'] . '">' .  $this->tabs[$i]['title'] . '</a></li>';
			$num_index=$i;
			}
			else
			{
			echo '<li><a href="' . $options_path . '&' . $this->parameter .'=' . $this->tabs[$i]['num'] . '">' .  $this->tabs[$i]['title'] . '</a></li>';
			}
		}
		
		echo '</ul>';
		
		
		
	    if($num_index>=0)
	    {
	     	echo '<div class="tabContainer"><div id="tab1" class="tabContent">';
	     		include $util->get_plugin_path() . 'options/' . $this->tabs[$num_index]['content'];
	     	echo '</div></div>';
	    }
		
		
		
	}


}}

?>