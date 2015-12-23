<?php $view->extend('TuxCoffeeCornerCoreBundle::adminIndex.html.php') ?>
    <div class="page-header"><h3>Charity</h3></div>
	<form id="image-form" class="form-inline" action="#" enctype="multipart/form-data">
		<input id="image-hidden-input" name="image" type="file" />
		<input id="image-fake-input" class="form-control" name="fake-image" type="text" placeholder="image file path" />
		<button class="btn btn-primary" type="submit" title="upload"><span class="glyphicon glyphicon-upload"></span> Upload</button>
		<span class="glyphicon glyphicon-info-sign image-information" data-toggle="popover" data-placement="right" title="Default image" data-content="You can save a file under the name 'no_image.jpg' in order to use it as a default image."></span>
	</form><br>
	<div class="table-responsive">
	    <table class="table table-condensed table-bordered table-striped table-hover">
	        <thead>
	            <tr><th>#</th><th>Barcode</th><th>Organisation</th><th>Beginn</th><th>Ende</th><th>Spendenstand</th><th>Image</th><th><button id="btn-charity-add" class="btn btn-default btn-xs" type="button" title="add" data-target="#charity-modal" data-toggle="modal"><span class="glyphicon glyphicon-plus"></span></button></th></tr>
            </thead>
        	<tbody id="charitys">
        	    <?php echo $charitys; ?>
        	</tbody>
	    </table>
    </div>
	<div id="charity-modal" class="modal fade" tabindex="-1">
    	<div class="modal-dialog">
    	    <div class="modal-content">
        		<div class="modal-header">
        			<button class="close" type="button" data-dismiss="modal" tabindex="-1">&times;</button>
        			<h3 id="charity-header"></h3>
        		</div>
        		<div class="modal-body">
        			<div class="modal-errors"></div>
        			<form id="charity-form" class="form-horizontal" action="#">
        				<div class="form-group">
        					<label class="col-lg-2 control-label" for="charity-barcode">Barcode</label>
        					<div class="col-lg-10">
        						<input id="charity-barcode" class="form-control" name="barcode" type="text" placeholder="integer" />
        					</div>
        				</div>
        				<div class="form-group">
        					<label class="col-lg-2 control-label" for="charity-organisation">Organisation</label>
        					<div class="col-lg-10">
        						<input id="charity-organisation" class="form-control" name="organisation" type="text" placeholder="string" />
        					</div>
        				</div>
        				<div class="form-group">
        					<label class="col-lg-2 control-label" for="charity-beginn">Beginn</label>
        					<div class="col-lg-10">
        						<input id="charity-beginn" class="form-control" name="beginn" type="text" placeholder="string" />
        					</div>
        				</div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label" for="charity-ende">Ende</label>
                            <div class="col-lg-10">
                                <input id="charity-ende" class="form-control" name="ende" type="text" placeholder="string" />
                            </div>
                        </div>
                        <div class="form-group">
        					<label class="col-lg-2 control-label" for="charity-image">Image</label>
        					<div class="col-lg-10">
        						<select id="charity-image" class="form-control" name="image">
        							<option value="no_image.jpg">no_image.jpg</option>
        						</select>
        					</div>
        				</div>
        			</form>				
        		</div>
        		<div class="modal-footer">
        			<button class="btn btn-default" type="button" data-dismiss="modal">Close</button>
        			<button id="btn-charity-submit" class="btn btn-primary" type="button">Save</button>
        		</div>
        	</div>
        </div>
    </div>