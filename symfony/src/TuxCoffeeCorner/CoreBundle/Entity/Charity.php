<?php

namespace TuxCoffeeCorner\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Charity
{
	private $id_charity;
	private $barcode;
	private $organisation;
	private $beginn;
	private $ende;
	private $spendenstand;
	private $image = 'NoImage.jpg';

	public function getId()
	{
		return $this->id_charity;
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

	public function setOrganisation($organisation)
	{
		$this->organisation = $organisation;
		return $this;
	}

	public function getOrganisation()
	{
		return $this->organisation;
	}

	public function setBeginn($beginn)
	{
		$this->beginn = $beginn;
		return $this;
	}

	public function getBeginn()
	{
		return $this->beginn;
	}

	public function setEnde($ende)
	{
		$this->ende = $ende;
		return $this;
	}

	public function getEnde()
	{
		return $this->ende;
	}

	public function setSpendenstand($spendenstand)
	{
		$this->spendenstand = $spendenstand;
		return $this;
	}

	public function getSpendenstand()
	{
		return $this->spendenstand;
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

	public function donate()
	{
		$this->spendenstand += 1;
		return $this;
	}

	public function reset()
	{
		$this->spendenstand = 0;
		return $this;
	}
}
