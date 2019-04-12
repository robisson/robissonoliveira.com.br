<?php

if(!class_exists('ckeckboxlist')){
class ckeckboxlist{
      var $chname="check";
      var $check;
      var $script="";
      var $checkall="Check All";
      var $precheck="";
      var $width=100;
      var $height=100;
      var $selcolor="CCFFCC";


      //----------------------------------------------------------------------
      function ckeckboxlist($chname,$width=100,$height=100,$precheck,$selcolor="CCFFCC")
      {
        $this->chname=$chname;
        $this->check = new checkbox($chname);
        $this->check->chbox_init();
        $this->width=$width;
        $this->height=$height;
        $this->precheck=$precheck;
        $this->selcolor=$selcolor;
      }


      //----------------------------------------------------------------------

      function checkalltext($txt)
      {
      $this->checkall=$txt;

      }

      //----------------------------------------------------------------------
      function ischecked($val)
      {
      $chks=split(',',$this->precheck);
      for($i=0;$i< count($chks); $i++)
      	{ if($chks[$i]==$val)
      	  return'checked';
      	}

      return '';
      }


      //----------------------------------------------------------------------
      function additem($val,$txt)
      {
      $this->script=$this->script . "<div id='" . $this->check->getcheck($this->check->getcount()+1). "_div' >" . $this->check->get_chbox_add($this->check->getcount()+1,$val,$this->ischecked($val),$this->check->chname . "_div_list_color('" . $this->check->getcheck($this->check->getcount()+1). "')") . "<span style='cursor:hand'  onclick=\"". $this->check->chname ."_list_check('" . $this->check->getcheck($this->check->getcount()) . "') \"> $txt</span></div>";
      }

	  //------------------------------------------------------------------------
	  
	  function data_bind($tbl,$name="name",$id="id",$where="",$order="",$limit="")
	 	{
			global $mysql;
			$res=$mysql->sql(" select $name,$id from PREFIX_$tbl $where $order $limit ");
			while($ar=mysql_fetch_array($res)){
				$this->additem($ar[1],$ar[0]);
			}
	 	}
	
      //----------------------------------------------------------------------
      function endlist()
      {


      $js="<script>
      function " . $this->check->chname . "_list_check(check){
      if(document.getElementById(check).checked){
      document.getElementById(check).checked=false;
      }
      else
      {
      document.getElementById(check).checked=true;
      }

      " . $this->check->chname . "_div_list_color(check);
      " . $this->check->chname . "_update();
      }



      function " . $this->check->chname . "_div_list_color(check){
      if(document.getElementById(check).checked){
      document.getElementById(check + '_div').style.backgroundColor='#" . $this->selcolor . "';
      }
      else
      {
      document.getElementById(check + '_div').style.backgroundColor='';
      }
      }


      function " . $this->check->chname . "_all_div_list_color()
      {
      var count = document.getElementById('" . $this->check->chname . "_count').value;
      for(i=1;i<=count;i++)
      " . $this->check->chname . "_div_list_color('" . $this->check->chname . "_box' + i);

      }

      </script>";


      $htm="<div  style='background-color: #FFFFF6; width: " . $this->width . "px; height: " . $this->height . "px; overflow:auto;' >";

      echo $js;
      echo $htm;
      $this->check->chbox_all($this->check->chname . "_all_div_list_color()");
      echo $this->checkall . "<br>";
      echo $this->script;
      $this->check->chbox_finish();
      echo "</div>";
      echo "<script>" . $this->check->chname . "_all_div_list_color()</script>";
      echo "<script>" . $this->check->chname . "_update()</script>";
      }

}}



////@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

////@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

////  checkbox class to create a list of checks ! @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

/*

A class to create a custom group of check boxes to use in any report



------------------------------------------------------------------

example

------------------------------------------------------------------

<?

$ch = new checkbox('check33');

$ch->chbox_init();

?>



<? $ch->chbox_all(); ?>  Check all <br>



<p><? $ch->chbox_add(1,12) ?>i urtiurtowertowet</p>

<p><? $ch->chbox_add(2,13) ?> oiutoieru twoieurtoe</p>

<p><? $ch->chbox_add(3,14) ?> wiu riow uerowqeu r</p>



<p><? $ch->chbox_finish()?>





*/

////@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
if(!class_exists('checkbox')){
class checkbox{


var $chname="check";
var $count=0;


//----------------------------------------------------------------------

function set_sellect_all_functions($fun)

{

$this->sellect_all_functions=$fun;

}



//----------------------------------------------------------------------

function checkbox($chname)

{

$this->chname=$chname;

}


//----------------------------------------------------------------------

function chbox_init()

{


$js="



<script>

function " . $this->chname . "_update()

{

var check_name='" . $this->chname . "';

document.getElementById(check_name).value='';

count=document.getElementById(check_name + '_count').value;

if(count>0){

	for(i=1;i<=count;i++)

	{	

		if(document.getElementById(check_name + '_box' + i ).checked)

			{

			if(document.getElementById(check_name).value =='')

					document.getElementById(check_name).value=document.getElementById(check_name + '_box' + i ).value;

			else

					document.getElementById(check_name).value= document.getElementById(check_name).value + ',' +  document.getElementById(check_name + '_box' + i ).value;

			}

	}

}

}

</script>


<script>

function " . $this->chname . "_checkall()

{

count=document.getElementById('" . $this->chname . "_count').value;

if(count>0){

	

	initval=document.getElementById('" . $this->chname . "_allboxes' ).checked;

	for(i=1;i<=count;i++)

	{

	document.getElementById('" . $this->chname . "_box' + i ).checked=initval;



	}

}

" . $this->chname . "_update();

}

</script>

";



echo $js;

}



//----------------------------------------------------------------------

function chbox_all($fun="")

{

echo "<input type='checkbox' name='" . $this->chname . "_allboxes'  id='" . $this->chname . "_allboxes' onclick=\" " . $this->chname . "_checkall();$fun\" value='ON'>";

}


//----------------------------------------------------------------------

function chbox_add($num,$val,$ext='',$onclick='')

{

$this->count=$this->count+1;

echo "<input onclick=\" " . $this->chname . "_update();document.getElementById('" . $this->chname . "_allboxes' ).checked=false;$onclick\" type='checkbox' name='" . $this->chname . "_box". $num ."' id='" . $this->chname . "_box". $num ."' value='" . $val . "'  $ext >";


}


//----------------------------------------------------------------------

function get_chbox_add($num,$val,$ext='',$onclick='')

{

$this->count=$this->count+1;

return "<input onclick=\" " . $this->chname . "_update();document.getElementById('" . $this->chname . "_allboxes' ).checked=false;$onclick\" type='checkbox' name='" . $this->chname . "_box". $num ."' id='" . $this->chname . "_box". $num ."' value='" . $val . "'  $ext >";

}


//----------------------------------------------------------------------

function getcheck($num)

{

return $this->chname . "_box". $num;

}

//----------------------------------------------------------------------

function getcount()

{

return $this->count;

}



//----------------------------------------------------------------------

function chbox_finish()

{

 echo "<input type='hidden' name='" . $this->chname . "'  id='" . $this->chname . "' size='21'><input type='hidden' name='" . $this->chname . "_count' id='" . $this->chname . "_count' value='" . $this->count . "' >";

}


}}



?>