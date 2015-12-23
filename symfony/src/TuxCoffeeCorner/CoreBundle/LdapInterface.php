<?php

namespace TuxCoffeeCorner\CoreBundle;

use TuxCoffeeCorner\CoreBundle\Entity\Config;

class LdapInterface
{
    private $lastError;
    private $logname;
    private $log;
    private $hostname;
    private $port;
    private $user;
    private $password;
    private $connection;
    private $bound;
    private $searchBase;

    function __construct(\Doctrine\ORM\EntityManager $em, LogInterface $logi)
    {
    	$this->log = $logi;
    	$this->logname = "ldapInterface.log";

    	$repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Config');

    	$this->hostname = $repo->findOneBy(array('name' => "ldap_hostname"))->getValue();
    	$this->port = $repo->findOneBy(array('name' => "ldap_port"))->getValue();
    	$this->user = $repo->findOneBy(array('name' => "ldap_user"))->getValue();
    	$this->password = $repo->findOneBy(array('name' => "ldap_password"))->getValue();
    	$this->searchBase = $repo->findOneBy(array('name' => "ldap_search_base"))->getValue();

    	$this->connection = ldap_connect($this->hostname, $this->port);
    	
    	if ($this->connection) {
    	    $this->bound = ldap_bind($this->connection, $this->user, $this->password);
    	}
    }

    public function reInit($username, $password)
    {
        $this->user = $username;
        $this->password = $password;

        if ($this->connection) {
            $this->bound = ldap_bind($this->connection, $this->user, $this->password);
        }
        return $this->bound;
    }
    
    public function getLdapEntries($this->searchBase, $filter, $justthese)
    {
    	$this->log->writeLog($this->logname, "LdapInterface bound: '".$this->bound."'", 3);
    	$this->log->writeLog($this->logname, "getLdapEntries parameters: this->searchBase: '$this->searchBase'; filter: '$filter'; justthese: '(".implode(", ", $justthese).")';", 3);
    	
        if ($this->connection && $this->bound) {
            if (is_string($this->searchBase) && !empty($this->searchBase) && is_string($filter) && !empty($filter) && isset($justthese) && is_array($justthese)) {
                $result = ldap_search($this->connection, $this->searchBase, $filter , $justthese);
                
                if (!$result) {
                	$this->lastError = ldap_error($this->connection);
                	$this->log->writeLog($this->logname, $this->lastError, 1);
                	return false;
                } else {
                    return ldap_get_entries($this->connection, $result);
                }
            } else {
            	$this->lastError = "'getLdapEntries' has an argument error";
            	$this->log->writeLog($this->logname, $this->lastError, 1);
                return false;
            }
        } else {
            $this->lastError = "LDAP connection failed";
            $this->log->writeLog($this->logname, $this->lastError, 1);
            return false;
        }
    }
    
    public function getCustomerID($name)
    {
        $filter = "CN=".$name;
        $justthese = array("employeeID");
        
        $info = $this->getLdapEntries($this->searchBase, $filter, $justthese);
        
        if (!$info['count'] > 0) {
            return false;
        } else {
            return $info[0][$info[0][0]][0];
        }
    }
    
    public function getCustomerName($id)
    {
    	$id = sprintf("%08s", $id);
        $filter = "employeeID=$id";
        $justthese = array("sn", "givenName", "mail");
        
        $info = $this->getLdapEntries($this->searchBase, $filter, $justthese);
        
        if (!$info['count'] > 0) {
            return false;
        } else {
            return $info[0][$info[0][1]][0]." ".$info[0][$info[0][0]][0];
        }
    }
    
    public function getCustomerMail($id)
    {
    	$id = sprintf("%08s", $id);
        $filter="employeeID=$id";
        $justthese = array("sn", "givenName", "mail");
        
        $info = $this->getLdapEntries($this->searchBase, $filter, $justthese);
        
        if (!$info['count'] > 0) {
            return false;
        } else {
            return $info[0][$info[0][2]][0];
        }
    }
    
    public function idExists($id)
    {
    	$id = sprintf("%08s", $id);
        $filter = "employeeID=$id";
        $justthese = array("employeeID");
        
        $info = $this->getLdapEntries($this->searchBase, $filter, $justthese);

        if ($info['count'] > 0) 
            return true;
        else
            return false;
    }

    // used for Login
    public function getDistinguishedName($username)
    {
        $filter = "CN=$username";
        $justthese = array("dn");

        $info = $this->getLdapEntries($this->searchBase, $filter, $justthese);

        if (!$info['count'] > 0) {
            return false;
        } else {
            return $info[0]["dn"];
        }
    }
}
