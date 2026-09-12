<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use FraterSoft\PiaWebBundle\Entity\Moneda;
use FraterSoft\PiaWebBundle\Form\MonedaType;

/**
 * Maestro de Monedas (menu Maestros, solo ROLE_SUPER_ADMIN).
 * Rutas /maestros/monedas/* protegidas en security.yml.
 */
class MonedaController extends commonPIAClass
{
    private function crearFormMaestro(Moneda $entity, $action)
    {
        $form = $this->createForm(new MonedaType(), $entity, array(
            'action' => $action,
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Guardar'));
        return $form;
    }

    public function maestroIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:Moneda')
                ->findBy(array(), array('nombre' => 'ASC'));

        return $this->render('FraterSoftPiaWebBundle:Moneda:maestro_index.html.twig', array(
            'entities' => $entities,
        ));
    }

    public function maestroNewAction()
    {
        $entity = new Moneda();
        $entity->setEstatus(1);
        $form = $this->crearFormMaestro($entity, $this->generateUrl('maestro_moneda_create'));

        return $this->render('FraterSoftPiaWebBundle:Moneda:maestro_form.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'titulo' => 'Nueva Moneda',
        ));
    }

    public function maestroCreateAction(Request $request)
    {
        $entity = new Moneda();
        $form = $this->crearFormMaestro($entity, $this->generateUrl('maestro_moneda_create'));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Moneda creada');
            return $this->redirect($this->generateUrl('maestro_moneda'));
        }

        return $this->render('FraterSoftPiaWebBundle:Moneda:maestro_form.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'titulo' => 'Nueva Moneda',
        ));
    }

    public function maestroEditAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Moneda')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Moneda no encontrada');
        }
        $form = $this->crearFormMaestro($entity, $this->generateUrl('maestro_moneda_update', array('id' => $id)));

        return $this->render('FraterSoftPiaWebBundle:Moneda:maestro_form.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'titulo' => 'Editar Moneda',
        ));
    }

    public function maestroUpdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Moneda')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Moneda no encontrada');
        }
        $form = $this->crearFormMaestro($entity, $this->generateUrl('maestro_moneda_update', array('id' => $id)));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Moneda actualizada');
            return $this->redirect($this->generateUrl('maestro_moneda'));
        }

        return $this->render('FraterSoftPiaWebBundle:Moneda:maestro_form.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'titulo' => 'Editar Moneda',
        ));
    }

    public function maestroEliminarAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Moneda')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Moneda no encontrada');
        }
        try {
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Moneda eliminada');
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'No se pudo eliminar: la moneda tiene registros asociados (formas de pago, organizadores, precios, etc). Puede marcarla como Inactiva.');
        }
        return $this->redirect($this->generateUrl('maestro_moneda'));
    }
}
