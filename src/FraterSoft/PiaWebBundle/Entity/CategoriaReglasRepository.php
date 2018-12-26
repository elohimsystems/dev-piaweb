<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CategoriaReglasRepository extends EntityRepository
{

    public function arrayLista($idcategoria)
    {
        return $this->getEntityManager()
            ->createQuery(
                'select cr.id,ca.id as idcategoria,cr.atributo,cr.tipo,cr.valor1,cr.valor2  '
                    . 'from FraterSoftPiaWebBundle:Categoria ca inner join FraterSoftPiaWebBundle:CategoriaReglas cr WITH ca.id = cr.idcategoria
                    where cr.idcategoria=' . $idcategoria
            )
            ->getResult();
    }
}