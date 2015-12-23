<?php

namespace TuxCoffeeCorner\CoreBundle\Controller;

use TuxCoffeeCorner\CoreBundle\Entity\Charity;
use TuxCoffeeCorner\CoreBundle\Entity\Config;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CharitysController extends Controller
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
        
    	$charitys = $this->getCharitysAction()->getContent();
        
        return $this->render("TuxCoffeeCornerCoreBundle:adminPages:charitys.html.php", array('charitys' => $charitys));
    }
    
    public function getCharitysAction()
    {
    	$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Charity');
    	$charitys = $repo->findBy(array(), array('organisation' => 'asc'));

    	return $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:charitysTable.html.php", array('charitys' => $charitys));
    }
    
    public function getByIDAction($pid)
    {
    	$success = true;
    	$errors = array();
    	$data;
    	
    	$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Charity');
    	$fetch_res = $repo->findOneBy(array('id_charity' => $pid));
    	
    	if ($fetch_res) {
    		$data = array(
    			'id' => $fetch_res->getId(),
    			'barcode' => $fetch_res->getBarcode(),
    			'organisation' => $fetch_res->getOrganisation(),
    			'beginn' => $fetch_res->getBeginn(),
                'ende' => $fetch_res->getEnde(),
                'spendenstand' => $fetch_res->getSpendenstand(),
    			'image' => $fetch_res->getImage(),
    		);
    	} else {
    		$success = false;
    		$errors[] = "No entry with id '$pid' found: creating new";
    		$data = $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:".($success ? "successMsg" : "errorMsg").".html.php", array('errors' => $errors))->getContent();
    	}

    	return new Response(json_encode(array('success' => $success, 'data' => $data)), 200, array('content-type' => 'application/json'));
    }
    
	public function addAction()
    {
    	$success = true;
    	$errors = array();
    	
    	$charity = new Charity();
        $charity->setBarcode($_POST['barcode']);
    	$charity->setOrganisation($_POST['organisation']);
		$charity->setBeginn($_POST['beginn']);
    	$charity->setEnde($_POST['ende']);
        $charity->setSpendenstand(0);
    	$charity->setImage($_POST['image']);
    	
    	$postErrors = $this->get('validator')->validate($charity);
    	
    	if (count($postErrors) > 0) {
    		$success = false;
    		foreach ($postErrors as $error)
    			$errors[] = $error->getMessage();
    	} else {
    		$em = $this->get('Doctrine')->getManager();
    		$em->persist($charity);
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
    
    public function editAction($pid)
    {
    	$success = true;
    	$errors = array();
    	 
		$em = $this->get('doctrine')->getManager();
    	$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Charity');
    	$charity = $repo->findOneBy(array('id_charity' => $pid));
    	 
    	if (!$charity)
    		$charity = new Charity();
    	$charity->setBarcode($_POST['barcode']); 
		$charity->setOrganisation($_POST['organisation']);
		$charity->setBeginn($_POST['beginn']);
    	$charity->setEnde($_POST['ende']);
        // $charity->setSpendenstand($_POST['spendenstand']);
    	$charity->setImage($_POST['image']);
    	 
    	$postErrors = $this->get('validator')->validate($charity);
    	 
    	if (count($postErrors) > 0) {
    		$success = false;
    		foreach ($postErrors as $error)
    			$errors[] = $error->getMessage();
    	} else {
	    	$em->persist($charity);
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
    
    public function deleteAction($pid)
    {
    	$success = true;
    	$errors = array();
    	 
    	$em = $this->get('doctrine')->getManager();
    	$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Charity');
    	$charity = $repo->findOneBy(array('id_charity' => $pid));
    	 
    	if (!$charity) {
    		$success = false;
    		$errors[] = "Entry not found: '$pid'";
    	} else {
	    	$em->remove($charity);
	    	$em->flush();
    	}
    	 
    	$message = $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:".($success ? "successMsg" : "errorMsg").".html.php", array('errors' => $errors))->getContent();
    	return new Response(json_encode(array('success' => $success, 'message' => $message)), 200, array('content-type' => 'application/json'));
    }
    
    public function resetAction($pid)
    {
        $success = true;
        $errors = array();

        $em = $this->get('doctrine')->getManager();
        $repo = $repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Charity');
        $charity = $repo->findOneBy(array('id_charity' => $pid));

        $charity->reset();

        $em->persist($charity);
        $em->flush();

        $message = $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:".($success ? "successMsg" : "errorMsg").".html.php", array('errors' => $errors))->getContent();
        return new Response(json_encode(array('success' => $success, 'message' => $message)), 200, array('content-type' => 'application/json'));

    }

    public function getImagesAction()
    {
    	$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Config');
    	$path = $repo->findOneBy(array('name' => 'image_path'))->getValue();

        $images = "";
        if ($path != "") {
        	if ($scannedDirs = scandir($path)) {
        		$files = array_diff($scannedDirs, array(".", "..", "no_image.jpg"));
        		
        		foreach ($files as $image) {
        			$images = $images . '<option value="' . $image . '">' . $image . '</option>';
        		}
        	}
        }
        
        return new Response($images, 200, array('content-type' => 'text/plain'));
    }
    
    public function uploadImageAction()
    {
    	$success = true;
    	$errors = array();
    	
    	if ($_FILES['image']['size'] > 0) {
    	    if (preg_match("/^image\/[a-zA-Z.-]+/", $_FILES['image']['type'])) {
    	        $content = file_get_contents($_FILES['image']['tmp_organisation']);
    	  
    	        $repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Config');
    	        $path = $repo->findOneBy(array('organisation' => 'image_path'))->getValue();
    	        
        	    if (!$path) {
        	        $errors[] = "Environment variable 'image_path' is not configured!";
        	    } else {
        	        if (is_dir($path)) {
            	        $success = file_put_contents($path.$_FILES['image']['name'], $content);
        	             
            	        if ($success === false) {
            	            $errors[] = "Failed to save image file! Check permission of 'image_path'!";
            	        } else {
            	            $success = true;
            	        }
            	    } else {
            	    	$success = false;
            	        $errors[] = "Configured environment variable 'image_path' is not a path!";
            	    }    
        	    }
    	    } else {
    	    	$success = false;
    	        $errors[] = "Only image files are allowed!";
    	    }
        } else {
        	$success = false;
            $errors[] = "No file was uploaded!";
        }
        
		$message = $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:".($success ? "successMsg" : "errorMsg").".html.php", array('errors' => $errors))->getContent();
    	return new Response(json_encode(array('success' => $success, 'message' => $message)), 200, array('content-type' => 'application/json'));
    }
}