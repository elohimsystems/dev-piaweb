<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FraterSoft\PiaWebBundle\Entity\Club;
use FraterSoft\PiaWebBundle\Form\ClubType;

/**
 * Club controller.
 *
 */
class ClubController extends commonPIAClass
{

    /**
     * Lists all Club entities.
     *
     */
    public function indexAction($usuario)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FraterSoftPiaWebBundle:Club')->findAll();
        
        $this->get('session')->set('usuario',$usuario);

        $request = $this->container->get('request');
        $routeURL = $request->getRequestUri();
        $this->get('session')->set('urllistaclubes',$routeURL);        

        return $this->render('FraterSoftPiaWebBundle:Club:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new Club entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Club();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('club', array('usuario' => $this->get('session')->get('usuario'))));
        }

        return $this->render('FraterSoftPiaWebBundle:Club:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Club entity.
    *
    * @param Club $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Club $entity)
    {
        $form = $this->createForm(new ClubType(), $entity, array(
            'action' => $this->generateUrl('club_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Club entity.
     *
     */
    public function newAction()
    {
        $entity = new Club();
        $form   = $this->createCreateForm($entity);

        $form->add('usuario','hidden',array(
            'data' => $this->get('session')->get('usuario'),
        ));

        $this->addBotonRegresar($form,$this->generateUrl('club', array('usuario' => $this->get('session')->get('usuario'))));

        return $this->render('FraterSoftPiaWebBundle:Club:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Club entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Club')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Club entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FraterSoftPiaWebBundle:Club:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Club entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Club')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Club entity.');
        }
        
        $editForm = $this->createEditForm($entity);

        $this->addBotonRegresar($editForm,$this->generateUrl('club', array('usuario' => $this->get('session')->get('usuario'))));

        return $this->render('FraterSoftPiaWebBundle:Club:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Club entity.
    *
    * @param Club $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Club $entity)
    {
        $form = $this->createForm(new ClubType(), $entity, array(
            'action' => $this->generateUrl('club_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Club entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Club')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Club entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('club_edit', array('id' => $id)));
        }

        return $this->render('FraterSoftPiaWebBundle:Club:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Club entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FraterSoftPiaWebBundle:Club')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Club entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('club'));
    }

    /**
     * Creates a form to delete a Club entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('club_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
