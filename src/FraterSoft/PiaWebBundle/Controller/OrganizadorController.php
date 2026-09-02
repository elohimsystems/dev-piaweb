<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use FraterSoft\PiaWebBundle\Entity\Organizador;
use FraterSoft\PiaWebBundle\Form\OrganizadorType;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

/**
 * Competidor controller.
 *
 */
class OrganizadorController extends commonPIAClass {
    
    /**
    * Creates a form to edit a Organizador entity.
    *
    * @param Organizador $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function crearFormulario(Organizador $entity)
    {
        $form = $this->createForm(new OrganizadorType(), $entity, array(
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Guardar'));
        return $form;
    }    
        
    /**
     * Displays a form to create a new Organizador entity.
     *
     */
    public function guardarAction(Request $request,$email)
    {
     
        $em = $this->getDoctrine()->getManager();
        
        if($email=='admin')
            // llamar al index de organizadores
            return $this->render('FraterSoftPiaWebBundle:Organizador:guardar.html.twig', array(
                'form'   => $form->createView(),
                'email' => $email,
                'campos' => $this->getCampos($em,'Organizador'),
            ));
        else{
            $entity = new Organizador();  
            $busqueda=$em->getRepository('FraterSoftPiaWebBundle:Organizador')->findBy(array('email'=>$email));
            if (!$busqueda)
                $entity->setEmail($email);
            else
                $entity=$busqueda[0];
            $form = $this->crearFormulario($entity);
            $this->addBotonRegresar($form,$this->get('session')->get('urllistaeventos'));            
            $form->handleRequest($request); 
            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();          
            }            
            return $this->render('FraterSoftPiaWebBundle:Organizador:guardar.html.twig', array(
                'form'   => $form->createView(),
                'organizador' => $entity,
                'email' => $email,
                'campos' => $this->getCampos($em,'Organizador'),
            ));
        }
    }

    public function listaAjaxAction($idorganizador){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:Organizador')->arrayListas($idorganizador);

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($entities),
            "data"=>$entities)
                , 'json');
        
        return new response($jsonContent);                    
    }
   
    /**
     * Deletes a Organizador entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Organizador')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organizador entity.');
        }
        $em->remove($entity);
        $em->flush();
            return $this->redirect($this->generateUrl('organizador_gestion', array(
                'idorganizador' => $entity->getIdorganizador()->getId(),
                'estado' => 1
            )));
    }

    // ---------------------------------------------------------------------
    // Maestro de Organizadores (menu Maestros, solo ROLE_SUPER_ADMIN).
    // Rutas /maestros/organizadores/* protegidas en security.yml.
    // ---------------------------------------------------------------------

    private function crearFormMaestro(Organizador $entity, $action)
    {
        $form = $this->createForm(new OrganizadorType(), $entity, array(
            'action' => $action,
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Guardar'));
        return $form;
    }

    private function procesarLogo($form, Organizador $entity)
    {
        $file = $form->get('logo')->getData();
        if ($file) {
            $dir = $this->get('kernel')->getRootDir() . '/../web/bundles/fratersoftpiaweb/fine-uploader/files';
            $nombre = uniqid('org_') . '.' . $file->guessExtension();
            $file->move($dir, $nombre);
            $entity->setLogo($nombre);
        }
    }

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:Organizador')
                ->findBy(array(), array('nombre' => 'ASC'));

        return $this->render('FraterSoftPiaWebBundle:Organizador:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    public function newAction()
    {
        $entity = new Organizador();
        $form = $this->crearFormMaestro($entity, $this->generateUrl('maestro_organizador_create'));

        return $this->render('FraterSoftPiaWebBundle:Organizador:maestro_form.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'titulo' => 'Nuevo Organizador',
        ));
    }

    public function createAction(Request $request)
    {
        $entity = new Organizador();
        $form = $this->crearFormMaestro($entity, $this->generateUrl('maestro_organizador_create'));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->procesarLogo($form, $entity);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Organizador creado');
            return $this->redirect($this->generateUrl('maestro_organizador'));
        }

        return $this->render('FraterSoftPiaWebBundle:Organizador:maestro_form.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'titulo' => 'Nuevo Organizador',
        ));
    }

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Organizador')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Organizador no encontrado');
        }
        $form = $this->crearFormMaestro($entity, $this->generateUrl('maestro_organizador_update', array('id' => $id)));

        return $this->render('FraterSoftPiaWebBundle:Organizador:maestro_form.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'titulo' => 'Editar Organizador',
        ));
    }

    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Organizador')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Organizador no encontrado');
        }
        $form = $this->crearFormMaestro($entity, $this->generateUrl('maestro_organizador_update', array('id' => $id)));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->procesarLogo($form, $entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Organizador actualizado');
            return $this->redirect($this->generateUrl('maestro_organizador'));
        }

        return $this->render('FraterSoftPiaWebBundle:Organizador:maestro_form.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'titulo' => 'Editar Organizador',
        ));
    }

    public function eliminarAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Organizador')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Organizador no encontrado');
        }
        try {
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Organizador eliminado');
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'No se pudo eliminar: el organizador tiene registros asociados (eventos, usuarios, etc.)');
        }
        return $this->redirect($this->generateUrl('maestro_organizador'));
    }

}
