<?php
        /**
         * @package Elgg
         * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
         * @author Roger Curry, Grid Research Centre [curry@cpsc.ucalgary.ca]
         * @author Tingxi Tan, Grid Research Centre [txtan@cpsc.ucalgary.ca]
         * @link http://grc.ucalgary.ca/
         */
?>
<h1 class="mediaModalTitle">Attach File</h1>
<?php
	echo elgg_view('presentation/embed/tabs',array('tab' => 'media', 'internalname' => $vars['internalname']));
?>
	<div id='mediaEmbed'>
<?php
	echo elgg_view('embed/pagination',array(
												'offset' => $vars['offset'],
												'baseurl' => $vars['url'] . 'pg/presentation/embed/media?internalname=' . $vars['internalname'] . "&amp;simpletype=" . $vars['simpletype'],
												'limit' => $vars['limit'],
												'count' => $vars['count']
											));
	
	echo elgg_view('embed/simpletype',array(
												'internalname' => $vars['internalname'],
												'simpletypes' => $vars['simpletypes'],
												'simpletype' => $vars['simpletype'],
											));
											
	$context = get_context();
	$entities = $vars['entities'];
	if (is_array($entities) && !empty($entities)) {
		
		echo "<p class=\"embedInstructions\">" . elgg_echo('presentation:attachment:instruction') . "</p>";
		
		foreach($entities as $entity) {
			if ($entity instanceof ElggEntity) {

				$mime = $entity->mimetype; 
				
				$enttype = $entity->getType();
				$entsubtype = $entity->getSubtype();
				
				if (elgg_view_exists($enttype . '/' . $entsubtype . '/embed')) {
					$content = elgg_view($enttype . '/' . $entsubtype . '/embed', array('entity' => $entity, 'full' => true));
				} else {
					$content = elgg_view($enttype . '/default/embed', array('entity' => $entity, 'full' => true));
				}
				
				$content = str_replace("\n","", $content);
				$content = str_replace("\r","", $content);
				//$content = htmlentities($content,null,'utf-8');
				$content = htmlentities($content, ENT_COMPAT, "UTF-8");
				$fileguid = $entity->guid;
				$filename = $entity->title;
				$link = "javascript:addattachment('{$fileguid}','{$filename}');";
				//$link = "javascript:elggUpdateContent('{$content}','{$vars['internalname']}');";
				if ($entity instanceof ElggObject) { $title = $entity->title; $mime = $entity->mimetype; } else { $title = $entity->name; $mime = ''; }
				
				set_context('search');
				
				if (elgg_view_exists("{$enttype}/{$entsubtype}/embedlist")) {
					$entview = elgg_view("{$enttype}/{$entsubtype}/embedlist",array('entity' => $entity));
				} else {
					$entview = elgg_view_entity($entity);
				}
				$entview = str_replace($entity->getURL(),$link,$entview);
				echo $entview;
				
				set_context($context);				
			
			}
		}
	}

?>
</div>
