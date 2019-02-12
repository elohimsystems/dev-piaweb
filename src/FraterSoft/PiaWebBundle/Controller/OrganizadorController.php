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
            $entity = new Organizador();  
            $busqueda=$em->getRepository('FraterSoftPiaWebBundle:Organizador')->findBy(array('email'=>$email));
            if (!$busqueda)
                $entity->setEmail($email);
            else
                $entity=$busqueda[0];
            $form = $this->crearFormulario($entity);
            $this->addBotonRegresar($form,$this->get('session')->get('urllistaeventos'));            
            $form->handleRequest($request); 
            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();          
            }            
            return $this->render('FraterSoftPiaWebBundle:Organizador:guardar.html.twig', array(
                'form'   => $form->createView(),
                'organizador' => $entity,
                'email' => $email,
                'campos' => $this->getCampos($em,'Organizador'),
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
