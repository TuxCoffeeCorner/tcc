<?php $view->extend('TuxCoffeeCornerCoreBundle::shopIndex.html.php') ?>
<div class="jumbotron">
	<h1>Hallo <?php echo $customer->getName()?>!</h1>
	<p>Timeout in: <span id="timeout">15</span>s</p>
</div>
<div class="row">
	<div class="col-md-6">
		<div id="table-ltt" class="panel panel-primary">
			<?php echo $ltt;?>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel panel-primary">
			<div class="panel-body">
				<img id="img-img" src="<?php echo $img?>" class="img-rounded img-responsive">
			</div>
		</div>
	</div>
</div>