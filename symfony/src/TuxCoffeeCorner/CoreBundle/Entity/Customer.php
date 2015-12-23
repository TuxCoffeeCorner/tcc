<?php

namespace TuxCoffeeCorner\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Customer
{
	private $id_customer;
	private $name = "Anonymus";
	private $email;
	private $credit = 0.0;
	private $active = true;
	private $favorite;
	private $updated;

	public function __construct()
	{
		$this->updated = new \DateTime("now");
	}
	
	public function setId($id)
	{
		$this->id_customer = $id;
		return $this;
	}
	
	public function getId()
	{
		return $this->id_customer;
	}
	
	public function getFormatedId()
	{
		return sprintf("%08s", $this->id_customer);
	}

	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setEmail($email)
	{
		$this->email = $email;
		return $this;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function setCredit($credit)
	{
		$this->credit = $credit;
		return $this;
	}
	
	public function charge($amount)
	{
		$this->setUpdated();
		$this->credit = $this->credit + $amount;
		return $this;
	}

	public function getCredit()
	{
		return sprintf("%01.2f", $this->credit);
	}
	
	public function getDebt()
	{
		if ($this->credit < 0)
			return sprintf("%01.2f", abs($this->credit));
		else
			return sprintf("%01.2f", 0);
	}

	public function setActive($active)
	{
		$this->active = $active;
		return $this;
	}

	public function getActive()
	{
		return $this->active;
	}

	public function setFavorite(\TuxCoffeeCorner\CoreBundle\Entity\Product $favorite)
	{
		$this->favorite = $favorite;
		return $this;
	}

	public function getFavorite()
	{
		return $this->favorite;
	}

	public function setUpdated()
	{
		$this->active = true;
		$this->updated = new \DateTime("now");
		return $this;
	}

	public function getUpdated()
	{
		return $this->updated->format('Y-m-d H:i:s');
	}
}
