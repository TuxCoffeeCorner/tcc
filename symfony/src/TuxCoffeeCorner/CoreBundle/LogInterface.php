<?php

namespace TuxCoffeeCorner\CoreBundle;

use TuxCoffeeCorner\CoreBundle\Entity\Config;

class LogInterface
{
    public $lastError;
    private $repo;
    
    function __construct(\Doctrine\ORM\EntityManager $em)
    {
    	$this->repo = $em->getRepository('TuxCoffeeCornerCoreBundle:Config');
    }
    
    public function writeLog($logname, $message, $priority)
    {
    	$logdir = $this->repo->findOneBy(array('name' => "log_dir"))->getValue();
    	
        touch($logdir.$logname);
        
        switch ($priority){
        case 1:
            $priority = "[ERROR]";
            break;
        case 2:
            $priority = "[WARNING]";
            break;
        case 3:
            $priority = "[INFO]";
            break;
        default:
            $priority = "[WARNING]";
        }
        
        $logEntry = Date("Y-m-d H:i:s", time())." ".$priority." -> ".$message;
        $contents = file_get_contents($logdir.$logname);
        if(file_put_contents($logdir.$logname, $contents.$logEntry."\n")){
            return true;
        }else{
            $this->lastError = "Can't write log '$logdir$logname'";
            return false;
        }
    }
    
    public function getLastError()
    {
    	return $this->lastError;
    }
}