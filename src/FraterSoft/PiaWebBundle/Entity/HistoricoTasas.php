<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HistoricoTasas
 */
class HistoricoTasas
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Moneda
     */
    private $moneda;

    /**
     * @var float
     */
    private $monto;

    /**
     * @var \DateTime
     */
    private $publicadoel;
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
     * Set publicadoel
     *
     * @param \DateTime $publicadoel
     * @return HistoricoTasas
     */
    public function setPublicadoel($publicadoel)
    {
        $this->publicadoel = $publicadoel;
    
        return $this;
    }

    /**
     * Get publicadoel
     *
     * @return \DateTime 
     */
    public function getPublicadoel()
    {
        return $this->publicadoel;
    }

    /**
     * Set idmoneda
     *
     * @param integer $idmoneda
     * @return HistoricoTasas
     */
    public function setIdmoneda($idmoneda)
    {
        $this->idmoneda = $idmoneda;
    
        return $this;
    }

    /**
     * Get idmoneda
     *
     * @return integer 
     */
    public function getIdmoneda()
    {
        return $this->idmoneda;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return HistoricoTasas
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
}
