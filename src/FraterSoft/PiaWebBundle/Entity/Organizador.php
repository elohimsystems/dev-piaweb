<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Organizador
 */
class Organizador
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $nombre;

    /**
     * @var string
     */
    private $abreviado;

    /**
     * @var string
     */
    private $logo;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $banco;

    /**
     * @var integer
     */
    private $tipocuenta;

    /**
     * @var string
     */
    private $numerocuenta;

    /**
     * @var string
     */
    private $rif;

    /**
     * @var string
     */
    private $contacto;

    /**
     * @var string
     */
    private $telefonocontacto;

    /**
     * @var string
     */
    private $emailcontacto;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Organizador
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set abreviado
     *
     * @param string $abreviado
     * @return Organizador
     */
    public function setAbreviado($abreviado)
    {
        $this->abreviado = $abreviado;

        return $this;
    }

    /**
     * Get abreviado
     *
     * @return string 
     */
    public function getAbreviado()
    {
        return $this->abreviado;
    }

    /**
     * Set logo
     *
     * @param string $logo
     * @return Organizador
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Organizador
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set banco
     *
     * @param string $banco
     * @return Organizador
     */
    public function setBanco($banco)
    {
        $this->banco = $banco;

        return $this;
    }

    /**
     * Get banco
     *
     * @return string 
     */
    public function getBanco()
    {
        return $this->banco;
    }

    /**
     * Set tipocuenta
     *
     * @param integer $tipocuenta
     * @return Organizador
     */
    public function setTipocuenta($tipocuenta)
    {
        $this->tipocuenta = $tipocuenta;

        return $this;
    }

    /**
     * Get tipocuenta
     *
     * @return integer 
     */
    public function getTipocuenta()
    {
        return $this->tipocuenta;
    }

    /**
     * Set numerocuenta
     *
     * @param string $numerocuenta
     * @return Organizador
     */
    public function setNumerocuenta($numerocuenta)
    {
        $this->numerocuenta = $numerocuenta;

        return $this;
    }

    /**
     * Get numerocuenta
     *
     * @return string 
     */
    public function getNumerocuenta()
    {
        return $this->numerocuenta;
    }

    /**
     * Set rif
     *
     * @param string $rif
     * @return Organizador
     */
    public function setRif($rif)
    {
        $this->rif = $rif;

        return $this;
    }

    /**
     * Get rif
     *
     * @return string 
     */
    public function getRif()
    {
        return $this->rif;
    }

    /**
     * Set contacto
     *
     * @param string $contacto
     * @return Organizador
     */
    public function setContacto($contacto)
    {
        $this->contacto = $contacto;

        return $this;
    }

    /**
     * Get contacto
     *
     * @return string 
     */
    public function getContacto()
    {
        return $this->contacto;
    }

    /**
     * Set telefonocontacto
     *
     * @param string $telefonocontacto
     * @return Organizador
     */
    public function setTelefonocontacto($telefonocontacto)
    {
        $this->telefonocontacto = $telefonocontacto;

        return $this;
    }

    /**
     * Get telefonocontacto
     *
     * @return string 
     */
    public function getTelefonocontacto()
    {
        return $this->telefonocontacto;
    }

    /**
     * Set emailcontacto
     *
     * @param string $emailcontacto
     * @return Organizador
     */
    public function setEmailcontacto($emailcontacto)
    {
        $this->emailcontacto = $emailcontacto;

        return $this;
    }

    /**
     * Get emailcontacto
     *
     * @return string 
     */
    public function getEmailcontacto()
    {
        return $this->emailcontacto;
    }
    
    /**
     * to string
     *
     * @return string 
     */
    public function __toString() {
        return $this->nombre;
    }     
}
