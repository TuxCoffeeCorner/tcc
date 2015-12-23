<?php 
$view->extend('TuxCoffeeCornerCoreBundle::base.html.php'); 
$page = $app->getRequest()->attributes->get('_route');

$em = $this->container->get('doctrine')->getManager();
$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Config');
$system_name = $repo->findOneBy(array('name' => 'system_name'))->getValue();
$css_file = $repo->findOneBy(array('name' => 'css_admin'))->getValue();

$view['slots']->set('system_name', $system_name);

// $remoteUser = "";
// if (isset($_SERVER['REMOTE_USER'])) 
// 	$remoteUser = $_SERVER['REMOTE_USER'];
// elseif (isset($_SERVER['REDIRECT_REMOTE_USER'])) 
// 	$remoteUser = $_SERVER['REDIRECT_REMOTE_USER'];


$remoteUser = $app->getSession()->get('username');
?>
<?php $view['slots']->start('style') ?>
<link href="<?php echo $view['assets']->getUrl('bundles/tuxcoffeecorner/css/bootstrap.min.css') ?>" rel="stylesheet"/>
<link href="<?php echo $view['assets']->getUrl('bundles/tuxcoffeecorner/css/'.$css_file) ?>" rel="stylesheet"/>
<?php $view['slots']->stop() ?>
<?php $view['slots']->start('body') ?>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/admin/config/"><?php $view['slots']->output('system_name')?></a>
        </div>
    	<div class="collapse navbar-collapse">
    		<ul class="nav navbar-nav">
    			<li class="<?php echo ($page == 'config') ? "active" : "" ?>"><a href="<?php echo $view['router']->generate('config') ?>"><span class="glyphicon glyphicon-cog"></span> Configuration</a></li>
    			<li class="<?php echo ($page == 'customers' || $page == 'admin') ? "active" : "" ?>"><a href="<?php echo $view['router']->generate('customers') ?>"><span class="glyphicon glyphicon-user"></span> Customers</a></li>
    			<li class="<?php echo ($page == 'products') ? "active" : "" ?>"><a href="<?php echo $view['router']->generate('products') ?>"><span class="glyphicon glyphicon-barcode"></span> Products</a></li>
                <li class="<?php echo ($page == 'charitys') ? "active" : "" ?>"><a href="<?php echo $view['router']->generate('charitys') ?>"><span class="glyphicon glyphicon-globe"></span> Charity</a></li>
                <li class="<?php echo ($page == 'mails') ? "active" : "" ?>"><a href="<?php echo $view['router']->generate('mails') ?>"><span class="glyphicon glyphicon-envelope"></span> Mail</a></li>
    			<li class="<?php echo ($page == 'vault') ? "active" : "" ?>"><a href="<?php echo $view['router']->generate('vault') ?>"><span class="glyphicon glyphicon-usd"></span> Vault</a></li>
    		</ul>
    		<p id="remoteUser" class="navbar-text navbar-right"><?php echo $remoteUser ?></p>
    	</div>
	</div>
</nav>
<div class="container content">
    <?php $view['slots']->output('_content') ?>
</div>
<?php $view['slots']->stop() ?>
<?php $view['slots']->start('script') ?>
<script src="<?php echo $view['assets']->getUrl('bundles/tuxcoffeecorner/libs/bootstrap.min.js') ?>"></script>
<script src="<?php echo $view['assets']->getUrl('bundles/tuxcoffeecorner/js/admin.js') ?>"></script>
<?php $view['slots']->stop() ?>
