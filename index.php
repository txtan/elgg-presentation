<?php
        /**
         * @package Elgg
         * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
         * @author Roger Curry, Grid Research Centre [curry@cpsc.ucalgary.ca]
         * @author Tingxi Tan, Grid Research Centre [txtan@cpsc.ucalgary.ca]
         * @link http://grc.ucalgary.ca/
         */


	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
		
	$page_owner = page_owner_entity();
	if ($page_owner === false || is_null($page_owner)) {
		$page_owner = $_SESSION['user'];
		set_page_owner($_SESSION['guid']);
	}
	
	if($page_owner == $_SESSION['user']){
		$area2 = elgg_view_title(elgg_echo('presentation:authored:your'));
	}else{
		$area1 = elgg_view_title($page_owner->name . "'s " . elgg_echo('presentation'));
	}
		
	set_context('references');
	$area2 .= '<div class="contentWrapper">';
	
	if($page_owner instanceof ElggGroup)
		$area2 .= list_entities_from_relationship('tagby',$page_owner->getGUID(),false,'object','presentation',0,999,false,false,true);
	else	
		$area2 .= list_entities_from_relationship('author',$page_owner->getGUID(),true,'object','presentation',0,999,false,false,true);
	$area2 .= '</div>';
	set_context('presentation');
	if($page_owner == $_SESSION['user']){
		$area2 .= elgg_view_title(elgg_echo('presentation:catalogued:your'));
		set_context('references');
		$area2 .= '<div class="contentWrapper">';
		$area2 .= list_user_objects($page_owner->getGUID(),'presentation',0,false,false,true);
		$area2 .= '</div>';
		set_context('presentation');
	}

	$area3 = elgg_view('presentation/search');
        $body = elgg_view_layout("two_column_left_sidebar", '', $area1 . $area2, $area3);
	page_draw(sprintf(elgg_echo('presentation:user'),$page_owner->name),$body);
		
?>
