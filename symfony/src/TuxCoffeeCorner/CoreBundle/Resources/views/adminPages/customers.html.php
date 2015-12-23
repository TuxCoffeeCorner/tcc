<?php $view->extend('TuxCoffeeCornerCoreBundle::adminIndex.html.php') ?>
<div class="page-header"><h3>Customers</h3></div>
<div class="tabbable">
	<ul class="nav nav-tabs nav-justified" id="tabs-panel">
   		<li class="active"><a href="#active" data-toggle="tab">Active</a></li>
   		<li><a href="#inactive" data-toggle="tab">Inactive</a></li>
   	</ul>
   	<div class="tab-content">
		<div class="tab-pane active" id="active">
			<div class="table-responsive">
				<table class="table table-condensed table-bordered table-striped table-hover">
    				<thead>
    	    			<tr><th>#</th><th class="sort-name">Name <span class="glyphicon glyphicon-chevron-up pull-right"></span></th><th>E-Mail</th><th class="sort-credit">Credit <span class="glyphicon glyphicon-minus pull-right"></span></th><th>Last transaction</th><th></th></tr>
    				</thead>
    				<tbody id="customer-active" class="customers">
    				<?php echo $aC ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="tab-pane" id="inactive">
			<div class="table-responsive">
	    		<table class="table table-condensed table-bordered table-striped table-hover">
	    			<thead>
	    	    		<tr><th>#</th><th class="sort-name">Name <span class="glyphicon glyphicon-chevron-up pull-right"></span></th><th>E-Mail</th><th class="sort-credit">Credit <span class="glyphicon glyphicon-minus pull-right"></span></th><th>Last transaction</th><th></th></tr>
	    			</thead>
	    			<tbody id="customer-inactive" class="customers">
					<?php echo $iaC ?>
					 </tbody>
				</table>
			</div>
		</div>
   	</div>
</div>