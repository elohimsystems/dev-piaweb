<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Preciosevento
 */
class Preciosevento
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
     * @var string
     */
    private $texto;

    /**
     * @var string
     */
    private $imagen;

    /**
     * @var string
     */
    private $moneda;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Evento
     */
    private $idevento;

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
     * Set precio
     *
     * @param float $precio
     * @return Preciosevento
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
     * @return Preciosevento
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
     * @param $hasta
     * @return Preciosevento
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
     * @return Preciosevento
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
     * Set texto
     *
     * @param string $texto
     * @return Preciosevento
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;

        return $this;
    }

    /**
     * Get texto
     *
     * @return string 
     */
    public function getTexto()
    {
        return $this->texto;
    }

    /**
     * Set imagen
     *
     * @param string $imagen
     * @return Preciosevento
     */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;

        return $this;
    }

    /**
     * Get $imagen
     *
     * @return string 
     */
    public function getImagen()
    {
        return $this->imagen;
    }

    /**
     * Set moneda
     *
     * @param string $moneda
     * @return Preciosevento
     */
    public function setMoneda($moneda)
    {
        $this->moneda = $moneda;

        return $this;
    }

    /**
     * Get $moneda
     *
     * @return string 
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * Set idevento
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Evento $idevento
     * @return Preciosevento
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
     * Set idmoneda
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Evento $idmoneda
     * @return Preciosevento
     */
    public function setIdmoneda(\FraterSoft\PiaWebBundle\Entity\Moneda $idmoneda = null)
    {
        $this->idmoneda = $idmoneda;

        return $this;
    }

    /**
     * Get idmoneda
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Evento 
     */
    public function getIdmoneda()
    {
        return $this->idmoneda;
    }
    
}
