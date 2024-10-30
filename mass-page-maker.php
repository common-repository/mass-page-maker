<?php
/*
Plugin Name: Mass Page Maker
Plugin URI: http://www.wesg.ca/2008/06/wordpress-plugin-mass-page-maker/
Description: Easily create posts based on web input.
Version: 2.8
Author: Wes Goodhoofd
Author URI: http://www.wesg.ca/

This program is free software; you can redistribute it and/or
modify it under the terms of version 2 of the GNU General Public
License as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details, available at
http://www.gnu.org/copyleft/gpl.html
or by writing to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// definitions
require_once("panels.php");

//plugin version
global $mpm_version;
global $plugin_domain;

$mpm_version = '2.8';
$plugin_domain = 'mass-page-maker';

//add the action so the blog knows it's there
add_action('admin_menu', 'add_mpm_menu');
add_action('admin_init', 'mpm_initialize');
add_action('admin_enqueue_scripts', 'mpm_enqueue_scripts');

// unique to this version
add_action('wp_ajax_dismiss_plugin_message', 'mpm_dismiss_message');

function mpm_initialize() {
	add_action('admin_head', 'mpm_admin_header');
	ini_set("auto_detect_line_endings", true);
	ob_start();
}

//plugin function
function add_mpm_menu() {
	global $mpm_version;
	add_management_page('Mass Page Maker ' . $mpm_version, 'Mass Page Maker', 'edit_posts', __FILE__, 'mpm_page_admin');
}

function mpm_enqueue_scripts() {
	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/ui-lightness/jquery-ui.css');
	wp_enqueue_script('jQuery');
	wp_enqueue_script("jquery-ui-core", array('jQuery'));
	wp_enqueue_script('jquery-ui-datepicker', array('jQuery', 'jquery-ui-core'));
	wp_enqueue_script("jquery-ui-slider", array('jQuery', 'jquery-ui-core'));
	wp_enqueue_script("jquery-datetimepicker", plugins_url('/jquery-ui-timepicker-addon.js', __FILE__), array('jquery', 'jquery-ui-datepicker'));
	wp_enqueue_script("jquery-validator", "http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js");
}

function mpm_dismiss_message() {
	add_option('mpm_hide_message', true);
}

//function to actually add to the database

function mpm_load_web_pages($params) {
	global $current_user;
	global $plugin_domain;
	wp_get_current_user();

	$input_params = array(
		'title' => $params['page_title'],
		'pages' => max(1, $params['number_pages']),
		'comments' => (isset($params['comments']) && $params['comments']) ? "open" : "closed",
		'pings' => (isset($params['pings']) && $params['pings']) ? "open" : "closed",
		'post_type' => $params['page_type'],
		'content' => $params['page_content'],
		'excerpt' => $params['page_excerpt'],
		'status' => $params['page_status'],
		'category' => $params['categories'],
		'parent' => ($params['parent_id'] == -1) ? 0 : $params['parent_id'],
		'template' => $params['page_template'],
		'meta_keys' => $params['meta_keys'],
		'meta_values' => $params['meta_values'],
		'post_visibility' => $params['post_visibility'],
		'password' => $params['post_password'],
		'order' => $params['order'],
		'tags_input' => $params['tags_input'],
		'date' => $params['start_date'],
		'sticky' => false,
	);

	// validate some things
	if ($input_params['pages'] > 10) {
		return array('error' => true, 'show' => true, 'message' => __("This plugin version is limited to 10 posts or less.", $plugin_domain));
	}

	$input_params['start_page'] = max(1, $params['start_number']);
	$input_params['end_page'] = $input_params['start_page'] + ($input_params['pages']-1);

	// get the post interval
	$interval_components = explode(":", $params['post_interval']);
	if (count($interval_components) == 3) {
		// hour, minute, seconds
		$input_params['post_interval'] = 3600 * $interval_components[0] + 60 * $interval_components[1] + $interval_components[2];
	} else if (count($interval_components) == 2) {
		// hour, minute
		$input_params['post_interval'] = 3600 * $interval_components[0] + 60 * $interval_components[1];
	} else if (count($interval_components) == 1) {
		// hour
		$input_params['post_interval'] = 3600 * $interval_components[0];
	} else {
		// default, 1 second
		$input_params['post_interval'] = max(1, $params['post_interval']);
	}

	//seed the start time for the posts
	if (empty($input_params['date']))
		$input_params['date'] = current_time('mysql');

	// meta values
	$meta = array();
	foreach ($input_params['meta_keys'] as $index=>$key) {
		$meta[$key] = $input_params['meta_values'][$index];
	}
	$input_params['meta'] = $meta;

	// replacements
	$replacements = array(
		'blog_url' => get_bloginfo('url'),
		'blog_description' => get_bloginfo('description'),
		'blog_title' => get_bloginfo('title'),
	);

	$post_array = array();

	for ($page_number = $input_params['start_page']; $page_number <= $input_params['end_page']; $page_number++) {
		$post_data = array();
		$post_data['post_author'] = $current_user->ID;

		// do some shortcode replacements
		$content = $input_params['content'];
		$excerpt = $input_params['excerpt'];
		$title = $input_params['title'];
		if (has_action('do_shortcode')) {
			$content = do_shortcode($content);
			$excerpt = do_shortcode($excerpt);
			$title = do_shortcode($title);
		}

		foreach ($replacements as $search=>$replace) {
			$search_text = sprintf("[%s]", $search);
			$content = str_replace($search_text, $replace, $content);
			$excerpt = str_replace($search_text, $replace, $excerpt);
			$title = str_replace($search_text, $replace, $title);
		}
		$title = str_replace("[+]", $page_number, $title);
		$content = str_replace("[+]", $page_number, $content);
		$excerpt = str_replace("[+]", $page_number, $excerpt);

		// add to the wordpress array
		$post_data['post_title'] = $title;
		$post_data['post_content'] = $content;
		$post_data['post_excerpt'] = $excerpt;

		$post_data['menu_order'] = $input_params['order'];
		$input_params['order'] += 1;

		$post_data['ping_status'] = $input_params['pings'];
		$post_data['comment_status'] = $input_params['comments'];
		$post_data['sticky'] = $input_params['sticky'];

		$post_data['post_category'] = $input_params['category'];

		$post_data['post_date'] = $input_params['date'];
		$post_date['post_date_gmt'] = get_gmt_from_date($post_data['post_date']);
		$post_data['date'] = date('Y-m-d G:i:s', strtotime($input_params['date'] + $input_params['post_interval']));

		if (empty($input_params['slug']))
			$post_data['post_name'] = sanitize_title($post_data['post_title'], '(none)');
		else
			$post_data['post_name'] = $input_params['slug'];

		if ($input_params['post_type'] != 'post')
			$post_data['post_parent'] = $input_params['parent'];

		if ($input_params['post_visibility'] == 'password')
			$post_data['post_password'] = $input_params['pasword'];
		else if ($input_params['post_visibility'] == 'sticky') {
			$post_data['post_visibility'] = 'public';
			$post_data['sticky'] = true;
		}

		$post_data['post_status'] = $input_params['status'];

		$post_data['post_type'] = $input_params['post_type'];

		$tags = array_map('trim', $input_params['tags_input']);
		$post_data['tags_input'] = $tags;

		$post_data['template'] = $input_params['template'];

		$post_data['meta'] = $input_params['meta'];

		// add to main array
		$post_array[] = $post_data;
	}

	return $post_array;
}

function mpm_process_inputs() {
	global $wpdb;
	global $current_user;
	global $mpm_version;
	global $plugin_domain;
	$debug = false;

	// start the clock
	$start_time = microtime(true);

	$posts = mpm_load_web_pages($_POST);

	// check for error
	if (array_key_exists('error', $posts)) {
		return $posts;
	} else {
		// process pages
		$result = mpm_process_pages($posts);

	   	if (array_key_exists('success', $result) && !array_key_exists('error', $result))
		   	$import_result = array('show' => false, 'message' => sprintf(__('Successfully processed %d posts in %%.3f seconds', $plugin_domain), $result['success']));
		else if (!array_key_exists('success', $result) && array_key_exists('error', $result))
			$import_result = array('show' => true, 'message' => sprintf(__('Error adding posts', $plugin_domain)));
		else
			$import_result = array('show' => true, 'message' => sprintf(__('There were %d %s processed correctly, but %d %s not.', $plugin_domain), $result['success'], ($result['success'] == 1) ? 'post' : 'posts', $result['error'], ($result['error'] == 1) ? 'was' : 'were'));
	}

	// stop the clock
	$end_time = microtime(true);
	$total_time = ($end_time - $start_time);

	// add the time to the message
	$import_result['message'] = sprintf($import_result['message'], $total_time);
	return $import_result;
}

function mpm_process_pages($data) {
	global $plugin_domain;

	// take the array of pages, and add to the database

	$page_number = 1;
	$success = 0;
	$errors = 0;
	foreach ($data as $page) {
		// check for error
		if (array_key_exists('error', $page)) {
			mpm_buffer_message(sprintf('<p>%d. %s <span class="import-error">%s</span></p>', $page_number, $page['post_title'], $page['message']));
			$errors++;
		} else {
			// preprocess some values
			if (array_key_exists('post_parent', $page) && $page['post_type'] == 'page') {
				if (!preg_match('/^\d{1,}$/', $page['post_parent'])) {
					$existing_page = get_page_by_title($page['post_parent'], ARRAY_A);
					if ($existing_page)
						$page['post_parent'] = $existing_page['ID'];
				}
			}

			$insert_result = wp_insert_post($page, true);
			if (!is_object($insert_result) && $insert_result) {
				mpm_buffer_message(sprintf('<p>%d. %s</p>', $page_number, $page['post_title']));
				$success++;

				// add custom fields
				if (array_key_exists('meta', $page)) {
					$custom_fields = $page['meta'];
					foreach ($custom_fields as $custom_key => $custom_value) {
						add_post_meta($insert_result, $custom_key, $custom_value);
					}
				}

				// add template
				if ($page['post_type'] != 'post' && array_key_exists('post_template', $page))
					add_post_meta($insert_result, '_wp_page_template', $page['post_template']);

				// check for sticky
				if (isset($page['sticky']) && $page['sticky']) {
					$current_stickies = get_option('sticky_posts');
					$current_stickies[] = $insert_result;
					update_option('sticky_posts', $current_stickies);
				}
			}
			else {
				mpm_buffer_message(sprintf('<p>%d. %s <span class="import-error">ERROR</span></p>', $page_number, $page['post_title']));
				$errors++;
			}
		}

		$page_number++;
	}

	mpm_buffer_message(sprintf('<h4>%s</h4>', __('Done', $plugin_domain)));
	mpm_buffer_message(sprintf('<p><a href="%s">%s</a></p>', add_query_arg(array('action' => null)), __('Return to form', $plugin_domain)));
	$return = array();
	if ($success > 0)
		$return['success'] = $success;
	if ($errors > 0)
		$return['error'] = $errors;
	return $return;
}