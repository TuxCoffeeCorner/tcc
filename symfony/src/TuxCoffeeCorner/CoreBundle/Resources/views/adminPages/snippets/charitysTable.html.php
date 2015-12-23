<?php foreach ($charitys as $charity): ?>
<tr id="<?php echo $charity->getId(); ?>">
	<td><?php echo $charity->getId(); ?></td>
	<td><?php echo $charity->getBarcode(); ?></td>
	<td><?php echo $charity->getOrganisation(); ?></td>
	<td><?php echo $charity->getBeginn(); ?><td><?php echo $charity->getEnde(); ?></td><td><?php echo $charity->getSpendenstand(); ?><td><?php echo $charity->getImage(); ?></td>
	<td><button class="btn btn-default btn-xs btn-charity-edit" type="button" title="edit" data-target="#charity-modal" data-toggle="modal"><span class="glyphicon glyphicon-pencil"></span></button>
	<button class="btn btn-default btn-xs btn-charity-del" type="button" title="delete"><span class="glyphicon glyphicon-trash"></span></button>
	<button class="btn btn-default btn-xs btn-charity-reset" type="button" title="reset"><span class="glyphicon glyphicon-flash"></span></button>
	</td></tr>
<?php endforeach; ?>