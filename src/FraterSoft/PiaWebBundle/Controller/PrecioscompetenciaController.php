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

use FraterSoft\PiaWebBundle\Entity\Precioscompetencia;
use FraterSoft\PiaWebBundle\Form\PrecioscompetenciaType;

/**
 * Precioscompetencia controller.
 *
 */
class PrecioscompetenciaController extends commonPIAClass
{

    /**
     * Lists all Precioscompetencia entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FraterSoftPiaWebBundle:Precioscompetencia')->findAll();

        return $this->render('FraterSoftPiaWebBundle:Precioscompetencia:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Precioscompetencia entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Precioscompetencia();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        
        //$form->getData()->sethasta(new \datetime('2018-01-01 12:00:00'));

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('formaspagocompetencia_new', array(
                'idcompetencia' => $entity->getIdevento()->getId(),
                'estado' => 2
            )));            
        }

        $errors=$this->getErrorMessages($form);
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => $this->generateUrl('preciosevento_new', array('idevento' => $entity->getIdevento()->getId())),
                    'texto' => json_encode($errors),
                    'tema' => $entity->getIdevento()->getTema()
        ));        
    }

    /**
    * Creates a form to create a Precioscompetencia entity.
    *
    * @param Precioscompetencia $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Precioscompetencia $entity)
    {
        $form = $this->createForm(new PrecioscompetenciaType(), $entity, array(
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Precioscompetencia entity.
     *
     */
    public function newAction($idevento,$estado)
    {
        $entity = new Precioscompetencia();
        $form   = $this->createCreateForm($entity);

        $em = $this->getDoctrine()->getManager();                   
                
        $competencias = $em->getRepository('FraterSoftPiaWebBundle:Competencia')->findBy(array('idevento'=>$idevento));                
        
        return $this->render('FraterSoftPiaWebBundle:Precioscompetencia:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'idevento' => $idevento,
            'campos' => $this->getCampos($em,'Precioscompetencia'),            
            'estado' => $estado, 
            'competencias' => $competencias,
        ));
    }
    
    public function listaAjaxAction($idevento){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:Precioscompetencia')->arrayPreciosEvento($idevento);

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($entities),
            "data"=>$entities)
                , 'json');
        
        return new response($jsonContent);                    
    }
    
    /**
    * Creates a form to edit a Precioscompetencia entity.
    *
    * @param Precioscompetencia $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Precioscompetencia $entity)
    {
        $form = $this->createForm(new PrecioscompetenciaType(), $entity, array(
            'method' => 'POST',
        ));
        return $form;
    }
    /**
     * Edits an existing Precioscompetencia entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Precioscompetencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Precioscompetencia entity.');
        }
        
        $editForm = $this->createEditForm($entity);

        $editForm->handleRequest($request);
        
        //$editForm->getdata()->setHasta(new \DateTime('2018-01-01T12:00:00'));

        if ($editForm->isValid()) {
            $em->flush();
            
            return $this->redirect($this->generateUrl('precioscompetencia_new', array(
                'idevento' => $entity->getIdcompetencia()->getIdevento()->getId(),
                'estado' => 3
            )));            
        }

        $errors=$this->getErrorMessages($editForm);
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => $this->generateUrl('precioscompetencia_new', array('idevento' => $entity->getIdevento()->getId())),
                    'texto' => json_encode($errors),
                    'tema' => $entity->getIdevento()->getTema()
        ));        
    }
    /**
     * Deletes a Precioscompetencia entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FraterSoftPiaWebBundle:Precioscompetencia')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Precioscompetencia entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('precioscompetencia'));
    }

    /**
     * Creates a form to delete a Precioscompetencia entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('precioscompetencia_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
