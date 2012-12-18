<?php
        /**
         * @package Elgg
         * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
         * @author Roger Curry, Grid Research Centre [curry@cpsc.ucalgary.ca]
         * @author Tingxi Tan, Grid Research Centre [txtan@cpsc.ucalgary.ca]
         * @link http://grc.ucalgary.ca/
         */


	gatekeeper();
        action_gatekeeper();

	$guid = (int) get_input('presentationpost');
	$title = get_input('presentationtitle');
	$abstract = get_input('presentationabstract');
//	$access = get_input('access_id');
	$access = ACCESS_PUBLIC;
	$keywords = get_input('presentationkeywords');
	$comments_on = get_input('comments_select','Off');
	$authors = get_input('authorselected');
	$videolink = get_input('video');
	$event_name = get_input('event_name');
	$event_loc = get_input('event_loc');
	$slideembed = get_input('slide','',false);
	$date = get_input('date');
	$pubs = get_input('pub');
	if($slideembed){
		$slideembed = htmlentities($slideembed);
		if(!(preg_match('/.*www\.slideshare\.net.*/',$slideembed))){
			register_error('Please enter embed code from SlideShare');
			forward($_SERVER['HTTP_REFERER']);
		}
	}
	/*foreach($CONFIG->presentation as $shortname => $valuetype){
		$params_value[$shortname] = get_input($shortname);
	}*/
	if(is_array($authors)){
		$pauthors = array();
                	for($i = 0; $i < count($authors); $i++){
                  		$ca = preg_split('/,/',$authors[$i]);
                               	if($ca[0] == 'new') 
					$pauthors[$i] = $ca[1];
                               	else 
					$pauthors[$i] = (int)$ca[0];
                       }
       }else{
       		register_error(elgg_echo("presentation:blankauthors"));
                forward($_SERVER['HTTP_REFERER']);
       }
	$attachment = get_input('attachment_guid');
			
	$presentation = get_entity($guid);
	if ($presentation->getSubtype() == "presentation" && $presentation->canEdit()) {
		$_SESSION['user']->presentationtitle = $title;
		$_SESSION['user']->presentationabstract = $abstract;
		$_SESSION['user']->presentationkeywords = $keywords;
		$_SESSION['user']->presentationauthors = $authors;
		$_SESSION['user']->presentationexauthors = $exauthors;
		$_SESSION['user']->presentationvideo = $videolink;
		
		$tagarray = string_to_tag_array($keywords);
			
		if (empty($title)) {
			register_error(elgg_echo("presentation:blank"));
			forward("mod/presentation/add.php");
		} else {
			$owner = get_entity($presentation->getOwner());
			$presentation->access_id = $access;
			$presentation->title = $title;
			$presentation->description = $abstract;
			if (!$presentation->save()) {
				register_error(elgg_echo("presentation:error"));
				forward("mod/presentation/edit.php?presentationpost=" . $guid);
			}
			$presentation->clearMetadata('tags');
			if (is_array($tagarray)) {
				$presentation->tags = $tagarray;
			}
			$presentation->comments_on = $comments_on; //whether the users wants to allow comments or not on the presentation post
			$presentation->video = $videolink;
			$presentation->slide = $slideembed;
			$presentation->date = $date;
			$presentation->event_name = $event_name;
			$presentation->event_loc = $event_loc;
			/*foreach($CONFIG->presentation as $shortname => $valuetype)
			$presentation->$shortname = $params_value[$shortname];*/
			$presentation->clearRelationships();
			if(is_array($pubs) && sizeof($pubs) > 0){
				foreach($pubs as $pub){
					add_entity_relationship($presentation->getGUID(), 'presentation_of', $pub);
				}
			}
			if (is_array($pauthors) && sizeof($pauthors) > 0) {
				foreach($pauthors as $author) {
					if(is_int($author))
					add_entity_relationship($presentation->getGUID(),'author',$author);
				}
			}
			$pauthors = implode(',',$pauthors);	
			$presentation->authors = $pauthors;
			$presentation->attachment = $attachment;
				
			system_message(elgg_echo("presentation:posted"));
			add_to_river('river/object/presentation/update','update',$_SESSION['user']->guid,$presentation->guid);
				
			remove_metadata($_SESSION['user']->guid,'presentationtitle');
			remove_metadata($_SESSION['user']->guid,'presentationabstract');
			remove_metadata($_SESSION['user']->guid,'presentationkeywords');
			remove_metadata($_SESSION['user']->guid,'presentationauthors');
			remove_metadata($_SESSION['user']->guid,'presentationexauthors');
			remove_metadata($_SESSION['user']->guid,'presentationuri');
			remove_metadata($_SESSION['user']->guid,'presentationsource');
			remove_metadata($_SESSION['user']->guid,'presentationyear');
			
			$username = $_SESSION['user']->username;
			forward("pg/presentation/$username");
					
			}
		
		}


?>
