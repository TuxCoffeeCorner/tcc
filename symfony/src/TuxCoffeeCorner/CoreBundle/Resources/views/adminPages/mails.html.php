<?php $view->extend('TuxCoffeeCornerCoreBundle::adminIndex.html.php') ?>
    <div class="page-header"><h3>Mails</h3></div>
	<p>Following identifiers are reserved for the system:</p>
	<ul>
	    <li>reminder -> Notification for customers in debt, triggered when shopping</li>
	    <li>receipt -> Receipt for charging, triggered on charge (sms and manually)</li>
	</ul>
	<p>Following placeholders are possible:</p>
	<ul>
	    <li>[NAME] -> Name of recipient</li>
	    <li>[CREDIT] -> Credit of recipient</li>
	    <li>[DEBT] -> Debt of recipient</li>
		<li>[CHARGE] -> Amount wich was added to the customers credit</li>
	    <li>[EMAIL] -> E-Mail address of recipient</li>
	</ul>
	<div class="table-responsive">
	    <table class="table table-condensed table-bordered table-striped table-hover">
	    	<thead>
	    	    <tr><th>Identifier</th><th>Subject</th><th>Body</th><th>To</th><th>CC</th><th>From</th><th><button id="btn-mail-add" class="btn btn-default btn-xs" type="button" title="add" data-target="#mail-modal" data-toggle="modal"><span class="glyphicon glyphicon-plus"></span></a></th></tr>
	        </thead>
	    	<tbody id="mails">
			<?php echo $mails; ?>
			</tbody>
	    </table>
	</div>
	<div id="mail-modal" class="modal fade" tabindex="-1">
	    <div class="modal-dialog">
	        <div class="modal-content">
        		<div class="modal-header">
        			<button class="close" type="button" data-dismiss="modal" tabindex="-1">&times;</button>
        			<h3 id="mail-header"></h3>
        		</div>
    			<div class="modal-body">
    				<div class="modal-errors"></div>
        			<form id="mail-form" class="form-horizontal" action="#" method="post">
        				<div class="form-group">
        					<label class="col-lg-2 control-label" for="mail-identifier">Identifier</label>
        					<div class="col-lg-10">
        						<input id="mail-identifier" class="form-control" name="identifier" type="text" placeholder="Identifier" />
        					</div>
        				</div>
        				<div class="form-group">
        					<label class="col-lg-2 control-label" for="mail-subject">Subject</label>
        					<div class="col-lg-10">
        						<input id="mail-subject" class="form-control" name="subject" type="text" placeholder="Subject" />
        					</div>
        				</div>
        				<div class="form-group">
        					<label class="col-lg-2 control-label" for="mail-body">Body</label>
        					<div class="col-lg-10">
        						<textarea id="mail-body" class="form-control" name="body"></textarea>
        					</div>
        				</div>
        				<div class="form-group">
                            <label class="col-lg-2 control-label" for="mail-to">To</label>
                            <div class="col-lg-10">
                                <input id="mail-to" class="form-control" name="to" type="text" placeholder="To">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label" for="mail-cc">CC</label>
                            <div class="col-lg-10">
                                <input id="mail-cc" class="form-control" name="cc" type="text" placeholder="CC">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label" for="mail-from">From</label>
                            <div class="col-lg-10">
                                <input id="mail-from" class="form-control" name="from" type="text" placeholder="From">
                            </div>
                        </div>
                    </form>
    			</div>
    			<div class="modal-footer">
    				<button class="btn btn-default" type="button" data-dismiss="modal">Close</button>
    				<button id="btn-mail-submit" class="btn btn-primary" type="button">Save</button>
    			</div>
	        </div>
	    </div>
	</div>