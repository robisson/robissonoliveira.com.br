<?php
$template = array(

'edit' => array(
		'content'=> "<a href='{param}'><div class='edit_template'></div></a>",
		'options' => array(
							'width' => '20px',
							'align' => 'center'
								)
				)
	,	

'go_link' => array(
		
		'content' => "<a target='_blank' href='{param}'><div class='go_link_template'></div></a>",
		'options' => array(
							'width' => '20px',
							'align' => 'center'
								)
				)
	,	

'redirect_link' => array(
		
		'content' => "<a href='{param}'><div class='go_link_template'></div></a>",
		'options' => array(
							'width' => '20px',
							'align' => 'center'
								)
				)
	,		

'del' => array(
		
		'content' => "<a href='#' onclick=\"if(confirm('Are you sure you want to delete this item?'))window.location='{param}';\"><div class='del_template'></div></a>",
		'options' => array(
							'width' => '20px',
							'align' => 'center'
								)
				)

);
