<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ciudad
 */
class Ciudad
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
     * @var \FraterSoft\PiaWebBundle\Entity\Estado
     */
    private $idestado;


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
     * @return Ciudad
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
     * Set idestado
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Estado $idestado
     * @return Ciudad
     */
    public function setIdestado(\FraterSoft\PiaWebBundle\Entity\Estado $idestado = null)
    {
        $this->idestado = $idestado;

        return $this;
    }

    /**
     * Get idestado
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Estado 
     */
    public function getIdestado()
    {
        return $this->idestado;
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
