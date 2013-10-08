<?php
global $title, $screen_layout_columns;
add_meta_box("wptabs_content", $title, "wptabs_meta_box", "wptabs_new", "normal", "core");
?>

<div class="wrap">

	<div id="wptabs-index-container" class="metabox-holder">
		<?php do_meta_boxes('wptabs_new','normal', null); ?>
	</div>

</div>


<?php
function wptabs_meta_box(){
global $wpdb;
?>

	<form name="frmwptabs" action="<?php echo admin_url('admin.php') .'?page=wptabs-new'; ?>" method="post">
		<input type="hidden" name="admin-action" value="create" />

		<ul class="wptabs-ul-settings">
			<li>
				<label>Title:</label>
				<input type="text" name="tab_container_title" value="">
			</li>
		</ul>

		<input type="submit" class="button-primary" value="Add tab container" />

	</form>

<?php } ?>