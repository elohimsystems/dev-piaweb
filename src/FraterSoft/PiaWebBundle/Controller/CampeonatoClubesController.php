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

use FraterSoft\PiaWebBundle\Entity\CampeonatoClubes;
use FraterSoft\PiaWebBundle\Form\CampeonatoClubesType;

/**
 * CampeonatoClubes controller.
 *
 */
class CampeonatoClubesController extends commonPIAClass
{

    /**
     * Lists all CampeonatoClubes entities.
     *
     */
    public function indexAction($idcampeonato)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FraterSoftPiaWebBundle:CampeonatoClubes')->findBy(array('idcampeonato'=>$idcampeonato));

        return $this->render('FraterSoftPiaWebBundle:CampeonatoClubes:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new CampeonatoClubes entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new CampeonatoClubes();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('campeonatoclubes_new', array(
                'idcampeonato' => $entity->getIdcampeonato()->getId(),
                'estado' => 2
            )));            
        }

        return $this->render('FraterSoftPiaWebBundle:CampeonatoClubes:new.html.twig', array(
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
    private function createCreateForm(CampeonatoClubes $entity)
    {
        $form = $this->createForm(new CampeonatoClubesType(), $entity, array(
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new CampeonatoClubes entity.
     *
     */
    public function newAction($idcampeonato,$estado)
    {
        $entity = new CampeonatoClubes();
        $form   = $this->createCreateForm($entity);

        $em = $this->getDoctrine()->getManager();        
        
        //llena el Select con el evento
        $form->add('idcampeonato','entity',array(
                'class' => 'FraterSoftPiaWebBundle:Campeonato',
                'query_builder' => function (EntityRepository $er) use ( $idcampeonato ) {
                    return $er->createQueryBuilder('c')
                            ->where('c.id=:idcampeonato')
                            ->setParameter('idcampeonato',$idcampeonato);
                },
            ));

        //Llena el select con los atributos que no han sido seleccionados
        $form->add('idclub', 'entity', array(
            'class' => 'FraterSoftPiaWebBundle:Club',
            'label' => 'Club',
            'query_builder' => function (EntityRepository $er) use ( $idcampeonato ) {
                return $er->createQueryBuilder('c')
                        ->leftJoin('FraterSoftPiaWebBundle:CampeonatoClubes','cc','WITH','c.id=cc.idclub and cc.idcampeonato=:idcampeonato')
                        //->where('ea.idatributo is null')
                        ->setParameter('idcampeonato',$idcampeonato);
            },
        ));     
            
        $em = $this->getDoctrine()->getManager();
        $campeonato=$em->getRepository('FraterSoftPiaWebBundle:Campeonato')->find($idcampeonato);
        
        return $this->render('FraterSoftPiaWebBundle:CampeonatoClubes:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'campos' => $this->getCampos($em,'CampeonatoClubes'),            
            'campeonato' => $campeonato,
            'estado' => $estado,
        ));
    }

    public function listaAjaxAction($idcampeonato){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:CampeonatoClubes')->arrayCampeonatoClubes($idcampeonato);

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($entities),
            "data"=>$entities)
                , 'json');
        
        return new response($jsonContent);                    
    }

    
    /**
     * Finds and displays a CampeonatoClubes entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:CampeonatoClubes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CampeonatoClubes entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FraterSoftPiaWebBundle:CampeonatoClubes:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing CampeonatoClubes entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:CampeonatoClubes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CampeonatoClubes entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FraterSoftPiaWebBundle:CampeonatoClubes:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a CampeonatoClubes entity.
    *
    * @param CampeonatoClubes $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CampeonatoClubes $entity)
    {
        $form = $this->createForm(new CampeonatoClubesType(), $entity, array(
            'method' => 'POST',
        ));
        return $form;
    }
    /**
     * Edits an existing CampeonatoClubes entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:CampeonatoClubes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CampeonatoClubes entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        
        if ($editForm->isSubmitted()) {
            $em->flush();
            return $this->redirect($this->generateUrl('campeonatoclubes_new', array(
                'idcampeonato' => $entity->getIdcampeonato()->getId(),
                'estado' => 3
            )));            
        }
        
        $errors = $this->getErrorMessages($editForm);        
        return new response($errors);
    }
    /**
     * Deletes a CampeonatoClubes entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:CampeonatoClubes')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CampeonatoClubes entity.');
        }
        $em->remove($entity);
        $em->flush();        
        return $this->redirect($this->generateUrl('campeonatoclubes_new', array(
            'idcampeonato' => $entity->getIdcampeonato()->getId(),
            'estado' => 1
        )));            
    }

    /**
     * Creates a form to delete a CampeonatoClubes entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('campeonatoclubes_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
