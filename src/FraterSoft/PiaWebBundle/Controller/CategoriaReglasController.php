<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FraterSoft\PiaWebBundle\Entity\CategoriaReglas;
use FraterSoft\PiaWebBundle\Form\CategoriaReglasType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;


/**
 * CategoriaReglas controller.
 *
 */
class CategoriaReglasController extends Controller
{
    
    /**
     * Lists all Categoria entities.
     *
     */
    public function gestionAction($idcategoria)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FraterSoftPiaWebBundle:CategoriaReglas')->findBy(array('idcategoria'=>$idcategoria));

        return $this->render('FraterSoftPiaWebBundle:CategoriaReglas:gestion.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Lists all Categoria entities.
     *
     */
    public function listarajaxAction($idcategoria)
    {   
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FraterSoftPiaWebBundle:CategoriaReglas')->arrayLista($idcategoria);
        
        //print_r($entities);

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($entities),
            "data"=>$entities)
                , 'json');
        return new response($jsonContent);               
    }
    
    /**
     * Creates a new CategoriaReglas entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new CategoriaReglas();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            //Si el idcompetenia es null, se esta creando la regla desde campeonato
            if(!is_null($entity->getIdcategoria()->getIdcampeonato()))
                return $this->redirect($this->generateUrl('categoria_campeonato_new', array(
                    'idcampeonato' => $entity->getIdcategoria()->getIdcampeonato(),
                    'estado' => 5
                )));            
            else
                return $this->redirect($this->generateUrl('categoria_new', array(
                    'idevento' => $entity->getIdcategoria()->getIdcompetencia()->getIdevento()->getId(),
                    'estado' => 5
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
    * Creates a form to create a CategoriaReglas entity.
    *
    * @param CategoriaReglas $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(CategoriaReglas $entity)
    {
        $form = $this->createForm(new CategoriaReglasType(), $entity, array(
            'action' => $this->generateUrl('categoriareglas_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new CategoriaReglas entity.
     *
     */
    public function newAction()
    {
        $entity = new CategoriaReglas();
        $form   = $this->createCreateForm($entity);

        return $this->render('FraterSoftPiaWebBundle:CategoriaReglas:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a CategoriaReglas entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:CategoriaReglas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CategoriaReglas entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FraterSoftPiaWebBundle:CategoriaReglas:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing CategoriaReglas entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:CategoriaReglas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CategoriaReglas entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FraterSoftPiaWebBundle:CategoriaReglas:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a CategoriaReglas entity.
    *
    * @param CategoriaReglas $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CategoriaReglas $entity)
    {
        $form = $this->createForm(new CategoriaReglasType(), $entity, array(
            'action' => $this->generateUrl('categoriareglas_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing CategoriaReglas entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:CategoriaReglas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CategoriaReglas entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('categoriareglas_edit', array('id' => $id)));
        }

        return $this->render('FraterSoftPiaWebBundle:CategoriaReglas:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a CategoriaReglas entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FraterSoftPiaWebBundle:CategoriaReglas')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CategoriaReglas entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('categoriareglas'));
    }

    /**
     * Deletes a CategoriaReglas entity.
     *
     */
    public function deleteallcategoriaAction($idcategoria)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:CategoriaReglas')->findBy(array('idcategoria'=>$idcategoria));

        foreach($entities as $entity)
            $em->remove($entity);
        $em->flush();

        //Si el idcompetenia es null, se esta creando la regla desde campeonato
        if(!is_null($entity->getIdcategoria()->getIdcampeonato()))
            return $this->redirect($this->generateUrl('categoria_campeonato_new', array(
                'idcampeonato' => $entity->getIdcategoria()->getIdcampeonato(),
                'estado' => 4
            )));            
        else
            return $this->redirect($this->generateUrl('categoria_new', array(
                'idevento' => $entity->getIdcategoria()->getIdcompetencia()->getIdevento()->getId(),
                'estado' => 4
            )));            
    }

    /**
     * Creates a form to delete a CategoriaReglas entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('categoriareglas_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
