<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Estado
 */
class Estado
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    public $nombre;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Pais
     */
    private $idpais;    

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
     * Set estado
     *
     * @param string $estado
     * @return Estado
     */
    public function setEstado($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string 
     */
    public function getEstado()
    {
        return $this->nombre;
    }
    
    /**
     * to string
     *
     * @return string 
     */
    public function __toString() {
        return $this->nombre;
    }      

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Estado
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
     * Set idpais
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Pais $idpais
     * @return Estado
     */
    public function setIdpais(\FraterSoft\PiaWebBundle\Entity\Pais $idpais = null)
    {
        $this->idpais = $idpais;

        return $this;
    }

    /**
     * Get idpais
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Pais 
     */
    public function getIdpais()
    {
        return $this->idpais;
    }
}
