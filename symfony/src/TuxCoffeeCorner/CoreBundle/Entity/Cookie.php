<?php

namespace TuxCoffeeCorner\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Cookie{
	private $id_cookie;
	private $personalnummer;
	private $username;
	private $sessionid;

	public function getId()
	{
		return $this->id_cookie;
	}

	public function setPersonalnummer($personalnummer)
	{
		$this->personalnummer = $personalnummer;
		return $this;
	}

	public function getPersonalnummer()
	{
		return $this->personalnummer;
	}

	public function setUsername($username)
	{
		$this->username = $username;
		return $this;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function setSessionid($session_id)
	{
		$this->sessionid = $session_id;
		return $this;
	}

	public function getSessionid()
	{
		return $this->sessionid;
	}
}