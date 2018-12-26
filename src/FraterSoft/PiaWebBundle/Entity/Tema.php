<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tema
 */
class Tema
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $styles;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Evento
     */
    private $idevento;


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
     * Set styles
     *
     * @param string $styles
     * @return Tema
     */
    public function setStyles($styles)
    {
        $this->styles = $styles;

        return $this;
    }

    /**
     * Get styles
     *
     * @return string 
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * Set idevento
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Evento $idevento
     * @return Tema
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
     * to string
     *
     * @return string 
     */
    public function __toString() {
        return $this->styles;
    }     
}
