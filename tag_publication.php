<?php

	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	$pub_guid = get_input('guid');
	$pub = get_entity($pub_guid);
	$pub_info = elgg_view("presentation/publication",array('entity'=>$pub, 'type'=>'info'));
	echo $pub_info;

?>
