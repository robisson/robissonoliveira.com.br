<?php
/*
Author: Fakhri Alsadi
Date: 16-7-2010
Contact: www.clogica.com   info@clogica.com    mobile: +972599322252
*/

if(!class_exists('clogica_util_1')){
class clogica_util_1{

private  $slug;
private  $plugin_file;
private  $plugin_path;
private  $plugin_url;

private  $option_group_name='clogica_option_group';
private  $plugin_folder='plugin_folder_name'; 



public function init ($option_gruop='clogica_option_group',$plugin_file='')
{
   $this->set_option_gruop($option_gruop); 
   $this->set_plugin_folder(basename(dirname($plugin_file)));
    $this->plugin_file = $plugin_file;
    $this->slug =  basename($plugin_file);
    $this->plugin_path = dirname($plugin_file) . '/';
    $this->plugin_url =plugin_dir_url($plugin_file);
   
}
public function get($key,$type='text')
{

	if(array_key_exists($key,$_GET))
	{
		  $unsafe_val=$_GET[$key];
	      return $this->sanitize_req($unsafe_val,$type);	  
	}
	else
	{
	    return '';
	}
}

//---------------------------------------------------- 

public function post($key,$type='text')
{
	if(array_key_exists($key,$_POST))
	{
		  $unsafe_val=$_POST[$key];
	      return $this->sanitize_req($unsafe_val,$type);	  
	}
	else
	{
	    return '';
	}
}


//----------------------------------------------------

public function sanitize_req($unsafe_val,$type='text')
{
	 switch ($type) {
	   case 'text': return sanitize_text_field($unsafe_val);
	   break;
	   
	   case 'int': return intval($unsafe_val);
	   break;
	   
	   case 'email': return sanitize_email($unsafe_val);
	   break;
	   
	   case 'filename': return sanitize_file_name($unsafe_val);
	   break;
	   
	   case 'title': return sanitize_title($unsafe_val);
	   break;
	      
	   default:
        return sanitize_text_field($unsafe_val);
	   
	   }
}

//---------------------------------------------------- 

public function get_ref()
{
	if(array_key_exists('HTTP_REFERER',$_SERVER))
	{
	      return $this->sanitize_req(strip_tags($_SERVER['HTTP_REFERER'])); 
	}
	else
	{
	    return '';
	}
}

//---------------------------------------------------- 

public function set_option_gruop($option_group_name)
{
	$this->option_group_name=$option_group_name;
}

//---------------------------------------------------- 

public function get_option_gruop()
{
	return $this->option_group_name;
}

//----------------------------------------------------

public function set_plugin_folder($folder)
{
	$this->plugin_folder=$folder;
}


//----------------------------------------------------

public function get_plugin_folder()
{
	return $this->plugin_folder;
}

//---------------------------------------------------- 


public function update_my_options($options)
{	
	update_option($this->get_option_gruop(),$options);
}

//---------------------------------------------------- 

public function get_my_options()
{	
	$options=get_option($this->get_option_gruop());
	if(!is_array($options))
	{
		add_option($this->get_option_gruop());
		$options= array();
	}
	return $options;
}

//---------------------------------------------------

public function get_option_value($key)
{
	$options=$this->get_my_options();
        if(array_key_exists($key,$options))
        {
            return $options[$key];
        }else
        {
            return '';
        }
}
//---------------------------------------------------- 

public function update_option($key,$value)
{	
	$options=$this->get_my_options();
	$options[$key]=$value;
	$this->update_my_options($options);
}

//---------------------------------------------------- 

public function update_post_option($key)
{	
	$options=$this->get_my_options();
	$options[$key]=intval($this->post($key));
	$this->update_my_options($options);				
}


//---------------------------------------------------- 


public function delete_my_options()
{	
    delete_option($this->get_option_gruop());
}



/* get_current_URL ----------------------------------------------  */
		public function get_current_URL()
		{
			$pageURL = 'http';
			if ( array_key_exists("HTTPS",$_SERVER) && $_SERVER["HTTPS"] == "on")
			{
				$pageURL .= "s";
			}
			$pageURL .= "://";

			if (array_key_exists("SERVER_PORT",$_SERVER) && $_SERVER["SERVER_PORT"] != "80") {
				$pageURL .= $_SERVER["HTTP_HOST"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
			} else {
				$pageURL .= $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
			}
			return $pageURL;
		}

//-----------------------------------------------------

		public function remove_url_http_www($url)
		{
			$url = str_ireplace("http://www.",'',$url);
			$url = str_ireplace("https://www.",'',$url);
			$url = str_ireplace("http://",'',$url);
			$url = str_ireplace("https://",'',$url);
			return $url;
		}
//-----------------------------------------------------
		public function make_relative_url($url)
		{
			if($url=="")
			{
				return "";
			}
			$site_url = $this->remove_url_http_www(site_url());
			if(stripos($url,$site_url) !==false)
			{
				$url_no_www = $this->remove_url_http_www($url);
				if(strtolower(substr($url_no_www,0,strlen($site_url))) == strtolower($site_url))
				{
					$url = str_ireplace($site_url,'',$url_no_www);
				}
			}
			if($url=="")
			{
				$url="/";
			}
			return $url;
		}

//----------------------------------------------------
		public function make_absolute_url($url)
		{
			if(substr($url,0,1)=='/')
			{
				$url = site_url() . $url;
			}
			return $url;
		}

//----------------------------------------------------

		public function get_current_relative_url()
		{
			return $this->make_relative_url($this->get_current_URL());
		}
//----------------------------------------------------
		public function is_valid_url($url)
		{
			if(stripos($url,'://')!== false || substr($url,0, 1)=='/')
			{
				return true;
			}else{
				return false;
			}
		}
//----------------------------------------------------

public function get_current_parameters($remove_parameter="")
{	
	
	if($_SERVER['QUERY_STRING']!='')
	{
		$qry = '?' . $_SERVER['QUERY_STRING'];

		if(is_array($remove_parameter))
		{
			for($i=0;$i<count($remove_parameter);$i++)
			{
				if(array_key_exists($remove_parameter[$i],$_GET)){
    				$string_remove = '&' . $remove_parameter[$i] . "=" . $this->get($remove_parameter[$i]);
    				$qry=str_replace($string_remove,"",$qry);
    				$string_remove = '?' . $remove_parameter[$i] . "=" . $this->get($remove_parameter[$i]);
    				$qry=str_replace($string_remove,"",$qry);
				}
			}
			
		}else{		
			if($remove_parameter!='')
			{
				if(array_key_exists($remove_parameter,$_GET)){
				    $string_remove = '&' . $remove_parameter . "=" . $this->get($remove_parameter);
				    $qry=str_replace($string_remove,"",$qry);
				    $string_remove = '?' . $remove_parameter . "=" . $this->get($remove_parameter);
				    $qry=str_replace($string_remove,"",$qry);
				}
			}
		}
		return $qry;
	}else
	{
		return "";
	}
} 


//---------------------------------------------------------------

public function get_plugin_path($folder='')
{
        return WP_PLUGIN_DIR . '/' .  $this->get_plugin_folder() . '/' . $folder;
}

/* get_plugin_path ---------------------------------------------  
public function get_plugin_path()
{
   return $this->plugin_path;
}*/

/* get plugin slug -------------------------------------------- */
public function get_plugin_slug()
{
    return $this->slug;
}
        
 /* get plugin slug -------------------------------------------- */
public function get_plugin_file()
{
    return $this->plugin_file;
}
//-----------------------------------------------------


public function get_plugin_url($url='')
{
	return $this->plugin_url;
}


//----------------------------------------------------

public function get_visitor_IP()
{
	$ipaddress = $_SERVER['REMOTE_ADDR'];
	
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	
	return $ipaddress ;
}

//----------------------------------------------------


public function get_visitor_OS()
{

$userAgent= $_SERVER['HTTP_USER_AGENT'];
		$oses = array (
		'iPhone' => '(iPhone)',
		'Windows 3.11' => 'Win16',
		'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)', 
		'Windows 98' => '(Windows 98)|(Win98)',
		'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
		'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
		'Windows 2003' => '(Windows NT 5.2)',
		'Windows Vista' => '(Windows NT 6.0)|(Windows Vista)',
		'Windows 7' => '(Windows NT 6.1)|(Windows 7)',
		'Windows 8' => '(Windows NT 6.2)|(Windows 8)',
		'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
		'Windows ME' => 'Windows ME',
		'Open BSD'=>'OpenBSD',
		'Sun OS'=>'SunOS',
		//'Linux'=>'(Linux)|(X11)', to detect if android or not
		'Safari' => '(Safari)',
		'Macintosh'=>'(Mac_PowerPC)|(Macintosh)',
		'QNX'=>'QNX',
		'BeOS'=>'BeOS',
		'OS/2'=>'OS\/2',
		'SearchBot'=>'(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp\/cat)|(msnbot)|(ia_archiver)'
	);

	foreach($oses as $os=>$pattern){ 

		if(preg_match('/'.$pattern. '/i', $userAgent)) { 
			return $os; 
		}
	}
	
	// more tests
	
	$ua = strtolower($userAgent);
	
	if(stripos($ua,'android') !== false) { 
	    return 'Android';
	}
	
	if(stripos($ua,'iphone') !== false) {
	    return 'iOS';
	}
	

	if(stripos($ua,'ipad') !== false) {
	    return 'iOS';
	}
	
	if(stripos($ua,'ipod') !== false) {
	    return 'iOS';
	}
	
	if(stripos($ua,'windows') !== false) {
	    return 'Windows';
	}
	
	
	if(stripos($ua,'linux') !== false) {
	    return 'Linux';
	}
	
	
	if(stripos($ua,'googlebot') !== false) {
	    return 'Googlebot';
	}
	
	if(stripos($ua,'bot') !== false) {
	    return 'SearchBot';
	}
			
	
	return 'Unknown';
}

//-----------------------------------------------------------------

public function get_visitor_Browser()
{

$u_agent= $_SERVER['HTTP_USER_AGENT'];
$bname = 'Unknown';

if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Internet Explorer';
    }
    elseif(preg_match('/Firefox/i',$u_agent))
    {
        $bname = 'Firefox';
    }
    elseif(preg_match('/Chrome/i',$u_agent))
    {
        $bname = 'Chrome';
    }
    elseif(preg_match('/Safari/i',$u_agent))
    {
        $bname = 'Safari';
    }
    elseif(preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Opera';
    }
    elseif(preg_match('/Netscape/i',$u_agent))
    {
        $bname = 'Netscape';
    }
    elseif(preg_match('/googlebot/i',$u_agent))
    {
        $bname = 'GoogleBot';
    }
    elseif(preg_match('/bot/i',$u_agent))
    {
        $bname = 'SearchBot';
    }
    
    
 return $bname;    
    
}

//----------------------------------------------------

public function get_visitor_country()
{
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];
    $result  = "Unknown";
    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));

    if($ip_data && $ip_data->geoplugin_countryName != null)
    {
        $result = $ip_data->geoplugin_countryName;
    }

    return $result;
}

//---------------------------------------------------- 

public function option_msg($msg,$out='echo')
{	
	$msg = '<div id="message" class="updated"><p>' . $msg . '</p></div>';
	if($out=='echo')
	echo $msg;	
	elseif($out=='push')
	$this->push_msg($msg);
}

//---------------------------------------------------- 


public function info_option_msg($msg,$out='echo')
{	

    $msg = '<div id="message" class="updated"><p><div class="info_icon"></div> ' . $msg . '</p></div>';
	if($out=='echo')
	echo $msg;	
	elseif($out=='push')
	$this->push_msg($msg);
	

}

//---------------------------------------------------- 


public function warning_option_msg($msg,$out='echo') 
{	
	$msg = '<div id="message" class="error"><p><div class="warning_icon"></div> ' . $msg . '</p></div>';		
	if($out=='echo')
	echo $msg;	
	elseif($out=='push')
	$this->push_msg($msg);
	
	
}

//---------------------------------------------------- 

public function success_option_msg($msg,$out='echo')
{	
	$msg = '<div id="message" class="updated"><p><div class="success_icon"></div> ' . $msg . '</p></div>';		
	if($out=='echo')
	echo $msg;	
	elseif($out=='push')
	$this->push_msg($msg);
}

//---------------------------------------------------- 

public function failure_option_msg($msg,$out='echo')
{	
	$msg =  '<div id="message" class="error"><p><div class="failure_icon"></div> ' . $msg . '</p></div>';		
	if($out=='echo')
	echo $msg;	
	elseif($out=='push')
	$this->push_msg($msg);
	
}

//----------------------------------------------------

private function push_msg($msg)
{	
	global $utilpro;
	$msgs=$utilpro->get_option_value('admin_notices');
	if(is_array($msgs))
    {
        $msgs[count($msgs)]=$msg;
        
    }else
    {
        $msgs = array();
        $msgs[0]=$msg;
    }
    
    $utilpro->update_option('admin_notices',$msgs);
	
}

//---------------------------------------------------- 


public function there_is_cache()
{	

$plugins=get_option( 'active_plugins' );

		    for($i=0;$i<count($plugins);$i++)
		    {   
		       if ( array_key_exists($i, $plugins) && stripos($plugins[$i],'cache')!==false)
		       {
		       	  return $plugins[$i];
		       }
		    }


	return '';				
}

//---------------------------------------------------- 


public function there_is_plugin($plugin)
{	

$plugins=get_option( 'active_plugins' );

		    for($i=0;$i<count($plugins);$i++)
		    {   
		       $phpfile = substr( $plugins[$i], strrpos( $plugins[$i], '/' )+1 );
		       $phpfile = explode(".", $phpfile);
		       $plugin_name = $phpfile[0];
		       if ($plugin_name==$plugin)
		       {
		         return true;  
		       }
		    }


	return false;				
}
	
//---------------------------------------------------------------
	
public function regex_prepare($string)
{
 
    $from= array('.', '+', '*', '?','[','^',']','$','(',')','{','}','=','!','<','>','|',':','-',')','/', '\\');
    $to= array('\\.', '\\+', '\\*', '\\?','\\[','\\^','\\]','\\$','\\(','\\)','\\{','\\}','\\=','\\!','\\<','\\>','\\|','\\:','\\-','\\)','\\/','\\\\');
    return str_replace($from,$to,$string);
 
}
	
 
}}
     
?>