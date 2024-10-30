<?php

function mpm_admin_header() {
	global $plugin_domain;
	// scripts
	?>
	<style type="text/css">
		.custom-input-only td { background: #dfdfdf; }

	</style>

	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$("#start_date").datetimepicker({
				dateFormat: "yy-mm-dd"
			});

			$("#default_format").click(function() {
			$("#add-format-button").attr("disabled", true)
				$(".custom-input-only").hide()
			})
			$("#custom_format").click(function() {
				$("#add-format-button").attr("disabled", false)
				$(".custom-input-only").show()
			})

			$("#mass_page_form").submit(function() {
				var validForm = validateForm()
				return validForm
			})

			$("input[name=post_visibility]").change(function() {
				var visibilityValue = $(this).val()
				$(".sticky-options").toggle(visibilityValue == "public")
			})

			function validateForm() {
				var usingCSV = jQuery("#file-upload").is(":visible")
				var hasFile = (jQuery("#csvfile").val() != '')
				var zeroPages = (jQuery())
				var valid = true

				// block the page number
				var pageNumber = jQuery("input[name=number_pages]").val()
				if (pageNumber > 10) {
					alert("<?php _e("This version of the plugin is limited to 10 posts or less.", $plugin_domain); ?>")
					valid = false
					return valid
				}

				// check for specific relationships
				jQuery(".required").each(function() {
					var value = jQuery(this).val()
					if (value == "") {
						alert("<?php _e("The web form is missing required data", $plugin_domain); ?>")
						valid = false
						return valid
					}
				})

				// post password requires password
				var pageVisibility = jQuery("input[name=post_visibility]").val()
				if (pageVisibility == "password") {
					var postPassword = jQuery("input[name=post_password]").val()
					if (postPassword == "") {
						alert("<?php _e("Post password missing", $plugin_domain); ?>")
						valid = false
						return valid
					}
				}

				return valid
			}
		});

		function addCustomField() {
			var fields = '<tr valign="top" class="meta-fields">\
				<td><textarea name="meta_keys[]" cols="40" rows="2"></textarea></td>\
				<td><textarea name="meta_values[]" cols="40" rows="2"></textarea></td>\
				<td><a href="#" class="remove-meta-field" onclick="return false">Remove</a></td>\
			</tr>'
			jQuery('.meta-fields').last().after(fields)
			jQuery("a.remove-meta-field").on("click", removeTableRow);
		}

		function addTagField() {
			var fields = '<tr valign="top" class="tag-fields">\
				<td><input name="tags_input[]" type="text" size="40"></td>\
				<td><a href="#" class="remove-tag-field" onclick="return false">Remove</a></td>\
			</tr>'
			jQuery('.tag-fields').last().after(fields)
			jQuery("a.remove-tag-field").on("click", removeTableRow)
		}

		function addCategory() {
			var row = jQuery('.category-row').first().clone()
			var removeButton = '<td><a href="#" class="remove-category" onclick="return false">Remove</a></td>'
			row.append(removeButton)
			jQuery('.category-row').last().after(row)
			jQuery("a.remove-category").on("click", removeTableRow)
		}

		function removeTableRow() {
			var row = jQuery(this).parent().parent()
			jQuery(row).remove()
		}

		function mpm_dismiss_plugin_message() {
			// ajax request
			jQuery.ajax(ajaxurl+'?action=dismiss_plugin_message', {
				complete : function() {
					jQuery("#message").fadeOut(500);
				}
			});
		}
	</script>
	<?php
}
//function to display the admin panel
function mpm_page_admin() {
	global $wpdb;
	global $mpm_version;
	global $plugin_domain;

	//load translations
	load_plugin_textdomain($plugin_domain);
?>

<div class="wrap">
	<div id="icon-tools" class="icon32"></div>
	<h2><?php _e('Mass Page Maker ' . $mpm_version, $plugin_domain);?></h2>

<?php	if (isset($_GET['action']) && $_GET['action'] == 'submit' && !empty($_POST)) {
			$result = mpm_process_inputs($_POST);
			$show = $result['show'];
			mpm_show_message($result['message']);
		} else
			$show = true;
	if ($show) { ?>
	<?php
	if (!get_option('mpm_hide_message'))
		mpm_show_message(__('As a result of WordPress plugin repository policies, this version of the plugin has now been limited to 10 posts or less using only the web interface. This public version has received user interface updates to make importing web posts easier, but to continue using CSV import, please purchase <a href="http://www.wesg.ca/wordpress-plugins/mass-page-maker-pro/" target="_blank">Mass Page Maker Pro</a>. If you\'d prefer, you may continue to use the older version of the complete plugin <a href="http://www.wesg.ca/ft7">available here</a>. <a href="#" onclick="return mpm_dismiss_plugin_message()">Dismiss Message</a>', $plugin_domain)); ?>
	<p><?php _e('From this page, you can create as many posts or pages as you like, but keep server capability and memory in mind. Customize every aspect of the pages using the options below. Use [+] to insert the incremental value for the pages. The increment can be used in the title, content, excerpt and menu order field.', $plugin_domain); ?></p>
	<p><?php _e('To keep the web interface straightforward, it no longer supports unique post titles, content or excerpts. To create unique pages, please use a CSV file.', $plugin_domain); ?></p>
	<p><?php _e('Set the post time for the future, past or present. Add a time interval for separating pages. For dates in the future, the pages are added to the publishing queue.', $plugin_domain); ?></p>
	<p><?php _e('You may use placeholders to enter standard information into the page title, page content, page excerpt using the web interface. Use <strong>[blog_title]</strong>, <strong>[blog_description]</strong>, <strong>[blog_url]</strong> or <strong>[page_title]</strong>.', $plugin_domain); ?></p>
	<p><?php _e('Using the core WordPress post creating system, this plugin has a fairly high post limit. The primary limit is your patience.', $plugin_domain);?></p>
	<p><?php _e('Plugin translations are welcome! Use Twitter or my website to get in contact and provide the required files.', $plugin_domain); ?></p>

<?php wp_nonce_field('update-options'); ?>

<form name="mass_page" id="mass_page_form" method="post" action="<?php echo add_query_arg(array('action' => 'submit')); ?>" enctype="multipart/form-data">
<input name="active-section" value="" id="active-section" type="hidden">

		<p><strong>* Required</strong></p>
		<table class="form-table" border="0" id="web-input">
			<tr valign="top">
				<th scope="row"><?php _e('Number of pages', $plugin_domain); ?>*</th>
				<td><input type="text" name="number_pages" size="4" class="required"> <span class="description">Limit 10. To create more, please use <a href="http://www.wesg.ca/wordpress-plugins/mass-page-maker-pro/">Mass Page Maker Pro</a></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Start of page increment', $plugin_domain); ?></td>
				<td><input type="text" name="start_number" size="4"></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Starting page order', $plugin_domain); ?></th>
				<td><input type="text" name="order" size="4"></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Starting date', $plugin_domain); ?></th><td><input type="text" name="start_date" id="start_date" size="24"> <span class="description"><em><?php _e('Use the format YYYY-MM-DD HH:MM:SS', $plugin_domain); ?></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Post Interval', $plugin_domain); ?></th>
				<td><input type="text" name="post_interval" size="8"> <span class="description"><?php _e('Use the format HH:MM:SS', $plugin_domain); ?></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Page Title', $plugin_domain); ?>*</th>
				<td><input type="text" name="page_title" size="81" class="required"></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Page Content', $plugin_domain); ?></th>
				<td><textarea name="page_content" cols="80" rows="8"></textarea></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Page Excerpt', $plugin_domain); ?></th>
				<td><textarea name="page_excerpt" cols="80" rows="8"></textarea></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Page Category', $plugin_domain); ?></th>
				<td><input type="button" class="button-secondary" value="<?php _e("Add Category", $plugin_domain); ?>" onclick="addCategory()"></td>
			</tr>
			<tr valign="top" class="category-row">
				<td>&nbsp;</td>
				<td><?php wp_dropdown_categories(array('show_count' => 1, 'hide_empty' => 0, 'name' => 'categories[]')); ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Page Parent', $plugin_domain); ?></th>
				<td>
					<?php wp_dropdown_pages(array('show_option_no_change' => 'None', 'name' => 'parent_id')); ?>
				</td>
			</tr>
			<?php if ( 0 != count( get_page_templates() ) ) { ?>
			<tr valign="top">
				<td><?php _e('Page Template') ?></td>
				<td>
					<select name="page_template" id="page_template">
						<option value='default'><?php _e('Default Template'); ?></option>
						<?php page_template_dropdown(); ?>
					</select>
				</td>
			</tr>
			<?php } ?>

			<tr valign="top">
				<th scope="row"><?php _e('Page type', $plugin_domain); ?></th>
				<td><?php mpm_post_types(); ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Comments Open', $plugin_domain); ?></th>
				<td><input type="checkbox" name="comments" value="1" checked></td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Pings Open', $plugin_domain); ?></th>
				<td><input type="checkbox" name="pings" value="1" checked></td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Page status', $plugin_domain); ?></th>
				<td>
					<input type="radio" name="page_status" value="publish" checked>&nbsp;<?php _e('Published', $plugin_domain); ?>
					<input type="radio" name="page_status" value="draft">&nbsp;<?php _e('Draft', $plugin_domain); ?>
					<input type="radio" name="page_status" value="pending">&nbsp;<?php _e('Pending Review', $plugin_domain); ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Page visibility', $plugin_domain); ?></th>
				<td>
					<input type="radio" name="post_visibility" value="public" checked>&nbsp;<?php _e('Public', $plugin_domain); ?>
					<input type="radio" name="post_visibility" value="private">&nbsp;<?php _e('Private', $plugin_domain); ?>
					<input type="radio" name="post_visibility" value="password">&nbsp;<?php _e('Password', $plugin_domain); ?> <input type="text" name="post_password">
					<p class="sticky-options"><input type="checkbox" name="sticky_post" value="1"> <?php _e('Sticky post', $plugin_domain); ?></p>
					</td>
			</tr>

			<tr valign="top">
				<td colspan="3"><h4><?php _e('Custom Fields', $plugin_domain); ?></h4></td>
			</tr>
			<tr valign="top">
				<td colspan="3"><?php _e('Add custom fields to your posts, with a single key/value in their respective box. Each post will contain the same data, which is added to the <code>wp_postmeta</code> database table.', $plugin_domain); ?></td>
			</tr>

			<tr valign="top">
				<td colspan="3"><input type="button" id="add-custom-field" onclick="addCustomField()" value="Add Custom Field" class="button-secondary"></td>
			</tr>

			<tr valign="top" id="meta-fields-section">
				<th scope="row" width="33%"><?php _e('Name', $plugin_domain); ?></th>
				<th scope="row"><?php _e('Value', $plugin_domain); ?></th>
			</tr>
			<tr valign="top" class="meta-fields">
				<td><textarea name="meta_keys[]" cols="40" rows="2"></textarea></td>
				<td><textarea name="meta_values[]" cols="40" rows="2"></textarea></td>
			</tr>

			<tr valign="top">
				<td colspan="3" width="33%"><h4><?php _e('Tags', $plugin_domain); ?></h4></td>
			</tr>
			<tr valign="top">
				<td colspan="3"><?php _e('All posts entered here will have the same tags. For separate tags on each post, use a CSV file with <a href="http://www.wesg.ca/wordpress-plugins/mass-page-maker-pro/">Mass Page Maker Pro</a>.', $plugin_domain); ?></td>
			</tr>
			<tr valign="top">
				<td colspan="3"><input type="button" id="add-tag-field" onclick="addTagField()" value="Add Tag" class="button-secondary"></td>
			</tr>
			<tr valign="top" id="tag-fields-section" class="tag-fields">
				<td colspan="3"><input name="tags_input[]" type="text" size="40"></td>
			</tr>


		</table>

<p class="submit"><input type="submit" class="button-primary" name="Submit" value="<?php _e('Add Pages', $plugin_domain) ?>" /></p>
</form>
<?php } ?>

<div id="meta">
	<p>
		<strong>Plugin by wesg</strong> &ndash;
		<a href="http://www.wesg.ca" target="_blank">www.wesg.ca</a> &ndash;
		<a href="http://twitter.com/wesgood" target="_blank">Twitter</a> &ndash;
		<a href="http://www.wesg.ca/wordpress-plugins/mass-page-maker/" target="_blank">Plugin Documentation</a> &ndash;
		<a href="http://www.wesg.ca/wordpress-plugins/" target="_blank">Complete plugin list</a> &ndash;
		<strong><a href="http://www.wesg.ca/wordpress-plugins/mass-page-maker-pro/" target="_blank">Get Mass Page Maker Pro</a></strong>
	</p>
</div>

</div>
<?php
}

function mpm_show_message($message) {
	echo '<div id="message" class="updated fade"><p>' . $message . '</p></div>';
}

function mpm_buffer_message($message) {
	echo $message;
	ob_flush();
	flush();
}

function mpm_post_types() {
	$post_types = get_post_types(array('show_ui' => true, 'public' => true));
	echo '<select name="page_type">'."\n";
	foreach ($post_types as $type) {
		if ($type == 'post')
			$checked = 'checked="checked"';
		else
			$checked = '';
		echo sprintf('<option value="%s" %s> %s</option>', $type, $checked, $type);
	}
	echo '</select>'."\n";
}
?>