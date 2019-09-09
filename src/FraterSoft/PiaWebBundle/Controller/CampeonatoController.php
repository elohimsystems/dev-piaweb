<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FraterSoft\PiaWebBundle\Entity\Campeonato;
use FraterSoft\PiaWebBundle\Form\CampeonatoType;

/**
 * Campeonato controller.
 *
 */
class CampeonatoController extends commonPIAClass
{

    /**
     * Lists all Campeonato entities.
     *
     */
    public function indexAction($usuario)
    {
        $em = $this->getDoctrine()->getManager();

        if($usuario=='admin'){
            $entities = $em->getRepository('FraterSoftPiaWebBundle:Campeonato')->findAll();
        }
        else{
            $entities = $em->getRepository('FraterSoftPiaWebBundle:Campeonato')->findBy(array(
                'usuario'=>$usuario,
            ));
        }
        
        $this->get('session')->set('usuario',$usuario);

        $request = $this->container->get('request');
        $routeURL = $request->getRequestUri();
        $this->get('session')->set('urllistacampeonatos',$routeURL);        

        return $this->render('FraterSoftPiaWebBundle:Campeonato:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Campeonato entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Campeonato();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('campeonato', array('usuario' => $this->get('session')->get('usuario'))));
        }

        return $this->render('FraterSoftPiaWebBundle:Campeonato:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Campeonato entity.
    *
    * @param Campeonato $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Campeonato $entity)
    {
        $form = $this->createForm(new CampeonatoType(), $entity, array(
            'action' => $this->generateUrl('campeonato_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Campeonato entity.
     *
     */
    public function newAction()
    {
        $entity = new Campeonato();
        $form   = $this->createCreateForm($entity);
        
        $form->add('usuario','hidden',array(
            'data' => $this->get('session')->get('usuario'),
        ));

        $this->addBotonRegresar($form,$this->generateUrl('campeonato', array('usuario' => $this->get('session')->get('usuario'))));
        
        return $this->render('FraterSoftPiaWebBundle:Campeonato:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Campeonato entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Campeonato')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Campeonato entity.');
        }

        return $this->render('FraterSoftPiaWebBundle:Campeonato:show.html.twig', array(
            'entity'      => $entity,
            'usuario' => $this->get('session')->get('usuario'),
        ));
    }

    /**
     * Displays a form to edit an existing Campeonato entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Campeonato')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Campeonato entity.');
        }

        $editForm = $this->createEditForm($entity);

        $this->addBotonRegresar($editForm,$this->generateUrl('campeonato', array('usuario' => $this->get('session')->get('usuario'))));

        return $this->render('FraterSoftPiaWebBundle:Campeonato:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Campeonato entity.
    *
    * @param Campeonato $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Campeonato $entity)
    {
        $form = $this->createForm(new CampeonatoType(), $entity, array(
            'action' => $this->generateUrl('campeonato_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Campeonato entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Campeonato')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Campeonato entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('campeonato', array('usuario' => $this->get('session')->get('usuario'))));
        }

        return $this->render('FraterSoftPiaWebBundle:Campeonato:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Campeonato entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Campeonato')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Campeonato entity.');
        }

        $em->remove($entity);
        $em->flush();
            
        return $this->redirect($this->generateUrl('campeonato', array('usuario' => $this->get('session')->get('usuario'))));
    }
}
