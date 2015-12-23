<?php

namespace TuxCoffeeCorner\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Config
{
    private $name;
    private $value;
    private $description;
    private $datatype;
    
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDatatype($datatype)
    {
        $this->datatype = $datatype;

        return $this;
    }

    public function getDatatype()
    {
        return $this->datatype;
    }
}
