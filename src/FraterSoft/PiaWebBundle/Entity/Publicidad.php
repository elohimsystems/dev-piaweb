<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Publicidad
 */
class Publicidad
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $idubicacion;

    /**
     * @var integer
     */
    private $estatus;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Evento
     */
    private $idevento;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Patrocinante
     */
    private $idpatrocinante;

    /**
     * @var string
     */
    private $imagen;
    
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
     * Set idubicacion
     *
     * @param integer $idubicacion
     * @return Publicidad
     */
    public function setIdubicacion($idubicacion)
    {
        $this->idubicacion = $idubicacion;
    
        return $this;
    }

    /**
     * Get idubicacion
     *
     * @return integer 
     */
    public function getIdubicacion()
    {
        return $this->idubicacion;
    }

    /**
     * Set estatus
     *
     * @param integer $estatus
     * @return Publicidad
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;
    
        return $this;
    }

    /**
     * Get estatus
     *
     * @return integer 
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * Set idevento
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Evento $idevento
     * @return Publicidad
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
     * Set idpatrocinante
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Patrocinante $idpatrocinante
     * @return Publicidad
     */
    public function setIdpatrocinante(\FraterSoft\PiaWebBundle\Entity\Patrocinante $idpatrocinante = null)
    {
        $this->idpatrocinante = $idpatrocinante;
    
        return $this;
    }

    /**
     * Get idpatrocinante
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Patrocinante 
     */
    public function getIdpatrocinante()
    {
        return $this->idpatrocinante;
    }
    
    /**
     * Set imagen
     *
     * @param integer $imagen
     * @return Publicidad
     */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
    
        return $this;
    }

    /**
     * Get imagen
     *
     * @return string 
     */
    public function getImagen()
    {
        return $this->imagen;
    }    
}
