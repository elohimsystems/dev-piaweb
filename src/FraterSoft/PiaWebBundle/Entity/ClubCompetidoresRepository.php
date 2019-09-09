<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ClubCompetidoresRepository extends EntityRepository
{    
    public function arrayClubCompetidores($usuario)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT cc.id,cl.id as idclub,cl.nombre as club, cc.rol '
                    . 'FROM FraterSoftPiaWebBundle:ClubCompetidores cc '
                    . 'JOIN cc.idclub cl '
                    . 'JOIN cc.idpia co '
                    . 'WHERE co.emailpersonal =\'' . $usuario . '\''
            );
         return $query->getResult();
    }          
    
}
