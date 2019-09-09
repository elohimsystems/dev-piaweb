<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\EntityRepository;

class GrupoRepository extends EntityRepository {
    
    public function buscarPorEvento($idevento){
        return $this->getEntityManager()
            ->createQuery(
                "select c from FraterSoftPiaWebBundle:Competencia c "
                    //. "LEFT JOIN g.idcompetencia c "
                . "where c.idevento = " . $idevento
            )
            ->getResult();
    }    

}
