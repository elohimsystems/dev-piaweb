<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FraterSoft\PiaWebBundle\Entity\Categoria;
use FraterSoft\PiaWebBundle\Entity\CategoriaReglas;
use FraterSoft\PiaWebBundle\Form\CategoriaType;
use FraterSoft\PiaWebBundle\Form\CategoriaReglasType;

use Doctrine\ORM\EntityRepository;
/**
 * Categoria controller.
 *
 */
class CategoriaController extends commonPIAClass
{

    /**
     * Creates a new Categoria entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Categoria();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->asignarOrdenNuevo($em, $entity);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('categoria_new', array(
                'idevento' => $entity->getIdcompetencia()->getIdevento()->getId(),
                'estado' => 2
            )));            
        }
        $errors=$this->getErrorMessages($editForm);
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => null,
                    'texto' => json_encode($errors),
                    'tema' => $entity->getIdcompetencia()->getIdevento()->getTema()
        ));        
    }

    /**
    * Creates a form to create a Categoria entity.
    *
    * @param Categoria $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Categoria $entity)
    {
        $form = $this->createForm(new CategoriaType(), $entity, array(
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Categoria entity.
     *
     */
    public function newAction($idevento,$estado)
    {
        $entity = new Categoria();
        $form   = $this->createCreateForm($entity);
        
        $categoriareglas = new CategoriaReglas();
        $formCategoriaReglas   = $this->createCreateFormCategoriaReglas($categoriareglas);     
        
        $formCategoriaReglas->add('idcategoria','entity',array(
                'label' => 'Categoria',
                'class' => 'FraterSoftPiaWebBundle:Categoria',
                'query_builder' => function (EntityRepository $er) use ( $idevento ) {
                    return $er->createQueryBuilder('ca')
                            ->join('ca.idcompetencia','co')
                            ->where('co.idevento=:idevento')
                            ->setParameter('idevento',$idevento);
                            ;
                },
            ));
        
        //Llena el combo de los atributos que solo solo criterios
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('a.nombre')
           ->from('FraterSoftPiaWebBundle:EventoAtributos', 'ea')
                ->join('ea.idatributo','a')
           ->where('ea.idevento = ' . $idevento . ' and ea.criterio=true');
        $atributosCriterio = $qb->getQuery()->getResult();
        $arrayAtributos = array();
        foreach($atributosCriterio as $atributo){
             $arrayAtributos[$atributo['nombre']] = $atributo['nombre'];
        }        
        $formCategoriaReglas->add('atributo','choice',array(
                'label'=>'Atributos',
                'choices' => $arrayAtributos,
                'empty_value' => 'Seleccione un Atributo Criterio'
            ));
        
        $formCategoriaReglas->add('tipo','choice',array(
                'label'=>'Tipo',
                'choices' => array(
                    'empty_value' => 'Seleccione un Tipo',
                    '[]'=>'Rango',
                    '='=>'Igual',
                    //'>='=>'Mayor o Igual',
                    //'>'=>'Mayor',
                    //'<='=>'Menor o Igual',
                    //'<'=>'Menor',
                    //'<>'=>'Distinto'),
            )));

        $entities = $em->getRepository('FraterSoftPiaWebBundle:Categoria')->listaCategorias($idevento);
        $competencias = $em->getRepository('FraterSoftPiaWebBundle:Competencia')->findBy(array('idevento'=>$idevento));
        
        $camposCompetencia=$this->getCampos($em,'Competencia');
        
        return $this->render('FraterSoftPiaWebBundle:Categoria:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'formCategoriaReglas' => $formCategoriaReglas->createView(),
            'idevento' => $idevento,
            'campos' => $this->getCampos($em,'Categoria'),            
            'estado' => $estado,   
            'competencias' => $this->EntitiesToArray($competencias,$camposCompetencia),
        ));
    }
    
    public function listaAjaxAction($idevento){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:Categoria')->arrayLista($idevento);
        
        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($entities),
            "data"=>$entities)
                , 'json');
        
        return new response($jsonContent);
    }
   
    public function encampeonatocreateAction(Request $request)
    {
        $entity = new Categoria();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->asignarOrdenNuevo($em, $entity);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('categoria_campeonato_new', array(
                'idcampeonato' => $entity->getIdcampeonato(),
                'estado' => 2
            )));            
        }
        $errors=$this->getErrorMessages($editForm);
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => null,
                    'texto' => json_encode($errors),
                    'tema' => $entity->getIdcompetencia()->getIdevento()->getTema()
        ));        
    }
    
    public function encampeonatonewAction($idcampeonato,$estado)
    {
        $entity = new Categoria();
        $form   = $this->createCreateForm($entity);
        
        $categoriareglas = new CategoriaReglas();
        $formCategoriaReglas   = $this->createCreateFormCategoriaReglas($categoriareglas);     
        
        $formCategoriaReglas->add('idcategoria','entity',array(
                'class' => 'FraterSoftPiaWebBundle:Categoria',
                'query_builder' => function (EntityRepository $er) use ( $idcampeonato ) {
                    return $er->createQueryBuilder('ca')
                            //->join('ca.idcompetencia','co')
                            ->where('ca.idcampeonato=:idcampeonato')
                            ->setParameter('idcampeonato',$idcampeonato);
                            ;
                },
            ));
        
        //Llena el combo de los atributos que solo solo criterios
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('a.nombre')
           ->from('FraterSoftPiaWebBundle:Atributo', 'a');
        $atributosCriterio = $qb->getQuery()->getResult();
        $arrayAtributos = array();
        foreach($atributosCriterio as $atributo){
             $arrayAtributos[$atributo['nombre']] = $atributo['nombre'];
        }        
        $formCategoriaReglas->add('atributo','choice',array(
                'label'=>'Atributos',
                'choices' => $arrayAtributos,
                'empty_value' => 'Seleccione un Atributo Criterio'
            ));
        
        $formCategoriaReglas->add('tipo','choice',array(
                'label'=>'Tipo',
                'choices' => array('R'=>'Rango','V'=>'Valor'),
                'empty_value' => 'Seleccione un Tipo'
            ));

        $entities = $em->getRepository('FraterSoftPiaWebBundle:Categoria')->listaCategoriasCampeonato($idcampeonato);
        $em = $this->getDoctrine()->getManager();
        $campeonato=$em->getRepository('FraterSoftPiaWebBundle:Campeonato')->find($idcampeonato);
        //$competencias = $em->getRepository('FraterSoftPiaWebBundle:Competencia')->findBy(array('idevento'=>$idevento));
        
        return $this->render('FraterSoftPiaWebBundle:Categoria:encampeonatonew.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'formCategoriaReglas' => $formCategoriaReglas->createView(),
            'campeonato' => $campeonato,
            'campos' => $this->getCampos($em,'Categoria'),            
            'estado' => $estado,   
            //'competencias' => $competencias,
        ));
    }

    public function listaAjaxCampeonatoAction($idcampeonato){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:Categoria')->arrayListaCampeonato($idcampeonato);
        
        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($entities),
            "data"=>$entities)
                , 'json');
        
        return new response($jsonContent);
    }
    
    /**
     * Edits an existing Categoria entity.
     *
     */
    public function encampeonatoupdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Categoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Categoria entity.');
        }

        $ordenAnterior = $entity->getOrden();

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->moverOrden($em, $entity, $ordenAnterior);
            $em->flush();

            return $this->redirect($this->generateUrl('categoria_campeonato_new', array(
                'idcampeonato' => $entity->getIdcampeonato(),
                'estado' => 3
            )));            
            
            return $this->redirect($this->generateUrl('evento_configurarinscripciones'));
        }
        
        $errors=$this->getErrorMessages($editForm);
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => null,
                    'texto' => json_encode($errors),
                    'tema' => $entity->getIdcompetencia()->getIdevento()->getTema()
        ));        
    }

    /**
     * Deletes a Categoria entity.
     *
     */
    public function encampeonatodeleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Categoria')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Categoria entity.');
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('categoria_campeonato_new', array(
            'idcampeonato' => $entity->getIdcampeonato(),
            'estado' => 1
        )));            
    }
    
    /**
     * Finds and displays a Categoria entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Categoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Categoria entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FraterSoftPiaWebBundle:Categoria:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
    * Creates a form to edit a Categoria entity.
    *
    * @param Categoria $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Categoria $entity)
    {
        $form = $this->createForm(new CategoriaType(), $entity, array(
            'method' => 'POST',
        ));

        return $form;
    }
    
    /**
     * Edits an existing Categoria entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Categoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Categoria entity.');
        }

        $ordenAnterior = $entity->getOrden();

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->moverOrden($em, $entity, $ordenAnterior);
            $em->flush();

            return $this->redirect($this->generateUrl('categoria_new', array(
                'idevento' => $entity->getIdcompetencia()->getIdevento()->getId(),
                'estado' => 3
            )));            
            
            return $this->redirect($this->generateUrl('evento_configurarinscripciones'));
        }
        
        $errors=$this->getErrorMessages($editForm);
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => null,
                    'texto' => json_encode($errors),
                    'tema' => $entity->getIdcompetencia()->getIdevento()->getTema()
        ));        
    }
    
    /**
     * Deletes a Categoria entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Categoria')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Categoria entity.');
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('categoria_new', array(
            'idevento' => $entity->getIdcompetencia()->getIdevento()->getId(),
            'estado' => 1
        )));            
    }
    
    private function createCreateFormCategoriaReglas(CategoriaReglas $entity)
    {
        $form = $this->createForm(new CategoriaReglasType(), $entity, array(
            'method' => 'POST',
        ));
        return $form;
    }    

    /**
     * Orden automatico de la categoria (mismo criterio que EventoAtributos): al
     * crear se ignora el 'orden' que llegue del formulario y se asigna el
     * siguiente correlativo dentro de su competencia (o de su campeonato si no
     * tiene competencia), es decir, siempre al final.
     */
    private function asignarOrdenNuevo($em, Categoria $entity)
    {
        $maxOrden = $this->qbCategoriasMismoGrupo($em, $entity)
            ->select('MAX(ca.orden)')
            ->getQuery()
            ->getSingleScalarResult();
        $entity->setOrden(($maxOrden !== null ? (int) $maxOrden : 0) + 1);
    }

    /**
     * Mover categoria de orden: si el orden pedido en el formulario cambio
     * respecto al que tenia, se corren las categorias que quedan en el medio
     * en vez de pisar el valor (evita 'orden' duplicados). Ej.: de 5 a 2 -> las
     * que estaban en 2,3,4 suben a 3,4,5; de 2 a 5 -> las que estaban en 3,4,5
     * bajan a 2,3,4. Un orden vacio conserva el anterior y uno fuera de rango
     * se ajusta a [1, ultimo].
     */
    private function moverOrden($em, Categoria $entity, $ordenAnterior)
    {
        $ordenNuevo = $entity->getOrden();
        if ($ordenAnterior === null) {
            return;
        }
        if ($ordenNuevo === null) {
            $entity->setOrden($ordenAnterior);
            return;
        }

        $maxOrden = (int) $this->qbCategoriasMismoGrupo($em, $entity)
            ->select('MAX(ca.orden)')
            ->getQuery()
            ->getSingleScalarResult();
        $ordenNuevo = max(1, min((int) $ordenNuevo, $maxOrden));
        $entity->setOrden($ordenNuevo);
        if ($ordenNuevo == $ordenAnterior) {
            return;
        }

        $qb = $this->qbCategoriasMismoGrupo($em, $entity, true)
            ->andWhere('ca.id != :id')
            ->setParameter('id', $entity->getId());
        if ($ordenNuevo < $ordenAnterior) {
            $qb->set('ca.orden', 'ca.orden + 1')
                ->andWhere('ca.orden >= :nuevo AND ca.orden < :anterior');
        } else {
            $qb->set('ca.orden', 'ca.orden - 1')
                ->andWhere('ca.orden > :anterior AND ca.orden <= :nuevo');
        }
        $qb->setParameter('nuevo', $ordenNuevo)
            ->setParameter('anterior', $ordenAnterior)
            ->getQuery()
            ->execute();
    }

    /**
     * QueryBuilder (select o update) de las categorias que comparten el mismo
     * grupo de orden que $entity: su competencia o, si no tiene, su campeonato.
     */
    private function qbCategoriasMismoGrupo($em, Categoria $entity, $update = false)
    {
        $qb = $em->createQueryBuilder();
        if ($update) {
            $qb->update('FraterSoftPiaWebBundle:Categoria', 'ca');
        } else {
            $qb->from('FraterSoftPiaWebBundle:Categoria', 'ca');
        }
        if ($entity->getIdcompetencia()) {
            $qb->where('ca.idcompetencia = :grupo')
                ->setParameter('grupo', $entity->getIdcompetencia());
        } else {
            $qb->where('ca.idcompetencia IS NULL AND ca.idcampeonato = :grupo')
                ->setParameter('grupo', $entity->getIdcampeonato());
        }
        return $qb;
    }
}
