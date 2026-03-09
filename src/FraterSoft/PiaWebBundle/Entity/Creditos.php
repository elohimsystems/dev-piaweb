<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Creditos
 */
class Creditos
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $cedula;

    /**
     * @var string
     */
    private $monto;

    /**
     * @var integer
     */
    private $idevento;

    /**
     * @var \DateTime
     */
    private $usadoel;

    /**
     * @var boolean
     */
    private $disponible;

    /**
     * @var integer
     */
    private $idmoneda;

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
     * Set cedula
     *
     * @param string $cedula
     * @return Creditos
     */
    public function setCedula($cedula)
    {
        $this->cedula = $cedula;
    
        return $this;
    }

    /**
     * Get cedula
     *
     * @return string 
     */
    public function getCedula()
    {
        return $this->cedula;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return Creditos
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;
    
        return $this;
    }

    /**
     * Get monto
     *
     * @return string 
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set idevento
     *
     * @param integer $idevento
     * @return Creditos
     */
    public function setIdevento($idevento)
    {
        $this->idevento = $idevento;
    
        return $this;
    }

    /**
     * Get idevento
     *
     * @return integer 
     */
    public function getIdevento()
    {
        return $this->idevento;
    }

    /**
     * Set usadoel
     *
     * @param \DateTime $usadoel
     * @return Creditos
     */
    public function setUsadoel($usadoel)
    {
        $this->usadoel = $usadoel;
    
        return $this;
    }

    /**
     * Get usadoel
     *
     * @return \DateTime 
     */
    public function getUsadoel()
    {
        return $this->usadoel;
    }

    /**
     * Set disponible
     *
     * @param boolean $disponible
     * @return Creditos
     */
    public function setDisponible($disponible)
    {
        $this->disponible = $disponible;
    
        return $this;
    }

    /**
     * Get disponible
     *
     * @return boolean 
     */
    public function getDisponible()
    {
        return $this->disponible;
    }
    /**
     * Set idmoneda
     *
     * @param boolean $idmoneda
     * @return Creditos
     */
    public function setIdmoneda($idmoneda)
    {
        $this->idmoneda = $idmoneda;
    
        return $this;
    }

    /**
     * Get idmoneda
     *
     * @return boolean 
     */
    public function getIdmoneda()
    {
        return $this->idmoneda;
    }
}
