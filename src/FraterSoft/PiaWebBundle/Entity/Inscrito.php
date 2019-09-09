<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inscrito
 */
class Inscrito
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $numero;

    /**
     * @var \DateTime
     */
    private $fechahora;

    /**
     * @var string
     */
    private $punto;

    /**
     * @var string
     */
    private $equipo;

    /**
     * @var float
     */
    private $precio;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $publicado;

    /**
     * @var integer
     */
    private $secuencia;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Pago
     */
    private $idpago;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Competencia
     */
    private $idcompetencia;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Competidor
     */
    private $idpia;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Evento
     */
    private $idevento;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Categoria
     */
    private $idcategoria;
    
    private $notificado;    

    /**
     * @var string
     */
    private $idgrupo;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $pagos;

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
     * @param integer $numero
     * @return Inscrito
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set fechahora
     *
     * @param \DateTime $fechahora
     * @return Inscrito
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
     * Set punto
     *
     * @param string $punto
     * @return Inscrito
     */
    public function setPunto($punto)
    {
        $this->punto = $punto;

        return $this;
    }

    /**
     * Get punto
     *
     * @return string 
     */
    public function getPunto()
    {
        return $this->punto;
    }

    /**
     * Set equipo
     *
     * @param string $equipo
     * @return Inscrito
     */
    public function setEquipo($equipo)
    {
        $this->equipo = $equipo;

        return $this;
    }

    /**
     * Get equipo
     *
     * @return string 
     */
    public function getEquipo()
    {
        return $this->equipo;
    }

    /**
     * Set precio
     *
     * @param float $precio
     * @return Inscrito
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
     * Set status
     *
     * @param integer $status
     * @return Inscrito
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set publicado
     *
     * @param integer $publicado
     * @return Inscrito
     */
    public function setPublicado($publicado)
    {
        $this->publicado = $publicado;

        return $this;
    }

    /**
     * Get publicado
     *
     * @return integer 
     */
    public function getPublicado()
    {
        return $this->publicado;
    }

    /**
     * Set secuencia
     *
     * @param integer $secuencia
     * @return Inscrito
     */
    public function setSecuencia($secuencia)
    {
        $this->secuencia = $secuencia;

        return $this;
    }

    /**
     * Get secuencia
     *
     * @return integer 
     */
    public function getSecuencia()
    {
        return $this->secuencia;
    }

    /**
     * Set idpago
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Pago $idpago
     * @return Inscrito
     */
    public function setIdpago(\FraterSoft\PiaWebBundle\Entity\Pago $idpago = null)
    {
        $this->idpago = $idpago;
        return $this;
    }

    /**
     * Get idpago
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Pago 
     */
    public function getIdpago()
    {
        return $this->idpago;
    }

    /**
     * Set idcompetencia
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Competencia $idcompetencia
     * @return Inscrito
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
     * Set idpia
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Competidor $idpia
     * @return Inscrito
     */
    public function setIdpia(\FraterSoft\PiaWebBundle\Entity\Competidor $idpia = null)
    {
        $this->idpia = $idpia;

        return $this;
    }

    /**
     * Get idpia
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Competidor 
     */
    public function getIdpia()
    {
        return $this->idpia;
    }

    /**
     * Set idevento
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Evento $idevento
     * @return Inscrito
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
     * Set idcategoria
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Categoria $idcategoria
     * @return Inscrito
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
     * Set notificado
     *
     * @param boolean $notificado
     * @return Inscrito
     */
    public function setNotificado($notificado)
    {
        $this->notificado = $notificado;

        return $this;
    }

    /**
     * Get automatica
     *
     * @return boolean 
     */
    public function getNotificado()
    {
        return $this->notificado;
    }    
    
    /**
     * to string
     *
     * @return string 
     */
    public function __toString() {
        return $this->nombre;
    }       
    
    /**
     * Set idgrupo
     *
     * @param string $idgrupo
     * @return Inscrito
     */
    public function setIdgrupo($idgrupo)
    {
        $this->idgrupo = $idgrupo;

        return $this;
    }

    /**
     * Get idgrupo
     *
     * @return string 
     */
    public function getIdgrupo()
    {
        return $this->idgrupo;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pagos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add pagos
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Pago $pagos
     * @return Inscrito
     */
    public function addPago(\FraterSoft\PiaWebBundle\Entity\Pago $pagos)
    {
        $this->pagos[] = $pagos;
    
        return $this;
    }

    /**
     * Remove pagos
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Pago $pagos
     */
    public function removePago(\FraterSoft\PiaWebBundle\Entity\Pago $pagos)
    {
        $this->pagos->removeElement($pagos);
    }

    /**
     * Get pagos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPagos()
    {
        return $this->pagos;
    }
}
