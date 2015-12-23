<?php $view->extend('TuxCoffeeCornerCoreBundle::shopIndex.html.php') ?>
<div class="jumbotron">
	<h1>Willkommen!</h1>
	<?php if(isset($news)):?>
	<div class="alert alert-warning"><?php echo $news?></div>
	<?php endif;?>
	<p><?php echo shell_exec('/usr/games/fortune bofh-excuses 2>&1'); ?></p>
</div>