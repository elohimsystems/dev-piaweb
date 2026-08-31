<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InscritoCompetencia
 */
class InscritoCompetencia
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
     * @var \FraterSoft\PiaWebBundle\Entity\Inscrito
     */
    private $idinscrito;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Competencia
     */
    private $idcompetencia;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Categoria
     */
    private $idcategoria;

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
     * @return InscritoCompetencia
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
     * Set idinscrito
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Inscrito $idinscrito
     * @return InscritoCompetencia
     */
    public function setIdinscrito(\FraterSoft\PiaWebBundle\Entity\Inscrito $idinscrito = null)
    {
        $this->idinscrito = $idinscrito;

        return $this;
    }

    /**
     * Get idinscrito
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Inscrito
     */
    public function getIdinscrito()
    {
        return $this->idinscrito;
    }

    /**
     * Set idcompetencia
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Competencia $idcompetencia
     * @return InscritoCompetencia
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

    /**
     * Set idcategoria
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Categoria $idcategoria
     * @return InscritoCompetencia
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
}
