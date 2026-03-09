<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Numeracionexterna
 */
class Numeracionexterna
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $numero;

    /**
     * @var boolean
     */
    private $asignado;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Numeracion
     */
    private $idnumeracion;


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
     * Set numero
     *
     * @param integer $numero
     * @return Numeracionexterna
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set asignado
     *
     * @param boolean $asignado
     * @return Numeracionexterna
     */
    public function setAsignado($asignado)
    {
        $this->asignado = $asignado;

        return $this;
    }

    /**
     * Get asignado
     *
     * @return boolean 
     */
    public function getAsignado()
    {
        return $this->asignado;
    }

    /**
     * Set idnumeracion
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Numeracion $idnumeracion
     * @return Numeracionexterna
     */
    public function setIdnumeracion(\FraterSoft\PiaWebBundle\Entity\Numeracion $idnumeracion = null)
    {
        $this->idnumeracion = $idnumeracion;

        return $this;
    }

    /**
     * Get idnumeracion
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Numeracion 
     */
    public function getIdnumeracion()
    {
        return $this->idnumeracion;
    }
}
