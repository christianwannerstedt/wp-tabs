<?php
global $screen_layout_columns, $tab_container, $tabs;

add_meta_box("wptabs_content_tabs", "Edit tabs", "wptabs_edit_meta_box", "wptabs_edit", "normal", "core");
add_meta_box("wptabs_content_create", "Add new tabs", "wptabs_create_meta_box", "wptabs_create", "normal", "core");
add_meta_box("wptabs_content_settings", "Settings", "wptabs_settings_meta_box", "wptabs_settings", "normal", "core");


// ****************************** Structure ******************************
?>
<script type="text/javascript">
var tinymce_url = '<?php echo plugins_url(); ?>tiny-mce-textarea.php',
	tab_container_id = <?php echo $tab_container->id; ?>,
	tabs_json = <?php echo json_encode($tabs); ?>;
</script>

<div class="wrap">
	<div class="metabox-holder">
		<?php do_meta_boxes('wptabs_edit','normal', null); ?>
	</div>

	<div class="metabox-holder">
		<?php do_meta_boxes('wptabs_create','normal', null); ?>
	</div>

	<div class="metabox-holder">
		<?php do_meta_boxes('wptabs_settings','normal', null); ?>
	</div>
</div>


<?php
// ****************************** Create box ******************************
function wptabs_create_meta_box(){
global $wpdb, $slide_show;
?>

	<form id="form-create-tab" action="#" method="post" enctype="multipart/form-data">
		<input type="hidden" id="tab-container-id" value="<?php echo $tab_container->id; ?>">
		<ul class="wptabs-ul-settings">
			<li>
				<label>Title:</label>
				<input type="text" id="new-tab-container-title" value="">
			</li>
		</ul>
		<input type="submit" class="button-primary" id="wptabs-create-submit" value="Update" />
		<span id="wptabs-create-saving" style="display:none;">Saving, please wait..</span>
	</form>

<?php
}
?>


<?php
// ****************************** Edit box ******************************
function wptabs_edit_meta_box(){
global $wpdb, $tabs;
?>

<!-- Templates -->
<script type="text/template" id="tabs-template">
    <%
    var head_template = _.template(jQuery('#tab-head-template') .html());
    var inner_template = _.template(jQuery('#tab-template') .html());
    %>
	<div class="tabcontainer">
		<div id="tab-header">
			<% _.each(tabs, function(tab, i){ %>
				<% tab.i = i; %>
				<%= head_template(tab) %>
			<% }); %>
		</div>
		<div id="tab-content-container">
			<% _.each(tabs, function(tab, i){ %>
				<%= inner_template(tab) %>
			<% }); %>
		</div>
    </div>
</script>
<script type="text/template" id="tab-head-template">
	<div id="tab-<%= id %>" class="tab<% if (i == 0){ %> active<% } %>" data-tab="<%= i %>" data-id="<%= id %>"><%= title %></div>
</script>
<script type="text/template" id="tab-template">
	<div id="tab-content-<%= id %>" class="tab-content<% if (i == 0){ %> active<% } %>" data-tab="<%= i %>">
		<ul class="edit-tab-info">
			<li class="clearfix">
				<label for="tab-<%= id %>-title">Title: </label>
				<input id="tab-<%= id %>-title" type="text" value="<%= title %>">
			</li>
			<li class="clearfix">
				<label for="tab-<%= id %>-body">Body: </label>
				<textarea id="tab-<%= id %>-body" type="text"><%= body %></textarea>
			</li>
		</ul>
		<p>
			<input type="button" id="update-tab-<%= id %>" class="update-tab-button button-primary" value="Update" />
			<input type="button" id="delete-tab-<%= id %>" class="delete-tab-button button-secondary" value="Delete tab" />
		</p>
		<p id="tab-<%= id %>-updated" class="tab-updated">tab updated</p>
	</div>
</script>


	<div id="edit-tabs-list-container"></div>

<?php
}


// ****************************** Settings box ******************************
function wptabs_settings_meta_box(){
global $wpdb, $tab_container;
?>

	<form id="form-update-wptabs-settings" action="#" method="post" enctype="multipart/form-data">
		<input type="hidden" id="tab-container-id" value="<?php echo $tab_container->id; ?>">
		<ul class="wptabs-ul-settings">
			<li>
				<label>Title:</label>
				<input type="text" id="tab-container-title" value="<?php echo $tab_container->title; ?>">
			</li>
		</ul>
		<input type="submit" class="button-primary" id="wptabs-update-settings-submit" value="Update" />
		<span id="wptabs-update-settings-saving" style="display:none;">Saving, please wait..</span>
	</form>


<?php
}
?>