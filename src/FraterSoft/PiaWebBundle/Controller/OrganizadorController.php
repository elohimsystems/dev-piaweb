<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use FraterSoft\PiaWebBundle\Entity\Organizador;
use FraterSoft\PiaWebBundle\Form\OrganizadorType;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

/**
 * Competidor controller.
 *
 */
class OrganizadorController extends commonPIAClass {
    
    /**
    * Creates a form to edit a Organizador entity.
    *
    * @param Organizador $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function crearFormulario(Organizador $entity)
    {
        $form = $this->createForm(new OrganizadorType(), $entity, array(
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Guardar'));
        return $form;
    }    
        
    /**
     * Creates a new Organizador entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Organizador();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            //$entity->setIdOrganizador($em->getRepository('FraterSoftPiaWebBundle:Organizador')->find());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('organizador_gestion', array(
                'idorganizador' => $entity->getIdorganizador()->getId(),
                'estado' => 2
            )));            
        }
        $errors=$this->getErrorMessages($editForm);
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => null,
                    'texto' => json_encode($errors),
                    'tema' => $entity->getIdevento()->getTema()
        ));                

    }

    /**
    * Creates a form to create a Organizador entity.
    *
    * @param Organizador $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Organizador $entity)
    {
        $form = $this->createForm(new OrganizadorType(), $entity, array(
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Organizador entity.
     *
     */
    public function guardarAction(Request $request,$email)
    {
     
        $em = $this->getDoctrine()->getManager();
        
        if($email=='admin')
            // llamar al index de organizadores
            return $this->render('FraterSoftPiaWebBundle:Organizador:guardar.html.twig', array(
                'form'   => $form->createView(),
                'email' => $email,
                'campos' => $this->getCampos($em,'Organizador'),
            ));
        else{
            $entity=$em->getRepository('FraterSoftPiaWebBundle:Organizador')->findBy(array('email'=>$email));
            if (!$entity) {
                $entity = new Organizador();  
                $form = $this->crearFormulario($entity);
            }
            else{
                $form = $this->crearFormulario($entity[0]);
            }
            $form->handleRequest($request); 
            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();          
            }            
            $this->addBotonRegresar($form,$this->get('session')->get('urllistaeventos'));            
            return $this->render('FraterSoftPiaWebBundle:Organizador:guardar.html.twig', array(
                'form'   => $form->createView(),
                'email' => $email,
                //'campos' => $this->getCampos($em,'Organizador'),
            ));
        }
    }

    public function listaAjaxAction($idorganizador){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:Organizador')->arrayListas($idorganizador);

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($entities),
            "data"=>$entities)
                , 'json');
        
        return new response($jsonContent);                    
    }

    /**
    * Creates a form to edit a Organizador entity.
    *
    * @param Organizador $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Organizador $entity)
    {
        $form = $this->createForm(new OrganizadorType(), $entity, array(
            'method' => 'POST',
        ));

        return $form;
    }
    
    /**
     * Edits an existing Organizador entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Organizador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organizador entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('organizador_gestion', array(
                'idorganizador' => $entity->getIdorganizador()->getId(),
                'estado' => 3
            )));            
        }
        
        $errors=$this->getErrorMessages($editForm);
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => null,
                    'texto' => json_encode($errors),
                    'tema' => $entity->getIdevento()->getTema()
        ));        
    }
    
    /**
     * Deletes a Organizador entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Organizador')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organizador entity.');
        }
        $em->remove($entity);
        $em->flush();
            return $this->redirect($this->generateUrl('organizador_gestion', array(
                'idorganizador' => $entity->getIdorganizador()->getId(),
                'estado' => 1
            )));            
    }
    
}
