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
     * @var boolean
     */
    private $conciliado;

    /**
     * @var \DateTime
     */
    private $conciliadoel;

    /**
     * @var integer
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
     * @var string
     */
    private $idpagador;

    /**
     * @var string
     */
    private $nombrepagador;

    /**
     * @var string
     */
    private $correopagador;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Formaspago
     */
    private $idformapago;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Banco
     */
    private $idbanco;

    /**
     * @var \DateTime
     */
    private $fechapago;

    /**
     * @var float
     */
    private $precio;

    /**
     * @var float
     */
    private $recargas;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Moneda
     */
    private $idmoneda;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Inscrito
     */
    private $idinscrito;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Organizadorcuentas
     */
    private $idcuenta;
    
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
     * @param string $banco
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
     * @return boolean 
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
     * @param integer $idliquidacion
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
     * @return integer 
     */
    public function getIdliquidacion()
    {
        return $this->idliquidacion;
    }

    /**
     * Set notificado
     *
     * @param \DateTime $notificado
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
     * @return \DateTime 
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

    /**
     * Set idpagador
     *
     * @param string $idpagador
     * @return Pago
     */
    public function setIdpagador($idpagador)
    {
        $this->idpagador = $idpagador;
    
        return $this;
    }

    /**
     * Get idpagador
     *
     * @return string 
     */
    public function getIdpagador()
    {
        return $this->idpagador;
    }

    /**
     * Set nombrepagador
     *
     * @param string $nombrepagador
     * @return Pago
     */
    public function setNombrepagador($nombrepagador)
    {
        $this->nombrepagador = $nombrepagador;
    
        return $this;
    }

    /**
     * Get nombrepagador
     *
     * @return string 
     */
    public function getNombrepagador()
    {
        return $this->nombrepagador;
    }

    /**
     * Set correopagador
     *
     * @param string $correopagador
     * @return Pago
     */
    public function setCorreopagador($correopagador)
    {
        $this->correopagador = $correopagador;
    
        return $this;
    }

    /**
     * Get correopagador
     *
     * @return string 
     */
    public function getCorreopagador()
    {
        return $this->correopagador;
    }

    /**
     * Set idformapago
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Formaspago $idformapago
     * @return Pago
     */
    public function setIdformapago(\FraterSoft\PiaWebBundle\Entity\Formaspago $idformapago = null)
    {
        $this->idformapago = $idformapago;
    
        return $this;
    }

    /**
     * Get idformapago
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Formaspago 
     */
    public function getIdformapago()
    {
        return $this->idformapago;
    }

    /**
     * Set idbanco
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Banco $idbanco
     * @return Pago
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
     * Set fechapago
     *
     * @param \DateTime $fechapago
     * @return Pago
     */
    public function setFechapago($fechapago)
    {
        $this->fechapago = $fechapago;
    
        return $this;
    }

    /**
     * Get fechapago
     *
     * @return \DateTime 
     */
    public function getFechapago()
    {
        return $this->fechapago;
    }

    /**
     * Set precio
     *
     * @param float $precio
     * @return Pago
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
     * Set recargas
     *
     * @param float $recargas
     * @return Pago
     */
    public function setRecargas($recargas)
    {
        $this->recargas = $recargas;
    
        return $this;
    }

    /**
     * Get recargas
     *
     * @return float 
     */
    public function getRecargas()
    {
        return $this->recargas;
    }

    /**
     * Set idmoneda
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Moneda $idmoneda
     * @return Pago
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


    /**
     * Set idinscrito
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Inscrito $idinscrito
     * @return Pago
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
     * Set idcuenta
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Organizadorcuentas $idcuenta
     * @return Pago
     */
    public function setIdcuenta(\FraterSoft\PiaWebBundle\Entity\Organizadorcuentas $idcuenta = null)
    {
        $this->idcuenta = $idcuenta;
    
        return $this;
    }

    /**
     * Get idcuenta
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Organizadorcuentas 
     */
    public function getIdcuenta()
    {
        return $this->idcuenta;
    }

}
