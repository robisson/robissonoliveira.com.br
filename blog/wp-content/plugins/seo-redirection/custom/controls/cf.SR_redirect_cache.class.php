<?php
if(!class_exists('free_SR_redirect_cache')){
    class free_SR_redirect_cache {
        
        

        /*- Add Redirect ----------------------------------------*/
        public function add_redirect($post_id,$is_redirected,$redirect_from,$redirect_to,$redirect_type=301)
        {
            global $wpdb,$table_prefix;
            $table_name = $table_prefix . 'WP_SEO_Cache';
            $wpdb->query(" insert IGNORE into $table_name(ID,is_redirected,redirect_from,redirect_to,redirect_type) values('$post_id','$is_redirected','$redirect_from','$redirect_to','$redirect_type'); ");
        }

        /*- Fetch Redirect ----------------------------------------*/
        public function fetch_redirect($post_id)
        {
            global $wpdb,$table_prefix;
            $table_name = $table_prefix . 'WP_SEO_Cache';
            return $wpdb->get_row("select *  from  $table_name where ID='$post_id'; ");
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
        
        
        
        /*- Redirect Cache ----------------------------------------*/
        public function redirect_cached($post_id)
        {
            $redirect = $this->fetch_redirect($post_id);
            
            if($redirect != null && $redirect->redirect_from==$this->get_current_relative_url())
            {                                
                if($redirect->is_redirected==1)
                {
                    if($redirect->redirect_type==301)
                    {
                        header ('HTTP/1.1 301 Moved Permanently');
                        header ("Location: " . $redirect->redirect_to);
                        exit();
                    }
                    else if($redirect->redirect_type==307)
                    {
                        header ('HTTP/1.0 307 Temporary Redirect');
                        header ("Location: " . $redirect->redirect_to);
                        exit();
                    }
                    else if($redirect->redirect_type==302)
                    {
                        header ("Location: " . $redirect->redirect_to);
                        exit();
                    }
                }
                return 'not_redirected';
            }
            return 'not_found';
            
        }

        /*- Delete Redirect ----------------------------------------*/
        public function del_redirect($post_id)
        {
            global $wpdb,$table_prefix;
            $table_name = $table_prefix . 'WP_SEO_Cache';
            return $wpdb->get_var("delete from  $table_name where ID='$post_id'; ");
        }

        /*- Free Cache ----------------------------------------*/
        public function free_cache()
        {
            global $wpdb,$table_prefix;
            $table_name = $table_prefix . 'WP_SEO_Cache';
            $wpdb->query(" TRUNCATE TABLE  $table_name ");
        }

        /*- Cache Count ----------------------------------------*/
        public function count_cache()
        {
            global $wpdb,$table_prefix;
            $table_name = $table_prefix . 'WP_SEO_Cache';
            return $wpdb->get_var("select count(*) as cnt from  $table_name where 1;  ");
        }        

    }}