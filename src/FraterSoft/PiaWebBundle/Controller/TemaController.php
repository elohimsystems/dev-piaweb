<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use FraterSoft\PiaWebBundle\Entity\Tema;
use FraterSoft\PiaWebBundle\Form\TemaType;
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
class TemaController extends commonPIAClass {
    
    /**
    * Creates a form to edit a Tema entity.
    *
    * @param Tema $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function crearFormulario(Tema $entity)
    {
        $form = $this->createForm(new TemaType(), $entity, array(
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Guardar'));
        return $form;
    }    
        
    /**
     * Displays a form to create a new Tema entity.
     *
     */
    public function guardarAction(Request $request,$email)
    {
     
        $em = $this->getDoctrine()->getManager();
        
        if($email=='admin')
            // llamar al index de temaes
            return $this->render('FraterSoftPiaWebBundle:Tema:guardar.html.twig', array(
                'form'   => $form->createView(),
                'email' => $email,
                'campos' => $this->getCampos($em,'Tema'),
            ));
        else{
            $entity = new Tema();  
            $busqueda=$em->getRepository('FraterSoftPiaWebBundle:Tema')->findBy(array('email'=>$email));
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
            return $this->render('FraterSoftPiaWebBundle:Tema:guardar.html.twig', array(
                'form'   => $form->createView(),
                'tema' => $entity,
                'email' => $email,
                'campos' => $this->getCampos($em,'Tema'),
            ));
        }
    }

    public function listaAjaxAction($idtema){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:Tema')->arrayListas($idtema);

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($entities),
            "data"=>$entities)
                , 'json');
        
        return new response($jsonContent);                    
    }
   
    /**
     * Deletes a Tema entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Tema')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tema entity.');
        }
        $em->remove($entity);
        $em->flush();
            return $this->redirect($this->generateUrl('tema_gestion', array(
                'idtema' => $entity->getIdtema()->getId(),
                'estado' => 1
            )));            
    }
    
}
