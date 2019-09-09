<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Recarga
 */
class Recarga
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $idorganizador;

    /**
     * @var integer
     */
    private $idpais;

    /**
     * @var string
     */
    private $nombre;

    /**
     * @var float
     */
    private $valor;

    /**
     * @var float
     */
    private $porcentaje;

    /**
     * @var boolean
     */
    private $verporcentaje;

    /**
     * @var \DateTime
     */
    private $validohasta;

    /**
     * @var integer
     */
    private $estatus;

    /**
     * @var integer
     */
    private $idtiporecarga;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $idevento;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idevento = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set idorganizador
     *
     * @param integer $idorganizador
     * @return Recarga
     */
    public function setIdorganizador($idorganizador)
    {
        $this->idorganizador = $idorganizador;

        return $this;
    }

    /**
     * Get idorganizador
     *
     * @return integer 
     */
    public function getIdorganizador()
    {
        return $this->idorganizador;
    }

    /**
     * Set idpais
     *
     * @param integer $idpais
     * @return Recarga
     */
    public function setIdpais($idpais)
    {
        $this->idpais = $idpais;

        return $this;
    }

    /**
     * Get idpais
     *
     * @return integer 
     */
    public function getIdpais()
    {
        return $this->idpais;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Recarga
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
     * Set valor
     *
     * @param float $valor
     * @return Recarga
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return float 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set porcentaje
     *
     * @param float $porcentaje
     * @return Recarga
     */
    public function setPorcentaje($porcentaje)
    {
        $this->porcentaje = $porcentaje;

        return $this;
    }

    /**
     * Get porcentaje
     *
     * @return float 
     */
    public function getPorcentaje()
    {
        return $this->porcentaje;
    }

    /**
     * Set verporcentaje
     *
     * @param boolean $verporcentaje
     * @return Recarga
     */
    public function setVerporcentaje($verporcentaje)
    {
        $this->verporcentaje = $verporcentaje;

        return $this;
    }

    /**
     * Get verporcentaje
     *
     * @return boolean 
     */
    public function getVerporcentaje()
    {
        return $this->verporcentaje;
    }

    /**
     * Set validohasta
     *
     * @param \DateTime $validohasta
     * @return Recarga
     */
    public function setValidohasta($validohasta)
    {
        $this->validohasta = $validohasta;

        return $this;
    }

    /**
     * Get validohasta
     *
     * @return \DateTime 
     */
    public function getValidohasta()
    {
        return $this->validohasta;
    }

    /**
     * Set estatus
     *
     * @param integer $estatus
     * @return Recarga
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;

        return $this;
    }

    /**
     * Get estatus
     *
     * @return integer 
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * Set idtiporecarga
     *
     * @param integer $idtiporecarga
     * @return Recarga
     */
    public function setIdtiporecarga($idtiporecarga)
    {
        $this->idtiporecarga = $idtiporecarga;

        return $this;
    }

    /**
     * Get idtiporecarga
     *
     * @return integer 
     */
    public function getIdtiporecarga()
    {
        return $this->idtiporecarga;
    }

    /**
     * Add idevento
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Evento $idevento
     * @return Recarga
     */
    public function addIdevento(\FraterSoft\PiaWebBundle\Entity\Evento $idevento)
    {
        $this->idevento[] = $idevento;

        return $this;
    }

    /**
     * Remove idevento
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Evento $idevento
     */
    public function removeIdevento(\FraterSoft\PiaWebBundle\Entity\Evento $idevento)
    {
        $this->idevento->removeElement($idevento);
    }

    /**
     * Get idevento
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIdevento()
    {
        return $this->idevento;
    }
}
