<?php

     	/**
         * @package Elgg 
         * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
         * @author Roger Curry, Grid Research Centre [curry@cpsc.ucalgary.ca]
         * @author Tingxi Tan, Grid Research Centre [txtan@cpsc.ucalgary.ca]
         * @link http://grc.ucalgary.ca/
         */
 
    //the number of presentations to display
	$number = (int) $vars['entity']->num_display;
	if (!$number)
		$number = 4;
		
    //the page owner
	$owner = $vars['entity']->owner_guid;
      
	$presentations = get_entities_from_relationship('author',$owner,true,'object','presentation',0,'',$number);

    if($presentations){
		
		echo "<div id=\"presentationwidget\" class=\"contentWrapper\">";

		foreach($presentations as $presentation){
			set_context('references');
			echo elgg_view_entity($presentation);
		}
		echo "</div>";
    }
      
?>
