<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Atributo
 */
class Atributo
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
    private $tipodato;

    /**
     * @var integer
     */
    private $longitud;

    /**
     * @var string
     */
    private $tipo;


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
     * @return Atributo
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
     * Set tipodato
     *
     * @param string $tipodato
     * @return Atributo
     */
    public function setTipodato($tipodato)
    {
        $this->tipodato = $tipodato;

        return $this;
    }

    /**
     * Get tipodato
     *
     * @return string 
     */
    public function getTipodato()
    {
        return $this->tipodato;
    }

    /**
     * Set longitud
     *
     * @param integer $longitud
     * @return Atributo
     */
    public function setLongitud($longitud)
    {
        $this->longitud = $longitud;

        return $this;
    }

    /**
     * Get longitud
     *
     * @return integer 
     */
    public function getLongitud()
    {
        return $this->longitud;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     * @return Atributo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return $this->tipo;
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
