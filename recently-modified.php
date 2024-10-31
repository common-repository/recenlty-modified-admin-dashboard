<?php
/**
 * @package recently-modified
 * @version 0.0.1
 */
/*
Plugin Name: Recently Modified
Plugin URI: http://serversideguy.com/wordpress/plugins/recently-modified
Description: Plugin to list recently edited pages in the wordpress dashboard
Author: Tim Barsness / Barsness Solutions
Version: 0.0.5
Author URI: http://barsnesssolutions.com/
*/
/*  Copyright 2012  Timothy Barsness  (email : tbarsness@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function recently_modified_dashboard_widget_function() {
	global $wpdb;
	
	$querystr = "
		SELECT $wpdb->posts.* 
		FROM $wpdb->posts
		WHERE $wpdb->posts.post_type = 'page'
		ORDER BY $wpdb->posts.post_modified DESC
		LIMIT 0,25
	";

	echo "<ul>\n";
	foreach($wpdb->get_results($querystr, OBJECT) as $post) {
		echo '<li>'.edit_post_link( str_replace( site_url(), '', get_permalink( $post->ID ) ), '', '',  $post->ID )."</a></li>\n";
	}
	echo "</ul>\n";
} 

// Create the function use in the action hook

function recently_modified_add_dashboard_widgets() {
	wp_add_dashboard_widget('recently_modified_dashboard_widget', 'Recently Modified Pages', 'recently_modified_dashboard_widget_function');	
	
	// Globalize the metaboxes array, this holds all the widgets for wp-admin
	global $wp_meta_boxes;
	
	// Get the regular dashboard widgets array 
	// (which has our new widget already but at the end)
	$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
	
	// Backup and delete our new dashbaord widget from the end of the array
	$recently_modified_widget_backup = array('recently_modified_dashboard_widget' => $normal_dashboard['recently_modified_dashboard_widget']);
	unset($normal_dashboard['recently_modified_dashboard_widget']);

	// Merge the two arrays together so our widget is at the beginning
	$sorted_dashboard = array_merge($recently_modified_widget_backup, $normal_dashboard);

	// Save the sorted array back into the original metaboxes 
	$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
}
add_action('wp_dashboard_setup', 'recently_modified_add_dashboard_widgets' );
