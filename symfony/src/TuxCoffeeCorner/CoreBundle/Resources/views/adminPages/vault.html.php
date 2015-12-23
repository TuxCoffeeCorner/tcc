<?php $view->extend('TuxCoffeeCornerCoreBundle::adminIndex.html.php') ?>
	<div class="page-header"><h3>Vault</h3></div>
	<div class="table-responsive">
		<table class="table table-condensed table-bordered table-striped table-hover">
			<thead>
				<tr><th>#</th><th>Timestamp</th><th>Input</th><th>Outtake</th><th>Comment</th><th>Cashier</th><th><button id="btn-vault-add" class="btn btn-default btn-xs" type="button" title="add" data-target="#vault-modal" data-toggle="modal"><span class="glyphicon glyphicon-plus"></span></button></th></tr>
			</thead>
			<tbody id="vault">
			<?php echo $vault; ?>
			</tbody>
		</table>
	</div>
	<div id="vault-modal" class="modal fade" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button class="close" type="button" data-dismiss="modal" tabindex="-1">&times;</button>
					<h3 id="vault-header"></h3>
				</div>
				<div class="modal-body">
					<div class="modal-errors"></div>
					<form id="vault-form" class="form-horizontal" action="" enctype="multipart/form-data">
						<div class="form-group">
							<label class="col-lg-2 control-label" for="vault-timestamp">Date</label>
							<div class="col-lg-10">
								<input id="vault-timestamp" class="form-control" name="timestamp" type="text" placeholder="timestamp" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label" for="vault-input">Input</label>
							<div class="col-lg-10">
								<input id="vault-input" class="form-control" name="input" type="text" placeholder="0.0" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label" for="vault-outtake">Outtake</label>
							<div class="col-lg-10">
								<input id="vault-outtake" class="form-control" name="outtake" type="text" placeholder="0.0" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label" for="vault-comment">Comment</label>
							<div class="col-lg-10">
								<div class="input-group">
									<input id="vault-comment" class="form-control" name="comment" type="text" />
									<div class="input-group-btn">
										<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Comments <span class="caret"></span></button>
										<ul id="vault-comment-list" class="dropdown-menu pull-right">
											<li><a href="#">Einzahlung </a></li>
											<li><a href="#">Überweisung auf Postkonto</a></li>
											<li><a href="#">Einkauf bei </a></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label" for="vault-chashier">Cashier</label>
							<div class="col-lg-10">
								<input id="vault-cashier" class="form-control" name="cashier" type="text" />
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button class="btn btn-default" type="button" data-dismiss="modal">Close</button>
					<button id="btn-vault-submit" class="btn btn-primary" type="button">Save</button>
				</div>
			</div>
		</div>
	</div>