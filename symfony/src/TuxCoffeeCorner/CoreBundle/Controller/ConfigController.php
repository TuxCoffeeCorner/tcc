<?php

namespace TuxCoffeeCorner\CoreBundle\Controller;

use TuxCoffeeCorner\CoreBundle\Entity\Config;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ConfigController extends Controller
{
    public function indexAction()
    {
        // Check if user is logged in and is a Linux User
        $login_session = $this->getRequest()->getSession();
        $username = $login_session->get('username');
        $linux_user = exec("id $username");

        if(!$login_session->has('username') || $linux_user == "")
        {
            return $this->redirect($this->generateUrl('user_default'));
        }
        // Check end

    	$table = $this->getAction()->getContent();
    	
		return $this->render("TuxCoffeeCornerCoreBundle:adminPages:config.html.php", array('table' => $table));
    }
    
    public function setVarAction($var_name)
    {
    	$success = true;
    	$errors = array();
    	
    	$em = $this->get('doctrine')->getManager();
    	$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Config');
    	$var = $repo->findOneBy(array('name' => $var_name));
    	
    	if ($var) {
    		$var->setValue($_POST['value']);
    		$em->persist($var);
    		$em->flush();
    	} else {
    		$success = false;
    		$errors[] = "Config variable '$var_name' not found";
    	}
    	
    	$message = $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:".($success ? "successMsg" : "errorMsg").".html.php", array('errors' => $errors))->getContent();
		return new Response(json_encode(array('success' => $success, 'message' => $message)), 200, array('content-type' => 'application/json'));
    }
    
    public function getAction()
    {
    	$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Config');
    	$vars = $repo->findBy(array(), array('name' => 'asc'));

    	return $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:configTable.html.php", array('vars' => $vars));
    }
    
    public function getByIDAction($var_name)
    {
    	$success = true;
    	$errors = array();
    	$data;
    	
    	$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Config');
    	$fetch_res = $repo->findOneBy(array('name' => $var_name));
    	
    	if ($fetch_res) {
    		$data = array(
    				'name' => $fetch_res->getName(),
    				'value' => $fetch_res->getValue(),
    				'description' => $fetch_res->getDescription(),
    				'datatype' => $fetch_res->getDatatype(),
    		);
    	} else {
    		$success = false;
    		$errors[] = "No config var with name '$var_name' found";
    		$data = $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:".($success ? "successMsg" : "errorMsg").".html.php", array('errors' => $errors))->getContent();
    	}
    	 
    	return new Response(json_encode(array('success' => $success, 'data' => $data)), 200, array('content-type' => 'application/json'));
    }
}
