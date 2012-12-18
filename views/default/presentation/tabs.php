<?php

$limit = $vars['limit'];
$offset = $vars['offset'];
$userguid = $vars['user_guid'];
$selected = $vars['selected'];
$pubs = elgg_get_entities(array('types'=>'object','subtypes'=>'publication','limit='=>$limit,'owner_guids'=>$userguid,'offset'=>$offset));
$pubscount = elgg_get_entities(array('count'=>true,'types'=>'object','subtypes'=>'publication','limit'=>$limit,'owner_guids'=>$userguid,'offset'=>$offset));
$pagination = elgg_view('embed/pagination',array('offset'=>$offset,'baseurl'=>$vars['url'].'mod/presentation/load.php?user_guid='.$userguid.'&selected=0','limit'=>$limit,'count'=>$pubscount));

set_context('references');
foreach($pubs as $pub){
	$pubslist.= elgg_view('presentation/publication',array('entity'=>$pub)) . "<br/><br/>";
}

?>

<h2>Select Publication</h2>
<div style='margin-top:10px'>
	<?php echo $pagination;?>
	<?php echo $pubslist;?>
</div>

