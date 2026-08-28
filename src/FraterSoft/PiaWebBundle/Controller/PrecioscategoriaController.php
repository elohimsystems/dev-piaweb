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

use FraterSoft\PiaWebBundle\Entity\Precioscategoria;
use FraterSoft\PiaWebBundle\Form\PrecioscategoriaType;

/**
 * Precioscategoria controller.
 *
 */
class PrecioscategoriaController extends commonPIAClass
{

    /**
     * Lists all Precioscategoria entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FraterSoftPiaWebBundle:Precioscategoria')->findAll();

        return $this->render('FraterSoftPiaWebBundle:Precioscategoria:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Precioscategoria entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Precioscategoria();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('precioscategoria_new', array(
                'idevento' => $entity->getIdcategoria()->getIdcompetencia()->getIdevento()->getId(),
                'estado' => 2
            )));            
        }

        $errors=$this->getErrorMessages($form);
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => $this->generateUrl('precioscategoria_new', array('idevento' => $entity->getIdcategoria()->getIdcompetencia()->getIdevento()->getId())),
                    'texto' => json_encode($errors),
                    'tema' => $entity->getIdcategoria()->getIdcompetencia()->getIdevento()->getId()->getTema()
        ));        
    }

    /**
    * Creates a form to create a Precioscategoria entity.
    *
    * @param Precioscategoria $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Precioscategoria $entity)
    {
        $form = $this->createForm(new PrecioscategoriaType(), $entity, array(
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Precioscategoria entity.
     *
     */
    public function newAction($idevento,$estado)
    {
        $entity = new Precioscategoria();
        $form   = $this->createCreateForm($entity);

        $form->add('idcategoria','entity',array(
                'class' => 'FraterSoftPiaWebBundle:Categoria',
                'label' => 'Categoria',
                'query_builder' => function (EntityRepository $er) use ( $idevento ) {
                    return $er->createQueryBuilder('ca')
                            ->join('ca.idcompetencia','co')
                            ->where('co.idevento=:idevento')
                            ->setParameter('idevento',$idevento);
                            ;
                },
            ));
        
        $em = $this->getDoctrine()->getManager();

        $competencias = $em->getRepository('FraterSoftPiaWebBundle:Competencia')->findBy(array('idevento'=>$idevento));

        //Mapa idcategoria => idcompetencia, para filtrar en el dialogo las categorias segun
        //la competencia seleccionada.
        $categoriasEvento = $em->getRepository('FraterSoftPiaWebBundle:Categoria')
                ->createQueryBuilder('ca')
                ->join('ca.idcompetencia', 'co')
                ->where('co.idevento = :idevento')
                ->setParameter('idevento', $idevento)
                ->getQuery()->getResult();
        $categoriasCompetencia = array();
        foreach ($categoriasEvento as $categoria) {
            $categoriasCompetencia[$categoria->getId()] = $categoria->getIdcompetencia()->getId();
        }

        return $this->render('FraterSoftPiaWebBundle:Precioscategoria:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'idevento' => $idevento,
            'campos' => $this->getCampos($em,'Precioscategoria'),
            'estado' => $estado,
            'competencias' => $competencias,
            'categoriasCompetencia' => $categoriasCompetencia,
        ));
    }
    
    public function listaAjaxAction($idevento){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:Precioscategoria')->arrayPrecios($idevento);

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($entities),
            "data"=>$entities)
                , 'json');
        
        return new response($jsonContent);                    
    }
    
    /**
    * Creates a form to edit a Precioscategoria entity.
    *
    * @param Precioscategoria $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Precioscategoria $entity)
    {
        $form = $this->createForm(new PrecioscategoriaType(), $entity, array(
            'method' => 'POST',
        ));
        return $form;
    }
    /**
     * Edits an existing Precioscategoria entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Precioscategoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Precioscategoria entity.');
        }
        
        $editForm = $this->createEditForm($entity);

        $editForm->handleRequest($request);
        
        //$editForm->getdata()->setHasta(new \DateTime('2018-01-01T12:00:00'));

        if ($editForm->isValid()) {
            $em->flush();
            
            return $this->redirect($this->generateUrl('precioscategoria_new', array(
                'idevento' => $entity->getIdcategoria()->getIdcompetencia()->getIdevento()->getId(),
                'estado' => 3
            )));            
        }

        $errors=$this->getErrorMessages($editForm);
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => $this->generateUrl('precioscategoria_new', array('idevento' => $entity->getIdcategoria()->getIdcompetencia()->getIdevento()->getId())),
                    'texto' => json_encode($errors),
                    'tema' => $entity->getIdcategoria()->getIdcompetencia()->getIdevento()->getId(),
        ));        
    }
    /**
     * Deletes a Precioscategoria entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Precioscategoria')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Categoria entity.');
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('precioscategoria_new', array(
            'idevento' => $entity->getIdcategoria()->getIdcompetencia()->getIdevento()->getId(),
            'estado' => 1
        )));            
    }
}
