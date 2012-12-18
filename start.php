<?php
	/**
         * @package Elgg
         * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Roger Curry, Grid Research Centre [curry@cpsc.ucalgary.ca]
         * @author Tingxi Tan, Grid Research Centre [txtan@cpsc.ucalgary.ca]
         * @link http://grc.ucalgary.ca/
         */

	function presentation_init() {
	
		// Load system configuration
		global $CONFIG;
				
		// Set up menu for logged in users
		if (isloggedin()) {
			add_menu(elgg_echo('presentations'), $CONFIG->wwwroot . "pg/presentation/" . $_SESSION['user']->username);
					
			// And for logged out users
		} else {
			add_menu(elgg_echo('presentations'), $CONFIG->wwwroot . "mod/presentation/everyone.php",array());
		}
		/*if(!$CONFIG->presentation){
			$CONFIG->presentation['source'] = 'text';
			$CONFIG->presentation['year'] = 'text';
			$CONFIG->presentation['volume'] = 'text';
			$CONFIG->presentation['issue'] = 'text';
			$CONFIG->presentation['pages'] = 'text';
		}*/
		// Extend system CSS with our own styles, which are defined in the presentation/css view
		extend_view('css','presentation/css');
		extend_view('metatags','presentation/metatags');
		// Extend hover-over menu	
		extend_view('profile/menu/links','presentation/menu');
		extend_view('account/forms/register','presentation/register');
				
		// Register a page handler, so we can have nice URLs
		register_page_handler('presentation','presentation_page_handler');
		// Register a URL handler for presentation posts
		register_entity_url_handler('presentation_url','object','presentation');
		// Register this plugin's object for sending pingbacks
		register_plugin_hook('pingback:object:subtypes', 'object', 'presentation_pingback_subtypes');
		// Add a new widget
		add_widget_type('presentation',elgg_echo("presentation:widget"),elgg_echo("presentation:widget:description"));
		// Register granular notification for this type
		if (is_callable('register_notification_object'))
			register_notification_object('object', 'presentation', elgg_echo('presentation:newpost'));
		// Listen to notification events and supply a more useful message
		register_plugin_hook('notify:entity:message', 'object', 'presentation_notify_message');
		// Listen for new pingbacks
		register_elgg_event_handler('create', 'object', 'presentation_incoming_ping');
		// Register entity type
		register_entity_type('object','presentation');
			
		// Register an annotation handler for comments etc
		register_plugin_hook('entity:annotate', 'object', 'presentation_annotate_comments');
		add_group_tool_option('presentation',elgg_echo('presentation:enablepresentation'),true);
			
	}
	
	//extend the create user function to include additional information
	//for invited authors	
	function presentation_create_user($event, $object_type, $object){
		foreach($_POST as $key=>$value){
			if($key == 'author')
				$author = $value;
			if($key == 'presentation')
				$presentation = $value;
		}
		if($author && $presentation){
			create_metadata($object->guid, 'exauthor_name', $author, "", $object->guid, ACCESS_PUBLIC);	
			create_metadata($object->guid, 'firstpresentation', $presentation, "", $object->guid, ACCESS_PUBLIC);	
			}

		}

	/* Updates author's list when an invited author registers */
        function presentation_login_check($event, $object_type, $object){
                $user = get_loggedin_user();
                if($user->firstpresentation && $user->exauthor_name){
                        $exauthor_name = $user->exauthor_name;
                        $pub = get_entity($user->firstpresentation);
                        add_entity_relationship($user->firstpresentation, 'author', $user->guid);
                        remove_metadata($user->guid, 'firstpresentation');
                        remove_metadata($user->guid, 'exauthor_name');
                        $authors = $pub->authors;
                        $authors = explode(',',$authors);
                        foreach($authors as $key=>$value){
                                if($value == $exauthor_name)
                                        $authors[$key] = $user->guid;
                        }
                        $authors = implode(',',$authors);
                        $pub->authors = $authors;
                }
        }

        function presentation_write_permission_check($hook, $entity_type, $returnvalue, $params){
                if ($params['entity']->getSubtype() == 'presentation') {
                        $authors = $params['entity']->authors;
                        $user = $params['user']->guid;
                        if ($user){
                                if(check_entity_relationship($params['entity']->guid, 'author', $user))
                                return true;
                        }
                }
        }

	/* replaced register plugin when we have a an invited author */
	function presentation_custom_register($hook,$entity_type,$ret,$params){
		global $CONFIG;
		// Get variables
		$presentation = get_input('presentation');
		$author = get_input('author');
		if(!($author && $presentation)) return;
		$username = get_input('username');
		$password = get_input('password');
		$password2 = get_input('password2');
		$email = get_input('email');
		$name = get_input('name');
		$friend_guid = (int) get_input('friend_guid',0);
		$invitecode = get_input('invitecode');
		$admin = get_input('admin');
		if (is_array($admin)) $admin = $admin[0];
		
		
		if (!$CONFIG->disable_registration){
		// For now, just try and register the user
		try { 
			if (((trim($password)!="") && (strcmp($password, $password2)==0)) && ($guid = register_user($username, $password, $name, $email, false, $friend_guid, $invitecode))) {
			$new_user = get_entity($guid);
			if (($guid) && ($admin)){
				admin_gatekeeper(); 
				$new_user->admin = 'yes';
			}
			// Send user validation request on register only
			global $registering_admin;
			if (!$registering_admin)
				request_user_validation($guid);
		
			if (!$new_user->admin)
				$new_user->disable('new_user', false);	
				system_message(sprintf(elgg_echo("registerok"),$CONFIG->sitename));
				forward(); // Forward on success, assume everything else is an error...
			} else {
				register_error(elgg_echo("registerbad"));
			}
		} catch (RegistrationException $r) {
				register_error($r->getMessage());
		}}
		else
			register_error(elgg_echo('registerdisabled'));
		
		$qs = explode('?',$_SERVER['HTTP_REFERER']);
		$qs = $qs[0];
        	$qs .= "?u=" . urlencode($username) . "&e=" . urlencode($email) . "&n=" . urlencode($name) . "&friend_guid=" . $friend_guid . "&invidecode=". $invitecode ."&author=". urlencode($author) . "&presentation=".$presentation;
		forward($qs);
	}	

	
	function presentation_pagesetup() {
		global $CONFIG;
		//add submenu options

		if (get_context() == "presentation") {
			$page_owner = page_owner_entity();
			if ((page_owner() == $_SESSION['guid'] || !page_owner()) && isloggedin()) {
				add_submenu_item(elgg_echo('presentation:your'),$CONFIG->wwwroot."pg/presentation/" . $_SESSION['user']->username);
				if(get_loggedin_user() == $page_owner)	
					add_submenu_item(elgg_echo('presentation:friends'),$CONFIG->wwwroot."pg/presentation/" . $_SESSION['user']->username . "/friends/");
				add_submenu_item(elgg_echo('presentation:everyone'),$CONFIG->wwwroot."pg/presentation/" . $page_owner->username . "/everyone/");
			} else if (page_owner()) {
				add_submenu_item(sprintf(elgg_echo('presentation:user'),$page_owner->name),$CONFIG->wwwroot."pg/presentation/" . $page_owner->username);
				if ($page_owner instanceof ElggUser){
					if(get_loggedin_user() == $page_owner)	
						add_submenu_item(sprintf(elgg_echo('presentation:user:friends'),$page_owner->name),$CONFIG->wwwroot."pg/presentation/" . $page_owner->username . "/friends/");
				add_submenu_item(elgg_echo('presentation:everyone'),$CONFIG->wwwroot."pg/presentation/" . $page_owner->username . "/everyone/");
				}						
				if ($page_owner instanceof ElggGroup){	
					if(is_group_member($page_owner->guid, $_SESSION['guid']))
					add_submenu_item(elgg_echo('presentation:group:tag'),$CONFIG->wwwroot."pg/presentation/" . $page_owner->username . "/everyone/");
				}
			} else {
				//add_submenu_item(elgg_echo('presentation:everyone'),$CONFIG->wwwroot."pg/presentations/" . $page_owner->username . "/everyone/");
			}
					
			if (can_write_to_container(0, page_owner()) && isloggedin()){
				if(!($page_owner instanceof ElggGroup))
					add_submenu_item(elgg_echo('presentation:add'),$CONFIG->wwwroot."pg/presentation/{$page_owner->username}/new/");
			}	
		}
				
		// Group submenu
		$page_owner = page_owner_entity();
		if ($page_owner instanceof ElggGroup && get_context() == 'groups') {
	    		if($page_owner->presentation_enable != "no"){
			    add_submenu_item(sprintf(elgg_echo("presentation:group"),$page_owner->name), $CONFIG->wwwroot . "pg/presentation/" . $page_owner->username );
			}
		}
	}
	
	function presentation_page_handler($page) {
		// The first component of a presentation URL is the username
		if (isset($page[0])) {
			set_input('username',$page[0]);
		}
		if (isset($page[2])) {
			set_input('param2',$page[2]);
		}
		if (isset($page[3])) {
			set_input('param3',$page[3]);
		}
		if (isset($page[1])) {
			switch($page[1]) {
				case "read":	
					set_input('presentationpost',$page[2]);
					include(dirname(__FILE__) . "/read.php"); 
					return true;
					break;
				case "archive":	
					include(dirname(__FILE__) . "/archive.php"); 
					return true;
					break;
				case "friends":
					include(dirname(__FILE__) . "/friends.php"); 
					return true;
					break;
				case "everyone":
					include(dirname(__FILE__) . "/everyone.php");
					return true;
					break;
				case "new":
					include(dirname(__FILE__) . "/add.php"); 
					return true;
					break;
				case "embed":
			 		include(dirname(__FILE__) . "/embed.php");
					return true;
					break;
				case "upload":
			 		include(dirname(__FILE__) . "/upload.php"); 
					return true;
					break;
			}
		// If the URL is just 'presentation/username', or just 'presentation/', load the standard presentation index
		} else {
			@include(dirname(__FILE__) . "/index.php");
			return true;
		}
			
		return false;
			
	}
		
	function presentation_annotate_comments($hook, $entity_type, $returnvalue, $params){
		$entity = $params['entity'];
		$full = $params['full'];
		if (($entity instanceof ElggEntity) &&	($entity->getSubtype() == 'presentation') && ($entity->comments_on!='Off') && ($full)){
			// Display comments
			return elgg_view_comments($entity);
		}
			
	}

	function presentation_notify_message($hook, $entity_type, $returnvalue, $params){
		$entity = $params['entity'];
		$to_entity = $params['to_entity'];
		$method = $params['method'];
		if (($entity instanceof ElggEntity) && ($entity->getSubtype() == 'presentation')){
			$descr = $entity->description;
			$title = $entity->title;
			if ($method == 'sms') {
				$owner = $entity->getOwnerEntity();
				return $owner->username . ' via presentation: ' . $title;
			}
			if ($method == 'email') {
				$owner = $entity->getOwnerEntity();
				return $owner->username . ' via presentation: ' . $title . "\n\n" . $descr . "\n\n" . $entity->getURL();
			}
		}
		return null;
	}

	function presentation_url($presentationpost) {
		global $CONFIG;
		$title = $presentationpost->title;
		$title = friendly_title($title);
		return $CONFIG->url . "pg/presentation/" . $presentationpost->getOwnerEntity()->username . "/read/" . $presentationpost->getGUID() . "/" . $title;
	}
		
	function presentation_pingback_subtypes($hook, $entity_type, $returnvalue, $params){
		$returnvalue[] = 'presentation';			
		return $returnvalue;
	}
		
	// write permission plugin hooks
	register_plugin_hook('permissions_check', 'object', 'presentation_write_permission_check');
	register_elgg_event_handler('login','user','presentation_login_check');	
	// Make sure the presentation initialisation function is called on initialisation
	register_elgg_event_handler('init','system','presentation_init');
	register_elgg_event_handler('pagesetup','system','presentation_pagesetup');
	register_elgg_event_handler('create','user','presentation_create_user');
	register_plugin_hook('action','register','presentation_custom_register');
		
	// Register actions
	global $CONFIG;
	register_action("presentation/add",false,$CONFIG->pluginspath . "presentation/actions/add.php");
	register_action("presentation/tag",false,$CONFIG->pluginspath . "presentation/actions/tag.php");
	register_action("presentation/untag",false,$CONFIG->pluginspath . "presentation/actions/untag.php");
	register_action("presentation/edit",false,$CONFIG->pluginspath . "presentation/actions/edit.php");
	register_action("presentation/delete",false,$CONFIG->pluginspath . "presentation/actions/delete.php");
	register_action("presentation/invite",false,$CONFIG->pluginspath . "presentation/actions/invite.php");
		
?>
