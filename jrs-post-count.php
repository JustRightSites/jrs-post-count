<?php
/*
Plugin Name: JRS Post Count
Plugin URI: JustRightSites.com
Description: Counts page loads for all posts and pages
Version: 3.0.0
Author: Pat L.
Author URI: JustRightSites.com
Text Domain: jrs-post-count
*/


global $wpdb;
if (!defined( 'JRS_PLUGIN_URI')) define( 'JRS_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
if (!defined( 'JRS_PLUGIN_PATH')) define( 'JRS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
if (!defined( 'JRS_PREFIX')) define( 'JRS_PREFIX', $wpdb->prefix);

add_action( 'activate_plugin', function ( $plugin, $network_wide ) {
	if ($plugin === 'jrs-post-count/jrs-post-count.php') {
		create_jrs_post_count_tables();
	}
}, 10, 2 );

add_action( 'admin_menu', function() {
    add_menu_page( 'Post Count Reports', 'Post Count Reports', 'manage_options', 'post-count', 'display_post_count_reports_dashboard', 'dashicons-media-text', 150 );
});

add_action( 'admin_enqueue_scripts', function() {
	$page = (isset($_GET['page'])) ? strtolower($_GET['page']) : "";
	if (strpos($page, "post-count") !== false) {
		//wp_enqueue_script( 'jrspc-admin', JRS_PLUGIN_URI . "/admin.js", array ( 'jquery' ), 1.1, true);
		//@wp_localize_script( 'jrspc-admin', 'ajaxurl', admin_url( 'admin-ajax.php' ));
		wp_enqueue_style( 'jrspc-admin', JRS_PLUGIN_URI . "admin_style.css?t=" . time(), null );
	}
});

############# COUNT PAGE OR POST
add_action( 'template_redirect', function() {
	global $post;
	global $wpdb;
	$sql = "INSERT INTO " . JRS_PREFIX . "jrs_post_counts (post_id, post_count) VALUES (" . $post->ID . ",1) ON DUPLICATE KEY UPDATE post_count = post_count + 1;";
	$wpdb->query($sql);
});

function create_jrs_post_count_tables() {
	global $wpdb;

	$sql = "SHOW TABLES LIKE '" . JRS_PREFIX . "jrs_post_counts'";
	$rs = $wpdb->get_results($sql);

	if (empty($rs)) {
		$sql = "CREATE TABLE " . JRS_PREFIX . "jrs_post_counts (
					post_count_id int(11) NOT NULL,
					post_id int(11) NOT NULL,
					post_count int(11) NOT NULL,
					date_modified datetime NOT NULL DEFAULT current_timestamp()
				) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
		$wpdb->query($sql);
		$sql = "ALTER TABLE wp_jrs_post_counts ADD PRIMARY KEY (post_count_id) USING BTREE, ADD UNIQUE KEY Secondary (post_id) USING BTREE;";
		$wpdb->query($sql);
		$sql = "ALTER TABLE wp_jrs_post_counts MODIFY post_count_id int(11) NOT NULL AUTO_INCREMENT;";
		$wpdb->query($sql);
	}

}

function display_post_count_reports_dashboard() {
	global $wpdb;
	require_once(__DIR__ . "/post-count-dashboard.php");
}

function build_report($rs) {
	$return = "";
	return $return;
}


##################### AJAX ####################
function run_report() {

	$response['data'] = "";
	$response['msg'] = "";
	$response['stat'] = "ok";

	if (is_null($rs) || !is_array($rs) || count($rs) === 0) {
		$response['stat'] = "no";
		$response['msg'] = "No data returned for the requested criteria.";
		echo json_encode($response);
		wp_die();
	}

	//$response['data'] = "";
	$response['msg'] = count($rs) . " records found";	
	echo json_encode($response);
	wp_die();
	
}
add_action( 'wp_ajax_run_report', 'run_report');
add_action( 'wp_ajax_nopriv_run_report', 'run_report' );