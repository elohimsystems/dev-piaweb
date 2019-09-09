<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CampeonatoCompetidoresRepository extends EntityRepository
{    
    public function arrayCampeonatoCompetidores($idcampeonato)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT co.id as id,ca.id as idcampeonato,co.id as idcompetidor,ct.id as idcategoria,'
                    . 'cl.id as idclub,co.iddocumento,co.nombre,co.apellido,cc.numero,ct.descripcion as categoria,cl.nombre as club,cc.afiliadoel '
                    . 'FROM FraterSoftPiaWebBundle:CampeonatoCompetidores cc ' 
                    . 'INNER JOIN FraterSoftPiaWebBundle:Club cl WITH cl.id = cc.idclub '                  
                    . 'INNER JOIN FraterSoftPiaWebBundle:Campeonato ca WITH ca.id = cc.idcampeonato '                  
                    . 'INNER JOIN FraterSoftPiaWebBundle:Categoria ct WITH ct.id = cc.idcategoria '                  
                    . 'INNER JOIN FraterSoftPiaWebBundle:Competidor co WITH co.id = cc.idcompetidor '                  
                    . 'WHERE cc.idcampeonato =' . $idcampeonato . ''
            );
         return $query->getResult();
    }          
    
}
