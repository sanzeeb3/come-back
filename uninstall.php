<?php
/**
 * Uninstall Come Back!
 *
 * @since 1.3.3
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Delete all options set by Come Back!.
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'come_back\_%';" );

// Deltee email message option.
delete_option( 'come-back-email-editor' );

// Cancel all AS actions for Come Back!
if ( class_exists( 'ActionScheduler_DBStore' ) ) {
	\ActionScheduler_DBStore::instance()->cancel_actions_by_group( 'come_back' );
}