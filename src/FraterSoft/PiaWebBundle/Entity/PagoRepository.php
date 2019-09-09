<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PagoRepository extends EntityRepository
{
    public function conciliarLote($ids,$idevento)
    {
        $date = new \DateTime('now');
        $datestr=$date->format('Y-m-d H:i:s');
        return $this->getEntityManager()
            ->createQuery(
                'UPDATE FraterSoftPiaWebBundle:Pago p'
                    . ' SET p.conciliado=TRUE, p.conciliadoel=\'' . $datestr . '\''
                    . ' WHERE p.idinscrito IN (SELECT i.id FROM FraterSoftPiaWebBundle:Inscrito i '
                    . ' WHERE i.secuencia IN (' . $ids . ') AND i.idevento=' . $idevento . ')'
            )
            ->getResult();
    }     

    public function findIdArray($id)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p,i FROM FraterSoftPiaWebBundle:Pago p '
                    . 'JOIN '
                        . 'p.idinscrito i '
                    . 'WHERE '
                        . 'p.idinscrito=i '
                        . 'and p.id=' . $id                   
            )
            ->getArrayResult();
    } 
    
}