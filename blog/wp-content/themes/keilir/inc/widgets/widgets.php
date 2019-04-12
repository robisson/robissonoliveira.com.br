<?php

if (function_exists('register_sidebar')) {
	
	/* Side bar */
	register_sidebar(array(				
		'id' => 'sidebar', 					
		'name' => 'Sidebar',				
		'description' => 'Right sidebar', 
		'before_widget' => '<div id="%1$s" class="box %2$s">',	
		'after_widget' => '</div>',	
		'before_title' => '<h3 class="widget-head">',	
		'after_title' => '</h3>',		
		'empty_title'=> '',					
	));

	/* Footer Widgets */
	$footer_widgets_num = wp_get_sidebars_widgets();
	$footer_widgets_num = (isset($footer_widgets_num['footer-widgets'])) ? count( $footer_widgets_num['footer-widgets']) : 0;

	switch ($footer_widgets_num) {
		case 1:
			$footer_widgets_num = '12';
		break;
		case 2:
			$footer_widgets_num = '6';
		break;
		case 3:
			$footer_widgets_num = '4';
		break;
		case 4:
			$footer_widgets_num = '3';
		break;
		case 5:
			$footer_widgets_num = '2 offset1';
		break;
		case 6:
			$footer_widgets_num = '2';
		break;
		case 7:
			$footer_widgets_num = '1';
		break;
		case 8:
			$footer_widgets_num = '1 offset2';
		break;
		case 11:
			$footer_widgets_num = '1';
		break;
		case 12:
			$footer_widgets_num = '1';
		break;
		default:
			$footer_widgets_num = '1';
		break;
	}

	$before_widget = '<div id="%1$s" class="span'.$footer_widgets_num.' %2$s">';
	$after_widget = '</div>';

	if(of_get_option('footer_fluid')){
		$before_widget = '<li><div id="%1$s" class="%2$s">';
		$after_widget = '</li>';
	}

	register_sidebar(array(
	   'name' => __('Footer Widgets','bluth' ),
	   'id'   => 'footer-widgets',
		'description'   => __( 'There are 4 slots available in the footer','bluth' ),
		'before_widget' => $before_widget,
		'after_widget'  => $after_widget,
		'before_title'  => '<h3 class="widget-head">',
		'after_title'   => '</h3>'
   	));
}
