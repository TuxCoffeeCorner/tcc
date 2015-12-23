<?php

namespace TuxCoffeeCorner\CoreBundle\Controller;

use TuxCoffeeCorner\CoreBundle\Entity\Vault;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class VaultController extends Controller
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
        
    	$vault = $this->getVaultAction()->getContent();
        
        return $this->render("TuxCoffeeCornerCoreBundle:adminPages:vault.html.php", array('vault' => $vault));
    }

    public function getVaultAction()
    {
    	$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Vault');
    	$entries = $repo->findBy(array(), array('timestamp' => 'desc'));
    	
    	$totalIn = 0.0;
    	$totalOut = 0.0;
    	foreach ($entries as $entry) {
			$totalIn += $entry->getInput();
			$totalOut += $entry->getOuttake();
    	}
    	
    	$totalIn = sprintf("%01.2f", $totalIn);
    	$totalOut = sprintf("%01.2f", $totalOut);
    	$saldo = sprintf("%01.2f", $totalIn - $totalOut);
    	
    	return $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:vaultTable.html.php", array('entries' => $entries, 'totalIn' => $totalIn, 'totalOut' => $totalOut, 'saldo' => $saldo));
    }
    
    public function getByIDAction($vid)
    {
    	$success = true;
    	$errors = array();
    	$data;
    	
    	$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Vault');
    	$fetch_res = $repo->findOneBy(array('id_vault' => $vid));
    	
    	if ($fetch_res) {
    		$data = array(
    			'id' => $fetch_res->getId(),
    			'timestamp' => $fetch_res->getTimestamp(),
    			'input' => $fetch_res->getInput(),
    			'outtake' => $fetch_res->getOuttake(),
    			'comment' => $fetch_res->getComment(),
    			'cashier' => $fetch_res->getCashier(),
    		);
    	} else {
    		$success = false;
    		$errors[] = "No entry with id '$vid' found: creating new";
    		$data = $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:".($success ? "successMsg" : "errorMsg").".html.php", array('errors' => $errors))->getContent();
    	}

    	return new Response(json_encode(array('success' => $success, 'data' => $data)), 200, array('content-type' => 'application/json'));
    }
    
	public function addAction()
    {       
    	$success = true;
		$errors = array();
		
		$entry = new Vault();
		$entry->setTimestamp($_POST['timestamp']);
		$entry->setInput($_POST['input']);
		$entry->setOuttake($_POST['outtake']);
		$entry->setComment($_POST['comment']);
		$entry->setCashier($_POST['cashier']);
		
		$postErrors = $this->get('validator')->validate($entry);
    	
    	if (count($postErrors) > 0) {
    		$success = false;
    		foreach ($postErrors as $error)
    			$errors[] = $error->getMessage();
    	} else {
			$em = $this->get('Doctrine')->getManager();
			$em->persist($entry);
    		try {
		    	$em->flush();
			} catch (\Exception $e) {
				$success = false;
				$errors[] = '<b>Uuuh! Some nasty database exception caught:</b> '.$e->getMessage()."\n";
			}
		}
		
		$message = $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:".($success ? "successMsg" : "errorMsg").".html.php", array('errors' => $errors))->getContent();
		return new Response(json_encode(array('success' => $success, 'message' => $message)), 200, array('content-type' => 'application/json'));
    }
    
    public function editAction($vid)
    {
    	$success = true;
    	$errors = array();
    	
    	$em = $this->get('doctrine')->getManager();
    	$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Vault');
    	$entry = $repo->findOneBy(array('id_vault' => $vid));

    	if (!$entry)
    		$entry = new Vault();
    	
	    $entry->setTimestamp($_POST['timestamp']);
		$entry->setInput($_POST['input']);
    	$entry->setOuttake($_POST['outtake']);
    	$entry->setComment($_POST['comment']);
    	$entry->setCashier($_POST['cashier']);
    	
    	$postErrors = $this->get('validator')->validate($entry);
    	
    	if (count($postErrors) > 0) {
    		$success = false;
    		foreach ($postErrors as $error)
    			$errors[] = $error->getMessage();
    	} else {
    		$em = $this->get('Doctrine')->getManager();
    		$em->persist($entry);
    		try {
		    	$em->flush();
			} catch (\Exception $e) {
				$success = false;
				$errors[] = '<b>Uuuh! Some nasty database exception caught:</b> '.$e->getMessage()."\n";
			}
    	}
    	
    	$message = $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:".($success ? "successMsg" : "errorMsg").".html.php", array('errors' => $errors))->getContent();
    	return new Response(json_encode(array('success' => $success, 'message' => $message)), 200, array('content-type' => 'application/json'));
    }
    
    public function deleteAction($vid)
    {
    	$success = true;
    	$errors = array();
    	
    	$em = $this->get('doctrine')->getManager();
    	$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Vault');
    	$entry = $repo->findOneBy(array('id_vault' => $vid));
    	
    	if (!$entry) {
    		$success = false;
    		$errors[] = "Entry not found: '$vid'";
    	} else {
    		$em->remove($entry);
    		$em->flush();
    	}
    	
    	$message = $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:".($success ? "successMsg" : "errorMsg").".html.php", array('errors' => $errors))->getContent();
    	return new Response(json_encode(array('success' => $success, 'message' => $message)), 200, array('content-type' => 'application/json'));
    }
}