<?php
	global $CONFIG;

	/**
         * @package Elggi
         * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
         * @author Roger Curry, Grid Research Centre [curry@cpsc.ucalgary.ca]
         * @author Tingxi Tan, Grid Research Centre [txtan@cpsc.ucalgary.ca]
         * @link http://grc.ucalgary.ca/
         */

	$shortnames = $CONFIG->presentation;
	// Set title, form destination
		if (isset($vars['entity'])) {
			$action = "presentation/edit";
			$title = $vars['entity']->title;
			$abstract = $vars['entity']->description;
			$keywords = $vars['entity']->tags;
			$access_id = $vars['entity']->access_id;
			$owner = $vars['entity']->getOwnerEntity();
			$highlight = 'default';
			$authors = $vars['entity']->authors;
			$authors = explode(',',$authors);
			$attachment_guid = $vars['entity']->attachment;
			if($attachment_guid){
				$attachment_entity = get_entity($attachment_guid);
				if($attachment_entity)
					$attachment_file = $attachment_entity->title;
				else{
					$attachment_guid = '';
					$attachment_file = '';
				}
			}else{
				$attachment_file = '';
			}
			$video = $vars['entity']->video;
			$slide = $vars['entity']->slide;
			$date = $vars['entity']->date;
			$event_name = $vars['entity']->event_name;
			$event_loc = $vars['entity']->event_loc;
			$pubs = get_entities_from_relationship('presentation_of',$vars['entity']->guid, false, 'object','publication');
			/*foreach($shortnames as $shortname => $valuetype)
		 		$params_value[$shortname] = $vars['entity']->$shortname;*/
		} else  {
			$title = elgg_echo("presentation:add");
			$action = "presentation/add";
			$keywords = "";
			$title = "";
			$abstract = "";
			$access_id = ACCESS_PUBLIC;
			$owner = $vars['user'];
			$highlight = 'all';
			$authors = array();
			$attachment_guid = '';
			$attachment_file = '';
			$video = "";
			$slide = "";
			$date = "";
			$event_name = "";
			$event_loc = "";
			//$params_value = array();
			$container = $vars['container_guid'] ? elgg_view('input/hidden', array('internalname' => 'container_guid', 'value' => $vars['container_guid'])) : "";
		}

	// Just in case we have some cached details
		/*if (empty($abstract) || empty($title)) {
			$abstract = $vars['user']->presentationabstract;
			$title = $vars['user']->presentationtitle;
			$keywords = $vars['user']->presentationkeywords;
			$authors = $vars['user']->presentationauthors;
			$video = $vars['user']->presentationvideo;
			$source = $vars['user']->presentationsource;
			$year = $vars['user']->presentationyear;
		}*/

	// set the required variables

                $title_label = elgg_echo('title');
                $title_textbox = elgg_view('input/text', array('internalname' => 'presentationtitle', 'value' => $title));
                $abstract_label = elgg_echo('presentation:abstract');
                $abstract_textarea = elgg_view('input/longtext', array('internalname' => 'presentationabstract', 'value' => $abstract));
                $keywords_label = elgg_echo('presentation:keywords');
                $keywords_input = elgg_view('input/tags', array('internalname' => 'presentationkeywords', 'value' => $keywords));
         //       $access_label = elgg_echo('access');


          //$access_input = elgg_view('input/access', array('internalname' => 'access_id', 'value' => $access_id));
          $submit_input = elgg_view('input/submit', array('internalname' => 'submit', 'value' => elgg_echo('publish'),'js'=>'onclick=selectall()'));
		  $publish = elgg_echo('publish');
		  $//privacy = elgg_echo('access');
		  

		  $presentor_label= elgg_echo('presentation:presentor');
		  
if ($friends = get_entities('user',"",0,"",9999)) { 
		$presentor_input = elgg_view('presentation/authorentry', array('authors'=>$authors));
		  }
		  $video_label = elgg_echo('presentation:videolink');
		  $video_input = elgg_view('input/text', array('internalname' => 'video', 'value' => $video));
		  $slide_label = elgg_echo('presentation:slideembed');
		  $slide_input = "<input type='text' class='input-text' value='$slide' name='slide'/>";
		  $event_name_label = elgg_echo('presentation:eventname');
		  $event_name_input = "<input type='text' class='input-text' value='$event_name' name='event_name'/>";
		  $event_loc_label = elgg_echo('presentation:eventloc');
		  $event_loc_input = "<input type='text' class='input-text' value='$event_loc' name='event_loc'/>";
		
	          
		  $date_label = elgg_echo('presentation:date');
		  $datepicker = elgg_view('input/calendar', array('internalname'=>date,'value'=>$date));	
		  $pub_label = elgg_echo('presentation:publication');
		  $pub_add_btn = "<a class='save-button' href='$CONFIG->wwwroot/mod/presentation/load_publication.php' rel='facebox'>select</a>";
		  $pub_list = "<div class='contentWrapper' id='pub_list'>";
		  if($pubs){
		  foreach($pubs as $pub){
			$title = $pub->title;
			$pub_guid = $pub->guid;
			$pub_list .= "<script type='text/javascript'>add_pub('$title','$pub_guid')</script>";
		  }}
		  $pub_list .= "</div>";
		  /*foreach($shortnames as $shortname => $valuetype){
		  	$params_label[$shortname] = elgg_echo('presentation:'.$shortname);
			$params_input[$shortname] = elgg_view('input/text', array('internalname'=>$shortname,'value'=>$params_value[$shortname]));
		  }*/

		  if(get_plugin_setting('toggleattachment','presentation') != 'Off'){
		  $attachment_title = elgg_echo('presentation:attachment:title');
		  $attachment_name = elgg_view('input/text',array('internalid'=>'attachment_name','internalname'=>'attachment_name','value'=>$attachment_file,'disabled'=>true));
		  $attachment_hidden = elgg_view('input/hidden',array('internalid'=>'attachment_guid','internalname' => 'attachment_guid','value'=>$attachment_guid)); 
		  $attachment = elgg_view('presentation/embed/link',array('internalname'=>'pubattachment'));
		  }
		  $form_body = <<<EOT
	<div class="contentWrapper">
EOT;
                if (isset($vars['entity'])) {
                  $entity_hidden = elgg_view('input/hidden', array('internalname' => 'presentationpost', 'value' => $vars['entity']->getGUID()));
                } else {
                  $entity_hidden = '';
                }

                $form_body .= <<<EOT
		<p>
			<label>$title_label</label><br />
                        $title_textbox
		</p>
		<p>
			<label>Presenters</label> 
			$presentor_input
		</p>
		<p class='longtext_editarea'>
			<label>$abstract_label</label><br />
                        $abstract_textarea
		</p>
		<p>
			<label>$keywords_label</label><br />
                        $keywords_input
		</p>
		<p>
			<label>$event_name_label</label><br/>
			$event_name_input
		</p>
		<p>
			<label>$event_loc_label</label><br/>
			$event_loc_input
		</p>
		<p>
			<label>$date_label</label><br/>
			$datepicker
		</p>
		<p>
			<label>$video_label</label><br />
                        $video_input
		</p>
		<p>
			<label>$slide_label</label><br />
                        $slide_input
		</p>
		<p>
			<label>$pub_label</label> $pub_add_btn<br/>
			$pub_list
		</p>
EOT;
		/*foreach(array_keys($shortnames) as $shortname){
			$form_body .= "<p><label>$params_label[$shortname]</label><br/>$params_input[$shortname]</p>";
		}*/
		$form_body .= <<< EOT
		<p>
                        <label>$attachment_title</label> $attachment
                        $attachment_name
			$attachment_hidden
                </p>
		<p>
			$entity_hidden
			$submit_input
		</p>
	</div><div class="clearfloat"></div>
EOT;

      echo elgg_view('input/form', array('action' => "{$vars['url']}action/$action", 'body' => $form_body, 'internalid' => 'presentationPostForm'));
?>
