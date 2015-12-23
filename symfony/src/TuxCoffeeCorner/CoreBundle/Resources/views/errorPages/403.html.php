<?php $view->extend('TuxCoffeeCornerCoreBundle::base.html.php') ?>
<?php $view['slots']->start('style') ?>
<link href="<?php echo $view['assets']->getUrl('bundles/tuxcoffeecorner/css/bootstrap.min.css') ?>" rel="stylesheet"/>
<?php $view['slots']->stop() ?>
<?php $view['slots']->start('body') ?>
<div class="container">
    <h1>Error 403</h1>
	<p class="lead">You shall not pass!</p>
</div>
<?php $view['slots']->stop() ?>