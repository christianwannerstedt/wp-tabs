<?php
/**
 * @package Wordpress tabs
 * @version 1.0
 */
/*
Plugin Name: Wordpress tabs
Description: A plugin to easy create tabs to use in posts and pages
Author: Christian Wannerstedt @ Kloon Production AB
Version: 1.0
Author URI: http://www.kloon.se
*/


require_once(dirname(__FILE__) .'/lib/utils.php');
define('WPTABS_TABLE_TABS_CONTAINER', "wp_wptabs_tabs_container");
define('WPTABS_TABLE_TABS', "wp_wptabs_tabs");



// ****************************** Installation / Uninstallation ******************************
register_activation_hook( __FILE__, 'wptabs_install' );
register_deactivation_hook( __FILE__, 'wptabs_uninstall' );

function wptabs_install(){
	global $wpdb;
	$structure = "CREATE TABLE ". WPTABS_TABLE_TABS_CONTAINER ." (
	  `id` int(9) unsigned NOT NULL auto_increment,
	  `title` varchar(255) character set utf8 collate utf8_swedish_ci NOT NULL,
	  `created` datetime NOT NULL,
	  `updated` datetime NOT NULL,
	  PRIMARY KEY  (`id`)
		) DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci";
	$wpdb->query($structure);

	$structure = "CREATE TABLE ". WPTABS_TABLE_TABS ." (
		`id` MEDIUMINT( 8 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`tabContainerId` SMALLINT( 5 ) UNSIGNED NOT NULL ,
		`position` SMALLINT( 5 ) NOT NULL ,
		`title` varchar(255) character set utf8 collate utf8_swedish_ci NOT NULL,
		`body` text character set utf8 collate utf8_swedish_ci NOT NULL,
		`created` DATETIME NOT NULL ,
		`updated` DATETIME NOT NULL ,
		INDEX ( `tabContainerId` )
		) DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci";
	$wpdb->query($structure);
}
function wptabs_uninstall(){
	global $wpdb;
	$wpdb->query("DROP TABLE IF EXISTS `". WPTABS_TABLE_TABS_CONTAINER ."`;");
	$wpdb->query("DROP TABLE IF EXISTS `". WPTABS_TABLE_TABS ."`;");
}




// ****************************** Admin page ******************************
add_action('admin_menu', 'wptabs_menu');
function wptabs_menu() {
	# Add the menu button
	$wptabs_index_page = add_menu_page( 'Manage tabs', 'Tabs', 'manage_options', 'wptabs-index', 'wptabs_index_view', '', '3.0051');
	$wptabs_new_page = add_submenu_page( 'wptabs-index', 'New Tab container', 'New Tab container', 'manage_options', 'wptabs-new', 'wptabs_new_view');
	$wptabs_edit_tab_container_page = add_submenu_page( 'wptabs-edit-tab-container', 'Edit tab container', 'Edit tab container', 'manage_options', 'wptabs-edit-tab-container', 'wptabs_edit_tab_container_view');

	# Add css and js script links
	add_action( "admin_print_scripts-". $wptabs_edit_tab_container_page, 'wptabs_edit_tab_container_head' );
}



// ****************************** Admin index view ******************************
function wptabs_index_view() {
	wptabs_assert_admin_access();

	// Delete tab container
	if (wptabs_is_action("delete") && isset($_POST["sid"]) && is_numeric($_POST["sid"])){
		global $wpdb;
		$tab_container_id = $_POST["sid"];

		// Delete db records
		$wpdb->get_results(sprintf("DELETE FROM  `%s` WHERE tabContainerId='%d';", WPTABS_TABLE_TABS, $tab_container_id));
		$wpdb->get_results(sprintf("DELETE FROM  `%s` WHERE id='%d' LIMIT 1;", WPTABS_TABLE_TABS_CONTAINER, $tab_container_id));
	}


	wp_enqueue_style('wptabs_admin_style', plugins_url('css/admin.css', __FILE__));

	// Fetch tab containers and render view
	global $wpdb, $tab_containers;
	$tab_containers = $wpdb->get_results(sprintf("SELECT * FROM `%s` ORDER BY id;", WPTABS_TABLE_TABS_CONTAINER));
	foreach ($tab_containers as $tab_container){
		$tab_container->tabs = $wpdb->get_results(sprintf("SELECT title FROM `%s` WHERE tabContainerId='%d' ORDER BY position;", WPTABS_TABLE_TABS, $tab_container->id));
	}

	require_once(dirname(__FILE__) .'/admin-index.php');
}


// ****************************** Admin new view ******************************
function wptabs_new_view() {
	wptabs_assert_admin_access();

	if (wptabs_is_action("create") && isset($_POST["tab_container_title"])){

		// Save new tab container
		global $wpdb;
		$wpdb->get_results(sprintf("INSERT INTO `%s` (title,updated,created) VALUES('%s',NOW(),NOW());",
			WPTABS_TABLE_TABS_CONTAINER,
			mysql_real_escape_string($_POST["tab_container_title"])
		));

		wptabs_index_view();
	} else {

		wp_enqueue_script('jquery');
		wp_enqueue_script("jquery-effects-core");
		wp_enqueue_style('wptabs_admin_style', plugins_url('css/admin.css', __FILE__));
		require_once(dirname(__FILE__) .'/admin-new-tab-container.php');
	}
}


// ****************************** Update tab container settings (AJAX) ******************************
add_action('wp_ajax_update_tab_container_settings', 'wptabs_update_tab_container_settings');
function wptabs_update_tab_container_settings() {
	global $wpdb;

	if (isset($_POST['tab_container_id']) && is_numeric($_POST['tab_container_id'])){
		$tab_container_id = intval( $_POST['tab_container_id'] );

		$query = sprintf("UPDATE `%s` SET title='%s', updated=NOW() WHERE id='%d' LIMIT 1;",
			WPTABS_TABLE_TABS_CONTAINER,
            mysql_real_escape_string($_POST["title"]),
            $tab_container_id);

		$wpdb->get_results($query);

    	echo 200;
	} else {
		echo 500;
	}

	die(); // this is required to return a proper result
}


// ****************************** Admin edit tab container view ******************************
function wptabs_edit_tab_container_view() {
	global $wpdb, $tab_container, $tabs;

	// Access and input validation
	wptabs_assert_admin_access();
	$tab_container_id = wptabs_assert_numeric_get("tab_container_id");

	// Check if the tab container exists
	$result = $wpdb->get_results(sprintf("SELECT * FROM `%s` WHERE id=%d LIMIT 1;", WPTABS_TABLE_TABS_CONTAINER, $tab_container_id));
	if (!$result[0]){
		wp_die( __('The specified tab container does not exist.') );
	}

	if (isset($_POST["title"])){
		$wpdb->get_results(sprintf("INSERT INTO `%s` (tabContainerId,title,updated,created) VALUES(%d,'%s',NOW(),NOW());",
			WPTABS_TABLE_TABS,
			$tab_container_id,
			mysql_real_escape_string($_POST["title"])
		));
	}

	// Setup variables and render view
	$tab_container = $result[0];
	$tabs = $wpdb->get_results(sprintf("SELECT * FROM %s WHERE tabContainerId=%d ORDER BY position;", WPTABS_TABLE_TABS, $tab_container->id));

	require_once(dirname(__FILE__) .'/admin-edit-tab-container.php');
}
function wptabs_edit_tab_container_head() {
	// Add jquery
	wp_enqueue_script("jquery");
	wp_enqueue_script("jquery-effects-core");
	wp_enqueue_script("jquery-ui-core");
	wp_enqueue_script("jquery-ui-widget");
	wp_enqueue_script("jquery-ui-mouse");
	wp_enqueue_script("jquery-ui-sortable");

	// Needed for tiny mce
    wp_enqueue_script('tiny_mce');

	// Add own scripts
	wp_enqueue_script('wptabs-edit-tab-container', plugins_url('js/admin_edit_tab_container.js', __FILE__));

	// CSS
	wp_enqueue_style('wptabs_admin_style', plugins_url('css/admin.css', __FILE__));
}



// ****************************** Delete tab (AJAX) ******************************
add_action('wp_ajax_delete_tab', 'wptabs_delete_tab_callback');
function wptabs_delete_tab_callback() {
	wptabs_assert_admin_access();

	if (isset($_POST['tab_id']) && is_numeric($_POST['tab_id'])){
		global $wpdb;
		$tab_id = intval( $_POST['tab_id'] );

		// Delete record
		$wpdb->get_results(sprintf("DELETE FROM  `%s` WHERE id='%d' LIMIT 1;", WPTABS_TABLE_TABS, $tab_id));

    	echo 200;
	} else {
		echo 500;
	}

	die();
}


// ****************************** Delete tab (AJAX) ******************************
add_action('wp_ajax_tinymce_textarea', 'wptabs_tinymce_textarea_callback');
function wptabs_tinymce_textarea_callback() {
	wptabs_assert_admin_access();

	require_once(dirname(__FILE__) .'/tiny-mce-textarea.php');

	die();
}


// ****************************** Update tab (AJAX) ******************************
add_action('wp_ajax_update_tab', 'wptabs_update_tab_callback');
function wptabs_update_tab_callback() {
	global $wpdb;

	if (isset($_POST['tab_id']) && is_numeric($_POST['tab_id'])){

		$wpdb->get_results(sprintf("UPDATE  `%s` SET title='%s', body='%s', updated=NOW() WHERE id='%d' LIMIT 1;",
			WPTABS_TABLE_TABS,
            mysql_real_escape_string($_POST["title"]),
            mysql_real_escape_string($_POST["body"]),
            intval( $_POST['tab_id'] )
        ));
    	echo 200;

	} else {
		echo 500;
	}

	die();
}


// ****************************** Update tab position (AJAX) ******************************
add_action('wp_ajax_update_tab_position', 'wptabs_update_tab_position_callback');
function wptabs_update_tab_position_callback() {
	global $wpdb;

	if (isset($_POST["ids"])){

		$ids = $_POST["ids"];
		$arrIds = explode(",",  $ids);
		$position = 0;
		foreach ($arrIds as $id) {
			$wpdb->get_results(sprintf("UPDATE  `%s` SET position='%d' WHERE id='%d' LIMIT 1;",
				WPTABS_TABLE_TABS,
				$position,
            	$id
            ));
			$position++;
		}

		echo 200;

	} else {
		echo 500;
	}

	die();
}


// ****************************** Get single tab container (AJAX) ******************************
add_action('wp_ajax_get_tab_container', 'get_tab_container_callback');
add_action('wp_ajax_nopriv_get_tab_container', 'get_tab_container_callback');
function get_tab_container_callback() {
    global $wpdb;
	if (isset($_GET["tab_container_id"]) && is_numeric($_GET["tab_container_id"])){
       $id = $_GET["tab_container_id"];
       $tab_container_html = wptabs_get_tab_container_output_for_id( $id );
       echo json_encode(array(
           'status' => 200,
           'id' => $id,
           'html' => $tab_container_html
       ));

	} else {
	       echo json_encode(array('status' => 500));
	}

	die();
};

add_action('the_content', 'wptabs_wptabs_the_content');
function wptabs_wptabs_the_content($content){
	$pattern = '/\[TABS (\d+)\]/';
	preg_match($pattern, $content, $matches);

	if ($matches[0]){
		$tab_container_html = "";

		// Get the tab container
		$id = $matches[1];
		$tab_container_html .= wptabs_get_tab_container_output_for_id($id);
		$content = str_replace($matches[0], $tab_container_html, $content);
	}

	return $content;
}
function wptabs_get_tab_container_output_for_id($id){
	global $wpdb;

	$tab_container_html = "";
	$result = $wpdb->get_results(sprintf("SELECT * FROM `%s` WHERE id=%d LIMIT 1;", WPTABS_TABLE_TABS_CONTAINER, $id));
	if ($result[0]){
		$tab_container = $result[0];

		// Added necessary js and css files
		wp_enqueue_script('jquery');
		wp_enqueue_script("jquery-effects-core");
		wp_enqueue_script('wptabs_tab_container', plugins_url('js/wptabs_tab_container.js', __FILE__));
		wp_enqueue_style('wptabs_style', plugins_url('css/wptabs.css', __FILE__));

		// Get the tabs
		$tabs = $wpdb->get_results(sprintf("SELECT * FROM %s WHERE tabContainerId=%d ORDER BY position;", WPTABS_TABLE_TABS, $tab_container->id));

		// Construct output
		$tab_container_html .= '<div class="tabcontainer">';
		$i = 0;
		foreach ($tabs as $tab){
			$tab_container_html .= '<div class="tab'. (($i == 0) ? ' active' : '') .'" data-tab="'. $i .'">'. $tab->title .'</div>';
			$i++;
		}
		$i = 0;
		foreach ($tabs as $tab){
			$tab_container_html .= '<div class="tab-content'. (($i == 0) ? ' active' : '') .'" data-tab="'. $i .'">'. stripslashes($tab->body) .'</div>';
			$i++;
		}
		$tab_container_html .= '</div>';
	}
	return $tab_container_html;
}




// ****************************** Add button to TinyMCE ******************************
function wptabs_add_tinymce_button() {
   	if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;
	if (get_user_option('rich_editing') == 'true'){
		add_filter('mce_external_plugins', 'wptabs_add_tinymce_plugin');
     	add_filter('mce_buttons', 'wptabs_register_tinymce_button');
   	}
}
add_action('init', 'wptabs_add_tinymce_button');
function wptabs_register_tinymce_button($buttons) {
	wp_enqueue_style('wptabs', plugins_url('/css/admin.css', __FILE__));
   	array_push($buttons, "|", "wptabs");
   	return $buttons;
}
function wptabs_add_tinymce_plugin($plugin_array) {
   $plugin_array['wptabs'] = plugins_url('/js/editor-plugin.js', __FILE__);
   return $plugin_array;
}

function wptabs_list() {
	global $wpdb, $tab_containers;
	$tab_containers = $wpdb->get_results(sprintf("SELECT * FROM `%s` ORDER BY id;", WPTABS_TABLE_TABS_CONTAINER));
	foreach ($tab_containers as $tab_container){
		$tab_container->tabs = $wpdb->get_results(sprintf("SELECT title FROM `%s` WHERE tabContainerId='%d' ORDER BY position;", WPTABS_TABLE_TABS, $tab_container->id));
	}

	require_once(dirname(__FILE__) .'/list-tab-containers.php');
	die();
}
add_action('wp_ajax_wptabs_list', 'wptabs_list');


?>