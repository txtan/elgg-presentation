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

	// Get input data
	$title = get_input('presentationtitle');
	$abstract = get_input('presentationabstract');
	$keywords = get_input('presentationkeywords');
//	$access = get_input('access_id');
	$access = ACCESS_PUBLIC;
	$comments_on = get_input('comments_select','Off');
	$authors = get_input('authorselected');
	$video = get_input('video');
	$slide = get_input('slide', '', false);
	$event_name = get_input('event_name');
        $event_loc = get_input('event_loc');
	$date = get_input('date');
	$pubs = get_input('pub');
	if($slide){
		$slide = htmlentities($slide);
                if(!(preg_match('/.*www\.slideshare\.net.*/',$slide))){
                        register_error('Please enter embed code from SlideShare');                        forward($_SERVER['HTTP_REFERER']);
                }
        }

	/*foreach($CONFIG->presentation as $shortname => $valuetype){
        	$params_value[$shortname] = get_input($shortname);
        }*/
	if(is_array($authors)){
		$pauthors = array();
		for($i=0; $i < count($authors); $i++){
			$ca = preg_split('/,/',$authors[$i]);
			if($ca[0] == 'new') $pauthors[] = $ca[1];
			else $pauthors[] = (int)$ca[0];
		}
	}else{
		register_error(elgg_echo("presentation:blankauthors"));
                forward($_SERVER['HTTP_REFERER']);
	}
	$attachment = get_input('attachment_guid');		
		
	// Cache to the session
	$_SESSION['user']->presentationtitle = $title;
	$_SESSION['user']->presentationabstract = $abstract;
	$_SESSION['user']->presentationkeywords = $keywords;
	$_SESSION['user']->presentationauthors = $authors;
	$_SESSION['user']->presentationexauthors = $exauthors;
	$_SESSION['user']->presentationvideo = $video;
		
	// Convert string of tags into a preformatted array
	$tagarray = string_to_tag_array($keywords);
		
	// Make sure the title / description aren't blank
	if (empty($title)) {
		register_error(elgg_echo("presentation:blank"));
		forward($_SERVER['HTTP_REFERER']);
	} else {
		$presentation = new ElggObject();
		$presentation->subtype = "presentation";
		$presentation->owner_guid = $_SESSION['user']->getGUID();
		$presentation->container_guid = (int)get_input('container_guid', $_SESSION['user']->getGUID());
		$presentation->access_id = $access;
		$presentation->title = $title;
		$presentation->description = $abstract;
		if (!$presentation->save()) {
			register_error(elgg_echo("presentation:error"));
			forward($_SERVER['HTTP_REFERER']);
		}
		if (is_array($tagarray)) {
			$presentation->tags = $tagarray;
		}
		$presentation->comments_on = $comments_on; 
		$presentation->video = $video;
		$presentation->slide = $slide;
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
		$presentation->authors=$pauthors;
		$presentation->attachment = $attachment;
		
		system_message(elgg_echo("presentation:posted"));
	        add_to_river('river/object/presentation/create','create',$_SESSION['user']->guid,$presentation->guid);
			
		remove_metadata($_SESSION['user']->guid,'presentationtitle');
		remove_metadata($_SESSION['user']->guid,'presentationabstract');
		remove_metadata($_SESSION['user']->guid,'presentationkeywords');
		remove_metadata($_SESSION['user']->guid,'presentationauthors');
		remove_metadata($_SESSION['user']->guid,'presentationexauthors');
		remove_metadata($_SESSION['user']->guid,'presentationvideo');
		remove_metadata($_SESSION['user']->guid,'presentationsource');
		remove_metadata($_SESSION['user']->guid,'presentationyear');
			
		$page_owner = get_entity($presentation->container_guid);
		if ($page_owner instanceof ElggUser)
			$username = $page_owner->username;
		else if ($page_owner instanceof ElggGroup)
			$username = "group:" . $page_owner->guid;
		forward("pg/presentation/$username");
				
	}
		
?>
