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

use FraterSoft\PiaWebBundle\Entity\Formaspago;
use FraterSoft\PiaWebBundle\Form\FormaspagoType;

/**
 * Competencia controller.
 *
 */
class FormaspagoController extends commonPIAClass
{

    /**
     * Creates a new Competencia entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Competencia();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            return $this->redirect($this->generateUrl('competencia_new', array(
                'idevento' => $entity->getIdevento()->getId(),
                'estado' => 2
            )));            
            
        }
        
        $errors=$this->getErrorMessages($editForm);
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => null,
                    'texto' => json_encode($errors),
                    'tema' => $entity->getIdevento()->getTema()
        ));        

    }

    /**
    * Creates a form to create a Competencia entity.
    *
    * @param Competencia $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Formaspago $entity)
    {
        $form = $this->createForm(new FormaspagoType(), $entity, array(
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Formaspago entity.
     *
     */
    public function newAction($idevento,$estado)
    {
        $entity = new Formaspago();
        $form   = $this->createCreateForm($entity);

        $em = $this->getDoctrine()->getManager();        
        
        return $this->render('FraterSoftPiaWebBundle:Formaspago:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'idevento' => $idevento,
            'campos' => $this->getCampos($em,'Formaspago'),            
            'estado' => $estado,            
        ));
    }

    public function listaAjaxAction($idevento){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:Competencia')->arrayLista($idevento);

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($entities),
            "data"=>$entities)
                , 'json');
        
        return new response($jsonContent);                    
    }

    /**
    * Creates a form to edit a Competencia entity.
    *
    * @param Competencia $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Competencia $entity)
    {
        $form = $this->createForm(new CompetenciaType(), $entity, array(
            'method' => 'POST',
        ));
        return $form;
    }
    /**
     * Edits an existing Competencia entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Competencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Competencia entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('competencia_new', array(
                'idevento' => $entity->getIdevento()->getId(),
                'estado' => 3
            )));            
        }
        
        $errors=$this->getErrorMessages($editForm);
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => null,
                    'texto' => json_encode($errors),
                    'tema' => $entity->getIdevento()->getTema()
        ));        

    }
    /**
     * Deletes a Competencia entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Competencia')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Competencia entity.');
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('competencia_new', array(
            'idevento' => $entity->getIdevento()->getId(),
            'estado' => 1
        )));
    }

    // ---------------------------------------------------------------------
    // Maestro de Formas de Pago (menu Maestros, solo ROLE_SUPER_ADMIN).
    // Rutas /maestros/formaspago/* protegidas en security.yml.
    // ---------------------------------------------------------------------

    private function crearFormMaestro(Formaspago $entity, $action)
    {
        $form = $this->createForm(new FormaspagoType(), $entity, array(
            'action' => $action,
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Guardar'));
        return $form;
    }

    public function maestroIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:Formaspago')
                ->findBy(array(), array('nombre' => 'ASC'));

        return $this->render('FraterSoftPiaWebBundle:Formaspago:maestro_index.html.twig', array(
            'entities' => $entities,
        ));
    }

    public function maestroNewAction()
    {
        $entity = new Formaspago();
        $entity->setStatus(1);
        $entity->setVerificable(false);
        $form = $this->crearFormMaestro($entity, $this->generateUrl('maestro_formapago_create'));

        return $this->render('FraterSoftPiaWebBundle:Formaspago:maestro_form.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'titulo' => 'Nueva Forma de Pago',
        ));
    }

    public function maestroCreateAction(Request $request)
    {
        $entity = new Formaspago();
        $form = $this->crearFormMaestro($entity, $this->generateUrl('maestro_formapago_create'));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Forma de pago creada');
            return $this->redirect($this->generateUrl('maestro_formapago'));
        }

        return $this->render('FraterSoftPiaWebBundle:Formaspago:maestro_form.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'titulo' => 'Nueva Forma de Pago',
        ));
    }

    public function maestroEditAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Formaspago')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Forma de pago no encontrada');
        }
        $form = $this->crearFormMaestro($entity, $this->generateUrl('maestro_formapago_update', array('id' => $id)));

        return $this->render('FraterSoftPiaWebBundle:Formaspago:maestro_form.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'titulo' => 'Editar Forma de Pago',
        ));
    }

    public function maestroUpdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Formaspago')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Forma de pago no encontrada');
        }
        $form = $this->crearFormMaestro($entity, $this->generateUrl('maestro_formapago_update', array('id' => $id)));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Forma de pago actualizada');
            return $this->redirect($this->generateUrl('maestro_formapago'));
        }

        return $this->render('FraterSoftPiaWebBundle:Formaspago:maestro_form.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'titulo' => 'Editar Forma de Pago',
        ));
    }

    public function maestroEliminarAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Formaspago')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Forma de pago no encontrada');
        }
        try {
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Forma de pago eliminada');
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'No se pudo eliminar: la forma de pago tiene registros asociados (eventos, organizadores o pagos). Puede marcarla como Inactiva.');
        }
        return $this->redirect($this->generateUrl('maestro_formapago'));
    }

}
