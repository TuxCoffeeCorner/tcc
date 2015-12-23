<?php

namespace TuxCoffeeCorner\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Mail
{
    private $id_mail;
    private $subject;
    private $body;
    private $identifier;
    private $header_to;
    private $header_from;
    private $header_cc;

    public function getId()
    {
        return $this->id_mail;
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }
    
    public function getBodyShort()
    {
    	return substr($this->body, 0, 70);
    }

    public function setTo($to)
    {
        $this->header_to = $to;
        return $this;
    }

    public function getTo()
    {
        return $this->header_to;
    }

    public function setFrom($from)
    {
        $this->header_from = $from;
        return $this;
    }

    public function getFrom()
    {
        return $this->header_from;
    }

    public function setCc($cc)
    {
        $this->header_cc = $cc;
        return $this;
    }

    public function getCc()
    {
        return $this->header_cc;
    }
}
