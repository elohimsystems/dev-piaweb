<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\EntityRepository;

class EventoAtributosRepository extends EntityRepository
{
    public function atributosCriterios($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT ea FROM FraterSoftPiaWebBundle:EventoAtributos ea ' 
                    . 'JOIN ea.idatributo a '
                    . 'WHERE ea.idevento =\'' . $idevento . '\' and ea.criterio=true'
            )
            ->getResult();
    }    
    
    public function atributosEvento($idevento)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT ea,a FROM FraterSoftPiaWebBundle:EventoAtributos ea ' 
                    . 'JOIN ea.idatributo a '
                    . 'WHERE ea.idevento =' . $idevento . ' Order by ea.orden'
            );
         return $query->getResult();
    }          
    
    public function arrayAtributosEvento($idevento)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT ea.id,a.id as idatributo,e.id as idevento,ea.orden,a.nombre as atributo,ea.etiqueta,ea.requerido,ea.criterio,ea.busqueda,ea.mascara,ea.estadistica '
                    . 'FROM FraterSoftPiaWebBundle:EventoAtributos ea ' 
                    . 'JOIN ea.idatributo a '                  
                    . 'JOIN ea.idevento e '                  
                    . 'WHERE ea.idevento =' . $idevento . ' Order by ea.orden'
            );
         return $query->getResult();
    }          

    public function atributosOptions($idevento)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT ea,a FROM FraterSoftPiaWebBundle:EventoAtributos ea ' 
                    . 'JOIN ea.idatributo a '
                    . 'WHERE ea.idevento =' . $idevento . ' and a.tipodato=\'O\' Order by ea.orden'
            );
         return $query->getResult();
    }          

    public function atributosEstadistica($idevento)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT ea,a FROM FraterSoftPiaWebBundle:EventoAtributos ea ' 
                    . 'JOIN ea.idatributo a '
                    . 'WHERE ea.idevento =' . $idevento . ' and ea.estadistica = true'
                    . ' Order by ea.orden'
            );
         return $query->getResult();
    }          
    
    
}
