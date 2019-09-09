<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Listasevento
 */
class Listasevento
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $codigo;
    
    /**
     * @var string
     */
    private $valor;
    
    /**
     * @var integer
     */
    private $limite;    

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Evento
     */
    private $idevento;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Atributo
     */
    private $idatributo;


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
     * Set codigo
     *
     * @param string $codigo
     * @return Listasevento
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }
    
    /**
     * Set valor
     *
     * @param string $valor
     * @return Listasevento
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string 
     */
    public function getValor()
    {
        return $this->valor;
    }
    
    /**
     * Set limite
     *
     * @param integer $limite
     * @return Listasevento
     */
    public function setLimite($limite)
    {
        $this->limite = $limite;

        return $this;
    }

    /**
     * Get limite
     *
     * @return integer 
     */
    public function getLimite()
    {
        return $this->limite;
    }    

    /**
     * Set idevento
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Evento $idevento
     * @return Listasevento
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
     * Set idatributo
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Atributo $idatributo
     * @return Listasevento
     */
    public function setIdatributo(\FraterSoft\PiaWebBundle\Entity\Atributo $idatributo = null)
    {
        $this->idatributo = $idatributo;

        return $this;
    }

    /**
     * Get idatributo
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Atributo 
     */
    public function getIdatributo()
    {
        return $this->idatributo;
    }
}
