<?php 
$view->extend('TuxCoffeeCornerCoreBundle::base.html.php');

$em = $this->container->get('doctrine')->getManager();
$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Config');
$system_name = $repo->findOneBy(array('name' => 'system_name'))->getValue();
$system_maintainer = $repo->findOneBy(array('name' => 'system_maintainer'))->getValue();
$css_file = $repo->findOneBy(array('name' => 'css_user'))->getValue();

$view['slots']->set('system_name', $system_name);
$view['slots']->set('system_maintainer', $system_maintainer);
?>
<?php $view['slots']->start('style') ?>
<link href="<?php echo $view['assets']->getUrl('bundles/tuxcoffeecorner/css/bootstrap.min.css') ?>" rel="stylesheet"/>
<link href="<?php echo $view['assets']->getUrl('bundles/tuxcoffeecorner/css/'.$css_file) ?>" rel="stylesheet"/>
<?php $view['slots']->stop() ?>
<?php $view['slots']->start('body') ?>
<div class="container">	
    <div class="jumbotron">
        <h1><?php $view['slots']->output('system_name')?></h1>
        <h2>Hallo <?php echo $customer->getName(); ?></h2>
    </div>
	<div class="row">
	    <div class="col-md-6">
    		<div class="panel panel-primary">
		        <?php echo $ltt; ?>
    		</div>
		</div>
		<div class="col-md-6">
    		<div class="panel panel-primary">
    		    <div class="panel-heading">
    		        <h3 class="panel-title">Deine Statistiken</h3>
    		    </div>
    			<table class="table">
                    <?php print_r($stats); ?>
                </table>
                <div class="panel-body">
        			<br><p>Kontaktiere uns: <a href="mailto:<?php $view['slots']->output('system_maintainer')?>?subject=TCC">E-Mail senden</a></p>
    			</div>
    		</div>
    	</div>
	</div>
</div>
<?php $view['slots']->stop() ?>
<?php $view['slots']->start('script') ?>
<script src="<?php echo $view['assets']->getUrl('bundles/tuxcoffeecorner/js/user.js') ?>"></script>
<?php $view['slots']->stop() ?>