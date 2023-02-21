<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\PropertyAccess\PropertyAccess;

use FraterSoft\PiaWebBundle\Entity\Competidor;
use FraterSoft\PiaWebBundle\Entity\Inscrito;
use FraterSoft\PiaWebBundle\Form\CompetidorType;
use FraterSoft\PiaWebBundle\Entity\CampeonatoCompetidores;

/**
 * Competidor controller.
 *
 */
class CompetidorController extends commonPIAClass {

    /**
     * Lists all Competidor entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FraterSoftPiaWebBundle:Competidor')->findAll();

        return $this->render('FraterSoftPiaWebBundle:Competidor:index.html.twig', array(
                    'entities' => $entities,
        ));
    }

    /**
     * Creates a new Competidor entity.
     *
     */
    public function createAction(Request $request) {
        $entity = new Competidor();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('competidor_show', array('id' => $entity->getId())));
        }

        return $this->render('FraterSoftPiaWebBundle:Competidor:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Competidor entity.
     *
     * @param Competidor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Competidor $entity) {
        $form = $this->createForm(new CompetidorType(), $entity, array(
            'attr' => ['id' => 'competidor-form'],
            'action' => $this->generateUrl('competidor_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Competidor entity.
     *
     */
    public function newAction() {
        $entity = new Competidor();
        $form = $this->createCreateForm($entity);

        return $this->render('FraterSoftPiaWebBundle:Competidor:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Competidor entity.
     *
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Competidor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Competidor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FraterSoftPiaWebBundle:Competidor:show.html.twig', array(
                    'entity' => $entity,
                    'delete_form' => $deleteForm->createView(),));
    }

    /**
     * Displays a form to edit an existing Competidor entity.
     *
     */
    public function editAction($iddocumento) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Competidor')->findOneBy(array('iddocumento'=>$iddocumento));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Competidor entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($entity->getId());

        return $this->render('FraterSoftPiaWebBundle:Competidor:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Competidor entity.
     *
     * @param Competidor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Competidor $entity) {
        $form = $this->createForm(new CompetidorType(), $entity, array(
            'action' => $this->generateUrl('competidor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Competidor entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Competidor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Competidor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('competidor_edit', array('iddocumento' => $entity->getIddocumento())));
        }

        return $this->render('FraterSoftPiaWebBundle:Competidor:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Competidor entity.
     *
     */
//    public function actualizaAction($request) {
//        $em = $this->getDoctrine()->getManager();
//        $competidor = new Competidor();
//
//        $entity = $em->getRepository('FraterSoftPiaWebBundle:Competidor')->findOneBy(array('iddocumento'=>$request['iddocumento']));
//
//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find Competidor entity.');
//        }
//        
//        $jsoncompetidor = json_encode($request);
//        
//        $encoders = array(new XmlEncoder(), new JsonEncoder());
//        $normalizers = array(new GetSetMethodNormalizer());
//        $serializer = new Serializer($normalizers, $encoders);
//
//        $competidor = $serializer->deserialize($jsoncompetidor,'FraterSoft\PiaWebBundle\Entity\Competidor','json');        
//        $competidor->setId($entity->getId());
//        print_r($competidor);
//        
//        //if ($editForm->isValid()) {
//            $em->flush();
//            //$em->persist($entity);
//            return(1);
//        //}
//        //return(0);
//    }
    
    /**
     * Deletes a Competidor entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FraterSoftPiaWebBundle:Competidor')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Competidor entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('competidor'));
    }

    /**
     * Creates a form to delete a Competidor entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('competidor_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

    public function findformAction(Request $request, $idevento) {
        //Valida que la aplicacion no sea usada con Internet Explorer
        $em = $this->getDoctrine()->getManager();
        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($idevento);
        if ($evento) {        
            $browser = $this->getBrowser();
            $navegador = $browser['name'];
            $versionB = $browser['version'];
            if ($navegador == "Internet Explorer") {
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                            'texto' => 'Navegador no soportado por el Sistema',
                            'tema' => $evento->getTema()
                ));            
            }
            
            //Verifica si el evento esta abierto
            $hoy=new \DateTime('now');
            if ($evento->getZonahoraria()) //Valida que el evento tenga configurado el timezone
                $hoy->setTimezone(new \DateTimeZone($evento->getZonahoraria()));
            $fechainicio=$evento->getFechainicio();
            if ($hoy < $fechainicio){
                return $this->render('FraterSoftPiaWebBundle:Competidor:iniciar.html.twig', array(
                            'evento' => $evento,
                ));
            }           

            //Verifica si no se ha configurado el Organizador del evento
            if (!$evento->getIdorganizador()){
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                            'texto' => 'No se ha configurado el Organizador del Evento',
                            'tema' => $evento->getTema()
                ));
            }
            

            //Verifica si el evento no esta cerrado
            if ($evento->getFechacierre() < new \DateTime('now')){
                return $this->redirect($this->generateUrl('competidor_consultar', array('idevento' => $evento->getId())));                
            }

            //Verifica si el esta configurado para control de cupo y valida si llego al maximo
            if ($evento->getCupocontrol()){
                $cantidadinscritos = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->cantidad($idevento);
                if ($cantidadinscritos >= $evento->getCupomaximo())
                    return $this->redirect($this->generateUrl('competidor_consultar', array('idevento' => $evento->getId())));  
            }
            
        } else
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                        'texto' => 'Evento ' . $idevento . ' no ha sido configurado',
                        'tema' => $evento->getTema()
            ));            

        $buscar = '';
        $atributo = '';
        $competidor = new Competidor();
        
        //Si el tipo de evento es campeonato, agrega formulario para buscar por Cédula o numero
        if ($evento->getIdCampeonato()) {
            $form = $this->createFormBuilder(null,array('csrf_protection' => false))
                ->add('atributo', 'choice', array(
                    'choices' => array('iddocumento' => 'Cédula', 'numero' => 'Número'),
                    'data'=>'iddocumento',
                    'label' => 'Buscar Por',
                    'expanded' => true,
                ))
                ->add('buscar', 'text', array(
                    'label' => 'Cédula',
                    'method' => 'POST',
                    'attr' => array('placeholder' => 'Ej: 12660131'),
                )) 
                ->add('Buscar', 'submit', array('label' => 'Iniciar o Consultar tu Inscripción'))
                ->getForm();
        }
        else{//Si el evento no es tipo campeonato busca solo por Cédula
            $form = $this->createFormBuilder(null,array('csrf_protection' => false))
                ->add('atributo', 'hidden', array(
                    'label' => 'Ingresa tu Cédula',
                ))
                ->add('buscar', 'text', array(
                    'label' => 'Cédula',
                    'method' => 'POST',
                    'attr' => array('placeholder' => 'Ej: 12660131'),
                ))
                ->add('Buscar', 'submit', array('label' => 'Iniciar o Consultar tu Inscripción'))
                ->getForm();            
        }
        
        //Busca la edad minima del evento configurada
        $edadminima = $em->getRepository('FraterSoftPiaWebBundle:Evento')
            ->edadminima($idevento);     
                
        if(!is_null($edadminima) && $edadminima<9){
            $form->add('siniddocumento', 'checkbox', array(
                'required' => false,
            ));
        }        

        $form->handleRequest($request);

        //Buscar los precios del evento
        $preciosPorEvento =  $em->getRepository('FraterSoftPiaWebBundle:Preciosevento')
                        ->findBy(array('idevento' => $idevento));

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            //Buscar para eventos tipo Campeonato
            if ($evento->getIdCampeonato()) {
                if($form->get("atributo")->getData()!='iddocumento'){
                    $atributo='id';
                    $campcomp = $em->getRepository('FraterSoftPiaWebBundle:CampeonatoCompetidores')
                    ->findOneBy(array($form->get("atributo")->getData() => $form->get("buscar")->getData()));
                    $buscar=$campcomp?$campcomp->getIdcompetidor():null;
                 }
                 else{
                    $atributo = $form->get("atributo")->getData();
                    $buscar = $form->get("buscar")->getData();
                 }   
            }
            else{
                //Buscar para eventos tipo Unico
                $atributo = 'iddocumento';
                $buscar = $form->get("buscar")->getData();                
            }

            if($atributo == 'iddocumento'){ //Si se esta buscando por IdDocumento
                $competidor = $em->getRepository('FraterSoftPiaWebBundle:Competidor')
                        ->findOneBy(array($atributo => $buscar));
                if (!$competidor) {
                    return $this->redirect($this->generateUrl('inscrito_new', array(
                                        'idevento' => $idevento,
                                        'idcompetidor' => 0,
                                        'iddocumento' => $buscar,
                    )));
                } else {
                    return $this->redirect($this->generateUrl('inscrito_new', array(
                                        'idevento' => $idevento,
                                        'idcompetidor' => $competidor->getId(),
                                        'iddocumento' => $competidor->getIdDocumento(),
                    )));
                }                
            }
            else{ //Si se esta buscando por Numero
                $competidor = $em->getRepository('FraterSoftPiaWebBundle:Competidor')
                        ->findOneBy(array($atributo => $buscar));
                if (!$competidor) {
                    $url = $this->generateUrl('competidor_find', array('idevento' => $idevento));
                    return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                                'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                                'texto' => 'Número no está asignado a ningún Competidor',
                                'tema' => $evento->getTema()
                    ));                    
                } else {
                    return $this->redirect($this->generateUrl('inscrito_new', array(
                                        'idevento' => $idevento,
                                        'idcompetidor' => $competidor->getId(),
                                        'iddocumento' => $competidor->getIdDocumento(),
                    )));
                }                   
            }
        }

        return $this->render('FraterSoftPiaWebBundle:Competidor:find.html.twig', array(
                    'evento' => $evento,
                    'form' => $form->createView(),
                    'preciosEvento' => $preciosPorEvento,
        ));
    }
    
    public function buscarAction(Request $request, $idevento) {
        //Valida que la aplicacion no sea usada con Internet Explorer
        $browser = $this->getBrowser();
        $navegador = $browser['name'];
        $versionB = $browser['version'];
        if ($navegador == "Internet Explorer") {
            return $this->render('FraterSoftPiaWebBundle:Inscrito:noSoportado.html.twig', array(
                        "navegador" => $navegador,
                        "idevento" => $idevento
            ));
        }

        $em = $this->getDoctrine()->getManager();
        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')
                ->find($idevento);
        if ($evento) {        

            //Verifica si no se ha configurado el Organizador del evento
            if (!$evento->getActivo()){
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $this->generateUrl('competidor_buscar', array('idevento' => $idevento)),
                            'texto' => 'El evento esta Inactivo',
                ));
            }

            //Verifica si no se ha configurado el Organizador del evento
            if (!$evento->getIdorganizador()){
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $this->generateUrl('competidor_buscar', array('idevento' => $idevento)),
                            'texto' => 'No se ha configurado el Organizador del Evento',
                ));
            }
            
        } else
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('competidor_buscar', array('idevento' => $idevento)),
                        'texto' => 'Evento ' . $idevento . ' no ha sido configurado',
            ));            

        $buscar = '';
        $atributo = '';
        $competidor = new Competidor();
        
        //Si el tipo de evento es campeonato, agrega formulario para buscar por Cédula o numero
        if ($evento->getIdCampeonato()) {
            $form = $this->createFormBuilder(null)
                ->add('atributo', 'choice', array(
                    'choices' => array('iddocumento' => 'Cédula', 'numero' => 'Número'),
                    'data'=>'iddocumento',
                    'label' => 'Buscar Por',
                    'expanded' => true,
                ))
                ->add('buscar', 'text', array(
                    'label' => 'Cédula',
                    'method' => 'POST',
                )) 
                ->add('Buscar', 'submit')
                ->getForm();
        }
        else{//Si el evento no es tipo campeonato busca solo por Cédula
            $form = $this->createFormBuilder(null)
                ->add('atributo', 'hidden', array(
                    'label' => 'Ingresa tu Cédula',
                ))
                ->add('buscar', 'text', array(
                    'label' => 'Cédula',
                    'method' => 'POST',
                ))
                ->add('Buscar', 'submit')
                ->getForm();            
        }
        
        //Busca la edad minima del evento configurada
        $edadminima = $em->getRepository('FraterSoftPiaWebBundle:Evento')
            ->edadminima($idevento);     
        
        if($edadminima<9){
            $form->add('siniddocumento', 'checkbox', array(
                'required' => false,
            ));
        }        

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            //Buscar para eventos tipo Campeonato
            if ($evento->getIdCampeonato()) {
                if($form->get("atributo")->getData()!='iddocumento'){
                    $atributo='id';
                    $campcomp = $em->getRepository('FraterSoftPiaWebBundle:CampeonatoCompetidores')
                    ->findOneBy(array($form->get("atributo")->getData() => $form->get("buscar")->getData()));
                    $buscar=$campcomp?$campcomp->getIdcompetidor():null;
                 }
                 else{
                    $atributo = $form->get("atributo")->getData();
                    $buscar = $form->get("buscar")->getData();
                 }   
            }
            else{
                //Buscar para eventos tipo Unico
                $atributo = 'iddocumento';
                $buscar = $form->get("buscar")->getData();                
            }

            if($atributo == 'iddocumento'){ //Si se esta buscando por IdDocumento
                $competidor = $em->getRepository('FraterSoftPiaWebBundle:Competidor')
                        ->findOneBy(array($atributo => $buscar));
                if (!$competidor) {
                    return $this->redirect($this->generateUrl('inscrito_new', array(
                                        'idevento' => $idevento,
                                        'idcompetidor' => 0,
                                        'iddocumento' => $buscar,
                    )));
                } else {
                    return $this->redirect($this->generateUrl('inscrito_new', array(
                                        'idevento' => $idevento,
                                        'idcompetidor' => $competidor->getId(),
                                        'iddocumento' => $competidor->getIdDocumento(),
                    )));
                }                
            }
            else{ //Si se esta buscando por Numero
                $competidor = $em->getRepository('FraterSoftPiaWebBundle:Competidor')
                        ->findOneBy(array($atributo => $buscar));
                if (!$competidor) {
                    $url = $this->generateUrl('competidor_buscar', array('idevento' => $idevento));
                    return new Response(
                        '<h1 align="center">Número no está asignado a ningún Competidor</h1><p align="center"><a href="' . $url . '">Regresar</a>' 
                    );
                } else {
                    return $this->redirect($this->generateUrl('inscrito_new', array(
                                        'idevento' => $idevento,
                                        'idcompetidor' => $competidor->getId(),
                                        'iddocumento' => $competidor->getIdDocumento(),
                    )));
                }                   
            }
        }

        return $this->render('FraterSoftPiaWebBundle:Competidor:buscar.html.twig', array(
                    'evento' => $evento,
                    'form' => $form->createView(),
        ));
    }

    public function consultarAction(Request $request, $idevento) {
        //Valida que la aplicacion no sea usada con Internet Explorer
        $browser = $this->getBrowser();
        $navegador = $browser['name'];
        $versionB = $browser['version'];
        if ($navegador == "Internet Explorer") {
            return $this->render('FraterSoftPiaWebBundle:Inscrito:noSoportado.html.twig', array(
                        "navegador" => $navegador,
                        "idevento" => $idevento
            ));
        }

        $em = $this->getDoctrine()->getManager();
        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')
                ->find($idevento);
        if ($evento) {        

            //Verifica si no se ha configurado el Organizador del evento
            if (!$evento->getActivo()){
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $this->generateUrl('competidor_buscar', array('idevento' => $idevento)),
                            'texto' => 'El evento esta Inactivo',
                ));
            }

            //Verifica si no se ha configurado el Organizador del evento
            if (!$evento->getIdorganizador()){
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $this->generateUrl('competidor_buscar', array('idevento' => $idevento)),
                            'texto' => 'No se ha configurado el Organizador del Evento',
                ));
            }
            
        } else
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('competidor_buscar', array('idevento' => $idevento)),
                        'texto' => 'Evento ' . $idevento . ' no ha sido configurado',
            ));            

        $buscar = '';
        $atributo = '';
        $competidor = new Competidor();
        
        //Si el tipo de evento es campeonato, agrega formulario para buscar por Cédula o numero
        if ($evento->getIdCampeonato()) {
            $form = $this->createFormBuilder(null)
                ->add('atributo', 'choice', array(
                    'choices' => array('iddocumento' => 'Cédula', 'numero' => 'Número'),
                    'data'=>'iddocumento',
                    'label' => 'Buscar Por',
                    'expanded' => true,
                ))
                ->add('buscar', 'text', array(
                    'label' => 'Cédula',
                    'method' => 'GET',
                )) 
                ->add('Buscar', 'submit')
                ->getForm();
        }
        else{//Si el evento no es tipo campeonato busca solo por Cédula
            $form = $this->createFormBuilder(null)
                ->add('atributo', 'hidden', array(
                    'label' => 'Ingresa tu Cédula',
                ))
                ->add('buscar', 'text', array(
                    'label' => 'Cédula',
                    'method' => 'GET',
                ))
                ->add('Buscar', 'submit')
                ->getForm();            
        }
        
        //Busca la edad minima del evento configurada
        $edadminima = $em->getRepository('FraterSoftPiaWebBundle:Evento')
            ->edadminima($idevento);     
        
        if($edadminima<9){
            $form->add('siniddocumento', 'checkbox', array(
                'required' => false,
            ));
        }        

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            //Buscar para eventos tipo Campeonato
            if ($evento->getIdCampeonato()) {
                if($form->get("atributo")->getData()!='iddocumento'){
                    $atributo='id';
                    $campcomp = $em->getRepository('FraterSoftPiaWebBundle:CampeonatoCompetidores')
                    ->findOneBy(array($form->get("atributo")->getData() => $form->get("buscar")->getData()));
                    $buscar=$campcomp?$campcomp->getIdcompetidor():null;
                 }
                 else{
                    $atributo = $form->get("atributo")->getData();
                    $buscar = $form->get("buscar")->getData();
                 }   
            }
            else{
                //Buscar para eventos tipo Unico
                $atributo = 'iddocumento';
                $buscar = $form->get("buscar")->getData();                
            }

            $competidor = $em->getRepository('FraterSoftPiaWebBundle:Competidor')
                    ->findOneBy(array($atributo => $buscar));
            if (!$competidor) {
                return $this->render('FraterSoftPiaWebBundle:Competidor:consultar.html.twig', array(
                            'evento' => $evento,
                            'form' => $form->createView(),
                            'mensaje' => 'No existe un participante registrado con ese criterio de busqueda',
                            'tema' => $evento->getTema()
                ));                 
            } else {
                $entity = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->buscarInscrito($idevento, $competidor->getId());
                if ($entity != null) {               
                    if(!is_null($entity[0]->getPagos()[0]) && $entity[0]->getPagos()[0]->getConciliado()){
                        return $this->render('FraterSoftPiaWebBundle:Competidor:consultar.html.twig', array(
                                    'evento' => $evento,
                                    'form' => $form->createView(),
                                    'mensaje' => "El pago de la Pre-Inscripci&oacute;n "
                                    . "fu&eacute; conciliado satisfactoriamente. "
                                    . "El participante con <b>" . $atributo . "=" . $buscar . "</b> ingresado, "
                                    . "est&aacute; oficialmente inscrito para el evento <b>" 
                                    . $evento->getNombre() . "</b>.",
                                    'tema' => $evento->getTema(),
                                    'numero' => $entity[0]->getNumero(),
                        ));                              
                    }
                    else{
                        return $this->render('FraterSoftPiaWebBundle:Competidor:consultar.html.twig', array(
                                    'evento' => $evento,
                                    'form' => $form->createView(),
                                    'mensaje' => 'Se ha encontrado una Pre-Inscripci&oacute;n<br>'
                                    . 'El organizador aun no ha conciliado la informacion de pago. Escriba a <b>' . $evento->getIdorganizador()->getEmail()
                                    . "</b> para mayor informaci&oacute;n",
                                    'tema' => $evento->getTema()
                        ));                                                         
                    }
                }
                else{
                        return $this->render('FraterSoftPiaWebBundle:Competidor:consultar.html.twig', array(
                                    'evento' => $evento,
                                    'form' => $form->createView(),
                                    'mensaje' => 'No se ha encontrado una Inscripci&oacute;n para ese valor de busqueda<br>'
                                    . 'Escriba a <b>' . $evento->getIdorganizador()->getEmail()
                                    . "</b> para mayor informaci&oacute;n",
                                    'tema' => $evento->getTema()
                        ));                              
                }
            }                
        }

        return $this->render('FraterSoftPiaWebBundle:Competidor:consultar.html.twig', array(
                    'evento' => $evento,
                    'form' => $form->createView(),
        ));
    }

    public function cargarXMLAction() {
        $crawler = new Crawler();

        $em = $this->getDoctrine()->getManager();

        $crawler->addXmlContent(file_get_contents('c:\ftpfiles\PIAK02_DataPia.xml'));
        $raiz = $crawler->filter('root');
        $grupo = $crawler->filter('root')->children();
        foreach ($grupo as $domElement) {


            $id = $domElement->getElementsByTagName("IdAtleta")->item(0)->nodeValue;
            $competidor = $em->getRepository('FraterSoftPiaWebBundle:Competidor')->find($id);

            if (!$competidor) {
                $competidor = new Competidor();

                $iddocumento = $domElement->getElementsByTagName("IdDocumento")->item(0)->nodeValue;
                if ($iddocumento)
                    $competidor->setIddocumento($iddocumento);

                $nombre = $domElement->getElementsByTagName("Nombre")->item(0)->nodeValue;
                if ($nombre)
                    $competidor->setNombre($nombre);

                $apellido = $domElement->getElementsByTagName("Apellido")->item(0)->nodeValue;
                if ($apellido)
                    $competidor->setApellido($apellido);

                $foto = $domElement->getElementsByTagName("Foto")->item(0)->nodeValue;
                if ($foto)
                    $competidor->setFoto($foto);

                $fechanacimiento = $domElement->getElementsByTagName("FechaNacimiento")->item(0)->nodeValue;
                if ($fechanacimiento)
                    $competidor->setFechanacimiento($fechanacimiento);

                $sexo = $domElement->getElementsByTagName("Sexo")->item(0)->nodeValue;
                if ($sexo)
                    $competidor->setSexo($sexo);

                $equipo = $domElement->getElementsByTagName("Equipo")->item(0)->nodeValue;
                if ($equipo)
                    $competidor->setEquipo($equipo);

                $edad = $domElement->getElementsByTagName("Edad")->item(0)->nodeValue;
                if ($edad)
                    $competidor->setEdad($edad);

                $peso = $domElement->getElementsByTagName("Peso")->item(0)->nodeValue;
                if ($peso)
                    $competidor->setPeso($peso);

                $condicion = $domElement->getElementsByTagName("Condicion")->item(0)->nodeValue;
                if ($condicion)
                    $competidor->setCondicion($condicion);

                $email = $domElement->getElementsByTagName("Email")->item(0)->nodeValue;
                if ($email)
                    $competidor->setEmail($email);

                $telefono = $domElement->getElementsByTagName("Telefono")->item(0)->nodeValue;
                if ($telefono)
                    $competidor->setTelefono($telefono);

                $telefono = $domElement->getElementsByTagName("Telefono")->item(0)->nodeValue;
                if ($telefono)
                    $competidor->setTelefono($telefono);

                $idestado = $domElement->getElementsByTagName("IdEstado")->item(0)->nodeValue;
                if ($idestado)
                    $competidor->setIdEstado($idestado);

                $idpais = $domElement->getElementsByTagName("IdPais")->item(0)->nodeValue;
                if ($idpais)
                    $competidor->setIdPais($idpais);

                $tallafranela = $domElement->getElementsByTagName("TallaFranela")->item(0)->nodeValue;
                if ($tallafranela)
                    $competidor->setTallafranela($tallafranela);

                $em->persist($competidor);
                $em->flush();

                $inscripciones = $domElement->getElementsByTagName("Inscripcion");
                foreach ($inscripciones as $inscripcion) {
                    $inscrito = new Inscrito();
                    $idevento = $inscripcion->getElementsByTagName("IdEvento")->item(0)->nodeValue;
                    $inscrito->setIdevento($em->getRepository('FraterSoftPiaWebBundle:Evento')->find($idevento));
                    $secuencia = $inscripcion->getElementsByTagName("Id")->item(0)->nodeValue;
                    $inscrito->setSecuencia($secuencia);
                    $inscrito->setIdpia($competidor);
                    print_r('Secuencia: ' . $inscrito->getSecuencia());
                    print_r("<br>");
                    print_r('Evento: ' . $inscrito->getIdevento()->getNombre());
                    print_r("<br>");
                    print_r('Competidor: ' . $inscrito->getIdpia()->getNombre());
                    print_r("<br>");
                    $inscrito = null;
                }

                $competidor = null;
            }
        }
        return new response('x');
    }

    protected function getBrowser() {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
        
        // Next get the name of the useragent yes seperately and for good reason
        $ub ="";
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
                ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == "") {
            $version = "?";
        }

        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern
        );
    }
    
    public function asociadosajaxAction($iddocumento) {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());

        $em = $this->getDoctrine()->getManager();
        $asociados = $em->getRepository('FraterSoftPiaWebBundle:Competidor')
                ->buscaAsociados($iddocumento);
        
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($asociados),
            "data"=>$asociados)
                , 'json');
        return new response($jsonContent);
    }    

    public function validaemailpersonalajaxAction($email) {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();

        $competidor = $em->getRepository('FraterSoftPiaWebBundle:Competidor')
                ->findOneBy(array('emailpersonal'=>$email));        

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($competidor),
            "data"=>$competidor)
                , 'json');
        return new response($jsonContent);            
    }       

    public function importarAction(Request $request){
        $accessor = PropertyAccess::createPropertyAccessor();        
        $em = $this->getDoctrine()->getManager();
        $camposCompetidor = $this->getCampos($em, 'Competidor');
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('competidor_importar'))
            ->setMethod('POST')                
            ->add('archivo', 'text',array(
                'label'=>'Archivo',
            ))
            ->add('actualiza', 'checkbox',array(
                'label'=>'Actualia existentes?',
            ))
            ->add('cabecera', 'checkbox',array(
                'label'=>'Posee cabecera?',
            ))
            ->add('Importar', 'submit')
        ->getForm();     
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $file = "bundles/fratersoftpiaweb/fine-uploader/files/" . $form->get('archivo')->getData();
            $myfile = fopen($file, "r") or die("Imposible abrir el archivo!");
            $csv = array_map('str_getcsv', file($file));
            array_walk($csv, function(&$a) use ($csv) {
              $a = array_combine($csv[0], $a);
            });
            array_shift($csv); # remove column header            
            fclose($myfile);        
            //recorre las lineas del archivo leido
            $count_competidores=0;
            foreach($csv as $csvcompetidor){
                $competidor = new Competidor();
                //recorre los campos de la linea leida
                foreach($csvcompetidor as $clave => $valor){
                    //recorre los campos de la entidad pasada
                    foreach($camposCompetidor as $campo => $valores){
                        if($clave==$campo){
                            switch (true){
                                case $valores['tipo']=='string':
                                    if(strlen($valor)<=$valores['longitud'])
                                        if($valor!="")
                                            $accessor->setValue($competidor, $campo, $valor);
                                    else
                                        $valor='Error de longitud';
                                    break;
                                case $valores['tipo']=='datetime':
                                    if($this->isDate($valor))
                                        $accessor->setValue($competidor, $campo, new \DateTime($valor));
                                    else
                                        $valor='Error de Fecha';
                                    break;
                                case strpos($valores['tipo'],'Entity')>0: //Si el tipo de dato contiene Entity
                                    if($valor){
                                        $entity = $em->getRepository($valores['tipo'])->findOneBy(array('id'=>$valor));
                                        if($entity)
                                            $accessor->setValue($competidor,$campo,$entity);
                                    }
                                    break;
                                default:
                                    if($valor!="")
                                        $accessor->setValue($competidor, $campo, $valor);
                            }
                            //Valida que para los campos unique el valor leido del
                            //archivo csv no exista en la base de datos
                            if($valores['constraint']=='unique'){
                                $unique=$em->getRepository($camposCompetidor['entity'])->findOneBy(array($campo=>$valor));
                                if($unique){
                                    $valor='Error: ' . $valor . ' duplicado';
                                    $accessor->setValue($competidor, $campo, null);
                                }
                            }
                            break;
                        }
                    }
                }
                $competidorfind=$em->getRepository("FraterSoftPiaWebBundle:Competidor")->findOneBy(array('iddocumento'=>$competidor->getIddocumento()));
                if(!$competidorfind){
                    $count_competidores++;
                    $em->persist($competidor);
                }
                else{
                    print_r("entre");
                    $accessor->setValue($competidor,'equipo', $competidorfind->getequipo());
                    $em->persist($competidor);
                    $count_competidores++;
//                    $competidor->setequipo($competidorfind->getequipo());
                }
            }
            try{
                $em->flush();
            }
            catch(\PDOException $e){
                print_r("error");
            }
            
            return $this->render('FraterSoftPiaWebBundle:Competidor:importar.html.twig', array(
                        'form' => $form->createView(),
                        'campos'=> $camposCompetidor,
                        'actualizados'=> $count_competidores
            ));            
        }        
        return $this->render('FraterSoftPiaWebBundle:Competidor:importar.html.twig', array(
                    'form' => $form->createView(),
                    'campos'=> $camposCompetidor,
        ));
        
    }
    
    public function importarencampeonatoAction(Request $request,$idcampeonato){
        $accessor = PropertyAccess::createPropertyAccessor();        
        $em = $this->getDoctrine()->getManager();
        $camposCompetidor = $this->getCampos($em, 'Competidor');
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('competidor_importar_encampeonato', array('idcampeonato' => $idcampeonato)))
            ->setMethod('POST')                
            ->add('archivo', 'text',array(
                'label'=>'Archivo',
            ))
            ->add('actualiza', 'checkbox',array(
                'label'=>'Actualia existentes?',
            ))
            ->add('cabecera', 'checkbox',array(
                'label'=>'Posee cabecera?',
            ))
            ->add('Importar', 'submit')
        ->getForm();     
        
        $this->addbotónRegresar($form,$this->generateUrl('campeonatocompetidores_new', array('idcampeonato' => $idcampeonato)));
        
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $file = "bundles/fratersoftpiaweb/fine-uploader/files/" . $form->get('archivo')->getData();
            $myfile = fopen($file, "r") or die("Imposible abrir el archivo!");
            $csv = array_map('str_getcsv', file($file));
            array_walk($csv, function(&$a) use ($csv) {
              $a = array_combine($csv[0], $a);
            });
            array_shift($csv); # remove column header            
            fclose($myfile);        
            //recorre las lineas del archivo leido
            $count_competidores_i=0; //contador de competidores insertados
            $count_competidores_a=0; //contador de competidores actualizados
            foreach($csv as $csvcompetidor){
                $competidor = new Competidor();
                //recorre los campos de la linea leida
                foreach($csvcompetidor as $clave => $valor){
                    //recorre los campos de la entidad pasada
                    foreach($camposCompetidor as $campo => $valores){
                        if($clave==$campo){
                            switch (true){
                                case $valores['tipo']=='string':
                                    if(strlen($valor)<=$valores['longitud'])
                                        if($valor!="")
                                            $accessor->setValue($competidor, $campo, $valor);
                                    else
                                        $valor='Error de longitud';
                                    break;
                                case $valores['tipo']=='datetime':
                                    if($this->isDate($valor))
                                        $accessor->setValue($competidor, $campo, new \DateTime($valor));
                                    else
                                        $valor='Error de Fecha';
                                    break;
                                case strpos($valores['tipo'],'Entity')>0: //Si el tipo de dato contiene Entity
                                    if($valor){
                                        $entity = $em->getRepository($valores['tipo'])->findOneBy(array('id'=>$valor));
                                        if($entity)
                                            $accessor->setValue($competidor,$campo,$entity);
                                    }
                                    break;
                                default:
                                    if($valor!="")
                                        $accessor->setValue($competidor, $campo, $valor);
                            }
                            //Valida que para los campos unique el valor leido del
                            //archivo csv no exista en la base de datos
                            if($valores['constraint']=='unique'){
                                $unique=$em->getRepository($camposCompetidor['entity'])->findOneBy(array($campo=>$valor));
                                if($unique)
                                    $valor='Error: ' . $valor . ' duplicado';
                                $accessor->setValue($competidor, $campo, $valor);
                            }
                            break;
                        }
                    }
                }
                $competidorfind=$em->getRepository("FraterSoftPiaWebBundle:Competidor")->findOneBy(array('iddocumento'=>$competidor->getIddocumento()));
                if(!$competidorfind){//Si competidor no existe
                    $count_competidores_i++;
                    $em->persist($competidor);
                }
                else{//Si competidor existe
                    //$this->actualizaEntity($em, $competidor, $competidorfind);
                    //$em->persist($competidorfind);
                    //$count_competidores_a++;
                }
                
            }            
            try{
                $em->flush();
            }
            catch(\PDOException $e){
                print_r("error al guardar competidor");
            }
            
            
            //Recorre nuevamente el archivo para insertar los competidores en el campeonato
            $index=1;
            $arrayids = array();
            foreach($csv as $csvcompetidor){
                $campeonatocompetidores = new CampeonatoCompetidores();
                $competidor=$em->getRepository("FraterSoftPiaWebBundle:Competidor")->findOneBy(array(
                    'iddocumento'=>$csvcompetidor['iddocumento']
                ));
                if($competidor){//Si competidor existe
                    
                    //Valida que el identificador del competidor no este repetido en el archivo
                    if(array_search($csvcompetidor['iddocumento'], $arrayids))
                        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                                    'url' => $this->generateUrl('competidor_importar_encampeonato', array(
                                        'idcampeonato' => $idcampeonato
                                    )),
                                    'texto' => "Error en la fila " . $index . 
                                        '. El IDENTIFICADOR:<b>' . $csvcompetidor['iddocumento'] . '</b> del competidor <b>' . 
                                        $csvcompetidor['nombre'] . " " . $csvcompetidor['apellido'] . 
                                        "</b> esta repetido. Elimine el registro repetido en el archivo" .
                                        " y vuelva a cargarlo.",
                        ));      
                    array_push($arrayids,$csvcompetidor['iddocumento']);
                    
                    //Busca los datos del campeonato
                    $campeonato=$em->getRepository("FraterSoftPiaWebBundle:Campeonato")->findOneBy(array(
                        'id'=>$idcampeonato)
                    );
                    if($campeonato)
                        $campeonatocompetidores->setIdcampeonato($campeonato->getId());

                    //Busca y valida la categoria
                    if($csvcompetidor['categoria']!=""){
                        $categoria=$em->getRepository("FraterSoftPiaWebBundle:Categoria")->findOneBy(array(
                            'idcampeonato'=>$idcampeonato,
                            'descripcion'=>$csvcompetidor['categoria'])
                        );
                        if($categoria)
                            $campeonatocompetidores->setIdcategoria($categoria->getId());
                    }
                    if($csvcompetidor['categoria']=="" || !$categoria)
                        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                                    'url' => $this->generateUrl('competidor_importar_encampeonato', array(
                                        'idcampeonato' => $idcampeonato
                                    )),
                                    'texto' => "Error en la fila " . $index . 
                                        '. La CATEGORIA:<b>' . $csvcompetidor['categoria'] . '</b> del competidor <b>' . 
                                        $csvcompetidor['nombre'] . " " . $csvcompetidor['apellido'] .
                                        "</b> no esta cargada en el campeonato. Agregue la categoria al Campeonato" .
                                        " o corrija la categoria en el archivo. Vuelva a cargar el archivo.",
                        ));            

                    //Busca y valida el equipo
                    if($csvcompetidor['equipo']!=""){
                        $club=$em->getRepository("FraterSoftPiaWebBundle:Club")->findOneBy(array(
                            'nombre'=>$csvcompetidor['equipo'])
                        );
                        if($club){
                            $clubcampeonato=$em->getRepository("FraterSoftPiaWebBundle:CampeonatoClubes")->findOneBy(array(
                                'idclub'=>$club->getId(),
                                'idcampeonato'=>$idcampeonato)
                            );
                            if($clubcampeonato);
                                $campeonatocompetidores->setIdclub($clubcampeonato->getIdclub());
                        }
                    }
                    if($csvcompetidor['equipo']=="" || !$club || !$clubcampeonato)
                        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                                    'url' => $this->generateUrl('competidor_importar_encampeonato', array(
                                        'idcampeonato' => $idcampeonato
                                    )),
                                    'texto' => "Error en la fila " . $index . 
                                        '. El CLUB:<b>' . $csvcompetidor['equipo'] . '</b> del competidor <b>' . 
                                        $csvcompetidor['nombre'] . " " . $csvcompetidor['apellido'] .
                                        "</b> no existe o no esta cargado en el campeonato. Agregue la club al Campeonato" .
                                        " o corrija el club en el archivo. Vuelva a cargar el archivo.",
                        ));            

                    $campeonatocompetidores->setIdcompetidor($competidor->getId());
                    if(array_key_exists('numero',$csvcompetidor))
                        $campeonatocompetidores->setNumero($csvcompetidor['numero']);
                    $campeonatocompetidoresfind=$em->getRepository("FraterSoftPiaWebBundle:CampeonatoCompetidores")->findOneBy(array(
                        'idcampeonato'=>$campeonatocompetidores->getIdcampeonato(),
                        'idcompetidor'=>$campeonatocompetidores->getIdcompetidor(),
                    ));
                    if($campeonatocompetidoresfind){
                        $this->actualizaEntity($em, $campeonatocompetidores, $campeonatocompetidoresfind);
                        $em->persist($campeonatocompetidoresfind);
                    }
                    else
                        $em->persist($campeonatocompetidores);
                }
                $index++;
            }
            try{
                $em->flush();
            }
            catch(\PDOException $e){
                print_r("error al guardar competidor");
            }
            
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('competidor_importar_encampeonato', array(
                            'idcampeonato' => $idcampeonato
                        )),
                        'texto' => "Archivo cargado satisfactoriamente ".($count_competidores_i+$count_competidores_a).
                        " registros en total. " . $count_competidores_i .
                        " agregados, y ".$count_competidores_a." actualizados ",
            ));               
//            return $this->render('FraterSoftPiaWebBundle:Competidor:importarencampeonato.html.twig', array(
//                        'form' => $form->createView(),
//                        'campos'=> $camposCompetidor,
//                        'insertados'=> $count_competidores_i,
//                        'actualizados'=> $count_competidores_a,
//                        'idcampeonato'=>$idcampeonato,
//            ));            
        }        
        return $this->render('FraterSoftPiaWebBundle:Competidor:importarencampeonato.html.twig', array(
                    'form' => $form->createView(),
                    'campos'=> $camposCompetidor,
                    'idcampeonato'=>$idcampeonato,
        ));
    }

    public function findgrupoAction(Request $request, $idevento,$idcompetencia,$idgrupo) {
                
        //Valida que la aplicacion no sea usada con Internet Explorer
        $em = $this->getDoctrine()->getManager();

        $cantidad_integrantes=$em->getRepository('FraterSoftPiaWebBundle:Inscrito')->cantidadIntegrantesGrupo($idgrupo);
                
        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($idevento);
        if ($evento) {        
            $browser = $this->getBrowser();
            $navegador = $browser['name'];
            $versionB = $browser['version'];
            if ($navegador == "Internet Explorer") {
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                            'texto' => 'Navegador no soportado por el Sistema',
                            'tema' => $evento->getTema()
                ));            
            }
            
            //Verifica si el evento esta abierto
            $hoy=new \DateTime('now');
            if ($evento->getZonahoraria()) //Valida que el evento tenga configurado el timezone
                $hoy->setTimezone(new \DateTimeZone($evento->getZonahoraria()));
            $fechainicio=$evento->getFechainicio();
            if ($hoy < $fechainicio){
                return $this->render('FraterSoftPiaWebBundle:Competidor:iniciar.html.twig', array(
                            'evento' => $evento,
                ));
            }           

            //Verifica si no se ha configurado el Organizador del evento
            if (!$evento->getIdorganizador()){
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                            'texto' => 'No se ha configurado el Organizador del Evento',
                            'tema' => $evento->getTema()
                ));
            }
            

            //Verifica si el evento no esta cerrado
            if ($evento->getFechacierre() < new \DateTime('now')){
                return $this->redirect($this->generateUrl('competidor_consultar', array('idevento' => $evento->getId())));                
            }

            //Verifica si el esta configurado para control de cupo y valida si llego al maximo
            if ($evento->getCupocontrol()){
                $cantidadinscritos = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->cantidad($idevento);
                if ($cantidadinscritos >= $evento->getCupomaximo())
                    return $this->redirect($this->generateUrl('competidor_consultar', array('idevento' => $evento->getId())));  
            }
            
        } else
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                        'texto' => 'Evento ' . $idevento . ' no ha sido configurado',
                        'tema' => $evento->getTema()
            ));            

        $buscar = '';
        $atributo = '';
        $competidor = new Competidor();
        
        //Si el tipo de evento es campeonato, agrega formulario para buscar por Cédula o numero
        if ($evento->getIdCampeonato()) {
            $form = $this->createFormBuilder(null)
                ->add('atributo', 'choice', array(
                    'choices' => array('iddocumento' => 'Cédula', 'numero' => 'Número'),
                    'data'=>'iddocumento',
                    'label' => 'Buscar Por',
                    'expanded' => true,
                ))
                ->add('buscar', 'text', array(
                    'label' => 'Cédula',
                    'method' => 'POST',
                    'attr' => array('placeholder' => 'Ej: 12660131'),
                )) 
                ->add('Buscar', 'submit', array('label' => 'Agregar Integrante N° ' . strval($cantidad_integrantes+1)))
                ->getForm();
        }
        else{//Si el evento no es tipo campeonato busca solo por Cédula
            $form = $this->createFormBuilder(null)
                ->add('atributo', 'hidden', array(
                    'label' => 'Ingresa tu Cédula',
                ))
                ->add('buscar', 'text', array(
                    'label' => 'Cédula',
                    'method' => 'POST',
                    'attr' => array('placeholder' => 'Ej: 12660131'),
                ))
                ->add('Buscar', 'submit', array('label' => 'Agregar Integrante N° ' . strval($cantidad_integrantes+1)))
                ->getForm();            
        }
        
        //Busca la edad minima del evento configurada
        $edadminima = $em->getRepository('FraterSoftPiaWebBundle:Evento')
            ->edadminima($idevento);     
                
        if(!is_null($edadminima) && $edadminima<9){
            $form->add('siniddocumento', 'checkbox', array(
                'required' => false,
            ));
        }        

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            //Buscar para eventos tipo Campeonato
            if ($evento->getIdCampeonato()) {
                if($form->get("atributo")->getData()!='iddocumento'){
                    $atributo='id';
                    $campcomp = $em->getRepository('FraterSoftPiaWebBundle:CampeonatoCompetidores')
                    ->findOneBy(array($form->get("atributo")->getData() => $form->get("buscar")->getData()));
                    $buscar=$campcomp?$campcomp->getIdcompetidor():null;
                 }
                 else{
                    $atributo = $form->get("atributo")->getData();
                    $buscar = $form->get("buscar")->getData();
                 }   
            }
            else{
                //Buscar para eventos tipo Unico
                $atributo = 'iddocumento';
                $buscar = $form->get("buscar")->getData();                
            }

            if($atributo == 'iddocumento'){ //Si se esta buscando por IdDocumento
                $competidor = $em->getRepository('FraterSoftPiaWebBundle:Competidor')
                        ->findOneBy(array($atributo => $buscar));
                if (!$competidor) {
                    return $this->redirect($this->generateUrl('inscrito_new', array(
                                        'idevento' => $idevento,
                                        'idcompetidor' => 0,
                                        'iddocumento' => $buscar,
                                        'idcompetencia' => $idcompetencia,
                                        'idgrupo' => $idgrupo,
                    )));
                } else {
                    return $this->redirect($this->generateUrl('inscrito_new', array(
                                        'idevento' => $idevento,
                                        'idcompetidor' => $competidor->getId(),
                                        'iddocumento' => $competidor->getIdDocumento(),
                                        'idcompetencia' => $idcompetencia,
                                        'idgrupo'=>$idgrupo,
                    )));
                }                
            }
            else{ //Si se esta buscando por Numero
                $competidor = $em->getRepository('FraterSoftPiaWebBundle:Competidor')
                        ->findOneBy(array($atributo => $buscar));
                if (!$competidor) {
                    $url = $this->generateUrl('competidor_find', array('idevento' => $idevento));
                    return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                                'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                                'texto' => 'Número no está asignado a ningún Competidor',
                                'tema' => $evento->getTema()
                    ));                    
                } else {
                    return $this->redirect($this->generateUrl('inscrito_new', array(
                                        'idevento' => $idevento,
                                        'idcompetidor' => $competidor->getId(),
                                        'iddocumento' => $competidor->getIdDocumento(),
                                        'idcompetencia' => $idcompetencia,
                                        'idgrupo'=>$idgrupo,
                    )));
                }                   
            }
        }

        return $this->render('FraterSoftPiaWebBundle:Competidor:findgrupo.html.twig', array(
                    'evento' => $evento,
                    'form' => $form->createView(),
        ));
    }
    
}
