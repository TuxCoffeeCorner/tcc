<?php foreach ($vars as $var): ?>
<?php
if ($var->getDatatype() == "string/password")
	$var->setValue("**********");
elseif ($var->getValue() == "")
	$var->setValue("-");
?>
<tr id="<?php echo $var->getName(); ?>"><td><?php echo $var->getName(); ?></td><td><a href="#" title="edit"><?php echo $var->getValue(); ?></a></td><td><?php echo $var->getDescription(); ?></td><td><?php echo $var->getDatatype(); ?></td></tr>
<?php endforeach; ?>