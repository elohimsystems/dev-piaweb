<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use FraterSoft\PiaWebBundle\Entity\Patrocinante;
use FraterSoft\PiaWebBundle\Form\PatrocinanteType;
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
class PatrocinanteController extends commonPIAClass {
        
    /**
     * Creates a new Patrocinante entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Patrocinante();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            print_r("entre");
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('patrocinante_gestion', array(
                'idorganizador' => $entity->getIdorganizador()->getId(),
                'estado' => 2
            )));            
        }
        $errors=$this->getErrorMessages($editForm);
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => null,
                    'texto' => json_encode($errors),
        ));                

    }

    /**
    * Creates a form to create a Patrocinante entity.
    *
    * @param Patrocinante $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Patrocinante $entity)
    {
        $form = $this->createForm(new PatrocinanteType(), $entity, array(
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Patrocinante entity.
     *
     */
    public function gestionAction($idorganizador)
    {
        $entity = new Patrocinante();
        $form   = $this->createCreateForm($entity);
        
        $em = $this->getDoctrine()->getManager();

        //llena el Select con el organizador y Oculta el control
        $form->add('idorganizador','entity',array(
                'class' => 'FraterSoftPiaWebBundle:Organizador',
                //'attr'=>array('style'=>'display:none'),
                //'label_attr'=>array('style'=>'display:none'),
                'query_builder' => function (EntityRepository $er) use ( $idorganizador ) {
                    return $er->createQueryBuilder('o')
                            ->where('o.id=:idorganizador')
                            ->setParameter('idorganizador',$idorganizador);
                },
            ));
        
        return $this->render('FraterSoftPiaWebBundle:Patrocinante:gestion.html.twig', array(
            'form'   => $form->createView(),
            'idorganizador' => $idorganizador,
            'campos' => $this->getCampos($em,'Patrocinante'),
        ));
    }

    public function listaAjaxAction($idorganizador){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:Patrocinante')->arrayListas($idorganizador);

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($entities),
            "data"=>$entities)
                , 'json');
        
        return new response($jsonContent);                    
    }

    /**
    * Creates a form to edit a Patrocinante entity.
    *
    * @param Patrocinante $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Patrocinante $entity)
    {
        $form = $this->createForm(new PatrocinanteType(), $entity, array(
            'method' => 'POST',
        ));

        return $form;
    }
    
    /**
     * Edits an existing Patrocinante entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Patrocinante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Patrocinante entity.');
        }

        $editForm = $this->createEditForm($entity);

        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('patrocinante_gestion', array(
                'idorganizador' => $entity->getIdorganizador()->getId(),
                'estado' => 3
            )));            
        }
        
        $errors=$this->getErrorMessages($editForm);
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => null,
                    'texto' => json_encode($errors),
        ));        
    }
    
    /**
     * Deletes a Patrocinante entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Patrocinante')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Patrocinante entity.');
        }
        $em->remove($entity);
        $em->flush();
            return $this->redirect($this->generateUrl('patrocinante_gestion', array(
                'idorganizador' => $entity->getIdorganizador()->getId(),
                'estado' => 1
            )));            
    }
    
}
