<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class OrganizadorFormaspago
{
    private $id;

    private $idorganizador;

    private $idformapago;

    private $observacion;

    public function getId()
    {
        return $this->id;
    }

    public function setIdorganizador(\FraterSoft\PiaWebBundle\Entity\Organizador $idorganizador = null)
    {
        $this->idorganizador = $idorganizador;

        return $this;
    }

    public function getIdorganizador()
    {
        return $this->idorganizador;
    }

    public function setIdformapago(\FraterSoft\PiaWebBundle\Entity\Formaspago $idformapago = null)
    {
        $this->idformapago = $idformapago;

        return $this;
    }

    public function getIdformapago()
    {
        return $this->idformapago;
    }

    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;

        return $this;
    }

    public function getObservacion()
    {
        return $this->observacion;
    }
}
