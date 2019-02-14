<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use FraterSoft\PiaWebBundle\Entity\Publicidad;
use FraterSoft\PiaWebBundle\Form\PublicidadType;
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
class PublicidadController extends commonPIAClass {
    
    /**
    * Creates a form to edit a Publicidad entity.
    *
    * @param Publicidad $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function crearFormulario(Publicidad $entity)
    {
        $form = $this->createForm(new PublicidadType(), $entity, array(
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Guardar'));
        return $form;
    }    
        
    /**
     * Displays a form to create a new Publicidad entity.
     *
     */
    public function guardarAction(Request $request,$email)
    {
     
        $em = $this->getDoctrine()->getManager();
        
        if($email=='admin')
            // llamar al index de publicidades
            return $this->render('FraterSoftPiaWebBundle:Publicidad:guardar.html.twig', array(
                'form'   => $form->createView(),
                'email' => $email,
                'campos' => $this->getCampos($em,'Publicidad'),
            ));
        else{
            $entity = new Publicidad();  
            $busqueda=$em->getRepository('FraterSoftPiaWebBundle:Publicidad')->findBy(array('email'=>$email));
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
            return $this->render('FraterSoftPiaWebBundle:Publicidad:guardar.html.twig', array(
                'form'   => $form->createView(),
                'publicidad' => $entity,
                'email' => $email,
                'campos' => $this->getCampos($em,'Publicidad'),
            ));
        }
    }

    public function listaAjaxAction($idpublicidad){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:Publicidad')->arrayListas($idpublicidad);

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($entities),
            "data"=>$entities)
                , 'json');
        
        return new response($jsonContent);                    
    }
   
    /**
     * Deletes a Publicidad entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Publicidad')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Publicidad entity.');
        }
        $em->remove($entity);
        $em->flush();
            return $this->redirect($this->generateUrl('publicidad_gestion', array(
                'idpublicidad' => $entity->getIdpublicidad()->getId(),
                'estado' => 1
            )));            
    }
    
}
