<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Doctrine\ORM\EntityRepository;

use FraterSoft\PiaWebBundle\Entity\ControlParental;
use FraterSoft\PiaWebBundle\Form\ControlParentalType;

class ControlParentalController extends commonPIAClass
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FraterSoftPiaWebBundle:ControlParental')->findAll();

        return $this->render('FraterSoftPiaWebBundle:ControlParental:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    public function createAction(Request $request)
    {
        $entity = new ControlParental();
        $form = $this->crearFormulario($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('controlparental_new', array(
                'idevento' => $entity->getIdevento()->getId(),
                'estado' => 2
            )));
        }

        return $this->render('FraterSoftPiaWebBundle:ControlParental:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    private function crearFormulario(ControlParental $entity)
    {
        $form = $this->createForm(new ControlParentalType(), $entity, array(
            'method' => 'POST',
        ));
        return $form;
    }

    public function newAction($idevento, $estado)
    {
        $entity = new ControlParental();
        $form   = $this->crearFormulario($entity);

        $form->add('idevento', 'entity', array(
            'class' => 'FraterSoftPiaWebBundle:Evento',
            'query_builder' => function (EntityRepository $er) use ($idevento) {
                return $er->createQueryBuilder('e')
                    ->where('e.id=:idevento')
                    ->setParameter('idevento', $idevento);
            },
        ));

        $em = $this->getDoctrine()->getManager();

        return $this->render('FraterSoftPiaWebBundle:ControlParental:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'campos' => $this->getCampos($em, 'ControlParental'),
            'idevento' => $idevento,
            'estado' => $estado,
        ));
    }

    public function listaAjaxAction($idevento)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:ControlParental')->findBy(array(
            'idevento' => $idevento
        ));

        $data = array();
        foreach ($entities as $entity) {
            $data[] = array(
                'id' => $entity->getId(),
                'edad_control' => $entity->getEdadControl(),
                'documento_pdf' => $entity->getDocumentoPdf(),
                'mensaje' => $entity->getMensaje(),
                'idevento' => $entity->getIdevento() ? $entity->getIdevento()->getId() : null,
            );
        }

        $response = new Response(json_encode(array(
            "recordsTotal" => count($entities),
            "data" => $data
        )));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:ControlParental')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ControlParental entity.');
        }

        $editForm = $this->crearFormulario($entity);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            $em->flush();
            return $this->redirect($this->generateUrl('controlparental_new', array(
                'idevento' => $entity->getIdevento()->getId(),
                'estado' => 3
            )));
        }

        $errors = $this->getErrorMessages($editForm);
        return new Response($errors);
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoft\PiaWebBundle:ControlParental')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ControlParental entity.');
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('controlparental_new', array(
            'idevento' => $entity->getIdevento()->getId(),
            'estado' => 1
        )));
    }

    public function configurarAction(Request $request, $idevento)
    {
        $em = $this->getDoctrine()->getManager();

        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($idevento);
        if (!$evento) {
            throw $this->createNotFoundException('Evento no encontrado');
        }

        $entity = $em->getRepository('FraterSoftPiaWebBundle:ControlParental')->findOneBy(array(
            'idevento' => $idevento
        ));

        if (!$entity) {
            $entity = new ControlParental();
            $entity->setIdevento($evento);
        }

        $form = $this->createForm(new ControlParentalType(), $entity, array(
            'action' => $this->generateUrl('controlparental_configurar', array('idevento' => $idevento)),
        ));
        $form->add('file', 'file', array(
            'label' => 'Archivo PDF',
            'required' => false,
            'mapped' => false,
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $file = $form->get('file')->getData();
                    if ($file) {
                        $uploadDir = __DIR__ . '/../../../../web/bundles/fratersoftpiaweb/fine-uploader/files/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $fileName = 'controlparental_' . $entity->getIdevento()->getId() . '_' . uniqid() . '.' . $file->guessExtension();
                        $file->move($uploadDir, $fileName);
                        $entity->setDocumentoPdf('bundles/fratersoftpiaweb/fine-uploader/files/' . $fileName);
                    }
                    $em->persist($entity);
                    $em->flush();
                    $response = new Response(json_encode(array('success' => true)));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                } catch (\Exception $e) {
                    $response = new Response(json_encode(array('success' => false, 'error' => $e->getMessage())));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
            } else {
                $errors = $this->getErrorMessages($form);
                $response = new Response(json_encode(array('success' => false, 'errors' => $errors)));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }

        return $this->render('FraterSoftPiaWebBundle:ControlParental:configurar.html.twig', array(
            'form' => $form->createView(),
            'entity' => $entity,
        ));
    }
}
