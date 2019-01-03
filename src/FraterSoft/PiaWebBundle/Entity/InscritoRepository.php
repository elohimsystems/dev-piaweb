<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\EntityRepository;

class InscritoRepository extends EntityRepository
{
    
    public function findIdArray($id)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i,p,c,co,e,ca,ev FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'LEFT JOIN '
                        . 'i.idpago p '
                    . 'JOIN '
                        . 'i.idcompetencia c '
                    . 'JOIN '
                        . 'i.idpia co '
                    . 'JOIN '
                        . 'co.idestado e '
                    . 'JOIN '
                        . 'i.idcategoria ca '                  
                    . 'JOIN '
                        . 'i.idevento ev '                  
                    . 'WHERE '
                        . '(i.idpago=p or i.idpago is null) '
                        . 'and i.idcompetencia=c '
                        . 'and i.idpia=co '
                        . 'and co.idestado=e '
                        . 'and i.idcategoria=ca '              
                        . 'and i.idevento=ev '              
                        . 'and i.id=' . $id                   
            )
            ->getArrayResult();
    } 
    
    public function buscarInscrito($idevento,$idpia)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i FROM FraterSoftPiaWebBundle:Inscrito i WHERE i.idpia =' . $idpia . ' and i.idevento=' . $idevento . " and i.status = 1"
            )
            ->getResult();
    }
    
    public function buscarSecuencias($secuencias,$idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'WHERE i.idevento=' . $idevento . ' and i.secuencia IN (' . $secuencias . ')'
            )
            ->getResult();
    }    
    
    public function maximaSecuencia($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT max(i.secuencia) as maxsec FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'WHERE i.idevento=' . $idevento
            )
            ->getResult()[0]['maxsec'];
    }
    
    public function listarConciliadas($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i,p FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'JOIN '
                        . 'i.idpago p '
                    . 'WHERE '
                        . 'i.idpago=p '
                    . 'and p.conciliado=true '
                        . 'and i.idevento=' . $idevento
                    . 'ORDER BY '
                        . 'i.secuencia ASC'                    
            )
            ->getResult();
    }    

    public function listarConciliadasAjax($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i,p,c,co,e,ca FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'JOIN '
                        . 'i.idpago p '
                    . 'JOIN '
                        . 'i.idcompetencia c '
                    . 'JOIN '
                        . 'i.idpia co '
                    . 'JOIN '
                        . 'co.idestado e '
                    . 'JOIN '
                        . 'i.idcategoria ca '
                    . 'WHERE '
                        . 'i.idpago=p '
                        . 'and i.idcompetencia=c '
                        . 'and i.idpia=co '
                        . 'and co.idestado=e '
                        . 'and i.idcategoria=ca '
                        . 'and i.status=1 '
                        . 'and p.conciliado=true '
                        . 'and i.idevento=' . $idevento
                    . 'ORDER BY '
                        . 'i.secuencia ASC'                    
            )
            ->getArrayResult();
    } 
    
    public function listarNoConciliadas($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i,p,c FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'LEFT JOIN '
                        . 'i.idpago p '
                    . 'JOIN '
                        . 'i.idcompetencia c '
                    . 'WHERE '
                        . '(i.idpago=p or i.idpago is null)'
                        . 'and i.idcompetencia=c '
                        . 'and i.status = 1 '
                        . 'and (p.conciliado=false or p.conciliado is null) '
                        . 'and (p.tipo=\'1\' or p.tipo=\'2\' or p.tipo is null)'
                        . 'and i.idevento=' . $idevento
                    . 'ORDER BY '
                        . 'i.secuencia ASC'
            )
            ->getResult();
    }    
    
    public function listarNoConciliadasAjax($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i,p,c,co,ca,es FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'LEFT JOIN '
                        . 'i.idpago p '
                    . 'JOIN '
                        . 'i.idcompetencia c '
                    . 'JOIN '
                        . 'i.idcategoria ca '
                    . 'JOIN '
                        . 'i.idpia co '                    
                    . 'JOIN '
                        . 'co.idestado es '                    
                    . 'WHERE '
                        . '(i.idpago=p or i.idpago is null) '
                        . 'and i.idcompetencia=c '
                        . 'and i.idcategoria=ca '
                        . 'and i.idpia=co '
                        . 'and co.idestado=es '
                        . 'and i.status = 1 '
                        . 'and (p.conciliado=false or p.conciliado is null) '
                        . 'and (p.tipo=\'1\' or p.tipo=\'2\' or p.tipo=\'5\' or p.tipo is null) '
                        . 'and i.idevento=' . $idevento . ' ' 
                    . 'ORDER BY '
                        . 'i.secuencia ASC'                 
            )
            ->getArrayResult();
    }     

    public function listarRegistrosAjax($idevento, $email)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i,p,c,co,ca,es FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'LEFT JOIN '
                        . 'i.idpago p '
                    . 'JOIN '
                        . 'i.idcompetencia c '
                    . 'JOIN '
                        . 'i.idcategoria ca '
                    . 'JOIN '
                        . 'i.idpia co '                    
                    . 'JOIN '
                        . 'co.idestado es '                    
                    . 'WHERE '
                        . '(i.idpago=p or i.idpago is null) '
                        . 'and i.idcompetencia=c '
                        . 'and i.idcategoria=ca '
                        . 'and i.idpia=co '
                        . 'and co.idestado=es '
                        . 'and i.status = 1 '
                        . 'and (p.conciliado=false or p.conciliado is null) '
                        . 'and (p.tipo=\'1\' or p.tipo=\'2\' or p.tipo=\'5\' or p.tipo is null) '
                        . 'and i.idevento=' . $idevento . ' ' 
                        . 'and (co.email = \'' . $email . '\' or co.emailpersonal=\'' . $email . '\') ' 
                    . 'ORDER BY '
                        . 'i.secuencia ASC'                 
            )
            ->getArrayResult();
    }     
    
    public function listarOperacionesTDC($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i,p,c FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'JOIN '
                        . 'i.idpago p '
                    . 'JOIN '
                        . 'i.idcompetencia c '
                    . 'WHERE '
                        . 'i.idpago=p '
                        . 'and i.idcompetencia=c '
                        . 'and i.status = 1 '
                        . 'and p.tipo=\'3\''
                        . 'and i.idevento=' . $idevento
                    . 'ORDER BY '
                        . 'i.secuencia ASC'
            )
            ->getResult();
    }      
    
    public function listarRivales($idevento,$idcategoria)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'JOIN '
                        . 'i.idpago p '
                    . 'WHERE '
                        . 'i.idpago=p '
                        . 'and p.conciliado=true '
                        . 'and i.idcategoria=' . $idcategoria 
                        . 'and i.idevento=' . $idevento
            )
            ->getResult();
    }
    
    public function listarAnuladas($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'WHERE '
                        . 'i.status = 0 '
                        . 'and i.idevento=' . $idevento
                    . 'ORDER BY '
                        . 'i.secuencia ASC'
            )
            ->getResult();
    }  
    
    public function cantidad($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT count(i) FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'WHERE '
                        . 'i.status = 1 '
                        . 'and i.idevento=' . $idevento
            )
            ->getResult()[0][1];
    }      
    
    public function inscritosPorCompetencia($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c.descripcion, count(i) as cantidad FROM FraterSoftPiaWebBundle:Inscrito i '
                    . ' JOIN'
                        . ' i.idcompetencia c'
                    . ' WHERE'
                        . ' i.status = 1 '
                        . ' and i.idevento=' . $idevento
                    . ' GROUP BY'
                        . ' c.descripcion'
            )
            ->getResult();
    }      
    
    public function inscritosPorEstatus($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p.conciliado, p.tipo, count(i) as cantidad FROM FraterSoftPiaWebBundle:Inscrito i '
                    . ' LEFT JOIN'
                        . ' i.idpago p'
                    . ' WHERE'
                        . ' i.status = 1 '
                        . ' and i.idevento=' . $idevento
                    . ' GROUP BY'
                        . ' p.conciliado, p.tipo'
            )
            ->getResult();
    }      
    
    public function inscritosPorFormaPago($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p.tipo, count(i) as cantidad FROM FraterSoftPiaWebBundle:Inscrito i '
                    . ' JOIN'
                        . ' i.idpago p'
                    . ' WHERE'
                        . ' i.status = 1 '
                        . ' and i.idevento=' . $idevento
                    . ' GROUP BY'
                        . ' p.tipo'
            )
            ->getResult();
    }          
    
    public function inscritosPorSexo($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p.sexo, count(i) as cantidad FROM FraterSoftPiaWebBundle:Inscrito i '
                    . ' JOIN'
                        . ' i.idpia p'
                    . ' WHERE'
                        . ' i.status = 1 '
                        . ' and i.idevento=' . $idevento
                    . ' GROUP BY'
                        . ' p.sexo'
            )
            ->getResult();
    }       
    
    public function inscritosPorCategoria($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c.descripcion, p.sexo, count(i) as cantidad FROM FraterSoftPiaWebBundle:Inscrito i '
                    . ' JOIN'
                        . ' i.idcategoria c'
                    . ' JOIN'
                        . ' i.idpia p'                    
                    . ' WHERE'
                        . ' i.status = 1 '
                        //. ' and i.idcompetencia in (select id from FraterSoftPiaWebBundle:Competencia where idevento='. $idevento. ')'
                        . ' and i.idevento = '. $idevento
                    . ' GROUP BY'
                        . ' c.descripcion, p.sexo'
                    . ' ORDER BY'
                        . ' cantidad DESC'
            )
            ->getResult();
    }      
    
    public function inscritosPorEstado($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT e.nombre as estado, count(i) as cantidad FROM FraterSoftPiaWebBundle:Inscrito i '
                    . ' JOIN'
                        . ' i.idpia p'
                    . ' JOIN'
                        . ' p.idestado e'
                    . ' WHERE'
                        . ' i.status = 1 '
                        . ' and i.idevento=' . $idevento
                    . ' GROUP BY'
                        . ' e.nombre'
                    . ' ORDER BY'
                        . ' cantidad DESC'                    
            )
            ->getResult();
    }       
    
    public function AnularNoConciliados($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i FROM FraterSoftPiaWebBundle:Inscrito i '
                    . ' JOIN i.idpago p'
                    . ' WHERE'
                        . ' i.status = 1 '
                        . ' and i.idevento=' . $idevento
                        . ' and p.tipo = \'3\''
                        . ' and p.notificado is not null'
                        . ' and p.conciliado is null'
            )
            ->getResult();
    }      
    
    public function inscritosPorPrecio($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i.precio, count(i) as cantidad FROM FraterSoftPiaWebBundle:Inscrito i '
                    . ' WHERE'
                        . ' i.status = 1 '
                        . ' and i.idevento=' . $idevento
                    . ' GROUP BY'
                        . ' i.precio'
                    . ' ORDER BY'
                        . ' cantidad DESC'                    
            )
            ->getResult();
    }     
    
    public function inscritosPorFecha($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT DATE_FORMAT(i.fechahora,"%d/%m/%y") as fecha, count(i) as cantidad FROM FraterSoftPiaWebBundle:Inscrito i '
                    . ' WHERE'
                        . ' i.status = 1 '
                        . ' and i.idevento=' . $idevento
                    . ' GROUP BY'
                        . ' i.fechahora'
                    . ' ORDER BY'
                        . ' cantidad DESC'                    
            )
            ->getResult();
    }      
    
    public function listaParaNumerar($idevento,$clasificador,$ordenarpor)
    {
        $em = $this->getEntityManager();
        //Busca el tipo del atributo configurado por evento
        //$atributo=$em->getRepository('FraterSoftPiaWebBundle:Atributo')->findOneBy(array('id'=>$idatributo));
        $ordenarpor=(is_null($ordenarpor))?"":"order by " . $ordenarpor;
        $clasificador=($clasificador!=null)?" and " . $clasificador:"";
        //$clasificador=(is_null($idatributo))?"":" and " . $atributo . "=" . (($atributo->getTipodato()=='S')?"'":"") . $valor . (($atributo->getTipodato()=='S')?"'":"");
        $sql = "select piaaccess.tminscritos.id from piaaccess.tminscritos "
                . "inner join piaaccess.tmcompetidores on tmcompetidores.id = tminscritos.idpia "
                . "inner join piaaccess.tmpagos on tmpagos.id = tminscritos.idpago "
                . "where idevento=" . $idevento . " and numero is null and conciliado=true " . $clasificador
                . $ordenarpor ;
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute(); 
        return $stmt->fetchAll();              
    }    
    
    public function resetearNumeracion($idevento) {
        $formaspago = $this->getEntityManager()
                ->createQuery(
                        "update FraterSoftPiaWebBundle:Inscrito i SET i.numero=NULL "
                        . "where i.idevento = " . $idevento 
                )
                ->getArrayResult();
        return $formaspago;
    }    
    
    public function listarAuditoriaAjax($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i,p,c,co,e,ca FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'LEFT JOIN '
                        . 'i.idpago p '
                    . 'LEFT JOIN '
                        . 'i.idcompetencia c '
                    . 'LEFT JOIN '
                        . 'i.idpia co '
                    . 'LEFT JOIN '
                        . 'co.idestado e '
                    . 'LEFT JOIN '
                        . 'i.idcategoria ca '
                    . 'WHERE '
                        . 'i.idpago=p '
                        . 'and i.idcompetencia=c '
                        . 'and i.idpia=co '
                        . 'and co.idestado=e '
                        . 'and i.idcategoria=ca '
                        . 'and p.conciliado=true '
                        . 'and i.idevento=' . $idevento
                    . 'ORDER BY '
                        . 'i.secuencia ASC'                    
            )
            ->getArrayResult();
    }   
    
    public function estadisticas($idevento,$entity)
    {
        $em = $this->getEntityManager();
        //Busca el tipo del atributo configurado por evento
        //$ordenarpor=(is_null($ordenarpor))?"":"order by " . $ordenarpor;
        //$clasificador=($clasificador!=null)?" and " . $clasificador:"";
        $sql = "select piaaccess.".$entity." as valor, count(piaaccess.".$entity.") as cantidad from piaaccess.tminscritos "
                . "inner join piaaccess.tmcompetidores on tmcompetidores.id = tminscritos.idpia "
                . "inner join piaaccess.tmpagos on tmpagos.id = tminscritos.idpago "
                . "left join piaaccess.tmcategorias on tmcategorias.id = tminscritos.idcategoria "
                . "left join piaaccess.tmcompetencias on tmcompetencias.id = tminscritos.idcompetencia "
                . "where tminscritos.idevento=" . $idevento . " and tmpagos.conciliado=true " 
                . "group by piaaccess.".$entity
                //. $ordenarpor
                ;
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute(); 
        //print_r($stmt->fetchAll());
        return $stmt->fetchAll();              
    }     
}