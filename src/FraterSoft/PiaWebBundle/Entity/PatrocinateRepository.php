<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PatrocinanteRepository extends EntityRepository
{
    public function arrayListas($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                "select * from FraterSoftPiaWebBundle:Patrocinante pa "
                    . "JOIN pa.idevento e "
                . "where pa.idevento = " . $idevento 
            )
            ->getResult();
    }  
}