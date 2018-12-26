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

use Doctrine\ORM\EntityRepository;

use FraterSoft\PiaWebBundle\Entity\EventoAtributos;
use FraterSoft\PiaWebBundle\Form\EventoAtributosType;


/**
 * EventoAtributos controller.
 *
 */
class EventoAtributosController extends commonPIAClass
{

    /**
     * Lists all EventoAtributos entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')->findAll();

        return $this->render('FraterSoftPiaWebBundle:EventoAtributos:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new EventoAtributos entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new EventoAtributos();
        $form = $this->crearFormulario($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('eventoatributos_new', array(
                'idevento' => $entity->getIdevento()->getId(),
                'estado' => 2
            )));            
        }

        return $this->render('FraterSoftPiaWebBundle:EventoAtributos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a EventoAtributos entity.
    *
    * @param EventoAtributos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function crearFormulario(EventoAtributos $entity)
    {
        $form = $this->createForm(new EventoAtributosType(), $entity, array(
            'method' => 'POST',        
        ));
        return $form;
    }

    /**
     * Displays a form to create a new EventoAtributos entity.
     *
     */
    public function newAction($idevento,$estado)
    {
        $entity = new EventoAtributos();
        $form   = $this->crearFormulario($entity);
        
        //llena el Select con el evento y Oculta el control
        $form->add('idevento','entity',array(
                'class' => 'FraterSoftPiaWebBundle:Evento',
                'query_builder' => function (EntityRepository $er) use ( $idevento ) {
                    return $er->createQueryBuilder('e')
                            ->where('e.id=:idevento')
                            ->setParameter('idevento',$idevento);
                },
            ));

        //Llena el select con los atributos que no han sido seleccionados
        $form->add('idatributo', 'entity', array(
            'class' => 'FraterSoftPiaWebBundle:Atributo',
            'label' => 'Atributo',
            'query_builder' => function (EntityRepository $er) use ( $idevento ) {
                return $er->createQueryBuilder('a')
                        ->leftJoin('FraterSoftPiaWebBundle:EventoAtributos','ea','WITH','a.id=ea.idatributo and ea.idevento=:idevento')
                        //->where('ea.idatributo is null')
                        ->setParameter('idevento',$idevento);
            },
        ));     

        $em = $this->getDoctrine()->getManager();

        return $this->render('FraterSoftPiaWebBundle:EventoAtributos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'campos' => $this->getCampos($em,'EventoAtributos'),            
            'idevento' => $idevento,
            'estado' => $estado,
        ));
    }

    public function listaAjaxAction($idevento){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')->arrayAtributosEvento($idevento);

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($entities),
            "data"=>$entities)
                , 'json');
        
        return new response($jsonContent);                    
    }
    
    /**
     * Finds and displays a EventoAtributos entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventoAtributos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FraterSoftPiaWebBundle:EventoAtributos:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Edits an existing EventoAtributos entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventoAtributos entity.');
        }

        $editForm = $this->crearFormulario($entity);
        $editForm->handleRequest($request);
        
        if ($editForm->isSubmitted()) {
            $em->flush();
            return $this->redirect($this->generateUrl('eventoatributos_new', array(
                'idevento' => $entity->getIdevento()->getId(),
                'estado' => 3
            )));            
        }
        
        $errors = $this->getErrorMessages($editForm);        
        return new response($errors);
    }
    /**
     * Deletes a EventoAtributos entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventoAtributos entity.');
        }
        $em->remove($entity);
        $em->flush();        
        return $this->redirect($this->generateUrl('eventoatributos_new', array(
            'idevento' => $entity->getIdevento()->getId(),
            'estado' => 1
        )));            
    }
}
