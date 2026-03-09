<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notificador
 */
class Notificador
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
     * @var string
     */
    private $destinatarios;

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
     * Set nombre
     *
     * @param string $nombre
     * @return Notificador
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
     * Set destinatarios
     *
     * @param string $destinatarios
     * @return Notificador
     */
    public function setDestinatarios($destinatarios)
    {
        $this->destinatarios = $destinatarios;
    
        return $this;
    }

    /**
     * Get destinatarios
     *
     * @return string 
     */
    public function getDestinatarios()
    {
        return $this->destinatarios;
    }

    /**
     * Set idorganizador
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Organizador $idorganizador
     * @return Notificador
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
}
