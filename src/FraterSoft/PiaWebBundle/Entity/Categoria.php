<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Categoria
 */
class Categoria
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $idcampeonato;

    /**
     * @var string
     */
    private $descripcion;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Competencia
     */
    private $idcompetencia;


    /**
     * to string
     *
     * @return string 
     */
    public function __toString() {
        return $this->descripcion;
    }        

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
     * Set idcampeonato
     *
     * @param integer $idcampeonato
     * @return Categoria
     */
    public function setIdcampeonato($idcampeonato)
    {
        $this->idcampeonato = $idcampeonato;

        return $this;
    }

    /**
     * Get idcampeonato
     *
     * @return integer 
     */
    public function getIdcampeonato()
    {
        return $this->idcampeonato;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Categoria
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
     * Set idcompetencia
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Competencia $idcompetencia
     * @return Categoria
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
