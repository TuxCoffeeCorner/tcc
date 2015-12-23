<?php $view->extend('TuxCoffeeCornerCoreBundle::shopIndex.html.php') ?>
<div class="jumbotron">
	<h1><?php echo $product->getName()?></h1>
	<p>Timeout in: <span id="timeout">15</span>s</p>
</div>
<div class="row">
	<div class="col-md-6">
	<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo $product->getName()?></h3>
			</div>
			<div class="panel-body">
				<h3>Preis: <?php echo $product->getPrice()?></h3>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel panel-primary">
			<div class="panel-body">
				<img src="<?php echo $img?>" class="img-rounded img-responsive">
			</div>
		</div>
	</div>
</div>