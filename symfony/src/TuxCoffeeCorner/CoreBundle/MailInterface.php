<?php

namespace TuxCoffeeCorner\CoreBundle;

use TuxCoffeeCorner\CoreBundle\Entity\Config;
use TuxCoffeeCorner\CoreBundle\Entity\Mail;
use TuxCoffeeCorner\CoreBundle\Entity\Customer;

class MailInterface
{
	private $logname;
	private $log;
    private $lastError; 
    private $em;
    
    function __construct(\Doctrine\ORM\EntityManager $em, LogInterface $logi)
    {
    	$this->log = $logi;
    	$this->logname = "mailInterface.log";

    	$this->em = $em;
    }
    
    public function sendMail(Mail $mail, Customer $customer, $opts)
    {
    	$config_repo = $this->em->getRepository('TuxCoffeeCornerCoreBundle:Config');
    	$suppress_email = strtolower($config_repo->findOneBy(array('name' => "suppress_email"))->getValue());
    
    	if($suppress_email == "false" || $suppress_email == "f" || $suppress_email == "0" || $suppress_email == "n" || $suppress_email == "no"){
    		$body = $mail->getBody();
    		$body = str_replace("[NAME]", $customer->getName(), $body);
    		$body = str_replace("[CREDIT]", $customer->getCredit(), $body);
    		$body = str_replace("[DEBT]", $customer->getDebt(), $body);
    		$body = str_replace("[EMAIL]", $customer->getEmail(), $body);
    		
    		if($opts != ""){
    			$args = explode(" ", $opts);
    			foreach($args as $arg){
    				$tmp = explode("=", $arg);
    				$body = str_replace($tmp[0], $tmp[1], $body);
    			}
    		}
    		
    		$recipient = $customer->getEmail();
    		
    		$super_recipient = $config_repo->findOneBy(array('name' => "super_recipient"))->getValue();
			if ($super_recipient != "") {
    			$this->log->writeLog($this->logname, "Supper recipient enabled: set recipient from '$recipient' to '$super_recipient'", 2);
    			$recipient = $super_recipient;
    		}
    		
    		$sender = $config_repo->findOneBy(array('name' => "system_maintainer"))->getValue();
    		if ($mail->getFrom() != "")
    			$sender = $mail->getFrom();
    		
    		$header = "From: ".$sender."\r\n";
    		$header .= "Cc: ".$mail->getCc()."\r\n";
    		$header .= "Subject: ".$mail->getSubject()."\r\n";
    		
    		$this->log->writeLog($this->logname, "Sending mail '".$mail->getIdentifier()."' to '".$customer->getId()."' with options '$opts' and header '$header'", 3);

    		$sent = mail($recipient, $mail->getSubject(), $body, $header);

    		$this->log->writeLog($this->logname, "Mail sent: '$sent'", ($sent) ? 3 : 1);
    		
    		return $sent;
    	} else {
    		$this->lastError = "E-Mail suppressed";
    		$this->log->writeLog($this->logname, $this->lastError, 2);
    		return false;    		
    	}
    }
    
    public function testMail(Mail $mail, Customer $customer)
    {
    	$config_repo = $this->em->getRepository('TuxCoffeeCornerCoreBundle:Config');
    	$suppress_email = strtolower($config_repo->findOneBy(array('name' => "suppress_email"))->getValue());
    
    	if($suppress_email == "false" || $suppress_email == "f" || $suppress_email == "0"){
    		$recipient = $customer->getEmail();
    		
    		$super_recipient = $config_repo->findOneBy(array('name' => "super_recipient"))->getValue();
    		if($super_recipient != "") {
    			$this->log->writeLog($this->logname, "Supper recipient enabled: set recipient from '$recipient' to '$super_recipient'", 2);
    			$recipient = $super_recipient;
    		}
    		
    		$sender = $config_repo->findOneBy(array('name' => "system_maintainer"))->getValue();
    		if ($mail->getFrom() != "")
    			$sender = $mail->getFrom();
    		
    		$header = "From: ".$sender."\r\n";
    		$header .= "Cc: ".$mail->getCc()."\r\n";
    		$header .= "Subject: ".$mail->getSubject()."\r\n";
    			
    		$this->log->writeLog($this->logname, "Testing mail '".$mail->getIdentifier()."' on '$recipient' with header '".str_replace("\r\n", "; ", $header)."'", 3);
    		
    		$sent = mail($recipient, $mail->getSubject(), $mail->getBody(), $header);
    		
    		$this->log->writeLog($this->logname, "Mail sent: '$sent'", ($sent) ? 3 : 1);
    		
    		return $sent;
    	} else {
    		$this->lastError = "E-Mail suppressed: suppress email: '$suppress_email'";
    		$this->log->writeLog($this->logname, $this->lastError, 2);
    		return false;
    	}
    }
    
    public function getLastError()
    {
    	return $this->lastError;
    }
}
