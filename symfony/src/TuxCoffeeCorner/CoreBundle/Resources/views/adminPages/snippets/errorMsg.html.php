<div class="alert alert-danger alert-dismissable">
	<button class="close" type="button" data-dismiss="alert">&times;</button>
	<ul>
		<?php foreach ($errors as $error): ?>
		<li><?php echo $error; ?></li>
		<?php endforeach; ?>
	</ul>
</div>