<?php
        /**
         * @package Elgg
         * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
         * @author Roger Curry, Grid Research Centre [curry@cpsc.ucalgary.ca]
         * @author Tingxi Tan, Grid Research Centre [txtan@cpsc.ucalgary.ca]
         * @link http://grc.ucalgary.ca/
         */

	foreach($_GET as $key=>$value){
		if ($key == 'author') $author = $value;
		if ($key == 'presentation') $presentation = $value;
	}
?>
	<div id='pub_info'>
<?php
	echo elgg_view('input/hidden', array('internalname' => 'author', 'value' => $author));
	echo elgg_view('input/hidden', array('internalname' => 'presentation', 'value' => $presentation));
?>
	</div>
	<script type="text/javascript">
	$('#pub_info').insertBefore('#register-box input[type=submit]');
	</script>
