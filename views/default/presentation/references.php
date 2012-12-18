<?php

	/**
         * @package Elgg
         * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
         * @author Roger Curry, Grid Research Centre [curry@cpsc.ucalgary.ca]
         * @author Tingxi Tan, Grid Research Centre [txtan@cpsc.ucalgary.ca]
         * @link http://grc.ucalgary.ca/
         */

		$info = "<em><b><a href=\"{$vars['entity']->getURL()}\">{$vars['entity']->title}</a></b></em>";
		//echo "$info by ";
		$authors = $vars['entity']->authors;
		$authors = explode(',',$authors);
		if (!empty($authors)) {
			for($index= 0; $index < count($authors) - 1; $index++) {
				$cauthor = $authors[$index];
				if(!ctype_digit($cauthor)) echo "$cauthor, ";
				else{
					$user = get_entity((int)$cauthor);
					echo '<a href="' . $CONFIG->wwwroot . 'pg/presentation/' . $user->username . '">' . $user->name . '</a>, ';
						
				}
			}
			$cauthor = $authors[$index];
			if(!ctype_digit($cauthor)) echo "$cauthor";
			else{
				$user = get_entity((int)$cauthor);
				echo '<a href="' . $CONFIG->wwwroot . 'pg/presentation/' . $user->username . '">' . $user->name . '</a>';
			}
			
		}
		echo ". $info.";
	
		if(!empty($vars['entity']->event_name)){
			$event_name = $vars['entity']->event_name;
			echo " $event_name";
		}
		if(!empty($vars['entity']->event_loc)){
			$event_loc = $vars['entity']->event_loc;
			echo ", $event_loc";
		}
		if(!empty($vars['entity']->date)){
			echo ", ".$vars['entity']->date.".";
		}	
		/*if (!empty($vars['entity']->source)) {
			echo " " . $vars['entity']->source;
		}
		if (!empty($vars['entity']->volume))
			echo ' ' . $vars['entity']->volume;
		if (!empty($vars['entity']->issue))
			echo '('.$vars['entity']->issue . ')';
		if (!empty($vars['entity']->pages))
			echo ':' . $vars['entity']->pages;
		if (!empty($vars['entity']->year)) {
                        echo ', ' . $vars['entity']->year;
                }*/
		
		$page_owner = page_owner_entity();
		if($page_owner instanceof ElggGroup){
			$group = get_entity($page_owner->guid);
			if(isloggedin() && is_group_member($group->guid,$_SESSION['guid'])){
				echo elgg_view('presentation/tag',array('pub'=>$vars['entity']->guid,'group'=>$group->guid));
			}
		}
		
?>
