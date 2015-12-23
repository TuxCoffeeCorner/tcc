<?php

namespace TuxCoffeeCorner\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class TccTrx
{
	private $id_tcctrx;
	private $timestamp;
	private $amount;
	private $status;
	private $customer;
	private $product;
	
	public function __construct()
	{
		$this->timestamp = new \DateTime("now");
	}

	public function getId()
	{
		return $this->id_tcctrx;
	}

	public function setTimestampNow()
	{
		$this->timestamp = new \DateTime("now");
		return $this;
	}

	public function getTimestamp()
	{
		return $this->timestamp->format('Y-m-d H:i:s');
	}

	public function setAmount($amount)
	{
		$this->amount = $amount;
		return $this;
	}

	public function getAmount()
	{
		return sprintf("%01.2f", $this->amount);
	}

	public function setStatus($status)
	{
		$this->status = $status;
		return $this;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function setCustomer(\TuxCoffeeCorner\CoreBundle\Entity\Customer $customer = null)
	{
		$this->customer = $customer;
		return $this;
	}

	public function getCustomer()
	{
		return $this->customer;
	}

	public function setProduct(\TuxCoffeeCorner\CoreBundle\Entity\Product $product = null)
	{
		$this->product = $product;
		return $this;
	}

	public function getProduct()
	{
		return $this->product;
	}
}
