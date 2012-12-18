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
		
	$presentation = get_entity($guid);
	if ($presentation->getSubtype() == "presentation" && $presentation->canEdit()) {
		$owner = get_entity($presentation->getOwner());
		$rowsaffected = $presentation->delete();
		if ($rowsaffected > 0) {
			system_message(elgg_echo("presentation:deleted"));
		} else {
			register_error(elgg_echo("presentation:notdeleted"));
		}
		
		forward("mod/presentation/?username=" . $owner->username);
	
	}
		
?>
