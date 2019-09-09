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

use FraterSoft\PiaWebBundle\Entity\CampeonatoCompetidores;
use FraterSoft\PiaWebBundle\Form\CampeonatoCompetidoresType;


/**
 * CampeonatoCompetidores controller.
 *
 */
class CampeonatoCompetidoresController extends commonPIAClass
{

    /**
     * Lists all CampeonatoCompetidores entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FraterSoftPiaWebBundle:CampeonatoCompetidores')->findAll();

        return $this->render('FraterSoftPiaWebBundle:CampeonatoCompetidores:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new CampeonatoCompetidores entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new CampeonatoCompetidores();
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

        return $this->render('FraterSoftPiaWebBundle:CampeonatoCompetidores:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    
    /**
    * Creates a form to create a CampeonatoClubes entity.
    *
    * @param CampeonatoClubes $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(CampeonatoCompetidores $entity)
    {
        $form = $this->createForm(new CampeonatoCompetidoresType(), $entity, array(
            'method' => 'POST',
        ));

        return $form;
    }    

    /**
     * Displays a form to create a new CampeonatoCompetidores entity.
     *
     */
    public function newAction($idcampeonato,$estado)
    {
        $entity = new CampeonatoCompetidores();
        $form   = $this->createCreateForm($entity);
        
        $em = $this->getDoctrine()->getManager();
        $campeonato=$em->getRepository('FraterSoftPiaWebBundle:Campeonato')->find($idcampeonato);

        return $this->render('FraterSoftPiaWebBundle:CampeonatoCompetidores:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'campos' => $this->getCampos($em,'CampeonatoCompetidores'),            
            'campeonato' => $campeonato,
            'estado' => $estado,
        ));
    }

    public function listaAjaxAction($idcampeonato){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:CampeonatoCompetidores')->arrayCampeonatoCompetidores($idcampeonato);

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($entities),
            "data"=>$entities)
                , 'json');
        
        return new response($jsonContent);                    
    }
    
    /**
     * Finds and displays a CampeonatoCompetidores entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:CampeonatoCompetidores')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CampeonatoCompetidores entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FraterSoftPiaWebBundle:CampeonatoCompetidores:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }
    
    /**
    * Creates a form to edit a CampeonatoClubes entity.
    *
    * @param CampeonatoClubes $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CampeonatoCompetidores $entity)
    {
        $form = $this->createForm(new CampeonatoCompetidoresType(), $entity, array(
            'method' => 'POST',
        ));
        return $form;
    }    

    /**
     * Edits an existing CampeonatoCompetidores entity.
     *
     */
    public function updateAction(Request $request, $idcampeonato,$idcompetidor)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:CampeonatoCompetidores')->findOneBy(array(
            'idcampeonato'=>$idcampeonato,
            'idcompetidor'=>$idcompetidor,
        ));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CampeonatoCompetidores entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        
        if ($editForm->isSubmitted()) {
            $em->flush();
            return $this->redirect($this->generateUrl('campeonatocompetidores_new', array(
                'idcampeonato' => $idcampeonato,
                'estado' => 3
            )));     
        }
        
        $errors = $this->getErrorMessages($editForm);        
        return new response($errors);            
    }
    /**
     * Deletes a CampeonatoCompetidores entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:CampeonatoCompetidores')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CampeonatoCompetidores entity.');
        }
        $em->remove($entity);
        $em->flush();        
        return $this->redirect($this->generateUrl('eventoatributos_new', array(
            'idevento' => $entity->getIdevento()->getId(),
            'estado' => 1
        )));            
    }
}
