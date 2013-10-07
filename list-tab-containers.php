<?php
global $tab_containers;
?>

<div class="header clearfix">
	<h2>Select tabs to include</h2>
	<a class="close">close</a>
</div>

<ul>
<?php
foreach ($tab_containers as $tab_container){ ?>
	<li>
		<a class='wptab-link' data-id='<?php echo $tab_container->id; ?>'><?php echo $tab_container->title; ?></a>
	</li>
	<?php }
?>
</ul>
