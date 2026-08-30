<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\EntityRepository;
use FraterSoft\PiaWebBundle\Controller\commonPIAClass;

class InscritoRepository extends EntityRepository
{
    
    public function findIdArray($id)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i,c,co,e,ca,ev,p,ic,icc,icca FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'LEFT JOIN i.pagos p '
                    . 'LEFT JOIN i.idcompetencia c '
                    . 'JOIN i.idpia co '
                    . 'JOIN co.idestado e '
                    . 'LEFT JOIN i.idcategoria ca '
                    . 'JOIN i.idevento ev '
                    . 'LEFT JOIN i.competencias ic '
                    . 'LEFT JOIN ic.idcompetencia icc '
                    . 'LEFT JOIN ic.idcategoria icca '
                    . 'WHERE '
//                        . 'i.idcompetencia=c '
//                        . 'and i.idpia=co '
//                        . 'and co.idestado=e '
//                        . 'and i.idcategoria=ca '              
//                        . 'and i.idevento=ev '              
                        . 'i.id=' . $id                   
            )
            ->getArrayResult();
    } 
    
    public function buscarInscrito($idevento,$idpia)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i FROM FraterSoftPiaWebBundle:Inscrito i WHERE i.idpia =' . $idpia . ' and i.idevento=' . $idevento . " and i.status <> 0"
            )
            ->getResult();
    }
    
    public function buscarSecuencias($secuencias,$idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i,e FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'JOIN i.idevento e '
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
                'SELECT i,p,c,ca,co,es,b,fp '
                . 'FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'LEFT JOIN i.pagos p '
                    . 'LEFT JOIN i.idcompetencia c '
                    . 'LEFT JOIN i.idcategoria ca '
                    . 'JOIN i.idpia co '
                    . 'LEFT JOIN co.idestado es '
                    . 'LEFT JOIN p.idbanco b '
                    . 'LEFT JOIN p.idformapago fp '
                    . 'WHERE '
                        . 'i.status=1 '
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
                'SELECT i,p,c,ca,co,es,b,fp,ic,icc,icca '
                . 'FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'LEFT JOIN i.pagos p '
                    . 'LEFT JOIN i.idcompetencia c '
                    . 'LEFT JOIN i.idcategoria ca '
                    . 'JOIN i.idpia co '
                    . 'LEFT JOIN co.idestado es '
                    . 'LEFT JOIN p.idbanco b '
                    . 'LEFT JOIN p.idformapago fp '
                    . 'LEFT JOIN i.competencias ic '
                    . 'LEFT JOIN ic.idcompetencia icc '
                    . 'LEFT JOIN ic.idcategoria icca '
                    . 'WHERE '
                        . 'i.status=1 '
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
                    . 'LEFT JOIN '
                        . 'i.idcompetencia c '
                    . 'WHERE '
                        . '(i.idpago=p or i.idpago is null)'
                        . 'and i.status = 1 '
                        . 'and (p.conciliado=false or p.conciliado is null) '
                        . 'and (p.tipo!=\'3\' or p.tipo is null)'
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
                'SELECT i,p,c,ca,co,es,b,fp '
                . 'FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'LEFT JOIN i.pagos p '
                    . 'LEFT JOIN i.idcompetencia c '
                    . 'LEFT JOIN i.idcategoria ca '
                    . 'JOIN i.idpia co '
                    . 'JOIN co.idestado es '
                    . 'LEFT JOIN p.idbanco b '
                    . 'LEFT JOIN p.idformapago fp '
                    . 'WHERE '
                        . 'i.status = 1 '
                        . 'and (p.conciliado=false or p.conciliado is null) '
                        . 'and (p.idformapago!=3 or p.idformapago is null) '
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
                    . 'LEFT JOIN '
                        . 'i.idcompetencia c '
                    . 'LEFT JOIN '
                        . 'i.idcategoria ca '
                    . 'JOIN '
                        . 'i.idpia co '
                    . 'JOIN '
                        . 'co.idestado es '
                    . 'WHERE '
                        . '(i.idpago=p or i.idpago is null) '
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
                    . 'LEFT JOIN '
                        . 'i.idcompetencia c '
                    . 'WHERE '
                        . 'i.idpago=p '
                        . 'and i.status = 1 '
                        . 'and p.tipo=\'3\''
                        . 'and i.idevento=' . $idevento
                    . 'ORDER BY '
                        . 'i.secuencia ASC'
            )
            ->getResult();
    }      
    
    public function listarRivales($idevento,$idcompetencia,$idcategoria,$sexo)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i,co FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'LEFT JOIN i.pagos p '
                    . 'JOIN '
                        . 'i.idpia co '                    
                    . 'WHERE '
                        . 'i.status=1 '
                        . 'and p.conciliado=true '
                        . 'and i.idevento=' . $idevento
                        . 'and i.idcompetencia=' . $idcompetencia
                        . 'and i.idcategoria=' . $idcategoria 
                        . 'and co.sexo=\'' . $sexo . '\''
                    . 'ORDER BY co.apellido '
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
    
    /**
     * Cupo asignado del evento para el control de cupos: preinscritos + inscritos.
     * NO cuenta las inscripciones anuladas (status = 0). Es lo que se compara contra
     * Evento.cupomaximo, con el mismo criterio que cantidadPorCompetencia().
     */
    public function cantidad($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT count(i) FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'WHERE '
                        . 'i.status <> 0 '
                        . 'and i.idevento=' . $idevento
            )
            ->getResult()[0][1];
    }

    /**
     * Cantidad de inscritos por competencia del evento, para el control de cupos.
     * NO cuenta las inscripciones anuladas (status = 0). Contempla tanto los inscritos
     * de una sola competencia (Inscrito.idcompetencia) como los multicompetencia
     * (InscritoCompetencia).
     *
     * @return array  mapa  idcompetencia => cantidad
     */
    public function cantidadPorCompetencia($idevento)
    {
        $em = $this->getEntityManager();
        // Los ids de evento superan el rango de un entero de 32 bits: se sanea a
        // solo digitos y se usa como string para no truncarlo en PHP de 32 bits.
        $idevento = preg_replace('/[^0-9]/', '', $idevento);
        $mapa = array();

        $simples = $em->createQuery(
                'SELECT c.id as idcompetencia, count(i.id) as cantidad '
                . 'FROM FraterSoftPiaWebBundle:Inscrito i JOIN i.idcompetencia c '
                . 'WHERE i.status <> 0 AND i.idevento = ' . $idevento . ' '
                . 'GROUP BY c.id'
            )->getResult();
        foreach ($simples as $fila) {
            $mapa[$fila['idcompetencia']] = (int) $fila['cantidad'];
        }

        $multi = $em->createQuery(
                'SELECT icc.id as idcompetencia, count(ic.id) as cantidad '
                . 'FROM FraterSoftPiaWebBundle:InscritoCompetencia ic '
                . 'JOIN ic.idinscrito i JOIN ic.idcompetencia icc '
                . 'WHERE i.status <> 0 AND i.idevento = ' . $idevento . ' '
                . 'GROUP BY icc.id'
            )->getResult();
        foreach ($multi as $fila) {
            $id = $fila['idcompetencia'];
            $mapa[$id] = (isset($mapa[$id]) ? $mapa[$id] : 0) + (int) $fila['cantidad'];
        }

        return $mapa;
    }


    /**
     * Resumen para el Tablero del evento.
     *  - inscritos:    inscripciones activas (status 1) con al menos un pago conciliado
     *  - preinscritos: inscripciones activas (status 1) sin pago conciliado
     *  - anulados:     inscripciones anuladas (status 0)
     *  - recaudado:    suma de montos de los pagos conciliados de inscripciones activas
     *
     * @return array  claves: inscritos, preinscritos, anulados, recaudado
     */
    public function resumenTablero($idevento)
    {
        $em = $this->getEntityManager();
        // Ver nota en cantidadPorCompetencia(): id de evento fuera del rango de 32 bits.
        $idevento = preg_replace('/[^0-9]/', '', $idevento);

        $activos = (int) $em->createQuery(
                'SELECT count(i.id) FROM FraterSoftPiaWebBundle:Inscrito i '
                . 'WHERE i.status = 1 AND i.idevento = ' . $idevento
            )->getSingleScalarResult();

        $inscritos = (int) $em->createQuery(
                'SELECT count(DISTINCT i.id) FROM FraterSoftPiaWebBundle:Inscrito i '
                . 'JOIN i.pagos p '
                . 'WHERE i.status = 1 AND p.conciliado = true AND i.idevento = ' . $idevento
            )->getSingleScalarResult();

        $anulados = (int) $em->createQuery(
                'SELECT count(i.id) FROM FraterSoftPiaWebBundle:Inscrito i '
                . 'WHERE i.status = 0 AND i.idevento = ' . $idevento
            )->getSingleScalarResult();

        $recaudado = $em->createQuery(
                'SELECT COALESCE(SUM(p.monto), 0) FROM FraterSoftPiaWebBundle:Pago p '
                . 'JOIN p.idinscrito i '
                . 'WHERE i.status = 1 AND p.conciliado = true AND i.idevento = ' . $idevento
            )->getSingleScalarResult();

        return array(
            'inscritos'    => $inscritos,
            'preinscritos' => max($activos - $inscritos, 0),
            'anulados'     => $anulados,
            'recaudado'    => (float) $recaudado,
        );
    }

    public function inscritosPorCompetencia($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c.descripcion, count(i) as cantidad FROM FraterSoftPiaWebBundle:Inscrito i '
                    . ' LEFT JOIN'
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
                    . ' LEFT JOIN'
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
                . "inner join piaaccess.tmpagos on tmpagos.idinscrito = tminscritos.id "
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
                'SELECT i,p,c,ca,co,es,b,fp '
                . 'FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'LEFT JOIN i.pagos p '
                    . 'LEFT JOIN i.idcompetencia c '
                    . 'LEFT JOIN i.idcategoria ca '
                    . 'JOIN i.idpia co '
                    . 'JOIN co.idestado es '
                    . 'LEFT JOIN p.idbanco b '
                    . 'LEFT JOIN p.idformapago fp '
                    . 'WHERE '
                        . 'i.idevento=' . $idevento
                    . 'ORDER BY '
                        . 'i.secuencia ASC'
            )
            ->getArrayResult();
    }
    
    public function estadisticas($idevento,$entidad_atributo)
    {
        $em = $this->getEntityManager();
        $entidad=substr($entidad_atributo,0,stripos($entidad_atributo,":"));
        $atributo=substr($entidad_atributo,stripos($entidad_atributo,":")+1,strlen($entidad_atributo));
        $tipo=$em->getClassMetadata('FraterSoft\PiaWebBundle\Entity\\' . $entidad)->getTypeOfField($atributo);
        $nombreTabla=$em->getClassMetadata('FraterSoft\PiaWebBundle\Entity\\' . $entidad)->getTableName();
        $sql="";
        switch(true){
            case ($entidad=="Inscrito" && $atributo=='status'):
                $sql = 'select valor,sum(cantidad) as cantidad from (' 
                    . 'select case when i.status=1 and p.conciliado=true then \'INSCRITOS\' ' 
            		. 'when i.status=0 then \'ANULADOS\' '
            		. 'when i.status=1 and p.id is not null and p.conciliado is null and p.tipo<>\'3\' then \'PREINSCRITOS CON PAGO\' ' 
            		. 'when i.status=1 and p.id is null then \'PREINSCRITOS SIN PAGO\' ' 
            		. 'when i.status=1 and p.conciliado is null and p.tipo=\'3\' then \'TDC SIN PAGAR\' '
                    . 'end as valor, '
                    . 'count(i) as cantidad '
                    . ' from piaaccess.tminscritos i left join piaaccess.tmpagos p on i.id=p.idinscrito where i.idevento= ' . $idevento
                    . 'group by p.id,p.conciliado, i.status, p.tipo) e group by valor order by cantidad desc';
                break;
            case ($entidad=="Categoria" && $atributo=='descripcion'):
                $sql = "select " . $nombreTabla . "." .$atributo . " || ' - ' || piaaccess.tmcompetidores.sexo as valor, count(".$nombreTabla."." .$atributo .") as cantidad from piaaccess.tminscritos "
                        . "inner join piaaccess.tmcompetidores on tmcompetidores.id = tminscritos.idpia "
                        . "inner join piaaccess.tmpagos on tmpagos.idinscrito = tminscritos.id "
                        . "left join piaaccess.tmcategorias on tmcategorias.id = tminscritos.idcategoria "
                        . "left join piaaccess.tmcompetencias on tmcompetencias.id = tminscritos.idcompetencia "
                        . "where tminscritos.idevento=" . $idevento . " and tmpagos.conciliado=true " 
                        . "group by ".$nombreTabla.".".$atributo . ",piaaccess.tmcompetidores.sexo order by cantidad desc";
                break;
            default:
                switch(true){
                    case ($tipo=="datetime" || $tipo=="datetimetz"):
                        $sql = 'select to_date(valor,\'dd/mm/yyyy\') as valor,sum(cantidad) as cantidad from (' 
                            . 'select to_char(i.fechahora,\'dd/mm/yyyy\') as valor,count(i) as cantidad '
                            . 'from piaaccess.tminscritos i left join piaaccess.tmpagos p on i.id=p.idinscrito where p.conciliado=true and i.idevento=' . $idevento
                            . 'group by i.fechahora) e group by valor order by valor';
                        break;
                    default:
                        $leftJoinEstado="";
                        if($atributo == "idestado"){
                            $leftJoinEstado = "left join piaaccess.tmestados on piaaccess.tmestados.id = piaaccess.tmcompetidores.idestado ";
                            $nombreTabla = "tmestados";
                            $atributo = "nombre";
                        }
                        $sql = "select " . $nombreTabla . "." .$atributo . " as valor, count(".$nombreTabla."." .$atributo .") as cantidad from piaaccess.tminscritos "
                                . "inner join piaaccess.tmcompetidores on tmcompetidores.id = tminscritos.idpia "
                                . "inner join piaaccess.tmpagos on tmpagos.idinscrito = tminscritos.id "
                                . "left join piaaccess.tmcategorias on tmcategorias.id = tminscritos.idcategoria "
                                . "left join piaaccess.tmcompetencias on tmcompetencias.id = tminscritos.idcompetencia "
                                . $leftJoinEstado
                                . "where tminscritos.idevento=" . $idevento . " and tmpagos.conciliado=true and tminscritos.status=1" 
                                . "group by ".$nombreTabla.".".$atributo . " order by cantidad desc";
                        break;
                }
        }
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute(); 
        return $stmt->fetchAll();              
    }     
    
    public function cantidadIntegrantesGrupo($idgrupo)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT count(i) FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'WHERE '
                        . 'i.idgrupo=\'' . $idgrupo . '\''
                        . ' and i.status<>0'
            )
            ->getResult()[0][1];
    }      
    
    public function ContarReglaGrupo($idevento,$idgrupo,$regla,$atributo)
    {
        switch($regla->getTipo()){
            case '[]':
                $criterio=$regla->getAtributo(). 'between ' . $regla->getValor1() . ' and ' . $regla->getValor2();
                break;
            case '=':
                if($atributo->getTipodato()=='S' || $atributo->getTipodato()=='O')
                    $criterio=strtolower($regla->getAtributo()) . '=\'' . $regla->getValor1() . '\'';
                else
                    $criterio=strtolower($regla->getAtributo()) . '=' . $regla->getValor1();
                break;                                    
        }        
        $sql='select count(c.'.strtolower($regla->getAtributo()).') as Cantidad'
                    . ' from FraterSoftPiaWebBundle:inscrito i join i.idpia c'
                    . ' where i.idevento=' . $idevento . ' and i.status<>0 and i.idgrupo=\'' . $idgrupo . '\' and c.' . $criterio;
        //print_r($sql);
        return $this->getEntityManager()
            ->createQuery($sql)
            ->getResult();
    }

    public function inscritosGrupo($idevento,$idgrupo,$campos=null)
    {
        $em = $this->getEntityManager();
        
        if($campos==null)
            $campos=["iddocumento","nombre","apellido"];

        $atributos = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')->arrayEtiquetasAtributos($idevento,$campos);
        $strcampos="";
        $cantidad_atributos=count($atributos);
        $i=1;
        foreach($atributos as $atributo){
            if(is_null($atributo['etiqueta']))
                $strcampos.="co." . $atributo['atributo'] . ",";
            else
                $strcampos.="co." . $atributo['atributo'] . ' as ' . $atributo['etiqueta'] . ",";
        }
        
        $atributoscriterios = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')->atributosCriterios($idevento);
        $criterios="";
        $cantidad_criterios=count($atributoscriterios);
        $i=1;
        foreach ($atributoscriterios as $atruibutocriterio) {
            $criterios.= "co." . $atruibutocriterio->getidatributo()->getNombre();
            $criterios.=($i++<$cantidad_criterios)?",":"";
        }
        
        $strcampos.=$criterios;
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i.id,ev.id as idevento,' . $strcampos . ' FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'JOIN '
                        . 'i.idpia co '
                    . 'JOIN '
                        . 'i.idevento ev '
                    . 'WHERE '
                        . 'i.idpia=co '
                        . 'and i.status<>0 '
                        . 'and i.idevento=' . $idevento
                        . ' and i.idgrupo=\'' . $idgrupo . '\' '
                    . 'ORDER BY '
                        . 'i.secuencia ASC'
            )
            ->getArrayResult();
    } 

    public function inscritosGrupoObjetos($idevento,$idgrupo)
    {
        $em = $this->getEntityManager();
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i,co,ev,ca FROM FraterSoftPiaWebBundle:Inscrito i '
                    . 'JOIN '
                        . 'i.idpia co '
                    . 'JOIN '
                        . 'i.idevento ev '
                    . 'JOIN '
                        . 'i.idcategoria ca '
                    . 'WHERE '
                        . 'i.idpia=co '
                        . 'and i.status<>0 '
                        . 'and i.idevento=' . $idevento
                        . ' and i.idgrupo=\'' . $idgrupo . '\' '
                    . 'ORDER BY '
                        . 'i.secuencia ASC'
            )
            ->getArrayResult();
    } 
    
}