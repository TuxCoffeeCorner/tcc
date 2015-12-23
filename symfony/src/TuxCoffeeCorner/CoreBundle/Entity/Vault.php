<?php

namespace TuxCoffeeCorner\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Vault
{
	private $id_vault;
	private $timestamp;
	private $input = 0.0;
	private $outtake = 0.0;
	private $comment = "";
	private $cashier = "";
	
	public function __construct()
	{
		$this->timestamp = new \DateTime("now");
	}
	
	public function isInputOuttake()
	{
		return ($this->input == "0.0" && $this->outtake == "0.0");
	}
	
	public function getId()
	{
		return $this->id_vault;
	}

	public function setTimestampNow()
	{
		$this->timestamp = new \DateTime("now");
		return $this;
	}
	
	public function setTimestamp($timestamp)
	{
		$this->timestamp = new \DateTime($timestamp);
		return $this;
	}
	
	public function getTimestamp()
	{
		return $this->timestamp->format('Y-m-d H:i:s');
	}
	
	public function setInput($input)
	{
		$this->input = ($input == "") ? 0.0 : $input;
		return $this;
	}

	public function getInput()
	{
		return sprintf("%01.2f", $this->input);
	}

	public function setOuttake($outtake)
	{
		$this->outtake = ($outtake == "") ? 0.0 : $outtake;
		return $this;
	}

	public function getOuttake()
	{
		return sprintf("%01.2f", $this->outtake);
	}

	public function setComment($comment)
	{
		$this->comment = $comment;
		return $this;
	}

	public function getComment()
	{
		return $this->comment;
	}

	public function setCashier($cashier)
	{
		$this->cashier = $cashier;
		return $this;
	}

	public function getCashier()
	{
		return $this->cashier;
	}
}
