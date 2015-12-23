<?php

namespace TuxCoffeeCorner\CoreBundle\Controller;

use TuxCoffeeCorner\CoreBundle\Entity\Config;
use TuxCoffeeCorner\CoreBundle\Entity\Customer;
use TuxCoffeeCorner\CoreBundle\Entity\Product;
use TuxCoffeeCorner\CoreBundle\Entity\Charity;
use TuxCoffeeCorner\CoreBundle\Entity\TccTrx;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;

class ShopController extends Controller
{
	private $logname = "shop.log";

	public function indexAction()
	{
		$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Config');

		$news = str_replace("\n", "<br>",$repo->findOneBy(array('name' => "news_text"))->getValue());
		$show_news = strtolower($repo->findOneBy(array('name' => "news_show"))->getValue());

		// $comicPath = $repo->findOneBy(array('name' => "image_path_comics"))->getValue();

		if($show_news == "true" || $show_news == "t" || $show_news == "1" || $show_news == "y" || $show_news == "yes")
			return $this->render("TuxCoffeeCornerCoreBundle:shopPages:entrance.html.php", array('news' => $news));
		else
			return $this->render("TuxCoffeeCornerCoreBundle:shopPages:entrance.html.php", array());
	}
	
	public function enterAction ($cid)
	{
		$logi = $this->get('log_interface');
		$session = $this->getRequest()->getSession();
		
		if ($session)
			$this->exitAction();
		else
			$session = new Session();

		$session->start();
		$session->set('id', $cid);
		$session->set('default', true);
		$logi->writeLog($this->logname, "Session started '$cid'", 3);
		
		$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Customer');
		$customer = $repo->findOneBy(array('id_customer' => $cid));

		$img = $this->getImageAction()->getContent();
		$ltt = $this->getLttAction()->getContent();
		
		return $this->render("TuxCoffeeCornerCoreBundle:shopPages:shop.html.php", array('ltt' => $ltt, 'img' => $img, 'customer' => $customer));
	}
	
	public function exitAction ()
	{
		$log = $this->get('log_interface');
		$cid = $this->getRequest()->getSession()->get('id');
		$this->sendReminder($cid);
		$this->getRequest()->getSession()->invalidate();
		$log->writeLog($this->logname, "Session closed for '$cid'", 3);
		
		return $this->redirect($this->generateUrl('shop'), 301);
	}
	//getLastTransactions
	public function getLttAction ()
	{
		$session = $this->getRequest()->getSession();
		$cid = $session->get('id');
		
		$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:TccTrx');
		$trx = $repo->findBy(array('customer' => $cid), array('timestamp' => 'desc'));
		$ltt = array_slice($trx, 0, 10); // Array with the last transactions default 11 products
		
		$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Customer');
		$customer = $repo->findOneBy(array('id_customer' => $cid));
		
		if($session->get('default')){
			$session->set('default', false);
			
			$tmpTrx = new TccTrx();
			$tmpTrx->setStatus(0);
		
			$product = $customer->getFavorite();
			if ($product) {
				$tmpTrx->setProduct($product);
				$tmpTrx->setAmount($product->getPrice());
				$ltt = array_merge(array($tmpTrx), $ltt);
			}
		}
		
		return $this->render("TuxCoffeeCornerCoreBundle:shopPages:lttTable.html.php", array('ltt' => $ltt, 'customer' => $customer));
	}
	
	public function getImageAction ()
	{
		$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Config');
		$img_path = $repo->findOneBy(array('name' => "image_path_relativ"))->getValue();
		$img_name = "no_image.jpg";
		
		$session = $this->getRequest()->getSession();
		$cid = $session->get('id');
		
		if ($session->get('default')) {
			$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Customer');
			$customer = $repo->findOneBy(array('id_customer' => $cid));
			
			$product = $customer->getFavorite();
			if ($product)
				$img_name = $product->getImage();
		} else {
			$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:TccTrx');
			$trx = $repo->findOneBy(array('customer' => $cid), array('timestamp' => 'desc'));
			
			if($trx && $trx->getProduct())
				$img_name = $trx->getProduct()->getImage();
		}
		
		return new Response($img_path.$img_name, 200, array('content-type' => 'text/plain'));
	}
	
	public function buyAction ($bar)
	{
		$log = $this->get('log_interface');
		$session = $this->getRequest()->getSession();
		$cid = $session->get('id');
		
		$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Product');
		$product = $repo->findOneBy(array('barcode' => $bar));
		
		$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Customer');
		$customer = $repo->findOneBy(array('id_customer' => $cid));
		
		$customer->charge(-$product->getPrice());
		$customer->setFavorite($product);
		
		$trx = new TccTrx();
		$trx->setAmount($product->getPrice());
		$trx->setStatus(1);
		$trx->setCustomer($customer);
		$trx->setProduct($product);
		
		$em = $this->get('Doctrine')->getManager();
		$em->persist($trx);
		$em->persist($customer);
		$em->flush();

		$log->writeLog($this->logname, "Customer '$cid' bought '$bar'", 3);
		
		return new Response("Done", 200, array('content-type' => 'text/plain'));
	}

	public function donateAction($bar)
	{
		$session = $this->getRequest()->getSession();

		$cid = $session->get('id');
		
		$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Charity');
		$charity = $repo->findOneBy(array('barcode' => $bar));
		
		$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Customer');
		$customer = $repo->findOneBy(array('id_customer' => $cid));

		$customer->charge(-1);
		$charity->donate();

		$em = $this->get('Doctrine')->getManager();
		$em->persist($charity);
		$em->persist($customer);
		$em->flush();

		return new Response("Done", 200, array('content-type' => 'text/plain'));
	}
	
	public function annulateAction()
	{
		$log = $this->get('log_interface');
		
		$session = $this->getRequest()->getSession();
		$cid = $session->get('id');
		
		if(!isset($_POST['item'])){
			$em = $this->get('Doctrine')->getManager();
			$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:TccTrx');
			$trx = $repo->findBy(array('customer' => $cid, 'status' => 1), array('timestamp' => 'desc')); /* fix this !!! */
			$trx = $trx[0];
			
			$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Customer');
			$customer = $repo->findOneBy(array('id_customer' => $cid));
			
			$trx->setStatus(3);
			$customer->charge($trx->getAmount());
			 
			$em = $this->get('Doctrine')->getManager();
			$em->persist($trx);
			$em->persist($customer);
			$em->flush();
			
			$log->writeLog($this->logname, "Customer '$cid' annulated his last transaction", 3);
		}
		
		return new Response("Done", 200, array('content-type' => 'text/plain'));
	}
	
	public function customerExistsAction($cid)
	{
		$ldapi = $this->get('ldap_interface');
		$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Customer');
		$customer = $repo->findOneBy(array('id_customer' => $cid));
		
		if($customer){
			return new Response("true", 200, array('content-type' => 'text/plain'));
		}elseif($ldapi->idExists($cid)){
			$this->addCustomer($cid);
			return new Response("true", 200, array('content-type' => 'text/plain'));
		}else{
			return new Response("false", 200, array('content-type' => 'text/plain'));
		}
	}
	
	public function productExistsAction($barcode)
	{
		$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Product');
		$product = $repo->findOneBy(array('barcode' => $barcode));
		 
		if ($product) {
			return new Response("true", 200, array('content-type' => 'text/plain'));
		} else {
			return new Response("false", 200, array('content-type' => 'text/plain'));
		}
	}

	public function charityExistsAction($barcode)
	{
		$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Charity');
		$charity = $repo->findOneBy(array('barcode' => $barcode));
		 
		if ($charity) {
			return new Response("true", 200, array('content-type' => 'text/plain'));
		} else {
			return new Response("false", 200, array('content-type' => 'text/plain'));
		}
	}
	
	public function productInfoAction($barcode)
	{
		$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Config');
		$path = $repo->findOneBy(array('name' => "image_path_relativ"));
		
		$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Product');
		$product = $repo->findOneBy(array('barcode' => $barcode));
		
		$img = $path->getValue().$product->getImage();
		
		if ($product) {
			return $this->render('TuxCoffeeCornerCoreBundle:shopPages:productInfo.html.php', array('product' => $product, 'img' => $img));
		} else {
			return $this->render('TuxCoffeeCornerCoreBundle:shopPages:error.html.php', array());
		}
	}
	
	private function addCustomer($cid)
	{
		$logi = $this->get('log_interface');
		$ldapi = $this->get('ldap_interface');
		
		$customer = new Customer();
		$customer->setId($cid);
		$customer->setName($ldapi->getCustomerName($cid));
		$customer->setEmail($ldapi->getCustomerMail($cid));
		
		$em = $this->get('Doctrine')->getManager();
		$em->persist($customer);
		$em->flush();
		
		$logi->writeLog($this->logname, "Customer '$cid' added", 3);
	}

	private function sendReminder($cid)
	{
		$log = $this->get('log_interface');
		
		$em = $this->get('doctrine')->getManager();
		$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Customer');
		$customer = $repo->findOneBy(array('id_customer' => $cid));
		
		$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Config');
		$threshold = $repo->findOneBy(array('name' => "shop_debt_threshold"))->getValue();
		
		if($customer){
			if($customer->getDebt() > $threshold){
				$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Mail');
				$mail = $repo->findOneBy(array('identifier' => "reminder"));
				
				if($mail)
					$this->get('mail_interface')->sendMail($mail, $customer, "");
				else
					$log->writeLog($this->logname, "Error while sending reminder: No 'reminder' mail found", 1);
			}
		} else {
			$log->writeLog($this->logname, "Error while sending reminder: No Customer with id '$cid'", 1);
		}
		
		return new Response(json_encode(array('success' => true, 'message' => 'Success!')), 200, array('content-type' => 'application/json'));
	}
	
	public function errorAction ()
	{
		return $this->render('TuxCoffeeCornerCoreBundle:shopPages:error.html.php', array());
	}
}
