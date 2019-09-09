<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CompetenciaRepository extends EntityRepository
{
    public function cantidad($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT count(c.id) FROM FraterSoftPiaWebBundle:Competencia c WHERE c.idevento =' . $idevento
            )
            ->getResult();
    }
    
    public function arrayLista($idevento)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT c.id,e.id as idevento,c.descripcion,c.fechacierre,c.cupomaximo,g.integrantes,g.secuencia '
                    . 'FROM FraterSoftPiaWebBundle:Competencia c '
                    . 'JOIN c.idevento e '                  
                    . 'LEFT JOIN c.grupo g '
                    . 'WHERE c.idevento =' . $idevento 
            );
         return $query->getResult();
    }          
    
    public function categorias($idevento)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT ca '
                    . 'FROM FraterSoftPiaWebBundle:Categoria ca '
                    . 'JOIN ca.idcompetencia co '                                  
                    . 'WHERE co.idevento =' . $idevento 
            );
         return $query->getResult();
    }      
    
}