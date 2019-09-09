<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Formaspago
 */
class Formaspago
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
     * @var integer
     */
    private $status;

    /**
     * @var string
     */
    private $icono;

    /**
     * @var string
     */
    private $parametros;

    /**
     * @var string
     */
    private $programa;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Moneda
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
     * Set nombre
     *
     * @param string $nombre
     * @return Formaspago
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
     * Set status
     *
     * @param integer $status
     * @return Formaspago
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * to string
     *
     * @return integer 
     */
    public function __toString() {
        return $this->getNombre();//$this->idformapago;
    }     

    /**
     * Set icono
     *
     * @param string $icono
     * @return Formaspago
     */
    public function setIcono($icono)
    {
        $this->icono = $icono;
    
        return $this;
    }

    /**
     * Get icono
     *
     * @return string 
     */
    public function getIcono()
    {
        return $this->icono;
    }

    /**
     * Set parametros
     *
     * @param string $parametros
     * @return Formaspago
     */
    public function setParametros($parametros)
    {
        $this->parametros = $parametros;
    
        return $this;
    }

    /**
     * Get parametros
     *
     * @return string 
     */
    public function getParametros()
    {
        return $this->parametros;
    }

    /**
     * Set programa
     *
     * @param string $programa
     * @return Formaspago
     */
    public function setPrograma($programa)
    {
        $this->programa = $programa;
    
        return $this;
    }

    /**
     * Get programa
     *
     * @return string 
     */
    public function getPrograma()
    {
        return $this->programa;
    }

    /**
     * Set idmoneda
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Moneda $idmoneda
     * @return Formaspago
     */
    public function setIdmoneda(\FraterSoft\PiaWebBundle\Entity\Moneda $idmoneda = null)
    {
        $this->idmoneda = $idmoneda;
    
        return $this;
    }

    /**
     * Get idmoneda
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Moneda 
     */
    public function getIdmoneda()
    {
        return $this->idmoneda;
    }
}
