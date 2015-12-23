<?php

// Change for Prod
//-----------------
// Set secure flag for the cookie

namespace TuxCoffeeCorner\UserBundle\Controller;

use TuxCoffeeCorner\CoreBundle\Entity\Cookie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class LoginController extends Controller
{
	private $username;
	private $password;

	public function indexAction()
	{

		$login_session = $this->getRequest()->getSession();
		
		if (!$login_session)
			$login_session = new Session();

		$this->setConfig();

		// Is the user already logged in? Redirect user to the private page
		if($login_session->has('username'))
		{
			// if logged in redirect to users page
			return $this->redirect($this->generateUrl('user_page')); //all Symfony versions
			// return $this->redirectToRoute('user_page'); // Symfony 2.6 and above
		}

		if($this->getRequest()->request->has('submit')) //request => POST, query => GET
		{
			$login_success = $this->doLogin();
			if($login_success){
				return $this->redirect($this->generateUrl('user_page')); //all Symfony versions
			}else{
				$login_error = "The submitted login info is incorrect or you're not a Tux Coffee Corner user.";
			}
		}
		//render login-form
		return $this->render('TuxCoffeeCornerUserBundle::userLogin.html.php', array('login_error' => $login_error));
	}


	private function setConfig()
	{
		error_reporting(E_ALL ^ E_NOTICE);
		 
		header('Cache-control: private'); // IE 6 FIX
		 
		// always modified
		header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
		// HTTP/1.1
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		// HTTP/1.0
		header('Pragma: no-cache');
		 
	 
		// ---------- Invoke Auto-Login if no session is registered ---------- //
		 
		if(!$this->getRequest()->request->has('username')){
			$this->autologin();
		}
		return true;
	}

	private function autoLogin()
	{
		$remember_me = $this->getRequest()->cookies->has('TuxCoffeeCorner'); 

		if ($remember_me) {
			// get $username = username from cookie
			// get $sid = session_id from cookie
			parse_str($this->getRequest()->cookies->get('TuxCoffeeCorner'));

			// compare with cookie table
			$repo = $this->getDoctrine()->getRepository('TuxCoffeeCornerCoreBundle:Cookie');
			$cookie_entry = $repo->findOneBy(array('username' => $username, 'sessionid' => $sid));
			
			if ($cookie_entry) {
				// Timestamp Validation
				//...
				$login_session = $this->getRequest()->getSession();
				$login_session->set('username', $username);

				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	private function doLogin()
	{

		$login_session = $this->getRequest()->getSession();

		// declare post fields (request => POST, query => GET)
		$this->username = $this->getRequest()->request->get('username');
		$this->password = $this->getRequest()->request->get('password');
		$autologin = $this->getRequest()->request->get('autologin');

		$customer_id = $this->ldapAuthentication();

		// Check if user is a TCC-Customer
		$repo = $this->getDoctrine()->getRepository('TuxCoffeeCornerCoreBundle:Customer');
		$customer = $repo->findOneBy(array('id_customer' => $customer_id));

		if($customer_id != false && $customer){
			//successful login, could fetch custormer id with USERS CREDENTIALS from Active Directory AND user is a Tcc Customer
			$login_session->set('username', $this->username);
			if($autologin == 1){
				// set cookie
			
				$repo = $this->getDoctrine()->getRepository('TuxCoffeeCornerCoreBundle:Config');
    			$days = $repo->findOneBy(array('name' => "remember_me_days"))->getValue();

				$cookie_name = 'TuxCoffeeCorner';
				$cookie_time = (3600 * 24 * $days);
				$session_id = $this->getRequest()->cookies->get('PHPSESSID'); 
		    		if ($this->getRequest()->isSecure()) {
			    		setcookie($cookie_name, 'username='. $this->username . '&sid=' . $session_id, time() + $cookie_time, "/", "", 0, 1);
			    	}

			// cookie-table entries  		    
			    $this->createCookieEntry($customer_id, $session_id);

			}else{
				$this->deleteCookieEntry();
			}

			return true;
		}else{
			// login error
			return false;
		}
	}

	private function ldapAuthentication(){
		// Get dn, ldap-binding with tux-user
		$ldapi = $this->get('ldap_interface');

		$dn = $ldapi->getDistinguishedName($this->username);

		// Get customerid/employeeid, ldap-binding with user and user-pw (authentication)
		$user_binding = $ldapi->reInit($dn, $this->password);
		
		if ($user_binding) {
			$customer_id = @$ldapi->getCustomerID($this->username); // The @-symbol supresses ldap-errormessages
			return $customer_id;
		}else{
			return false;
		}	
	}

	private function createCookieEntry($customer_id, $session_id){
		// Check if user in cookie table, if so replace entry, else add
		$repo = $this->getDoctrine()->getRepository('TuxCoffeeCornerCoreBundle:Cookie');
		$cookie_entry = $repo->findOneBy(array('username' => $this->username));

		$em = $this->getDoctrine()->getManager();


		if ($cookie_entry) {
			// update entry in cookie table
			$cookie_entry->setSessionid($session_id);
		}else{
			// add entry to the cookie table
			$cookie_entry = new Cookie();
	    	$cookie_entry->setPersonalnummer($customer_id);
	    	$cookie_entry->setUsername($this->username);
	    	$cookie_entry->setSessionid($session_id);

	    	$em->persist($cookie_entry);
		}		  
	    
	    $em->flush();
	}	

	private function deleteCookieEntry(){
		// Check if user in cookie-table, if so delete the user
		$repo = $this->getDoctrine()->getRepository('TuxCoffeeCornerCoreBundle:Cookie');
		$cookie_entry = $repo->findOneBy(array('username' => $this->username));

		if($cookie_entry){
			$em = $this->getDoctrine()->getManager();
			$em->remove($cookie_entry);
		    $em->flush();

		    $cookie_name = "TuxCoffeeCorner";
		    unset($_COOKIE['TuxCoffeeCorner']);
			setcookie($cookie_name, '', time() - 3600, "/");
		}
		return true;
	}
}
