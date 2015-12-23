<?php

namespace TuxCoffeeCorner\CoreBundle\Controller;

use TuxCoffeeCorner\CoreBundle\Entity\Product;
use TuxCoffeeCorner\CoreBundle\Entity\Config;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProductsController extends Controller
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

    	$products = $this->getProductsAction()->getContent();
        
        return $this->render("TuxCoffeeCornerCoreBundle:adminPages:products.html.php", array('products' => $products));
    }
    
    public function getProductsAction()
    {
    	$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Product');
    	$products = $repo->findBy(array(), array('name' => 'asc'));

    	return $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:productsTable.html.php", array('products' => $products));
    }
    
    public function getByIDAction($pid)
    {
    	$success = true;
    	$errors = array();
    	$data;
    	
    	$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Product');
    	$fetch_res = $repo->findOneBy(array('id_product' => $pid));
    	
    	if ($fetch_res) {
    		$data = array(
    			'id' => $fetch_res->getId(),
    			'barcode' => $fetch_res->getBarcode(),
    			'name' => $fetch_res->getName(),
    			'price' => $fetch_res->getPrice(),
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
    	
    	$product = new Product();
    	$product->setName($_POST['name']);
		$product->setPrice($_POST['price']);
    	$product->setBarcode($_POST['barcode']);
    	$product->setImage($_POST['image']);
    	
    	$postErrors = $this->get('validator')->validate($product);
    	
    	if (count($postErrors) > 0) {
    		$success = false;
    		foreach ($postErrors as $error)
    			$errors[] = $error->getMessage();
    	} else {
    		$em = $this->get('Doctrine')->getManager();
    		$em->persist($product);
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
    	$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Product');
    	$product = $repo->findOneBy(array('id_product' => $pid));
    	 
    	if (!$product)
    		$product = new Product();
    	 
		$product->setName($_POST['name']);
		$product->setPrice($_POST['price']);
    	$product->setBarcode($_POST['barcode']);
    	$product->setImage($_POST['image']);
    	 
    	$postErrors = $this->get('validator')->validate($product);
    	 
    	if (count($postErrors) > 0) {
    		$success = false;
    		foreach ($postErrors as $error)
    			$errors[] = $error->getMessage();
    	} else {
	    	$em->persist($product);
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
    	$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Product');
    	$product = $repo->findOneBy(array('id_product' => $pid));
    	 
    	if (!$product) {
    		$success = false;
    		$errors[] = "Entry not found: '$pid'";
    	} else {
	    	$em->remove($product);
	    	$em->flush();
    	}
    	 
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
    	        $content = file_get_contents($_FILES['image']['tmp_name']);
    	  
    	        $repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Config');
    	        $path = $repo->findOneBy(array('name' => 'image_path'))->getValue();
    	        
        	    if (!$path) {
        	        $errors[] = "Environment variable 'image_path' is not configured!";
        	    } else {
        	        if (is_dir($path)) {
            	        $success = file_put_contents($path."/".$_FILES['image']['name'], $content);
        	             
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