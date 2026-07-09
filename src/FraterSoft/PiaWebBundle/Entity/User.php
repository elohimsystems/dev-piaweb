<?php

namespace FraterSoft\PiaWebBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;

class User extends BaseUser
{
    protected $id;
    private $idorganizador;

    public function __construct()
    {
        parent::__construct();
    }

    public function getIdorganizador()
    {
        return $this->idorganizador;
    }

    public function setIdorganizador(Organizador $idorganizador = null)
    {
        $this->idorganizador = $idorganizador;
        return $this;
    }
}
