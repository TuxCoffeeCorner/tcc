<?php 
if ($active)
	$btnState = '<button class="btn btn-default btn-xs" type="button" title="set inactive"><span class="glyphicon glyphicon-remove"></span></button>';
else
	$btnState = '<button class="btn btn-default btn-xs" type="button" title="set active"><span class="glyphicon glyphicon-ok"></span></button>';
?>
<?php foreach ($customers as $customer): ?>
<tr id="<?php echo $customer->getId(); ?>" <?php echo (time()-strtotime($customer->getUpdated()) > 5184000 ? 'class="danger"' : ''); ?>><td><?php echo $customer->getId();?></td><td><?php echo $customer->getName();?></td><td><?php echo $customer->getEmail();?></td><td><a class="btn-customer-charge" href="#" title="charge"><?php echo $customer->getCredit();?></a></td><td><?php echo $customer->getUpdated();?></td><td><?php echo $btnState; ?></td></tr>
<?php endforeach; ?>