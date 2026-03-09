<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CampeonatoClubes
 */
class CampeonatoClubes
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $club;

    /**
     * @var boolean
     */
    private $pordefecto;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Club
     */
    private $idclub;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Campeonato
     */
    private $idcampeonato;


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
     * Set club
     *
     * @param string $club
     * @return CampeonatoClubes
     */
    public function setClub($club)
    {
        $this->club = $club;

        return $this;
    }

    /**
     * Get club
     *
     * @return string 
     */
    public function getClub()
    {
        return $this->club;
    }

    /**
     * Set pordefecto
     *
     * @param boolean $pordefecto
     * @return CampeonatoClubes
     */
    public function setPordefecto($pordefecto)
    {
        $this->pordefecto = $pordefecto;

        return $this;
    }

    /**
     * Get pordefecto
     *
     * @return boolean 
     */
    public function getPordefecto()
    {
        return $this->pordefecto;
    }

    /**
     * Set idclub
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Club $idclub
     * @return CampeonatoClubes
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
     * Set idcampeonato
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Campeonato $idcampeonato
     * @return CampeonatoClubes
     */
    public function setIdcampeonato(\FraterSoft\PiaWebBundle\Entity\Campeonato $idcampeonato = null)
    {
        $this->idcampeonato = $idcampeonato;

        return $this;
    }

    /**
     * Get idcampeonato
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Campeonato 
     */
    public function getIdcampeonato()
    {
        return $this->idcampeonato;
    }
}
