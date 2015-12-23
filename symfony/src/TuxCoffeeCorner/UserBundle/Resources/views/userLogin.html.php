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
        <h2>Login</h2>
    </div>
    <form class="form-horizontal" method="post" action="<?php echo $view['router']->generate('user_default') ?>">
      <div class="form-group">
        <label for="inputText3" class="col-sm-2 control-label">User</label>
        <div class="col-sm-3">
          <input type="text" class="form-control" id="inputText3" name="username" placeholder="Username">
        </div>
      </div>
      <div class="form-group">
        <label for="inputPassword3" class="col-sm-2 control-label">Password</label>
        <div class="col-sm-3">
          <input type="password" class="form-control" id="inputPassword3" name="password" placeholder="Password">
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <div class="checkbox">
            <label>
              <input type="checkbox" name="autologin" value="1"> Remember me
            </label>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <button type="submit" class="btn btn-default" name="submit" value="Login">Sign in</button>
        </div>
      </div>
    </form>
    <div style="color:red; font-size: 120%">
        <?php echo $login_error ?>
    </div>
	<p>Kontaktiere uns: <a href="mailto:<?php $view['slots']->output('system_maintainer')?>?subject=TCC">E-Mail senden</a></p>
</div>
<?php $view['slots']->stop() ?>
