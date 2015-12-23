<div class="panel-heading">
	<h3 class="panel-title">Deine letzten Transaktionen</h3>
</div>
<table class="table table-condensed table-striped">
	<tr><th>Datum</th><th>Produkt</th><th>Preis</th></tr>
	<?php foreach ($ltt as $trx): ?>
	<?php switch($trx->getStatus()): 
		case 0: ?>
	<tr id="<?php echo $trx->getProduct()->getBarcode(); ?>" class="warning">
	<?php break;?>
	<?php case 2: ?>
	<tr class="success">
	<?php break;?>
	<?php case 3: ?>
	<tr class="annulated">
	<?php break;?>
	<?php endswitch;?>
	<td><?php echo $trx->getTimestamp(); ?></td><td><?php echo $trx->getProduct()->getName(); ?></td><td><?php echo $trx->getAmount(); ?></td>
	</tr>
	<?php endforeach; ?>
</table>
<div class="panel-body">
	<p>Guthaben: <?php echo $customer->getCredit()?></p>
</div>