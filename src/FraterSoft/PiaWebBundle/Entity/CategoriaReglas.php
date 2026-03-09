<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoriaReglas
 */
class CategoriaReglas
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $atributo;

    /**
     * @var string
     */
    private $tipo;

    /**
     * @var string
     */
    private $valor1;

    /**
     * @var string
     */
    private $valor2;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Categoria
     */
    private $idcategoria;

    /**
     * @var string
     */
    private $accion;

    /**
     * @var integer
     */
    private $cantidad;

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
     * Set atributo
     *
     * @param string $atributo
     * @return CategoriaReglas
     */
    public function setAtributo($atributo)
    {
        $this->atributo = $atributo;

        return $this;
    }

    /**
     * Get atributo
     *
     * @return string 
     */
    public function getAtributo()
    {
        return $this->atributo;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     * @return CategoriaReglas
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
     * Set valor1
     *
     * @param string $valor1
     * @return CategoriaReglas
     */
    public function setValor1($valor1)
    {
        $this->valor1 = $valor1;

        return $this;
    }

    /**
     * Get valor1
     *
     * @return string 
     */
    public function getValor1()
    {
        return $this->valor1;
    }

    /**
     * Set valor2
     *
     * @param string $valor2
     * @return CategoriaReglas
     */
    public function setValor2($valor2)
    {
        $this->valor2 = $valor2;

        return $this;
    }

    /**
     * Get valor2
     *
     * @return string 
     */
    public function getValor2()
    {
        return $this->valor2;
    }

    /**
     * Set idcategoria
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Categoria $idcategoria
     * @return CategoriaReglas
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
     * Set accion
     *
     * @param string $accion
     * @return CategoriaReglas
     */
    public function setAccion($accion)
    {
        $this->accion = $accion;

        return $this;
    }

    /**
     * Get accion
     *
     * @return string 
     */
    public function getAccion()
    {
        return $this->accion;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     * @return CategoriaReglas
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

}
