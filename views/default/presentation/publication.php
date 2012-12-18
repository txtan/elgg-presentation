<?php
$type = $vars['type'];
$pub_guid = $vars['entity']->guid;
$title = $vars['entity']->title;
echo "<div style='float:right;width:95%'>";
 $info = "<em><b>{$vars['entity']->title}</b></em>";
                $authors = $vars['entity']->authors;
                $authors = explode(',',$authors);
                if (!empty($authors)) {
                        for($index= 0; $index < count($authors) - 1; $index++) {
                                $cauthor = $authors[$index];
                                if(!ctype_digit($cauthor)) echo "$cauthor, ";
                                else{
                                        $user = get_entity((int)$cauthor);
                                        echo $user->name . ', ';

                                }
                        }
                        $cauthor = $authors[$index];
                        if(!ctype_digit($cauthor)) echo "$cauthor";                        else{
                                $user = get_entity((int)$cauthor);                                echo $user->name;
                        }

                }

                echo ". $info.";
		echo "<span style='font-style:italic'>";
                if (!empty($vars['entity']->source)) {
                        echo " " . $vars['entity']->source;
                }
                if (!empty($vars['entity']->volume))
                        echo ' ' . $vars['entity']->volume;
                if (!empty($vars['entity']->issue))
                        echo '('.$vars['entity']->issue . ')';
                if (!empty($vars['entity']->pages))
                        echo ':' . $vars['entity']->pages;                if (!empty($vars['entity']->year)) {
                        echo ', ' . $vars['entity']->year;                }
                echo ".";
		echo "</span>";
echo "</div>";
		if($type != 'info')
		echo "<div style='margin-top:10px;float:left'><a onclick=\"add_pub('$title','$pub_guid')\" class='save-button'>Add</a></div>";

?>
<div class='clearfloat'></div>
