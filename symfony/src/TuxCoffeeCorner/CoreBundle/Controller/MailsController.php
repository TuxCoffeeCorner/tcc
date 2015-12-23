<?php

namespace TuxCoffeeCorner\CoreBundle\Controller;

use TuxCoffeeCorner\CoreBundle\Entity\Mail;
use TuxCoffeeCorner\CoreBundle\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class MailsController extends Controller
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
        
    	$mails = $this->getMailsAction()->getContent();
		
        return $this->render("TuxCoffeeCornerCoreBundle:adminPages:mails.html.php", array('mails' => $mails));
    }
    
    public function getMailsAction()
    {
    	$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Mail');
    	$mails = $repo->findBy(array(), array('identifier' => 'asc'));
    	
    	return $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:mailsTable.html.php", array('mails' => $mails));
    }
    
    public function getByIDAction($mid)
    {
    	$success = true;
    	$errors = array();
    	$data;
    	 
    	$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Mail');
    	$fetch_res = $repo->findOneBy(array('id_mail' => $mid));
    	 
    	if ($fetch_res) {
    		$data = array(
    			'id' => $fetch_res->getId(),
    			'identifier' => $fetch_res->getIdentifier(),
    			'subject' => $fetch_res->getSubject(),
    			'body' => $fetch_res->getBody(),
    			'to' => $fetch_res->getTo(),
    			'cc' => $fetch_res->getCc(),
    			'from' => $fetch_res->getFrom(),
    		);
    	} else {
    		$success = false;
    		$errors[] = "No mail with id '$mid' found: creating new";
    		$data = $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:".($success ? "successMsg" : "errorMsg").".html.php", array('errors' => $errors))->getContent();
    	}
    	
    	return new Response(json_encode(array('success' => $success, 'data' => $data)), 200, array('content-type' => 'application/json'));
    }
    
    public function addAction()
    {
    	$success = true;
    	$errors = array();
    	
    	$mail = new Mail();
    	$mail->setIdentifier($_POST['identifier']);
    	$mail->setSubject($_POST['subject']);
    	$mail->setBody($_POST['body']);
    	$mail->setTo($_POST['to']);
    	$mail->setCc($_POST['cc']);
    	$mail->setFrom($_POST['from']);
    	
    	$postErrors = $this->get('validator')->validate($mail);
    	
    	if (count($postErrors) > 0) {
    		$success = false;
    		foreach ($postErrors as $error)
    			$errors[] = $error->getMessage();
    	} else {
	    	$em = $this->get('Doctrine')->getManager();
	    	$em->persist($mail);
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
    
    public function editAction($mid)
    {
    	$success = true;
    	$errors = array();
    	 
    	$em = $this->get('doctrine')->getManager();
    	$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Mail');
    	$mail = $repo->findOneBy(array('id_mail' => $mid));
    	 
    	if (!$mail)
    		$mail = new Mail();
    	 
    	$mail->setIdentifier($_POST['identifier']);
    	$mail->setSubject($_POST['subject']);
    	$mail->setBody($_POST['body']);
    	$mail->setTo($_POST['to']);
    	$mail->setCc($_POST['cc']);
    	$mail->setFrom($_POST['from']);
    	 
    	$postErrors = $this->get('validator')->validate($mail);
    	 
    	if (count($postErrors) > 0) {
    		$success = false;
    		foreach ($postErrors as $error)
    			$errors[] = $error->getMessage();
    	} else {
	    	$em->persist($mail);
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
    
    public function deleteAction($mid)
    {
    	$success = true;
    	$errors = array();
    	 
    	$em = $this->get('doctrine')->getManager();
    	$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Mail');
    	$mail = $repo->findOneBy(array('id_mail' => $mid));
    	 
    	if (!$mail) {
    		$success = false;
    		$errors[] = "Entry not found: '$mid'";
    	} else {
    		$em->remove($mail);
    		$em->flush();
    	}
    	 
    	$message = $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:".($success ? "successMsg" : "errorMsg").".html.php", array('errors' => $errors))->getContent();
    	return new Response(json_encode(array('success' => $success, 'message' => $message)), 200, array('content-type' => 'application/json'));
    }
    
    public function sendTestAction($mident, $rec)
    {
    	$success = true;
    	$errors = array();
    	
    	$em = $this->get('doctrine')->getManager();
    	$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Mail');
    	$mail = $repo->findOneBy(array('id_mail' => $mident));
    	
    	$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Customer');
    	$customer = $repo->findOneBy(array('email' => $rec));
    	
    	if (!$this->get('mail_interface')->testMail($mail, $customer, "")) {
    		$success = false;
    		$errors[] = $this->get('mail_interface')->getLastError();
    	}
    	
    	$message = $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:".($success ? "successMsg" : "errorMsg").".html.php", array('errors' => $errors))->getContent();
    	return new Response(json_encode(array('success' => $success, 'message' => $message)), 200, array('content-type' => 'application/json'));
    }
}
