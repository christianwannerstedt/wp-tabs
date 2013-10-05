<?php
global $screen_layout_columns, $tab_container, $tabs;

add_meta_box("wptabs_content_tabs", "Edit tabs", "wptabs_edit_meta_box", "wptabs_edit", "normal", "core");
add_meta_box("wptabs_content_upload", "Add new tabs", "wptabs_upload_meta_box", "wptabs_upload", "normal", "core");
add_meta_box("wptabs_content_settings", "Settings", "wptabs_settings_meta_box", "wptabs_settings", "normal", "core");


// ****************************** Structure ******************************
?>
<script type="text/javascript">
var tab_container_id = <?php echo $tab_container->id; ?>,
	tabs_json = <?php echo json_encode($tabs); ?>;
</script>

<div class="wrap">
	<div class="metabox-holder">
		<?php do_meta_boxes('wptabs_edit','normal', null); ?>
	</div>

	<div class="metabox-holder">
		<?php do_meta_boxes('wptabs_settings','normal', null); ?>
	</div>
</div>



<?php
// ****************************** Edit box ******************************
function wptabs_edit_meta_box(){
global $wpdb, $tabs;
?>

<!-- Templates -->
<script type="text/template" id="tabs-template">
	<ul id="edit-tabs-list">
    <% var inner_template = _.template(jQuery('#tab-template') .html()) %>
    <% _.each(tabs, function(tab){ %>
    	<%= inner_template(tab) %>
    <% }); %>
    </ul>
</script>
<script type="text/template" id="tab-template">
	<li id="tab-<%= id %>" class="clearfix">
		<ul class="edit-tab-info">
			<li>
				<label for="tab-<%= id %>-title">Title: </label>
				<input id="tab-<%= id %>-title" type="text" value="<%= title %>">
			</li>
			<li>
				<label for="tab-<%= id %>-title">Body: </label>
				<textarea id="tab-<%= id %>-title" type="text"><%= body %></textarea>
			</li>
		</ul>
		<p>
			<input type="button" id="update-tab-<%= id %>" class="update-tab-button button-primary" value="Update" />
			<input type="button" id="delete-tab-<%= id %>" class="delete-tab-button button-secondary" value="Delete tab" />
		</p>
		<p id="tab-<%= id %>-updated" class="tab-updated">tab updated</p>
	</li>
</script>


	<div id="edit-tabs-list-container"></div>

<?php
}


// ****************************** Settings box ******************************
function wptabs_settings_meta_box(){
global $wpdb, $tab_container;
?>

	<form id="form-update-imsl-settings" action="#" method="post" enctype="multipart/form-data">
		<input type="hidden" id="tab-show-id" value="<?php echo $tab_container->id; ?>">
		<ul class="imsl-ul-settings">
			<li>
				<label>Title:</label>
				<input type="text" id="tab-show-title" value="<?php echo $tab_container->title; ?>">
			</li>
		</ul>
		<input type="submit" class="button-primary" id="imsl-update-settings-submit" value="Update" />
		<span id="imsl-update-settings-saving" style="display:none;">Saving, please wait..</span>
	</form>


<?php
}
?>