<?php

namespace TuxCoffeeCorner\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Product
{
	private $id_product;
	private $name;
	private $price = 0.0;
	private $barcode;
	private $image = 'NoImage.jpg';

	public function getId()
	{
		return $this->id_product;
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

	public function setPrice($price)
	{
		$this->price = ($price == "") ? 0.0 : $price;
		return $this;
	}

	public function getPrice()
	{
		return sprintf("%01.2f", $this->price);
	}

	public function setBarcode($barcode)
	{
		$this->barcode = $barcode;
		return $this;
	}

	public function getBarcode()
	{
		return $this->barcode;
	}

	public function setImage($image)
	{
		$this->image = $image;
		return $this;
	}

	public function getImage()
	{
		return $this->image;
	}
}
