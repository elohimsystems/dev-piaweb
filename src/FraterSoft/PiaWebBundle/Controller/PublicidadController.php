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

use FraterSoft\PiaWebBundle\Entity\Publicidad;
use FraterSoft\PiaWebBundle\Form\PublicidadType;

/**
 * Publicidad controller.
 *
 */
class PublicidadController extends commonPIAClass
{

    /**
     * Creates a new Publicidad entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Publicidad();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            return $this->redirect($this->generateUrl('publicidad_gestion', array(
                'idevento' => $entity->getIdevento()->getId(),
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
    * Creates a form to create a Publicidad entity.
    *
    * @param Publicidad $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Publicidad $entity)
    {
        $form = $this->createForm(new PublicidadType(), $entity, array(
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Formaspago entity.
     *
     */
    public function gestionAction($idevento,$estado)
    {
        $entity = new Publicidad();
        $form   = $this->createCreateForm($entity);

        $em = $this->getDoctrine()->getManager();        
        
        
        //llena el Select con el evento y lo oculta 
        //llena el select con los estatus 
        $form
            ->add('idevento','entity',array(
            'class' => 'FraterSoftPiaWebBundle:Evento',
            'attr' => array('style'=>'display:none'),
            'label_attr' => array('style'=>'display:none'),
            'query_builder' => function (EntityRepository $er) use ( $idevento ) {
                return $er->createQueryBuilder('e')
                        ->where('e.id=:idevento')
                        ->setParameter('idevento',$idevento);
            }))
            ->add('estatus','choice',$this->OpcionesEstatus())
        ;        
            
        
        return $this->render('FraterSoftPiaWebBundle:Publicidad:gestion.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'idevento' => $idevento,
            'campos' => $this->getCampos($em,'Publicidad'),            
            'estado' => $estado,            
        ));
    }

    public function listaAjaxAction($idevento){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:Publicidad')->arrayLista($idevento);

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($entities),
            "data"=>$entities)
                , 'json');
        
        return new response($jsonContent);                    
    }

    /**
    * Creates a form to edit a Publicidad entity.
    *
    * @param Publicidad $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Publicidad $entity)
    {
        $form = $this->createForm(new PublicidadType(), $entity, array(
            'method' => 'POST',
        ));
        return $form;
    }
    /**
     * Edits an existing Publicidad entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Publicidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Publicidad entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('publicidad_gestion', array(
                'idevento' => $entity->getIdevento()->getId(),
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
     * Deletes a Publicidad entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Publicidad')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Publicidad entity.');
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('publicidad_gestion', array(
            'idevento' => $entity->getIdevento()->getId(),
            'estado' => 1
        )));            
    }

}
