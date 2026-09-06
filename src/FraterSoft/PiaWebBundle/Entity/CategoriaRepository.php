<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CategoriaRepository extends EntityRepository
{
    public function listaCategorias($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'select ca from FraterSoftPiaWebBundle:Evento ev inner join FraterSoftPiaWebBundle:Competencia co WITH ev.id = co.idevento inner join FraterSoftPiaWebBundle:Categoria ca WITH co.id = ca.idcompetencia
where ev.id=' . $idevento
            )
            ->getResult();
    }

    public function listaCategoriasCampeonato($idcampeonato)
    {
        return $this->getEntityManager()
            ->createQuery(
                'select ca from FraterSoftPiaWebBundle:Categoria ca 
where ca.idcampeonato=' . $idcampeonato
            )
            ->getResult();
    }

    public function listaCategoriasGrupos($idevento,$idcompetencia)
    {
        return $this->getEntityManager()
            ->createQuery(
                'select ca from FraterSoftPiaWebBundle:Evento ev inner join FraterSoftPiaWebBundle:Competencia co WITH ev.id = co.idevento inner join FraterSoftPiaWebBundle:Categoria ca WITH co.id = ca.idcompetencia '
                . 'where ev.id=' . $idevento . ' and co.id=' . $idcompetencia
            )
            ->getResult();
    }
    
    /**
     * Devuelve un mapa (idcompetencia => cantidad de categorias configuradas) para las
     * competencias del evento. Lo usa el formulario multicompetencia para decidir si se
     * muestra el selector de categoria: solo si la competencia tiene mas de una.
     */
    public function conteoPorCompetencia($idevento)
    {
        $filas = $this->getEntityManager()
            ->createQuery(
                'select co.id as idcompetencia, count(ca.id) as total '
                . 'from FraterSoftPiaWebBundle:Competencia co '
                . 'inner join FraterSoftPiaWebBundle:Categoria ca WITH co.id = ca.idcompetencia '
                . 'where co.idevento = ' . (int) $idevento . ' '
                . 'group by co.id'
            )
            ->getResult();
        $mapa = array();
        foreach ($filas as $fila) {
            $mapa[$fila['idcompetencia']] = (int) $fila['total'];
        }
        return $mapa;
    }

    public function arrayCategorias($idcompetencia)
    {
        return $this->getEntityManager()
            ->createQuery(
                'select ca from FraterSoftPiaWebBundle:Competencia co inner join FraterSoftPiaWebBundle:Categoria ca WITH co.id = ca.idcompetencia
                where co.id=' . $idcompetencia
            )
            ->getArrayResult();
    }

    public function arrayLista($idevento)
    {
        return $this->getEntityManager()
            ->createQuery(
                'select ca.id,co.id as idcompetencia,ca.idcampeonato, ca.descripcion  '
                    . 'from FraterSoftPiaWebBundle:Competencia co inner join FraterSoftPiaWebBundle:Categoria ca WITH co.id = ca.idcompetencia
                    where co.idevento=' . $idevento
            )
            ->getResult();
    }

    public function arrayListaCampeonato($idcampeonato)
    {
        return $this->getEntityManager()
            ->createQuery(
                'select ca.id,0 as idcompetencia,ca.idcampeonato,ca.descripcion  '
                    . 'from FraterSoftPiaWebBundle:Categoria ca 
                    where ca.idcampeonato=' . $idcampeonato
            )
            ->getResult();
    }

    public function listadoPorEvento($idevento, array $criterios)
    {
        $em = $this->getDoctrine()->getManager();
        $atributoscriterios = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')->atributosCriterios($idevento);
        if($atributoscriterios){
            foreach ($atributoscriterios as $atruibutocriterio) {
                $valor = $competidor->getValorCampo(strtolower($atruibutocriterio->getIdatributo()->getNombre()));
                if ($valor) {
                    $criterios[$atruibutocriterio->getIdatributo()->getNombre()] = $valor;
                }
            }

            $categoriasselect = new ArrayCollection();

            $categorias = $this->listaCategorias($idevento);

            foreach ($categorias as $categoria) {
                $reglas = $em->getRepository('FraterSoftPiaWebBundle:CategoriaReglas')->findBy(array(
                    'idcategoria' => $categoria->getId()
                ));
                $parametrok = false;
                foreach ($reglas as $regla) {
                    $parametrok = false;
                    if ($regla->getTipo() == 'R') {
                        if ($regla->getValor1() <= $criterios[$regla->getAtributo()] &&
                                $regla->getValor2() >= $criterios[$regla->getAtributo()])
                            $parametrok = true;
                    }
                    else {
                        if ($regla->getValor1() == $criterios[$regla->getAtributo()])
                            $parametrok = true;
                    }
                    if (!$parametrok)
                        break;
                }
                if ($parametrok) {
                    $categoriasselect->add($categoria);
                    $parametrok = false;
                }
            }
            return $categoriasselect;
        }else{
            return null;
        }

    }
}