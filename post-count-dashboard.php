<div id='jrs_post_count_report'>
<h1>JRS Hit Counter</h1>
<?php

	if (isset($_GET['dir'])) { $dir = $_GET['dir']; }
	if (isset($_GET['order'])) { $order = $_GET['order']; }
	
	$post_id_direction = ($order === 'post_id') ? $dir : "ASC";
	$post_title_direction = ($order === 'post_title') ? $dir : "ASC";
	$post_count_direction = ($order === 'post_count') ? $dir : "DESC";
	$date_modified_direction = ($order === 'date_modified') ? $dir : "ASC";
	
	switch ($order) {
		case 'post_id': $order = "pc.post_id $post_id_direction"; break;
		case 'post_title': $order = "p.post_title $post_title_direction"; break;
		case 'post_count': $order = "pc.post_count $post_count_direction"; break;
		case 'date_modified': $order = "pc.date_modified $date_modified_direction"; break;
		default: $order = "pc.post_count DESC";
	}
	
	$sql = "SELECT p.post_title, pc.* FROM " . JRS_PREFIX . "jrs_post_counts pc, " . JRS_PREFIX . "posts p WHERE p.ID = pc.post_id ORDER BY $order;";
	$rs = $wpdb->get_results($sql);

	$post_id_direction = ($post_id_direction === "DESC") ? "ASC" : "DESC";
	$post_title_direction = ($post_title_direction === 'DESC') ? "ASC" : "DESC";
	$post_count_direction = ($post_count_direction === 'DESC') ? "ASC" : "DESC";
	$date_modified_direction = ($date_modified_direction === 'DESC') ? "ASC" : "DESC";

	if (is_array($rs) && !empty($rs)) {
		echo "<table>\n";
		
		$url = site_url() . "/wp-admin/admin.php?page=post-count&order=";
		
		echo "<tr><th><a href='{$url}post_id&dir={$post_id_direction}'>ID</a></th><th><a href='{$url}post_title&dir={$post_title_direction}'>Title</a></th><th><a href='{$url}post_count&dir={$post_count_direction}'>Hits</a></th><th><a href='{$url}date_modified&dir={$date_modified_direction}'>Last Hit</a></th></tr>\n";
		foreach ($rs as $record) {
			echo "<tr>";
			echo "<td class='post_id $post_id_direction'>" . $record->post_id . "</td>";
			echo "<td class='post_title $post_title_direction'>" . $record->post_title . "</td>";
			echo "<td class='post_count $post_count_direction'>" . $record->post_count . "</td>";
			echo "<td class='date_modified $date_modified_direction'>" . $record->date_modified . "</td>";
			echo "</tr>\n";
		}
		echo "</table>";
	}

?>
<a class='to_top' href='#top'>Top</a>
</div>