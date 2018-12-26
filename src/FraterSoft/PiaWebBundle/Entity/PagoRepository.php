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
                    . ' WHERE p.id IN (SELECT pi.id FROM FraterSoftPiaWebBundle:Inscrito i JOIN i.idpago pi'
                    . ' WHERE i.secuencia IN (' . $ids . ') AND i.idevento=' . $idevento . ')'
            )
            ->getResult();
    }     
}