<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Patrocinante
 */
class Patrocinante
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
    private $logo;

    /**
     * @var integer
     */
    private $idevento;

    /**
     * @var integer
     */
    private $estatus;


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
     * @return Patrocinante
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
     * Set logo
     *
     * @param string $logo
     * @return Patrocinante
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
     * Set idevento
     *
     * @param integer $idevento
     * @return Patrocinante
     */
    public function setIdevento($idevento)
    {
        $this->idevento = $idevento;

        return $this;
    }

    /**
     * Get idevento
     *
     * @return integer 
     */
    public function getIdevento()
    {
        return $this->idevento;
    }

    /**
     * Set estatus
     *
     * @param integer $estatus
     * @return Patrocinante
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
}
