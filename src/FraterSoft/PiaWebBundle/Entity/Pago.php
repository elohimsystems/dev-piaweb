<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pago
 */
class Pago
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $fechahora;

    /**
     * @var float
     */
    private $monto;

    /**
     * @var string
     */
    private $moneda;

    /**
     * @var string
     */
    private $referencia;

    /**
     * @var string
     */
    private $comprobante;

    /**
     * @var string
     */
    private $tipo;

    /**
     * @var string
     */
    private $banco;
    
    /**
     * @var string
     */
    private $conciliado;
    
    /**
     * @var \DateTime
     */
    private $conciliadoel;    

    /**
     * @var \Bigint
     */
    private $idliquidacion;        

    /**
     * @var \DateTime
     */
    private $notificado;
    
    /**
     * @var string
     */
    private $texto;    
    
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
     * Set fechahora
     *
     * @param \DateTime $fechahora
     * @return Pago
     */
    public function setFechahora($fechahora)
    {
        $this->fechahora = $fechahora;

        return $this;
    }

    /**
     * Get fechahora
     *
     * @return \DateTime 
     */
    public function getFechahora()
    {
        return $this->fechahora;
    }

    /**
     * Set monto
     *
     * @param float $monto
     * @return Pago
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return float 
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set moneda
     *
     * @param string $moneda
     * @return Pago
     */
    public function setMoneda($moneda)
    {
        $this->moneda = $moneda;

        return $this;
    }

    /**
     * Get moneda
     *
     * @return string 
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * Set referencia
     *
     * @param string $referencia
     * @return Pago
     */
    public function setReferencia($referencia)
    {
        $this->referencia = $referencia;

        return $this;
    }

    /**
     * Get referencia
     *
     * @return string 
     */
    public function getReferencia()
    {
        return $this->referencia;
    }

    /**
     * Set comprobante
     *
     * @param string $comprobante
     * @return Pago
     */
    public function setComprobante($comprobante)
    {
        $this->comprobante = $comprobante;

        return $this;
    }

    /**
     * Get comprobante
     *
     * @return string 
     */
    public function getComprobante()
    {
        return $this->comprobante;
    }
    
    /**
     * Set tipo
     *
     * @param string $tipo
     * @return Pago
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
     * Set banco
     *
     * @param string $tipo
     * @return Pago
     */
    public function setBanco($banco)
    {
        $this->banco = $banco;

        return $this;
    }

    /**
     * Get banco
     *
     * @return string 
     */
    public function getBanco()
    {
        return $this->banco;
    }

    /**
     * Set conciliado
     *
     * @param boolean $conciliado
     * @return Pago
     */
    public function setConciliado($conciliado)
    {
        $this->conciliado = $conciliado;

        return $this;
    }

    /**
     * Get conciliado
     *
     * @return string 
     */
    public function getConciliado()
    {
        return $this->conciliado;
    }

    /**
     * Set conciliadoel
     *
     * @param \DateTime $conciliadoel
     * @return Pago
     */
    public function setConciliadoel($conciliadoel)
    {
        $this->conciliadoel = $conciliadoel;

        return $this;
    }

    /**
     * Get conciliadoel
     *
     * @return \DateTime  
     */
    public function getConciliadoel()
    {
        return $this->conciliadoel;
    }    
    
    /**
     * Set idliquidacion
     *
     * @param \Bigint $idliquidacion
     * @return Pago
     */
    public function setIdliquidacion($idliquidacion)
    {
        $this->idliquidacion = $idliquidacion;

        return $this;
    }

    /**
     * Get idliquidacion
     *
     * @return \Bigint  
     */
    public function getIdliquidacion()
    {
        return $this->idliquidacion;
    }      
    
    /**
     * Set notificado
     *
     * @param \Datetime $notificado
     * @return Pago
     */
    public function setNotificado($notificado)
    {
        $this->notificado = $notificado;

        return $this;
    }

    /**
     * Get notificado
     *
     * @return \Datetime  
     */
    public function getNotificado()
    {
        return $this->notificado;
    }      
    
    /**
     * Set texto
     *
     * @param string $texto
     * @return Pago
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
}
