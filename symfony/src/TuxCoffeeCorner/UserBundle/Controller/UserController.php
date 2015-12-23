<?php

namespace TuxCoffeeCorner\UserBundle\Controller;

use TuxCoffeeCorner\CoreBundle\Entity\Customer;
use TuxCoffeeCorner\CoreBundle\Entity\TccTrx;
use TuxCoffeeCorner\CoreBundle\Entity\Product;
use TuxCoffeeCorner\CoreBundle\Entity\Cookie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends Controller
{
	private $ldapi;
	
    public function indexAction()
    {
    	
        $login_session = $this->getRequest()->getSession();
        $username = $login_session->get('username');

        if(!$login_session->has('username'))
        {
            // if NOT logged in redirect to login page
            return $this->redirect($this->generateUrl('user_default')); //all Symfony versions
        }        

        $this->ldapi = $this->get('ldap_interface');

        $cid = $this->ldapi->getCustomerID($username);


    	// if (isset($_SERVER['REMOTE_USER'])) {
    	//     $cid = $this->ldapi->getCustomerID($_SERVER['REMOTE_USER']);
    	// } elseif (isset($_SERVER['REDIRECT_REMOTE_USER'])) {
    	//     $cid = $this->ldapi->getCustomerID($_SERVER['REDIRECT_REMOTE_USER']);
    	// } else {
    	//     $cid = false;
    	// }
    	
    	$ltt = "Keine Transaktionen verfügbar.";
    	$stats = "Keine Statistiken verfügbar.";
    	
    	$customer = new Customer();
    	
    	$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:Customer');
    	$fech_res = $repo->findOneBy(array('id_customer' => $cid));
    	
    	if ($fech_res) {
    		$customer = $fech_res;
    		$ltt = $this->getLastTransactions($customer);
    		$stats = $this->getStats($customer);
    	}
    	
    	return $this->render('TuxCoffeeCornerUserBundle::userIndex.html.php', array('customer' => $customer, 'ltt' => $ltt, 'stats' => $stats));
    }
    
    private function getStats($customer)
    {
    	$query_date = date('Y-m-t', time());
    	$monthStart = date('Y-m-01', strtotime($query_date));
    	$monthEnd = date('Y-m-t', strtotime($query_date));
    	
    	$cid = $customer->getId();
    	$connection = $this->get('Doctrine')->getManager()->getConnection();
    	
    	/* alltime favorite */
    	$query = $connection->prepare("select count(product_id) as count, name from TccTrx join product on id_product = product_id where customer_id = $cid and status = 1 group by name order by count desc limit 1;");
    	$query->execute();
    	$res = $query->fetchAll();
    	$stats['a_fav_name'] = ($res)? $res[0]['name'] : 'None';
    	$stats['a_fav_count'] = ($res)? $res[0]['count'] : '0';
    	
    	/* alltime sum */
    	$query = $connection->prepare("select sum(amount) as sum from TccTrx where customer_id = $cid and status = 1 limit 1;");
		$query->execute();
    	$res = $query->fetchAll();
    	$stats['a_sum'] = ($res)? $res[0]['sum'] : '';
    	
    	/* alltime rank */
    	$query = $connection->prepare("select customer_id, sum(amount) as sum from TccTrx where status = 1 group by customer_id;");
    	$query->execute();
    	$res = $query->fetchAll();
    	$stats['a_rank'] = ($res)? $this->getRanking($cid, $res) : 'None';
    	
    	/* monthly favorite */
    	$query = $connection->prepare("select count(product_id) as count, name from TccTrx join product on id_product = product_id where customer_id = $cid and status = 1 and timestamp between '$monthStart' and '$monthEnd' group by name order by count desc limit 1;");
    	$query->execute();
    	$res = $query->fetchAll();
    	$stats['m_fav_name'] = ($res)? $res[0]['name'] : 'None';
    	$stats['m_fav_count'] = ($res)? $res[0]['count'] : '0';
    	 
    	/* monthly sum */
    	$query = $connection->prepare("select sum(amount) as sum from TccTrx where customer_id = $cid and status = 1 and timestamp between '$monthStart' and '$monthEnd' limit 1;");
    	$query->execute();
    	$res = $query->fetchAll();
    	$stats['m_sum'] = ($res)? $res[0]['sum'] : '';

    	/* monthly rank */
    	$query = $connection->prepare("select customer_id, sum(amount) as sum from TccTrx where status = 1 and timestamp between '$monthStart' and '$monthEnd' group by customer_id;");
    	$query->execute();
    	$res = $query->fetchAll();
    	$stats['m_rank'] = ($res)? $this->getRanking($cid, $res) : 'None';
    	
    	return $this->renderView("TuxCoffeeCornerUserBundle::statisticsTable.html.php", array('stats' => $stats));
    }
    
    private function getRanking($cid, $leaderboard)
    {
    	for ($i = 0; $i < count($leaderboard); $i++)
    		if ($leaderboard[$i]['customer_id'] == $cid)
    			return $i +1;
    	
    	return 'None';
    }
    
    private function getLastTransactions($customer)
    {
    	$repo = $this->get('doctrine')->getRepository('TuxCoffeeCornerCoreBundle:TccTrx');
    	$ltt = $repo->findBy(array('customer' => $customer->getId()), array('timestamp' => 'desc'));
    	$ltt = array_slice($ltt, 0, 10);
    	
        return $this->renderView("TuxCoffeeCornerUserBundle::lttTable.html.php", array('ltt' => $ltt, 'customer' => $customer));
    }
}
