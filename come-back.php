<?php
/**
 * Plugin Name: Come Back!
 * Description: Sends email notification to inactive customers.
 * Version: 1.3.3
 * Author: Sanjeev Aryal
 * Author URI: http://www.sanjeebaryal.com.np
 * Text Domain: come-back
 * Domain Path: /languages/
 *
 * @package    Come Back
 * @author     Sanjeev Aryal
 * @link       https://github.com/sanzeeb3/come-back
 * @since      1.0.0
 * @license    GPL-3.0+
 */

defined( 'ABSPATH' ) || exit;	// Exit if accessed directly.

define( 'COME_BACK', __FILE__ );

/**
 * Plugin version.
 *
 * @var string
 */
const COME_BACK_VERSION = '1.3.3';

require_once 'vendor/autoload.php';
require_once( 'wp-content/plugins/action-scheduler/action-scheduler.php' );

/**
 * Return the main instance of Plugin Class.
 *
 * @since  1.0.0
 *
 * @return Plugin.
 */
function come_back() {
	return \ComeBack\Plugin::get_instance();
}

come_back();

/**
 * Add current time on plugin activation.
 *
 * @since  1.0.0
 * 
 * @return void.
 */
function come_back_activate() {
	update_option( 'come_back_activation_date', time() );
}
register_activation_hook( __FILE__, 'come_back_activate' );
