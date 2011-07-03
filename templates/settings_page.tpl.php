<div class="wrap">
	<div class="icon32 icon32-posts-page" id="icon-edit"><br></div>
	<h2>Just Custom Fields</h2>
	<p>You should choose Custom Post Type first to configure fields:</p>
	<ul class="dotted-list jcf-bold">
	<?php foreach($post_types as $key => $obj) : ?>
		<li><a href="?page=just_custom_fields&amp;pt=<?php echo $key; ?>"><?php echo $obj->label; ?></a></li>
	<?php endforeach; ?>
	</ul>
</div>
