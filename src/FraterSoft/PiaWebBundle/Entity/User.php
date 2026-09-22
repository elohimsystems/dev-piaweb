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

    /**
     * FOSUserBundle solo genera el salt en el constructor (Model\User::__construct)
     * y nunca expone un setter - hace falta uno propio para poder asignarle un
     * salt nuevo a una cuenta ya existente (creada sin salt desde la app movil,
     * con bcrypt) al establecer una clave nueva por este sistema. Ver
     * ResettingController::resetAction.
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }
}
