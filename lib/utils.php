<?php
function wptabs_assert_admin_access(){
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
}

function wptabs_assert_numeric_get($key){
	if (!isset($_GET[$key]) || !is_numeric($_GET[$key])){
		wp_die( __('Incorrect indata.') );
	}
	return $_GET[$key];
}

function wptabs_assert_numeric_post($key){
	if (!isset($_POST[$key]) || !is_numeric($_POST[$key])){
		wp_die( __('Incorrect indata.') );
	}
	return $_POST[$key];
}

function wptabs_is_action($action){
	return (isset($_POST['admin-action']) && $_POST['admin-action'] == $action);
}
