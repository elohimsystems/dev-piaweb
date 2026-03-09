<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Competencia
 */
class Competencia
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $descripcion;

    /**
     * @var \DateTime
     */
    private $fechacierre;

    /**
     * @var integer
     */
    private $cupomaximo;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Evento
     */
    private $idevento;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Grupo
     */
    private $grupo;

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
     * Set descripcion
     *
     * @param string $descripcion
     * @return Competencia
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set fechacierre
     *
     * @param \DateTime $fechacierre
     * @return Competencia
     */
    public function setFechacierre($fechacierre)
    {
        $this->fechacierre = $fechacierre;

        return $this;
    }

    /**
     * Get fechacierre
     *
     * @return \DateTime 
     */
    public function getFechacierre()
    {
        return $this->fechacierre;
    }

    /**
     * Set cupomaximo
     *
     * @param integer $cupomaximo
     * @return Competencia
     */
    public function setCupomaximo($cupomaximo)
    {
        $this->cupomaximo = $cupomaximo;

        return $this;
    }

    /**
     * Get cupomaximo
     *
     * @return integer 
     */
    public function getCupomaximo()
    {
        return $this->cupomaximo;
    }

    /**
     * Set idevento
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Evento $idevento
     * @return Competencia
     */
    public function setIdevento(\FraterSoft\PiaWebBundle\Entity\Evento $idevento = null)
    {
        $this->idevento = $idevento;

        return $this;
    }

    /**
     * Get idevento
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Evento 
     */
    public function getIdevento()
    {
        return $this->idevento;
    }
    
    /**
     * to string
     *
     * @return string 
     */
    public function __toString() {
        return $this->descripcion;
    }      
    
    /**
     * Set Grupo
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Grupo $grupo
     * @return Evento
     */
    public function setGrupo(\FraterSoft\PiaWebBundle\Entity\Grupo $grupo = null)
    {
        $this->grupo = $grupo;
        return $this;
    }

    /**
     * Get Grupo
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Grupo 
     */
    public function getGrupo()
    {
        return $this->grupo;
    }
    
}
