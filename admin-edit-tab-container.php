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
global $wpdb, $tab_container;
?>

	<form id="form-create-tab" action="<?php echo admin_url('admin.php') .'?page=wptabs-edit-tab-container&tab_container_id='. $tab_container->id; ?>" method="post">
		<ul class="wptabs-ul-settings">
			<li>
				<label>Title:</label>
				<input type="text" id="new-tab-container-title" name="title" value="">
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

	<div id="edit-tabs-list-container">
		<div class="tabcontainer">
			<div id="tab-header">
				<?php
				$i = 0;
				foreach ($tabs as $tab) { ?>
					<div id="tab-<?php echo $tab->id ?>" class="tab<?php if ($i == 0) echo " active"; ?>" data-tab="<?php echo $i ?>" data-id="<?php echo $tab->id ?>">
						<?php echo $tab->title ?>
					</div>
					<?php
					$i++;
				} ?>
			</div>
			<div id="tab-content-container">
				<?php
				$i = 0;
				foreach ($tabs as $tab) { ?>
					<div id="tab-content-<?php echo $tab->id ?>" class="tab-content<?php if ($i == 0) echo " active"; ?>" data-tab="<?php echo $i ?>">
						<ul class="edit-tab-info">
							<li class="clearfix">
								<label for="tab-<?php echo $tab->id ?>-title">Title: </label>
								<input id="tab-<?php echo $tab->id ?>-title" type="text" value="<?php echo $tab->title ?>">
							</li>
							<li class="clearfix">
								<label for="tab-<?php echo $tab->id ?>-body">Body: </label>
								<?php wp_editor($tab->body, 'tab-'. $tab->id .'-body', array(
									'textarea_rows' => 15,
        							'media_buttons' => false
								)); ?>
							</li>
						</ul>
						<p>
							<input type="button" data-id="<?php echo $tab->id; ?>" class="update-tab-button button-primary" value="Update" />
							<input type="button" data-id="<?php echo $tab->id; ?>" class="delete-tab-button button-secondary" value="Delete tab" />
							<span id="tab-<?php echo $tab->id ?>-updated" class="tab-updated">Tab updated</span>
						</p>
					</div>
					<?php
					$i++;
				} ?>
			</div>
	    </div>

	</div>

<?php
}


// ****************************** Settings box ******************************
function wptabs_settings_meta_box(){
global $wpdb, $tab_container;
?>

	<form id="form-update-wptabs-settings" action="#" method="post">
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