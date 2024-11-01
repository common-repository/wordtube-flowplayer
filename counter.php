<?php
/**
 * Increase play counter of a video that is played
 */

// Load WordPress
$path  = ''; // It should be end with a trailing slash
if ( !defined('WP_LOAD_PATH') )
{
	$classic_root = dirname(dirname(dirname(dirname(__FILE__)))) . '/' ;
	if (file_exists( $classic_root . 'wp-load.php') )
		define( 'WP_LOAD_PATH', $classic_root);
	else
		if (file_exists( $path . 'wp-load.php') )
			define( 'WP_LOAD_PATH', $path);
		else
			exit("Could not find wp-load.php");
}
require_once( WP_LOAD_PATH . 'wp-load.php');

// Update video counter
global $wpdb;
$wpdb -> wordtube = $wpdb -> prefix . 'wordtube';
$wpdb -> query( 'UPDATE ' . $wpdb -> wordtube . ' SET counter=counter+1 WHERE vid = "' . intval( $_POST[ 'id' ] ) . '";' );

// Echo something (ajax sometimes is buggy if nothing is output)
echo "inc";

?>