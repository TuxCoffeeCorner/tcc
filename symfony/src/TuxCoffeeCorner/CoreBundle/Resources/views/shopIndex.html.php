<?php
$view->extend('TuxCoffeeCornerCoreBundle::base.html.php'); 

$em = $this->container->get('doctrine')->getManager();
$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Config');
$system_name = $repo->findOneBy(array('name' => 'system_name'))->getValue();
$css_file = $repo->findOneBy(array('name' => 'css_shop'))->getValue();

$view['slots']->set('system_name', $system_name);
?>
<?php $view['slots']->start('style') ?>
<link href="<?php echo $view['assets']->getUrl('bundles/tuxcoffeecorner/css/bootstrap.min.css') ?>" rel="stylesheet"/>
<link href="<?php echo $view['assets']->getUrl('bundles/tuxcoffeecorner/css/'.$css_file) ?>" rel="stylesheet"/>
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('body') ?>
<div class="container">
	<div class="shop-input"><input id="shop-input" type="text"></div> <!--class="shop-input"- hidden input field-->
	<?php $view['slots']->output('_content') ?>
</div>
<?php $view['slots']->stop() ?>
<?php $view['slots']->start('script') ?>
<script src="<?php echo $view['assets']->getUrl('bundles/tuxcoffeecorner/js/shop.js') ?>"></script>
<?php $view['slots']->stop() ?>