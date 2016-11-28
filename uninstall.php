<?php
/**
 * Uninstall actions for FES Google CSE
 */

// If uninstall.php is not called by WordPress, die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  die( 'Nope.' );
}

// Remove plugin option from the database.
delete_option( 'fes_gcse_options' );
?>
