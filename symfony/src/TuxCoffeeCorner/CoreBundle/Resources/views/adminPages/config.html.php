<?php $view->extend('TuxCoffeeCornerCoreBundle::adminIndex.html.php') ?>
<div class="page-header"><h3>Configuration</h3></div>
<div class="table-responsive">
    <table class="table table-condensed table-bordered table-striped table-hover">
    	<thead>
    	    <tr><th>Name</th><th>Value</th><th>Description</th><th>Datatype</th></tr>
	    </thead>
    	<tbody id="config">
    	    <?php echo $table; ?>
    	</tbody>
    </table>
</div>