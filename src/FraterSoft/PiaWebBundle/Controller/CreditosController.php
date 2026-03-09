<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FraterSoft\PiaWebBundle\Entity\Creditos;
use FraterSoft\PiaWebBundle\Form\CreditosType;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;


/**
 * Creditos controller.
 *
 */
class CreditosController extends Controller
{

    /**
     * Lists all Creditos entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FraterSoftPiaWebBundle:Creditos')->findAll();

        return $this->render('FraterSoftPiaWebBundle:Creditos:index.html.twig', array(
            'entities' => $entities,
        ));    
    }
    /**
     * Creates a new Creditos entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Creditos();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('creditos_show', array('id' => $entity->getId())));
        }

        return $this->render('FraterSoftPiaWebBundle:Creditos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Creditos entity.
    *
    * @param Creditos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Creditos $entity)
    {
        $form = $this->createForm(new CreditosType(), $entity, array(
            'action' => $this->generateUrl('creditos_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Creditos entity.
     *
     */
    public function newAction()
    {
        $entity = new Creditos();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Creditos entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Creditos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Creditos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Creditos entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Creditos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Creditos entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Creditos entity.
    *
    * @param Creditos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Creditos $entity)
    {
        $form = $this->createForm(new CreditosType(), $entity, array(
            'action' => $this->generateUrl('creditos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Creditos entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Creditos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Creditos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('creditos_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Creditos entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FraterSoftPiaWebBundle:Creditos')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Creditos entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('creditos'));
    }

    /**
     * Creates a form to delete a Creditos entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('creditos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function generarCreditosPorEventoAction($eventoId,$monedaId,$valorCredito)
    {
        $em = $this->getDoctrine()->getManager();

        $creditos = $em->getRepository('FraterSoftPiaWebBundle:Creditos')->findBy(['idevento' => $eventoId]);
        if(empty($creditos)) {
            $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($eventoId);
            if (!$evento) {
                throw $this->createNotFoundException('Evento no encontrado');
            }

            // 2. Obtener inscritos del evento
            $inscritos = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                            ->listarConciliadas($eventoId);
            if (!$inscritos) {
                return new Response('No hay inscritos para este evento.');
            }

            // 3. Crear créditos por cada inscrito
            foreach ($inscritos as $inscrito) {

                $credito = new Creditos();
                $credito->setCedula($inscrito->getIdpia()->getIddocumento());
                $credito->setMonto($valorCredito);
                $credito->setDisponible(true);
                $credito->setIdevento($eventoId);
                $credito->setIdmoneda($monedaId);

                $em->persist($credito);
            }

            // 4. Guardar en BD
            $em->flush();

            $creditos = $em->getRepository('FraterSoftPiaWebBundle:Creditos')->findBy(['idevento' => $eventoId]);
        }

        return $this->render('FraterSoftPiaWebBundle:Creditos:GenerarPorEvento.html.twig', array(
            'creditos' => $creditos,
            'eventoId' => $eventoId,
            'monedaId' => $monedaId,
            'valorCredito' => $valorCredito
        ));
    }    

    /**
     * Deletes a Creditos entity.
     *
     */
    public function deleteAllAction(Request $request, $eventoId)
    {
        $em = $this->getDoctrine()->getManager();
        $creditos = $em->getRepository('FraterSoftPiaWebBundle:Creditos')->findBy(['idevento' => $eventoId]);

        if (!$creditos) {
            throw $this->createNotFoundException('No se encontraron créditos para eliminar.');
        }

        foreach ($creditos as $entity) {
            $em->remove($entity);
            $em->flush();
        }

        $creditos = $em->getRepository('FraterSoftPiaWebBundle:Creditos')->findBy(['idevento' => $eventoId]);

        return $this->render('FraterSoftPiaWebBundle:Creditos:GenerarPorEvento.html.twig', array(
            'creditos' => $creditos,
            'eventoId' => $eventoId
        ));
    }

    public function consultarAjaxAction(Request $request)
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $precioselect = new ArrayCollection();

        $serializer = new Serializer($normalizers, $encoders);



        $idevento = $request->query->get('idevento');
        $iddocumento = $request->query->get('iddocumento');

        $em = $this->getDoctrine()->getManager();
        $creditos = $em->getRepository('FraterSoftPiaWebBundle:Creditos')
            ->findBy(['idevento' => $idevento, 'cedula' => $iddocumento]);

        if ($creditos) {
            $jsonContent = $serializer->serialize(array(
                "recordsTotal"=> count($creditos),
                "data"=>$creditos)
                    , 'json');
            return new response($jsonContent);         
        }
        return new response(0);
    }

    public function consultarAction($idevento, $iddocumento)
    {

        $em = $this->getDoctrine()->getManager();
        $creditos = $em->getRepository('FraterSoftPiaWebBundle:Creditos')
            ->findOneBy(['idevento' => $idevento, 'cedula' => $iddocumento]);
        if ($creditos) {
            return new response($creditos);
        }
        return new response(0);
    }
}