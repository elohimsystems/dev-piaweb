<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Organizadorcuentas
 */
class Organizadorcuentas
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $numero;

    /**
     * @var string
     */
    private $titular;

    /**
     * @var string
     */
    private $identificacion;

    /**
     * @var string
     */
    private $correo;

    /**
     * @var integer
     */
    private $tipo;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Banco
     */
    private $idbanco;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Organizador
     */
    private $idorganizador;


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
     * Set numero
     *
     * @param string $numero
     * @return Organizadorcuentas
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    
        return $this;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set titular
     *
     * @param string $titular
     * @return Organizadorcuentas
     */
    public function setTitular($titular)
    {
        $this->titular = $titular;
    
        return $this;
    }

    /**
     * Get titular
     *
     * @return string 
     */
    public function getTitular()
    {
        return $this->titular;
    }

    /**
     * Set identificacion
     *
     * @param string $identificacion
     * @return Organizadorcuentas
     */
    public function setIdentificacion($identificacion)
    {
        $this->identificacion = $identificacion;
    
        return $this;
    }

    /**
     * Get identificacion
     *
     * @return string 
     */
    public function getIdentificacion()
    {
        return $this->identificacion;
    }

    /**
     * Set correo
     *
     * @param string $correo
     * @return Organizadorcuentas
     */
    public function setCorreo($correo)
    {
        $this->correo = $correo;
    
        return $this;
    }

    /**
     * Get correo
     *
     * @return string 
     */
    public function getCorreo()
    {
        return $this->correo;
    }

    /**
     * Set tipo
     *
     * @param integer $tipo
     * @return Organizadorcuentas
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    
        return $this;
    }

    /**
     * Get tipo
     *
     * @return integer 
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set idbanco
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Banco $idbanco
     * @return Organizadorcuentas
     */
    public function setIdbanco(\FraterSoft\PiaWebBundle\Entity\Banco $idbanco = null)
    {
        $this->idbanco = $idbanco;
    
        return $this;
    }

    /**
     * Get idbanco
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Banco 
     */
    public function getIdbanco()
    {
        return $this->idbanco;
    }

    /**
     * Set idorganizador
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Organizador $idorganizador
     * @return Organizadorcuentas
     */
    public function setIdorganizador(\FraterSoft\PiaWebBundle\Entity\Organizador $idorganizador = null)
    {
        $this->idorganizador = $idorganizador;
    
        return $this;
    }

    /**
     * Get idorganizador
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Organizador 
     */
    public function getIdorganizador()
    {
        return $this->idorganizador;
    }
    
    /**
     * to string
     *
     * @return string 
     */
    public function __toString() {
        return $this->idbanco->getNombre();
    }        
    
}
