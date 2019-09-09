<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Precioscategoria
 */
class Precioscategoria
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
     * @var \FraterSoft\PiaWebBundle\Entity\Categoria
     */
    private $idcategoria;

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
     * @return Precioscategoria
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
     * @return Precioscategoria
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
     * @return Precioscategoria
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
     * @return Precioscategoria
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
     * Set idcategoria
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Categoria $idcategoria
     * @return Precioscategoria
     */
    public function setIdcategoria(\FraterSoft\PiaWebBundle\Entity\Categoria $idcategoria = null)
    {
        $this->idcategoria = $idcategoria;

        return $this;
    }

    /**
     * Get idcategoria
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Categoria 
     */
    public function getIdcategoria()
    {
        return $this->idcategoria;
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
     * Set idmoneda
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Moneda $idmoneda
     * @return Precioscategoria
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
