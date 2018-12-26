<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Club
 */
class Club
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $nombre;

    /**
     * @var integer
     */
    private $tipoIdentificacion;

    /**
     * @var string
     */
    private $numIdentificacion;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $logonombre;

    /**
     * @var string
     */
    private $logoarchivo;

    /**
     * @var string
     */
    private $logo;

    /**
     * @var string
     */
    private $usuario;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Ciudad
     */
    private $idciudad;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Disciplina
     */
    private $iddisciplina;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Estado
     */
    private $idestado;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Pais
     */
    private $idpais;


    /**
     * to string
     *
     * @return string 
     */
    public function __toString() {
        return $this->nombre;
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
     * Set nombre
     *
     * @param string $nombre
     * @return Club
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set tipoIdentificacion
     *
     * @param integer $tipoIdentificacion
     * @return Club
     */
    public function setTipoIdentificacion($tipoIdentificacion)
    {
        $this->tipoIdentificacion = $tipoIdentificacion;

        return $this;
    }

    /**
     * Get tipoIdentificacion
     *
     * @return integer 
     */
    public function getTipoIdentificacion()
    {
        return $this->tipoIdentificacion;
    }

    /**
     * Set numIdentificacion
     *
     * @param string $numIdentificacion
     * @return Club
     */
    public function setNumIdentificacion($numIdentificacion)
    {
        $this->numIdentificacion = $numIdentificacion;

        return $this;
    }

    /**
     * Get numIdentificacion
     *
     * @return string 
     */
    public function getNumIdentificacion()
    {
        return $this->numIdentificacion;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Club
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set logonombre
     *
     * @param string $logonombre
     * @return Club
     */
    public function setLogonombre($logonombre)
    {
        $this->logonombre = $logonombre;

        return $this;
    }

    /**
     * Get logonombre
     *
     * @return string 
     */
    public function getLogonombre()
    {
        return $this->logonombre;
    }

    /**
     * Set logoarchivo
     *
     * @param string $logoarchivo
     * @return Club
     */
    public function setLogoarchivo($logoarchivo)
    {
        $this->logoarchivo = $logoarchivo;

        return $this;
    }

    /**
     * Get logoarchivo
     *
     * @return string 
     */
    public function getLogoarchivo()
    {
        return $this->logoarchivo;
    }

    /**
     * Set logo
     *
     * @param string $logo
     * @return Club
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set usuario
     *
     * @param string $usuario
     * @return Club
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set idciudad
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Ciudad $idciudad
     * @return Club
     */
    public function setIdciudad(\FraterSoft\PiaWebBundle\Entity\Ciudad $idciudad = null)
    {
        $this->idciudad = $idciudad;

        return $this;
    }

    /**
     * Get idciudad
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Ciudad 
     */
    public function getIdciudad()
    {
        return $this->idciudad;
    }

    /**
     * Set iddisciplina
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Disciplina $iddisciplina
     * @return Club
     */
    public function setIddisciplina(\FraterSoft\PiaWebBundle\Entity\Disciplina $iddisciplina = null)
    {
        $this->iddisciplina = $iddisciplina;

        return $this;
    }

    /**
     * Get iddisciplina
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Disciplina 
     */
    public function getIddisciplina()
    {
        return $this->iddisciplina;
    }

    /**
     * Set idestado
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Estado $idestado
     * @return Club
     */
    public function setIdestado(\FraterSoft\PiaWebBundle\Entity\Estado $idestado = null)
    {
        $this->idestado = $idestado;

        return $this;
    }

    /**
     * Get idestado
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Estado 
     */
    public function getIdestado()
    {
        return $this->idestado;
    }

    /**
     * Set idpais
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Pais $idpais
     * @return Club
     */
    public function setIdpais(\FraterSoft\PiaWebBundle\Entity\Pais $idpais = null)
    {
        $this->idpais = $idpais;

        return $this;
    }

    /**
     * Get idpais
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Pais 
     */
    public function getIdpais()
    {
        return $this->idpais;
    }
}
