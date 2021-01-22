<?php

/*
Plugin Name: App Creator
Plugin URI:
Description: Connect easily your app with your Wordpress
Version: 1.1.0
Author: App Creator
*/

/**
 * @package     app-creator
 * @author      App Creator Team
 * @copyright   2013-2015 App Creator
 * @version     1.1.0
 */

define('APP_CREATOR_BASE_PATH', dirname(__FILE__));
@include_once APP_CREATOR_BASE_PATH."/models/default.php";
@include_once APP_CREATOR_BASE_PATH."/models/connector.php";

function app_creator_init() {

    if (phpversion() < 5) {
        add_action('admin_notices', 'app_creator_php_version_warning');
        return;
    }

    new App_Creator_Connector();
}

function app_creator_php_version_warning() {
    echo "<div id=\"app-creator-warning\" class=\"updated fade\"><p>Sorry, App Creator requires PHP version 5.0 or greater.</p></div>";
}

function app_creator_activation() {
    // Add the rewrite rule on activation
    global $wp_rewrite;
    add_filter('rewrite_rules_array', 'app_creator_rewrites');
    $wp_rewrite->flush_rules();
}

function app_creator_deactivation() {
    // Remove the rewrite rule on deactivation
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}

add_action('init', 'app_creator_init');
register_activation_hook(APP_CREATOR_BASE_PATH."/app-creator.php", 'app_creator_activation');
register_deactivation_hook(APP_CREATOR_BASE_PATH."/app-creator.php", 'app_creator_deactivation');
?>
