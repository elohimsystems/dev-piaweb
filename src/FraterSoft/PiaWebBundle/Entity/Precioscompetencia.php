<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Precioscompetencia
 */
class Precioscompetencia
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var float
     */
    private $precio;

    /**
     * @var integer
     */
    private $cantidad;

    /**
     * @var \DateTime
     */
    private $hasta;

    /**
     * @var string
     */
    private $prioridad;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Competencia
     */
    private $idcompetencia;


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
     * Set precio
     *
     * @param float $precio
     * @return Precioscompetencia
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio
     *
     * @return float 
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     * @return Precioscompetencia
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer 
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set hasta
     *
     * @param \DateTime $hasta
     * @return Precioscompetencia
     */
    public function setHasta($hasta)
    {
        $this->hasta = $hasta;

        return $this;
    }

    /**
     * Get hasta
     *
     * @return \DateTime 
     */
    public function getHasta()
    {
        return $this->hasta;
    }

    /**
     * Set prioridad
     *
     * @param string $prioridad
     * @return Precioscompetencia
     */
    public function setPrioridad($prioridad)
    {
        $this->prioridad = $prioridad;

        return $this;
    }

    /**
     * Get prioridad
     *
     * @return string 
     */
    public function getPrioridad()
    {
        return $this->prioridad;
    }

    /**
     * Set idcompetencia
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Competencia $idcompetencia
     * @return Precioscompetencia
     */
    public function setIdcompetencia(\FraterSoft\PiaWebBundle\Entity\Competencia $idcompetencia = null)
    {
        $this->idcompetencia = $idcompetencia;

        return $this;
    }

    /**
     * Get idcompetencia
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Competencia 
     */
    public function getIdcompetencia()
    {
        return $this->idcompetencia;
    }
}
