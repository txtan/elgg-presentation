<?php
        /**
         * @package Elgg
         * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
         * @author Roger Curry, Grid Research Centre [curry@cpsc.ucalgary.ca]
         * @author Tingxi Tan, Grid Research Centre [txtan@cpsc.ucalgary.ca]
         * @link http://grc.ucalgary.ca/
         */


if (isset($vars['entity'])) {
	if ($vars['entity']->comments_on == 'Off') {
		$comments_on = false;
	} else {
		$comments_on = true;
	}
			
	if (get_context() == "references" && $vars['entity'] instanceof ElggObject) {
		echo '<p>' . elgg_view("presentation/references",$vars) . '</p>';
	} else {
		if (get_context() == "search" && $vars['entity'] instanceof ElggObject) {
			echo '<div class="search_listing"><div class="search_listing_icon">';
			echo '<img border="0" src="' . $CONFIG->wwwroot . 'mod/presentation/_graphics/paper.png"/></div><div class="search_listing_info" style="min-height:52px">';
			echo 'Publication: ' . elgg_view('presentation/references',$vars) . '</div></div>';
		} else {
			if ($vars['entity'] instanceof ElggObject) {
				$url = $vars['entity']->getURL();
				$owner = $vars['entity']->getOwnerEntity();
				$canedit = $vars['entity']->canEdit();
			} else {
				$url = 'javascript:history.go(-1);';
				$owner = $vars['user'];
				$canedit = false;
			}
				
?>

	<div class="contentWrapper singleview">
	
	<div class="presentation_post">
	<h3><a href="<?php echo $url; ?>"><?php echo $vars['entity']->title; ?></a></h3>
			<div class="clearfloat"></div>
				
				<?php
					echo '<b>' . elgg_echo('presentation:presentor') . ':</b>';
					echo '<div class="contentWrapper info_div">';
					$authors = $vars['entity']->authors;
					$authors = explode(',',$authors);
					if(!(is_array($authors))) $authors = array($authors);
					foreach($authors as $author){
						if(!ctype_digit($author)) echo elgg_view('presentation/authorinvite',array('exauthor'=>$author,'presentation_guid'=>$vars['entity']->getGUID(),'canedit'=>$canedit));
						else{
							$user = get_entity($author);
							echo elgg_view_entity($user);
						}
					}
					echo '</div>';
					$tags = elgg_view('output/tags', array('tags' => $vars['entity']->tags));
					if (!empty($tags)) {
						echo '<b>' .elgg_echo('presentation:keywords') . ":</b> " . $tags . "<br/><br/>";
					}
				?>
			<?php
				set_context('references');
				$pubs = list_entities_from_relationship('presentation_of',$vars['entity']->guid, false, 'object','publication',0,10,false,false,true);
				if($pubs){
					echo '<b>'. elgg_echo('presentation:publication').":</b>"."<div class='contentWrapper info_div'>$pubs</div>";
				}
				set_context('presentation');
				if(!empty($vars['entity']->event_name)){
					$event_name = $vars['entity']->event_name;
					echo '<b>' . elgg_echo('presentation:eventname').':</b> '.$event_name."<br/><br/>";
				}
				if(!empty($vars['entity']->event_loc)){
					$event_loc = $vars['entity']->event_loc;
					echo '<b>' . elgg_echo('presentation:eventloc').':</b> '.$event_loc."<br/><br/>";
				}
				if(!empty($vars['entity']->date)){
					$date = $vars['entity']->date;
					echo '<b>' .elgg_echo('presentation:date').':</b> '.$date."<br/><br/>";
				}
				 
				if(!empty($vars['entity']->attachment)){
					$attachment = get_entity($vars['entity']->attachment);
					if($attachment){
					$attachment_url = $attachment->getUrl();
					echo '<b>' .elgg_echo('presentation:attachment:title'). ":</b> <a href='$attachment_url'> $attachment->title</a><br/><br/>";
					}
				}
			?>

			<b>Abstract:</b>
			<div class="presentation_post_body">

			<!-- display the actual presentation post -->
				<?php
						echo "<div class='contentWrapper info_div'>";	
							echo elgg_view('output/longtext',array('value' => $vars['entity']->description));
				echo "</div>";	
				echo '<b>'.elgg_echo('presentation:media').':</b><br/>';
				//videos and slides	
				if (!empty($vars['entity']->video)) {
					$videolink = $vars['entity']->video;	
					echo videoembed_create_embed_object($videolink, $vars['entity']->guid, 400);
				}
				if(!empty($vars['entity']->slide)){
					$slideembed = $vars['entity']->slide;
					echo html_entity_decode($slideembed);
				}
				?>
			</div><div class="clearfloat"></div>			
			<!-- display edit options if it is the presentation post owner -->
			<p class="options">
			<?php
	
				if ($canedit) {
			?>
					<a href="<?php echo $vars['url']; ?>mod/presentation/edit.php?presentationpost=<?php echo $vars['entity']->getGUID(); ?>"><?php echo elgg_echo("edit"); ?></a>  &nbsp; 
					<?php
					
						echo elgg_view("output/confirmlink", array(
																	'href' => $vars['url'] . "action/presentation/delete?presentationpost=" . $vars['entity']->getGUID(),
																	'text' => elgg_echo('delete'),
																	'confirm' => elgg_echo('deleteconfirm'),
																));
	
					// Allow the menu to be extended
					echo elgg_view("editmenu",array('entity' => $vars['entity']));
			?>
		<?php
			}
		?>
		</p>
	</div>
	</div>

<?php
		}
	}
}

?>
