<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CampeonatoClubesRepository extends EntityRepository
{    
    public function arrayCampeonatoClubes($idcampeonato)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT cc.id,ca.id as idcampeonato,cl.id as idclub,cl.nombre as club,cc.pordefecto '
                    . 'FROM FraterSoftPiaWebBundle:CampeonatoClubes cc ' 
                    . 'JOIN cc.idclub cl '                  
                    . 'JOIN cc.idcampeonato ca '                  
                    . 'WHERE cc.idcampeonato =' . $idcampeonato . ''
            );
         return $query->getResult();
    }          
    
}
