<?php

namespace TuxCoffeeCorner\CoreBundle\Controller;

use TuxCoffeeCorner\CoreBundle\Entity\Customer;
use TuxCoffeeCorner\CoreBundle\Entity\TccTrx;
use TuxCoffeeCorner\CoreBundle\Entity\Product;
use TuxCoffeeCorner\CoreBundle\Entity\Vault;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CustomersController extends Controller
{
	private $logname = "customer_ctl.log";
	
	public function indexAction()
    {
        
        $login_session = $this->getRequest()->getSession();
        
        // Autologin if no session exists but the TCC-Cookie is available
        if(!$login_session->has('username'))
        { 
            // compare with cookie table to create session
            parse_str($this->getRequest()->cookies->get('TuxCoffeeCorner'));
            $repo = $this->getDoctrine()->getRepository('TuxCoffeeCornerCoreBundle:Cookie');
            $cookie_entry = $repo->findOneBy(array('username' => $username, 'sessionid' => $sid));
            
            if ($cookie_entry) {
                // Timestamp Validation
                //...
                $login_session->set('username', $username);
            }
        }

        // Check if user is logged in and is a Linux User
    	$username = $login_session->get('username');
    	$linux_user = exec("id $username");

        if(!$login_session->has('username') || $linux_user == "")
        {
            return $this->redirect($this->generateUrl('user_default'));
        }
        // Check end

    	$aC = $this->getCustomersAction("active", "name", "asc")->getContent();
    	$iaC = $this->getCustomersAction("inactive", "name", "asc")->getContent();
    	
		return $this->render("TuxCoffeeCornerCoreBundle:adminPages:customers.html.php", array('aC' => $aC, 'iaC' => $iaC));
    }
    
    public function getCustomersAction($active, $column, $type){
    	$active = ($active == 'active' ? true : false);
    	
    	$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Customer');
    	$customers = $repo->findBy(array('active' => $active), array($column => $type));

    	return $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:customersTable.html.php", array('customers' => $customers, 'active' => $active));
    }
    
    public function changeCustomerStateAction($cid)
    {
    	$success = true;
    	$errors = array();
    	
    	$em = $this->get('doctrine')->getManager();
    	$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Customer');
    	$customer = $repo->findOneBy(array('id_customer' => $cid));
    	
    	if ($customer) {
    		$customer->setActive(!$customer->getActive());
    		$em->persist($customer);
    		$em->flush();
    	} else {
    		$success = false;
    		$errors[] = "Customer '$cid' not found";
    	}

		$message = $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:".($success ? "successMsg" : "errorMsg").".html.php", array('errors' => $errors))->getContent();
		return new Response(json_encode(array('success' => $success, 'message' => $message)), 200, array('content-type' => 'application/json'));
    }
    
    public function chargeCustomerAction($cid, $charge)
    {
    	$success = true;
    	$errors = array();
    	
    	$log = $this->get('log_interface');
    	
    	$em = $this->get('doctrine')->getManager();
    	$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Customer');
    	$customer = $repo->findOneBy(array('id_customer' => $cid));
    	
    	$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Product');
    	$product = $repo->findOneBy(array('barcode' => 1)); /* barcode 1 => einzahlung */
    	
    	if ($customer) {
			if (is_numeric($charge)) {
				if ($product) {
					$trx = new TccTrx();
					$trx->setAmount($charge);
					$trx->setStatus(2);
					$trx->setCustomer($customer);
					$trx->setProduct($product);
					$em->persist($trx);
				} else {
					$success = false;
					$errors[] = "Product 'Einzahlung' not found: it should have barcode '1'";
				}
				
				$vault = new Vault();
				if ($charge >= 0 )
					$vault->setInput($charge);
				else
					$vault->setOuttake(abs($charge));
				
                $login_session = $this->getRequest()->getSession();
                $username = $login_session->get('username');
                $vault->setCashier($username);
				// if (isset($_SERVER['REMOTE_USER']))
				// 	$vault->setCashier($_SERVER['REMOTE_USER']);
				// elseif (isset($_SERVER['REDIRECT_REMOTE_USER']))
				// 	$vault->setCashier($_SERVER['REDIRECT_REMOTE_USER']);

				$vault->setComment("Einzahlung ".$customer->getName());
				$em->persist($vault);
				
				$customer->charge($charge);
		    	$em->persist($customer);
		    	
		    	$em->flush();
		    	
		    	$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Mail');
		    	$mail = $repo->findOneBy(array('identifier' => "receipt"));
		    	
		    	if ($mail) {
		    		$charge = sprintf("%01.2f", $charge);
		    		$this->get('mail_interface')->sendMail($mail, $customer, "[CHARGE]=$charge");
		    	} else {
		    		$success = false;
		    		$errors[] = "No 'receipt' mail found";
		    		$log->writeLog($this->logname, "Error while sending reminder: No 'receipt' mail found", 1);
		    	}
			} else {
				$success = false;
				$errors[] = "Invalid value for charge: '$charge' must be numeric";
			}
    	} else {
    		$success = false;
    		$errors[] = "Customer '$cid' not found";
    		$log->writeLog($this->logname, "Error while charging customer '$cid': customer not found", 1);
    	}
    	
   	    $message = $this->render("TuxCoffeeCornerCoreBundle:adminPages/snippets:".($success ? "successMsg" : "errorMsg").".html.php", array('errors' => $errors))->getContent();
		return new Response(json_encode(array('success' => $success, 'message' => $message)), 200, array('content-type' => 'application/json'));
    }
}
