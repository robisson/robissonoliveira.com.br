<?php


	/**
	 * Pull Quote 
	 * @param  array $atts Array of attributes
	 * @return html returns a pull quote
	 */
	function bluth_pullquote($atts, $content = null){

		extract(shortcode_atts(array('align'=> 'left'),$atts));
		return '<blockquote class="pullquote '.(($align == 'right') ? 'pull-right' : 'pull-left').'"><i class="icon-quote-left"></i><p class="pullquote-text">'.do_shortcode($content).'</p><i class="icon-quote-right"></i></blockquote>';
	}

	/**
	 * Dropcaps 
	 * @param  array $atts Array of attributes
	 * @return html returns a drop cap
	 */
	function bluth_dropcap($atts, $content = null){

		extract(shortcode_atts(array('background'=> '', 'bgcolor'=> 'default'),$atts));
		return '<span class="dropcap'.((empty($background) or $background == 'no') ? '' : ' dropcap-bg').'"'.($bgcolor != 'default' ? ' style="background:'.$bgcolor.'"' : '') .'>'.do_shortcode($content).'</span>';
	}
	/**
	 * Alert box 
	 * @param  array $atts Array of attributes
	 * @return html returns an alert box
	 */
	function bluth_alert($atts, $content = null){

		extract(shortcode_atts(array(
	      'type' 	=> '',
	      'close'	=> 'true'
	    ),$atts));

		$html  = '<div class="alert'.(!empty($type) ? ' alert-'.$type : '').'">';
		if($close == 'true'){
			$html .= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
		}
		$html .= do_shortcode($content);
		$html .= '</div>';
		return $html;
	}

	/**
	 * Social
	 * @param  array $atts Aattributes
	 * @return returns a social link
	 */
	function bluth_social($atts, $content = null){
		extract(shortcode_atts(array('media'=> '', 'url' => ''),$atts));
		$name = $media;
		switch($media){
			case 'twitter':
			case 'linkedin':
				$media .= '-1';
			break;
			case 'flickr':
			case 'pinterest':
				$media .= '-circled';
			break;
			case 'vimeo':
			case 'tumblr':
				$media .= '-rect';
			break;
			case 'instagram':
				$media .= '-filled';
			break;
		}
		return '<a href="'.$url.'" class="bl-social-icon tips" data-title="'.$name.'"><i class="icon-'.$media.'"></i></a>';
	}

	/**
	 * Label
	 * @param  array $atts Aattributes
	 * @return html label span
	 */
	function bluth_label($atts, $content = null){

		extract(shortcode_atts(array('type'=> ''),$atts));
		return '<span class="label'.(!empty($type) ? ' label-'.$type : '').'">'.do_shortcode($content).'</span>';
	}

	/**
	 * Badge
	 * @param  array $atts Aattributes
	 * @return html badge span
	 */
	function bluth_badge($atts, $content = null){

		extract(shortcode_atts(array('type'=> ''),$atts));
		return '<span class="badge '.(!empty($type) ? ' label-'.$type : '').'">'.do_shortcode($content).'</span>';
	}

	/**
	 * Well
	 * @param  array $atts Aattributes
	 * @return html well inset effect
	 */
	function bluth_well($atts, $content = null){
		$content = wpautop(trim($content));
		return '<div class="well">'.do_shortcode($content).'</div>';
	}

	/**
	 * Button
	 * @param  array $atts Aattributes
	 * @return html style button link
	 */
	function bluth_button($atts, $content = null){

		extract(shortcode_atts(array(
        'url'      => '#',
		'style'     => '',
        'size'    	=> '',
        'block'    	=> '',
		'target'    => '_self',
		'icon'		=> ''
    	), $atts));
		return '<a href="'.$url.'" class="btn '.(($style == 'default') ? '' : 'btn-'.$style).' '.(($size == 'default') ? '' : 'btn-'.$size).' '.(($block == 'true') ? 'btn-block' : '').'" target="'.$target.'">'.(!empty($icon) ? '<i class="icon-'.$icon.'"></i> ' : '').do_shortcode($content).'</a>';
	}

	/**
	 * Blockquote
	 * @return html For quoting blocks of content
	 */
	function bluth_blockquote( $atts, $content = null) {
		
		extract(shortcode_atts(array('source' => ''), $atts));

		return '<blockquote><p>'.do_shortcode($content).(!empty($source) ? '<small>'.$source.'</small>' : '').'</p></blockquote>';
	}


	/**
	 * syntax highlighting
	 * @return html convert content to html enteties
	 */
	function bluth_syntax( $atts, $content = null) {
		
		extract(shortcode_atts(array('type' => 'html'), $atts));

		return '<pre class="'.$type.'">'.do_shortcode($content).'</pre>';
	}

	/**
	 * Tooltip
	 * @param  array $atts Aattributes
	 * @return html Anchor with a tooltip
	 */
	function bluth_tooltip( $atts, $content = null)
	{
		extract(shortcode_atts(array('text' => '', 'trigger' => 'hover'), $atts));

		return '<a href="javascript:void(0)" class="tips" data-trigger="'.$trigger.'" title="'.$text.'">'. do_shortcode($content) . '</a>';
	}

	/**
	 * Popover
	 * @param  array $atts Aattributes
	 * @return html Anchor with a popover
	 */
	function bluth_popover( $atts, $content = null)
	{
		extract(shortcode_atts(array('title' => '', 'trigger' => 'hover', 'placement' => 'top', 'body' => ''), $atts));

		return '<a href="javascript:void(0)" class="bl_popover" data-trigger="'.$trigger.'" data-placement="'.$placement.'" data-content="'.$body.'" title="'.$title.'">'. do_shortcode($content) . '</a>';
	}

	/**
	 * Accordion
	 * @param  array $atts Aattributes
	 * @return html accordion with multiple collapsible components
	 */
	function bluth_accordion($atts, $content = null){

		return '<div class="accordion" id="accordion2">'.do_shortcode($content).'</div>';
	}

	/**
	 * Accordion
	 * @param  array $atts Aattributes
	 * @return html accordion with multiple collapsible components
	 */
	$accordion = array('id' => 0, 'almost_unique' => rand(0,999));
	function bluth_accordion_group($atts, $content = null){

		global $accordion;
		$accordion['id']++;

		extract(shortcode_atts(array('title'=> 'Heading'),$atts));

		$html = '<div class="accordion-group">';
		$html .= '<div class="accordion-heading">';
		$html .= '<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#'.$accordion['almost_unique'].'_'.$accordion['id'].'">';
		$html .= $title;
		$html .= '</a>';
		$html .= '</div>';
		$html .= '<div id="'.$accordion['almost_unique'].'_'.$accordion['id'].'" class="accordion-body collapse'.(($accordion['id'] == 1) ? ' in' : '') . '">';
		$html .= '<div class="accordion-inner">';
		$html .= do_shortcode($content);
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Columns
	 * @return html returns the content in a column
	 */
	// [ ][ ]

	function two_first( $atts, $content = null ) {			return '<div class="row-fluid"><div class="span6">'. do_shortcode( $content ) . '</div>';	}
	function two_second( $atts, $content = null ) {			return '<div class="span6">'. do_shortcode( $content ) . '</div></div>';	}

	// [   ][ ]
	function two_one_first( $atts, $content = null ) {		return '<div class="row-fluid"><div class="span8">'. do_shortcode( $content ) . '</div>';	}
	function two_one_second( $atts, $content = null ) {		return '<div class="span4">'. do_shortcode( $content ) . '</div></div>';	}

	// [ ][   ]
	function one_two_first( $atts, $content = null ) {		return '<div class="row-fluid"><div class="span4">'. do_shortcode( $content ) . '</div>';	}
	function one_two_second( $atts, $content = null ) {		return '<div class="span8">'. do_shortcode( $content ) . '</div></div>';	}

	// [ ][ ][ ]
	function three_first( $atts, $content = null ) {		return '<div class="row-fluid"><div class="span4">'. do_shortcode( $content ) . '</div>';	}
	function three_second( $atts, $content = null ) {		return '<div class="span4">'. do_shortcode( $content ) . '</div>';	}
	function three_third( $atts, $content = null ) {		return '<div class="span4">'. do_shortcode( $content ) . '</div></div>';	}

	// [ ][ ][ ][ ]
	function four_first( $atts, $content = null ) {			return '<div class="row-fluid"><div class="span3">'. do_shortcode( $content ) . '</div>';	}
	function four_second( $atts, $content = null ) {		return '<div class="span3">'. do_shortcode( $content ) . '</div>';	}
	function four_third( $atts, $content = null ) {			return '<div class="span3">'. do_shortcode( $content ) . '</div>';	}
	function four_fourth( $atts, $content = null ) {		return '<div class="span3">'. do_shortcode( $content ) . '</div></div>';	}

	// [ ][ ][   ]
	function one_one_two_first( $atts, $content = null ) {	return '<div class="row-fluid"><div class="span3">'. do_shortcode( $content ) . '</div>';	}
	function one_one_two_second( $atts, $content = null ) {	return '<div class="span3">'. do_shortcode( $content ) . '</div>';	}
	function one_one_two_third( $atts, $content = null ) {	return '<div class="span6">'. do_shortcode( $content ) . '</div></div>';	}

	// [   ][ ][ ]
	function two_one_one_first( $atts, $content = null ) {	return '<div class="row-fluid"><div class="span6">'. do_shortcode( $content ) . '</div>';	}
	function two_one_one_second( $atts, $content = null ) {	return '<div class="span3">'. do_shortcode( $content ) . '</div>';	}
	function two_one_one_third( $atts, $content = null ) {	return '<div class="span3">'. do_shortcode( $content ) . '</div></div>';	}

	// [ ][   ][ ]
	function one_two_one_first( $atts, $content = null ) {	return '<div class="row-fluid"><div class="span3">'. do_shortcode( $content ) . '</div>';	}
	function one_two_one_second( $atts, $content = null ) {	return '<div class="span6">'. do_shortcode( $content ) . '</div>';	}
	function one_two_one_third( $atts, $content = null ) {	return '<div class="span3">'. do_shortcode( $content ) . '</div></div>';	}

	/**
	 * Divider 
	 * @param  array $atts Array of attributes
	 * @return html returns a row-fluid divider
	 */
	function bluth_divider( $atts, $content = null ) {

		extract(shortcode_atts(array(
	      'type' 	=> 'white',
	      'color' 	=> 'rgba(0,0,0,0.1)',
	      'text' 	=> '',
	    ),$atts));


		$spacing = !empty($atts['spacing']) ? ' margin-top:'.$atts['spacing'].'px; margin-bottom:'.$atts['spacing'].'px; ' : ' margin-top:10px; margin-bottom:10px; ';

		$html = '<div class="row-fluid" style="min-height:0;">';
		switch($type){
			case 'white';
				$html .= '<div class="span12" style="min-height:0; '.$spacing.'"></div>';
			break;
			case 'thin':
				$html .= '<div class="span12" style="min-height:0; border-bottom:1px solid '.$color.';'.$spacing.'"></div>';
			break;
			case 'thick':
				$html .= '<div class="span12" style="min-height:0; border-bottom:2px solid '.$color.';'.$spacing.'"></div>';
			break;
			case 'short':
				$html .= '<div class="span2 offset5" style="min-height:0; border-bottom:2px solid '.$color.';'.$spacing.'"></div>';
			break;
			case 'dotted':
				$html .= '<div class="span12" style="min-height:0; border-bottom:1px dotted '.$color.';'.$spacing.'"></div>';
			break;
			case 'dashed':
				$html .= '<div class="span12" style="min-height:0; border-bottom:1px dashed '.$color.';'.$spacing.'"></div>';
			break;
		}
		$html .= '</div>';

		return $html;
	}

	function bluth_process_shortcode($content) {
	    global $shortcode_tags;
	 
	    $original_shortcode_tags = $shortcode_tags;
	    remove_all_shortcodes();
	    
		add_shortcode('divider', 'bluth_divider');
		add_shortcode('social', 'bluth_social');
		add_shortcode('alert', 'bluth_alert');
		add_shortcode('label', 'bluth_label');
		add_shortcode('badge', 'bluth_badge');
		add_shortcode('well', 'bluth_well');
		add_shortcode('button', 'bluth_button');
		add_shortcode('blockquote', 'bluth_blockquote');
		add_shortcode('syntax', 'bluth_syntax');
		add_shortcode('tooltip', 'bluth_tooltip');
		add_shortcode('popover', 'bluth_popover');
		add_shortcode('accordion', 'bluth_accordion');
		add_shortcode('accordion-group', 'bluth_accordion_group');
		add_shortcode('dropcap', 'bluth_dropcap');
		add_shortcode('pullquote', 'bluth_pullquote');

		// COLUMNS
		add_shortcode( "two_first", "two_first" ); 
		add_shortcode( "two_second", "two_second" ); 

		add_shortcode( "two_one_first", "two_one_first" ); 
		add_shortcode( "two_one_second", "two_one_second" );

		add_shortcode( "one_two_first", "one_two_first" ); 
		add_shortcode( "one_two_second", "one_two_second" ); 

		add_shortcode( "three_first", "three_first" ); 
		add_shortcode( "three_second", "three_second" ); 
		add_shortcode( "three_third", "three_third" ); 

		add_shortcode( "four_first", "four_first" ); 
		add_shortcode( "four_second", "four_second" ); 
		add_shortcode( "four_third", "four_third" ); 
		add_shortcode( "four_fourth", "four_fourth" ); 

		add_shortcode( "one_one_two_first", "one_one_two_first" ); 
		add_shortcode( "one_one_two_second", "one_one_two_second" ); 
		add_shortcode( "one_one_two_third", "one_one_two_third" ); 

		add_shortcode( "two_one_one_first", "two_one_one_first" ); 
		add_shortcode( "two_one_one_second", "two_one_one_second" ); 
		add_shortcode( "two_one_one_third", "two_one_one_third" ); 

		add_shortcode( "one_two_one_first", "one_two_one_first" ); 
		add_shortcode( "one_two_one_second", "one_two_one_second" ); 
		add_shortcode( "one_two_one_third", "one_two_one_third" ); 
	 
	    $content = do_shortcode($content);
	    $shortcode_tags = $original_shortcode_tags;
	    return $content;
	}

	add_filter('the_content', 'bluth_process_shortcode', 7);
	// Shortcodes in widget
	add_filter('widget_text', 'bluth_process_shortcode', 7);
	add_filter('shortcode_filter', 'bluth_process_shortcode', 7);



	add_action('admin_head', 'blu_add_tinymce');
	function blu_add_tinymce() {  

	   if(current_user_can('edit_posts') && current_user_can('edit_pages')){  
	     add_filter('mce_external_plugins', 'blu_add_tinymce_plugin');  
	     add_filter('mce_buttons', 'blu_add_tinymce_button');
	   }  
	}  


	// Define Position of TinyMCE Icons
	function blu_add_tinymce_button($buttons) {  
		
		array_push($buttons, 'bluthcodes');  
		return $buttons;  
	} 
	function blu_add_tinymce_plugin($plugin_array) {  

		$plugin_array['bluthcodes_location'] = get_template_directory_uri() . '/inc/tinymce/tinymce.js';
	   	return $plugin_array;  
	}  
