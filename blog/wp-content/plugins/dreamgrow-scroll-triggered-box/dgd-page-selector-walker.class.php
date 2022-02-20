<?php

class Dgd_Page_Selector_Walker extends Walker_page {
    private $selected_pages=array();

    public function __construct($selected_pages) {
        $this->selected_pages=$selected_pages;
    }

    /**
     * Start the element output.
     *
     * @param  string $output Passed by reference. Used to append additional content.
     * @param  object $item   Menu item data object.
     * @param  int $depth     Depth of menu item. May be used for padding.
     * @param  array $args    Additional strings.
     * @return void
     */
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 )
    {
        if ( $depth )
            $indent = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $depth);
        else
            $indent = '';
        $output.= '<option value="'.$item->ID.'"';
        if(is_array($this->selected_pages) && in_array($item->ID, $this->selected_pages)) {
            $output.= ' selected="1"';
        }
        $output.= '>'.$indent.$item->post_title.'</option>';
    }
}