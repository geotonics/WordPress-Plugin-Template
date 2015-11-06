<?php
/*
 * Plugin Name: WordPress Plugin Template
 * Version: 1.0
 * Plugin URI: http://geotonics.com/
 * Description: This is your starter template for your next WordPress plugin.
 * Author: Peter Pitchford
 * Author URI: http://geotonics.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: wordpress-plugin-template
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Peter Pitchford
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Returns the main instance of WordPress_Plugin_Template to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object WordPress_Plugin_Template
 */
function WordPress_Plugin_Template () {
    require_once( 'includes/class-wordpress-plugin-template-init.php' );
    require_once( 'includes/class-wordpress-plugin-template.php' );
	$instance = WordPress_Plugin_Template::instance( __FILE__, '1.0.0' );
	return $instance;
}

WordPress_Plugin_Template();