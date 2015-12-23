<?php
$em = $this->container->get('doctrine')->getManager();
$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Config');
$system_name = $repo->findOneBy(array('name' => 'system_name'))->getValue();

$view['slots']->set('system_name', $system_name);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="Tux Coffee Corner">
		<title><?php $view['slots']->output('system_name')?></title>
		<?php $view['slots']->output('style') ?>
	</head>
	<body>
		<?php $view['slots']->output('body') ?>
		<script src="<?php echo $view['assets']->getUrl('bundles/tuxcoffeecorner/libs/jquery.js') ?>"></script>
		<?php $view['slots']->output('script') ?>
	</body>
</html>