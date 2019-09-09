<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClubCompetidores
 */
class ClubCompetidores
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $afiliadoel;

    /**
     * @var integer
     */
    private $rol;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Club
     */
    private $idclub;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Competidor
     */
    private $idpia;


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
     * Set afiliadoel
     *
     * @param \DateTime $afiliadoel
     * @return ClubCompetidores
     */
    public function setAfiliadoel($afiliadoel)
    {
        $this->afiliadoel = $afiliadoel;

        return $this;
    }

    /**
     * Get afiliadoel
     *
     * @return \DateTime 
     */
    public function getAfiliadoel()
    {
        return $this->afiliadoel;
    }

    /**
     * Set rol
     *
     * @param integer $rol
     * @return ClubCompetidores
     */
    public function setRol($rol)
    {
        $this->rol = $rol;

        return $this;
    }

    /**
     * Get rol
     *
     * @return integer 
     */
    public function getRol()
    {
        return $this->rol;
    }

    /**
     * Set idclub
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Club $idclub
     * @return ClubCompetidores
     */
    public function setIdclub(\FraterSoft\PiaWebBundle\Entity\Club $idclub = null)
    {
        $this->idclub = $idclub;

        return $this;
    }

    /**
     * Get idclub
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Club 
     */
    public function getIdclub()
    {
        return $this->idclub;
    }

    /**
     * Set idpia
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Competidor $idpia
     * @return ClubCompetidores
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
}
