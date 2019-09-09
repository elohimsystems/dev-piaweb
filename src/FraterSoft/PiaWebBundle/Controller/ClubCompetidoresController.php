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

use FraterSoft\PiaWebBundle\Entity\ClubCompetidores;
use FraterSoft\PiaWebBundle\Form\ClubCompetidoresType;

/**
 * ClubCompetidores controller.
 *
 */
class ClubCompetidoresController extends commonPIAClass
{

    /**
     * Lists all ClubCompetidores entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FraterSoftPiaWebBundle:ClubCompetidores')->findAll();

        return $this->render('FraterSoftPiaWebBundle:ClubCompetidores:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ClubCompetidores entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ClubCompetidores();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('clubcompetidores_show', array('id' => $entity->getId())));
        }

        return $this->render('FraterSoftPiaWebBundle:ClubCompetidores:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a ClubCompetidores entity.
    *
    * @param ClubCompetidores $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(ClubCompetidores $entity)
    {
        $form = $this->createForm(new ClubCompetidoresType(), $entity, array(
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * Displays a form to create a new ClubCompetidores entity.
     *
     */
    public function newAction($estado)
    {
        $entity = new ClubCompetidores();
        $form   = $this->createCreateForm($entity);
        $em = $this->getDoctrine()->getManager();        

        return $this->render('FraterSoftPiaWebBundle:ClubCompetidores:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'campos' => $this->getCampos($em,'ClubCompetidores'),
            'estado' => $estado,
        ));
    }

    public function listaAjaxAction(){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $usuario=$this->get('session')->get('usuario');
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:ClubCompetidores')->arrayClubCompetidores($usuario);

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($entities),
            "data"=>$entities)
                , 'json');
        
        return new response($jsonContent);                    
    }
    /**
     * Finds and displays a ClubCompetidores entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:ClubCompetidores')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ClubCompetidores entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FraterSoftPiaWebBundle:ClubCompetidores:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing ClubCompetidores entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:ClubCompetidores')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ClubCompetidores entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FraterSoftPiaWebBundle:ClubCompetidores:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ClubCompetidores entity.
    *
    * @param ClubCompetidores $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ClubCompetidores $entity)
    {
        $form = $this->createForm(new ClubCompetidoresType(), $entity, array(
            'action' => $this->generateUrl('clubcompetidores_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ClubCompetidores entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:ClubCompetidores')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ClubCompetidores entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('clubcompetidores_edit', array('id' => $id)));
        }

        return $this->render('FraterSoftPiaWebBundle:ClubCompetidores:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a ClubCompetidores entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FraterSoftPiaWebBundle:ClubCompetidores')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ClubCompetidores entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('clubcompetidores'));
    }

    /**
     * Creates a form to delete a ClubCompetidores entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('clubcompetidores_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
