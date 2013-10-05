<?php
global $screen_layout_columns;
add_meta_box("wptabs_content", "Tab containers", "wptabs_meta_box", "wptabs", "normal", "core");


// ****************************** Structure ******************************
?>
<div class="wrap">
	<div id="wptabs-index-container" class="metabox-holder">
		<div id="post-body" class="has-sidebar">
			<div id="post-body-content">
				<?php do_meta_boxes('wptabs','normal', null); ?>
			</div>
		</div>
		<br class="clear"/>
	</div>
</div>


<?php
// ****************************** Tab container list ******************************
function wptabs_meta_box(){
global $tab_containers;
?>

	<ul id="tab-container-content">
		<?php if (sizeof($tab_containers) == 0){ ?>
			There are no tab containers.
		<?php } else { ?>
			<?php foreach ($tab_containers as $tab_container) { ?>
				<li class="index-tabcontainer" id="tabcontainer-<?php echo $tab_container->id; ?>">

					<div class="clearfix">
						<ul>
							<li><strong>Title:</strong> <?php echo $tab_container->title; ?></li>
							<li>
								<strong>Tabs:</strong>
								<?php
								$first = true;
								foreach ($tab_container->tabs as $tab){
									if ($first){
										$first = false;
									} else {
										echo ", ";
									}
									echo $tab->title;
								}
								?>
							</li>
							<li><span>You can add this tab container to any page or post, by writing <strong>[TABS <?php echo $tab_container->id; ?>]</strong></span></li>
						</ul>
					</div>

					<div class="clearfix">
						<a href="<?php echo admin_url('admin.php') .'?page=wptabs-edit-tab-container&tab_container_id='. $tab_container->id; ?>" class="button-primary">Edit</a>

						<form name="frmTabContainers" action="<?php echo admin_url('admin.php') .'?page=wptabs-index'; ?>" method="post" onsubmit="return confirm('Do you really want to delete this tab container?!?');">
							<input type="hidden" name="admin-action" value="delete">
							<input type="hidden" name="sid" value="<?php echo $tab_container->id; ?>">
							<input type="submit" class="button-secondary" value="Delete">
						</form>
					</div>

				</li>
			<?php } ?>
		<?php } ?>
	</ul>

<?php } ?>