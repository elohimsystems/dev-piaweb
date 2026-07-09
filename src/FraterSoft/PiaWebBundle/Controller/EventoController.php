<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FraterSoft\PiaWebBundle\Entity\Evento;
use FraterSoft\PiaWebBundle\Form\EventoType;

/**
 * Evento controller.
 *
 */
class EventoController extends commonPIAClass
{

    /**
     * Lists all Evento entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FraterSoftPiaWebBundle:Evento')->findAll();

        return $this->render('FraterSoftPiaWebBundle:Evento:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Lists all Evento entities by Email.
     *
     */
    public function listaemailAction($email)
    {
        $em = $this->getDoctrine()->getManager();

        if($email=='admin'){
            $entities = $em->getRepository('FraterSoftPiaWebBundle:Evento')->enproceso('admin');             
            $ejecutados = $em->getRepository('FraterSoftPiaWebBundle:Evento')->ejecutados('admin');
            $nivelseguridad = 1;
        }
        else{
            $eventospatrocinantes = $em->getRepository('FraterSoftPiaWebBundle:Organizador')->eventosPubilidadPatrocinantePorEmail($email);
            if($eventospatrocinantes == null){
                $entities = $em->getRepository('FraterSoftPiaWebBundle:Evento')->enproceso($email);
                $ejecutados = $em->getRepository('FraterSoftPiaWebBundle:Evento')->ejecutados($email);
                $nivelseguridad = 1;
            }
            else{
                $entities = $eventospatrocinantes;
                $ejecutados = null;
                $nivelseguridad = 2;
            }
        }
        
        $request = $this->container->get('request');
        $routeURL = $request->getRequestUri();
        $this->get('session')->set('urllistaeventos',$routeURL);        

        return $this->render('FraterSoftPiaWebBundle:Evento:listaporemail.html.twig', array(
            'entities' => $entities,
            'ejecutados' => $ejecutados,
            'email' => $email,
            'nivel_seguridad' => $nivelseguridad,
        ));
    }
    /**
     * Lists all Evento entities by Email.
     *
     */
    public function listacompetidorAction($email)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FraterSoftPiaWebBundle:Evento')
                ->listaPorEmailCompetidor($email);

        return $this->render('FraterSoftPiaWebBundle:Evento:listacompetidor.html.twig', array(
            'entities' => $entities,
        ));
    }    
    /**
     * Creates a new Evento entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Evento();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('evento_show', array('id' => $entity->getId())));
        }

        return $this->render('FraterSoftPiaWebBundle:Evento:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Evento entity.
    *
    * @param Evento $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Evento $entity)
    {
        $form = $this->createForm(new EventoType(), $entity, array(
            'action' => $this->generateUrl('evento_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Evento entity.
     *
     */
    public function newAction()
    {
        $entity = new Evento();
        $form   = $this->createCreateForm($entity);

        return $this->render('FraterSoftPiaWebBundle:Evento:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Evento entity.
     *
     */
    public function showAction($id,$email)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Evento entity.');
        }

        return $this->render('FraterSoftPiaWebBundle:Evento:show.html.twig', array(
            'entity'      => $entity,
            'email' => $email
         ));
    }

    /**
     * Displays a form to edit an existing Evento entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Evento entity.');
        }

        $user = $this->getUser();
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $organizadorUsuario = $user->getIdorganizador();
            $organizadorEvento = $entity->getIdorganizador();
            if (!$organizadorUsuario || !$organizadorEvento || $organizadorUsuario->getId() !== $organizadorEvento->getId()) {
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => $this->generateUrl('frater_soft_pia_web_eventos'),
                    'texto' => 'Acceso no autorizado. Este evento no pertenece a tu organizador.'
                ));
            }
        }

        $editForm = $this->createEditForm($entity);
        $this->addBotonRegresar($editForm,$this->get('session')->get('urllistaeventos'));

        if ($entity->getIdestado()) {
            $editForm->get('pais')->setData($entity->getIdestado()->getIdpais());
        }

        return $this->render('FraterSoftPiaWebBundle:Evento:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Evento entity.
    *
    * @param Evento $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Evento $entity)
    {
        $form = $this->createForm(new EventoType(), $entity, array(
            'action' => $this->generateUrl('evento_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => ['id' => 'formulario'] /* Agregada para formatear estilo del formulario */
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Evento entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Evento entity.');
        }

        $user = $this->getUser();
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $organizadorUsuario = $user->getIdorganizador();
            $organizadorEvento = $entity->getIdorganizador();
            if (!$organizadorUsuario || !$organizadorEvento || $organizadorUsuario->getId() !== $organizadorEvento->getId()) {
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => $this->generateUrl('frater_soft_pia_web_eventos'),
                    'texto' => 'Acceso no autorizado. Este evento no pertenece a tu organizador.'
                ));
            }
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        $request = $this->getRequest();
        $referer = $request->headers->get('referer');         
        if ($editForm->isValid()) {
            $em->flush();
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $referer,
                        'texto' => 'Evento actualizado satisfactoriamente',
            ));
            //return $this->redirect($this->generateUrl('evento_edit', array('id' => $id)));
        }
        foreach ($editForm as $child) {
            if (!$child->isValid()) {
                var_dump($child->getName());
                $errors[$child->getName()] = $this->getErrorMessages($child);
                print_r($errors);
            }
        }
                
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => $referer,
                    'texto' => 'Error al actualizar el evento',
        ));
    }
    
    /**
     * Deletes a Evento entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Evento entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('evento'));
    }

    /**
     * Creates a form to delete a Evento entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('evento_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    public function configurarinscripcionesAction($idevento){
        return $this->render('FraterSoftPiaWebBundle:Evento:configurarinscripciones.html.twig', array(
                    'idevento' => $idevento,
                    'urllistaeventos' => $this->get('session')->get('urllistaeventos')
        ));
    }
}
