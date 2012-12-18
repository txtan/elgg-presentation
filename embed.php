<?php
        /**
         * @package Elgg
         * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
         * @author Roger Curry, Grid Research Centre [curry@cpsc.ucalgary.ca]
         * @author Tingxi Tan, Grid Research Centre [txtan@cpsc.ucalgary.ca]
         * @link http://grc.ucalgary.ca/
         */


	if (!is_callable('elgg_view')) exit;
		
	$internalname = get_input('internalname');
		
	if (!isloggedin()) exit;
	
	global $SESSION;
	
	$offset = (int) get_input('offset',0);
	$simpletype = get_input('simpletype');
	$entity_types = array('object' => array('file'));

	if (empty($simpletype)) {
		$count = get_entities('object','file',$SESSION['user']->guid,'',null,null,true);
		$entities = get_entities('object','file',$SESSION['user']->guid,'',6,$offset);
	} else {
		$count = get_entities_from_metadata('simpletype',$simpletype,'object','file',$SESSION['user']->guid,6,$offset,'',0,true);
		$entities = get_entities_from_metadata('simpletype',$simpletype,'object','file',$SESSION['user']->guid,6,$offset,'',0,false);
	}
	
	$types = get_tags(0,10,'simpletype','object','file',$SESSION['user']->guid);
		
	echo elgg_view('presentation/embed/media', array(
		'entities' => $entities,
		'internalname' => $internalname,
		'offset' => $offset,
		'count' => $count,
		'simpletype' => $simpletype,
		'limit' => 6,
		'simpletypes' => $types,
	));

?>
