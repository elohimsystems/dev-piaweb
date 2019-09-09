<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CompetidorRepository extends EntityRepository
{
    public function buscaValorCampo($id,$campo)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c.' . strtolower($campo) . ' FROM FraterSoftPiaWebBundle:Competidor c WHERE c.id =' . $id
            )
            ->getResult();
    }
    
    public function buscaAsociados($idocumento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c.id, c.iddocumento, c.nombre, c.apellido FROM FraterSoftPiaWebBundle:Competidor c '
                    . 'WHERE c.iddocumento like \'' . $idocumento . '-%\''
            )
            ->getResult();
    }
        
}