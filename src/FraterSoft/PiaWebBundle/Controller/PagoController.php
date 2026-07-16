<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityRepository;

use FraterSoft\PiaWebBundle\Entity\Pago;
use FraterSoft\PiaWebBundle\Form\PagoType;
use FraterSoft\PiaWebBundle\Entity\Preciosevento;
use FraterSoft\PiaWebBundle\Entity\Precioscompetencia;
use FraterSoft\PiaWebBundle\Entity\Inscrito;
use FraterSoft\PiaWebBundle\Entity\Formaspagoevento;
use FraterSoft\PiaWebBundle\Entity\Numeracion;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Pago controller.
 *
 */
class PagoController extends commonPIAClass {

    /**
     * Lists all Pago entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FraterSoftPiaWebBundle:Pago')->findAll();

        return $this->render('FraterSoftPiaWebBundle:Pago:index.html.twig', array(
                    'entities' => $entities,
        ));
    }

    /**
     * Creates a new Pago entity
     *
     */
    public function createAction(Request $request,$idinscripcion) {
        $em = $this->getDoctrine()->getManager();

        $inscrito = new \FraterSoft\PiaWebBundle\Entity\Inscrito();
        $inscripcion = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->findOneBy(array(
            'id' => $idinscripcion,
        ));
        
        $entity = new Pago();
        $form = $this->createCreateForm($entity,null,null,$idinscripcion);
        $form->handleRequest($request);
                
        if ($form->isValid()) {
            $entity->setIdinscrito($inscripcion);
            $em->persist($entity);
            
            $em->flush();

            $emails = array();
            $emails = $this->ValidarEmail($inscripcion->getIdpia()->getEmail(),$inscripcion->getIdpia()->getEmailPersonal());
                    
            switch(true){
                case $inscripcion->getIdevento()->getProceso()==1:
                    $mailer = $this->get('app.mail_controller');
                    $mailer->enviarPreinscripcion(
                            "pre-inscripcion@sistemapia.com.ve",
                            "Pre-Inscripcion " . $entity->getIdevento()->getNombre(), 
                            $emails,
                            $this->renderView('FraterSoftPiaWebBundle:Inscrito:email.html.twig', array('entity' => $entity))
                    );         
                    return $this->redirect($this->generateUrl('inscrito_confirmacion', array('id' => $entity->getId())));
                    break;
                case $inscripcion->getIdevento()->getProceso()==2 && $inscripcion->getIdevento()->getRegistropago()==1:
                    $mailer = $this->get('app.mail_controller');
                    $mailer->enviarConfirmacion(
                        "confirmacion@sistemapia.com.ve",
                        "Registro de Pago " . $inscripcion->getIdevento()->getNombre(), 
                        $emails, 
                        $this->renderView('FraterSoftPiaWebBundle:Pago:email_registropago.html.twig', array('inscrito' => $inscripcion))
                    );
                    break;
                case $inscripcion->getIdevento()->getProceso()==2 && $inscripcion->getIdevento()->getRegistropago()==2:
                    $entity->setConciliado(true);
                    $entity->setConciliadoel(new \DateTime('now'));
                    $em->persist($entity);
                    $em->flush();
                    $mailer = $this->get('app.mail_controller');
                    $mailer->enviarConfirmacion(
                        "confirmacion@sistemapia.com.ve",
                        "Confirmacion de Inscripcion " . $inscripcion->getIdevento()->getNombre(), 
                        $emails, 
                        $this->renderView('FraterSoftPiaWebBundle:Inscrito:emailok.html.twig', array('inscrito' => $inscripcion))
                    );
                    break;
            }
            return $this->redirect($this->generateUrl('pago_registro', array(
                'id' => $entity->getId(),
                'email'=>$this->get('session')->get('useremail'))));
        }
        if (!$form->isValid()) {
            print_r("form no es valido");print_r("<hr>");
            if(count($form->getErrors())>0){
                foreach ($form->getErrors() as $key => $error) {
                    if ($form->isRoot()) {
                        $errors['#'][] = $error->getMessage();
                    } else {
                        $errors[] = $error->getMessage();
                    }
                }
                print_r($errors);print_r("<hr>");
            }
        }
        foreach ($form->all() as $child) {
            if(count($child->getErrors())!=0){
                print_r($child->getName() . ': ');print_r($child->getErrors());print_r("<hr>");
            }
        }
        return new response("No Guarde");
    }

    /**
     * Creates a form to create a Pago entity.
     *
     * @param Pago $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Pago $entity,$idevento=null,$idgrupo=null,$idinscripcion=null) {
        if(is_null($idgrupo)){
            $form = $this->createForm(new PagoType(), $entity, array(
                'action' => $this->generateUrl('pago_create',array('idinscripcion'=>$idinscripcion)),
                'method' => 'POST',
            ));
        }
        else{
            $form = $this->createForm(new PagoType(), $entity, array(
                'action' => $this->generateUrl('pago_creategrupo',array(
                    'idevento'=>$idevento,
                    'idgrupo'=>$idgrupo,
                )),
                'method' => 'POST',
            ));
            $this->clearFormPago($form);
            $form
                ->add('idcategoria', 'entity', array(
                    'mapped' => false,
                    'class' => 'FraterSoftPiaWebBundle:Categoria',
                ))
                ->add('info',null,array(
                    'mapped' => false,
                    'label'=>'INFORMACION DE PAGO',
                    'label_attr'=>array('class'=>'group_fields'),
                    'attr'=> array('style'=>'display:none'),
                ))                      
                ->add('precio','text',array(
                    'mapped' => false,
                ))
                ->add('monto')
                ->add('referencia')
                ->add('idbanco')
                ->add('texto','hidden')                
            ;            
        }
        $form->add('submit', 'submit', array('label' => 'Continuar'));

        return $form;
    }

    /**
     * Displays a form to create a new Pago entity.
     *
     */
    public function newAction($idinscripcion,$email) {
        $inscrito = new \FraterSoft\PiaWebBundle\Entity\Inscrito();
        $monedas = new \FraterSoft\PiaWebBundle\Entity\Moneda();
        $em = $this->getDoctrine()->getManager();

        $inscripcion = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->findOneBy(array(
            'id' => $idinscripcion,
        ));
                
        $this->get('session')->set('useremail',$email);

        $pago = $em->getRepository('FraterSoftPiaWebBundle:Pago')
                ->findOneBy(array(
            'idinscrito' => $idinscripcion,
        ));
        if($pago){
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('inscrito_lista_preinscritos', array(
                                'idevento' => $inscripcion->getIdevento()->getId(),
                                'email'=>$email
                         )),
                        'texto' => 'Ya existe un Pago Registrado para la Pre-Inscripcion seleccionada',
                        'tema' => $inscripcion->getIdevento()->getTema()
            ));
        }
        

        $preciosarray = array();
        $preciosarray=$this->preciosArray($emptyvalue_precio,$inscripcion->getIdevento()->getId(), $inscripcion->getIdCompetencia()->getId());
        $monto_total=0;
        
        $entity = new Pago();
        $form = $this->createCreateForm($entity,null,null,$idinscripcion);
        
        /*
         * Busca las cuentas del organizador
         */
        $cuentas = $em->getRepository('FraterSoftPiaWebBundle:Organizadorcuentas')->findBy(array(
                'idorganizador'=>$inscripcion->getIdevento()->getIdorganizador()->getId()
         ));
        $arraycuentas=array();
        foreach ($cuentas as $cuenta){
            $arraycuentas[$cuenta->getId()]=$cuenta->getIdbanco()->getNombre();
        }

        /*
         * Busca las monedas que se manejaran en el evento, atraves de las que se configuraron en las formas de pago,
         * Ademas asigna la moneda por defecto basado en el pais del evento y el pais de mas monedas configuradas
         */        
        $arraymonedas=$this->MonedasEvento($em,$default_moneda,$inscripcion->getIdevento());        
        
        /*
         * Busca los precios del evento, basado en la moneda por defecto
         */
        $preciosarray = array();
        $precios = $this->buscarPrecio(
                $inscripcion->getIdevento()->getId(),
                $inscripcion->getIdcompetencia()->getId(), 
                $inscripcion->getIdcategoria()->getId(),
                $default_moneda
        );
        foreach ($precios as $precio) {
            $preciosarray[$precio['precio']] = $precio['texto'].' '.$precio['moneda'].' '.$precio['precio'];
        }
        $monto_total=0;
        $emptyvalue_precio = (count($preciosarray) > 1)?'Seleccione un Precio':null;        

        $form
            ->add('idmoneda', 'choice', array(
                'choices' => $arraymonedas,
                'data'=>'idmoneda',
                'label' => 'Moneda',
                'expanded' => true,
                'data'=>$default_moneda,
            ))
            ->add('precio','text',array(
                'label'=>'Precio',
                'required' => true,
            ))
            ->add('idcuenta', 'choice', array(
                'choices' => $arraycuentas,
            ))
            ->remove('info')
            ->add('submit', 'submit', array('label' => 'Registrar Pago'));
        ;
        
        /*
         * Busca las formas de pago, con la moneda por defecto
         */
        $formaspago=$this->addFormasPago($form,$inscripcion->getIdevento(),$default_moneda);
        if(is_null($formaspago))
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('competidor_find', array('idevento' => $inscripcion->getIdevento()->getId())),
                        'texto' => "No se ha configurado al menos una Forma De Pago Publica para este Evento",
                        'tema' => $inscripcion->getIdevento()->getTema()
            ));         
        $parametros=array();
        foreach($formaspago as $formapago){
            $parametros[$formapago->getIdformapago()->getId()]=$formapago->getIdformapago()->getParametros();
        }
        
        if($email!=null)
            $this->addBotonRegresar($form,$this->generateUrl('inscrito_lista_preinscritos', array(
                'idevento' => $inscripcion->getIdevento()->getId(),
                'email'=>$email,
                )));
        
        $arrayrecargas=array();
        
        return $this->render('FraterSoftPiaWebBundle:Pago:new.html.twig', array(
                    'entity' => $entity,
                    'inscripcion' => $inscripcion,
                    'form' => $form->createView(),
                    'fpparametros'=>$parametros,
                    'recargas'=>$arrayrecargas,
        ));
    }

    /**
     * Displays a form to create a new Pago entity.
     *
     */
    public function newTDCAction($idinscripcion) {
        $inscrito = new Inscrito();
        $pago = new Pago();        
        $em = $this->getDoctrine()->getManager();
        $inscrito = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->findOneBy(array(
            'id' => $idinscripcion,
        ));
        $monto=$this->buscarPrecio(
                $inscrito->getIdevento()->getId(),
                $inscrito->getIdcompetencia()->getId(),
                $inscrito->getIdcategoria()->getId())[0]['precio'];
        if($monto==0){
            $request = $this->getRequest();
            $referer = $request->headers->get('referer');             
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $referer,
                        'texto' => 'El monto de la inscripcion no ha sido configurado',
            ));            
        }
        $pago->setFechahora(new \DateTime('now'));
        $pago->setMonto($monto);
        $pago->setTipo(3);
        $em->persist($pago);
        $inscrito->setPrecio($monto);
        $inscrito->setIdpago($pago);   
        $em->flush();
        
        $em = $this->getDoctrine()->getManager();
        $formapago=$em->getRepository('FraterSoftPiaWebBundle:Formaspagoevento')
                    ->findBy(array('idevento'=>$inscrito->getIdevento()->getId(),'idformapago'=>3));     
        
        return $this->render('FraterSoftPiaWebBundle:Inscrito:boton123pago.html.twig', array(
                    'entity' => $inscrito,
                    'incremento' => $formapago[0]->getIncremento(),
                    'boton123Pago' => $this->forward('app.inscrito_controller:generarBoton123Pago',
                            array('entity' => $inscrito,'incremento'=>$formapago[0]->getIncremento()))
        ));        
    }

    /**
     * Finds and displays a Pago entity.
     *
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Pago')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pago entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FraterSoftPiaWebBundle:Pago:show.html.twig', array(
                    'entity' => $entity,
                    'delete_form' => $deleteForm->createView(),));
    }

    /**
     * Finds and displays a Pago entity.
     *
     */
    public function showgrupoAction($idpago,$idevento,$idgrupo) {
        $em = $this->getDoctrine()->getManager();

        $pago = $em->getRepository('FraterSoftPiaWebBundle:Pago')->find($idpago);
        
        $inscripcionesgrupo = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->inscritosGrupoObjetos(
                $idevento,
                $idgrupo
        );        
//        print_r($inscripcionesgrupo);
        $atributoscriterios = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')
                ->atributosCriterios($inscripcionesgrupo[0]['idevento']['id']);
        

        if (!$pago) {
            throw $this->createNotFoundException('Unable to find Pago entity.');
        }

        return $this->render('FraterSoftPiaWebBundle:Pago:showgrupo.html.twig', array(
                'pago' => $pago,
                'inscripcionesgrupo'=>$inscripcionesgrupo,
                'atributoscriterios'=>$atributoscriterios,
        ));
    }

    /**
     * Displays a form to edit an existing Pago entity.
     *
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Pago')->find($id);
        $inscrito = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->findOneBy(array(
            'idpago' => $id,
        ));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pago entity.');
        }

        $editForm = $this->createEditForm($entity);

        $editForm
                ->add('conciliado')
                ->add('conciliadoel', 'datetime', array(
                    //'widget' => 'single_text',
                    'data' => new \DateTime('now'),
                    //'format' => 'dd-MM-yyyy hh:mm',
                    'attr' => array('style' => 'display:none'),
                    'label' => ' ',
        ));

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FraterSoftPiaWebBundle:Pago:edit.html.twig', array(
                    'entity' => $entity,
                    'inscrito' => $inscrito,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Pago entity.
     *
     * @param Pago $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Pago $entity) {
        $form = $this->createForm(new PagoType(), $entity, array(
            'action' => $this->generateUrl('pago_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Pago entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $inscrito = new \FraterSoft\PiaWebBundle\Entity\Inscrito();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Pago')->find($id);
        $inscrito = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->findOneBy(array(
            'idpago' => $id,
        ));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pago entity.');
        }

//        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if(!is_null($editForm->getData()->getMonto()))
                $inscrito->setPrecio((double)$editForm->getData()->getMonto());
            $em->persist($inscrito);
            $entity->setMonto((double)$editForm->getData()->getMonto());
            $em->flush();

            if ($entity->getConciliado()) {
                
                $emails = array();
                if(!is_null($inscrito->getIdpia()->getEmail()) && filter_var($inscrito->getIdpia()->getEmail(), FILTER_VALIDATE_EMAIL))
                    array_push($emails,$inscrito->getIdpia()->getEmail());
                if(!is_null($inscrito->getIdpia()->getEmailpersonal()) && filter_var($inscrito->getIdpia()->getEmailpersonal(), FILTER_VALIDATE_EMAIL))
                    array_push($emails,$inscrito->getIdpia()->getEmailpersonal());
                
                //Se envia el correo de confirmacion si el pago es conciliado
//                $mensaje = \Swift_Message::newInstance()
//                        ->setSubject("Confirmacion de Inscripcion " . $inscrito->getIdevento()->getNombre())
//                        ->setFrom("confirmacion@sistemapia.com.ve")
//                        ->setCharset('iso-8859-1')
//                        ->setContentType('text/html')
//                        //->setTo($inscrito->getIdpia()->getEmailpersonal())
//                        ->setTo($emails)
//                        ->setBody(
//                        $this->renderView('FraterSoftPiaWebBundle:Inscrito:emailok.html.twig', array('inscrito' => $inscrito)
//                ));
//                $this->get('mailer')->send($mensaje);
                    $mailer = $this->get('app.mail_controller');
                    $mailer->enviarConfirmacion(
                            "Confirmacion de Inscripcion " . $entity->getIdevento()->getNombre(), 
                            $emails,
                            $this->renderView('FraterSoftPiaWebBundle:Inscrito:emailok.html.twig', array('inscrito' => $entity))
                    );                    
                
                return $this->render('FraterSoftPiaWebBundle:Pago:conciliado.html.twig', array(
                            'inscrito' => $inscrito,
                ));                
            }

            //Paso por aqui, si el participante trato de pagar con TDC y fue cancelada
            if ($entity->getTipo() <> 3){//Si elige otro metodo de pago se le envia la confirmacion de inscripcion
                //Se envia el correo de confirmacion
                $emails = array();
                if(!is_null($inscrito->getIdpia()->getEmail()) && filter_var($inscrito->getIdpia()->getEmail(), FILTER_VALIDATE_EMAIL))
                    array_push($emails,$inscrito->getIdpia()->getEmail());
                if(!is_null($inscrito->getIdpia()->getEmailpersonal()) && filter_var($inscrito->getIdpia()->getEmailpersonal(), FILTER_VALIDATE_EMAIL))
                    array_push($emails,$inscrito->getIdpia()->getEmailpersonal());
                
//                $mensaje = \Swift_Message::newInstance()
//                        ->setSubject("Pre-Inscripcion " . $inscrito->getIdevento()->getNombre())
//                        ->setFrom("pre-inscripcion@sistemapia.com.ve")
//                        ->setCharset('iso-8859-1')
//                        ->setContentType('text/html')
//                        //->setTo(array($inscrito->getIdpia()->getEmailpersonal()))
//                        ->setTo($emails)
//                        ->setBody(
//                        $this->renderView('FraterSoftPiaWebBundle:Inscrito:email.html.twig', array('entity' => $inscrito)
//                ));
//                $this->get('mailer')->send($mensaje);
                    $mailer = $this->get('app.mail_controller');
                    $mailer->enviarPreinscripcion(
                            "Pre-Inscripcion " . $entity->getIdevento()->getNombre(), 
                            $emails,
                        $this->renderView('FraterSoftPiaWebBundle:Inscrito:email.html.twig', array('entity' => $inscrito))
                    );                    
                return $this->redirect($this->generateUrl('inscrito_confirmacion', array('id' => $inscrito->getId())));
            }
            else //Si intenta nuevamente con la TDC se llama a la pagina de boton de pago
                $em = $this->getDoctrine()->getManager();
                $formapago=$em->getRepository('FraterSoftPiaWebBundle:Formaspagoevento')
                            ->findBy(array('idevento'=>$inscrito->getIdevento()->getId(),'idformapago'=>3));    
                return $this->render('FraterSoftPiaWebBundle:Inscrito:boton123pago.html.twig', array(
                            'entity' => $inscrito,
                            'incremento' => $formapago[0]->getIncremento(),
                            'boton123Pago' => $this->forward('app.inscrito_controller:generarBoton123Pago',
                                    array('entity' => $inscrito,'incremento'=>$formapago[0]->getIncremento()))
                ));
        }

        return $this->render('FraterSoftPiaWebBundle:Pago:edit.html.twig', array(
                    'entity' => $entity,
                    'inscrito' => $inscrito,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Pago entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FraterSoftPiaWebBundle:Pago')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Pago entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('pago'));
    }

    /**
     * Creates a form to delete a Pago entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('pago_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

    public function validartcAction($idpago) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->findOneBy(array(
            'idpago' => $idpago,
        ));
        if ($entity) {
            if ($entity->getIdpago()->getTipo() == 3)
                if ($entity->getIdpago()->getConciliado() == true) {
                    //Se envia el correo de confirmacion
                    $emails = array();
                    if(!is_null($entity->getIdpia()->getEmail()) && filter_var($entity->getIdpia()->getEmail(), FILTER_VALIDATE_EMAIL))
                        array_push($emails,$entity->getIdpia()->getEmail());
                    if(!is_null($entity->getIdpia()->getEmailpersonal()) && filter_var($entity->getIdpia()->getEmailpersonal(), FILTER_VALIDATE_EMAIL))
                        array_push($emails,$entity->getIdpia()->getEmailpersonal());
                        
//                    $mensaje = \Swift_Message::newInstance()
//                            ->setSubject("Confirmacion de Inscripcion " . $entity->getIdevento()->getNombre())
//                            ->setFrom("confirmacion@sistemapia.com.ve")
//                            ->setCharset('iso-8859-1')
//                            ->setContentType('text/html')
//                            //->setTo($entity->getIdpia()->getEmailpersonal())
//                            ->setTo($emails)
//                            ->setBody(
//                            $this->renderView('FraterSoftPiaWebBundle:Inscrito:emailok.html.twig', array('inscrito' => $entity)
//                    ));
//                    $this->get('mailer')->send($mensaje);
                    $mailer = $this->get('app.mail_controller');
                    $mailer->enviarConfirmacion(
                            "Confirmacion de Inscripcion " . $entity->getIdevento()->getNombre(), 
                            $emails,
                            $this->renderView('FraterSoftPiaWebBundle:Inscrito:emailok.html.twig', array('inscrito' => $entity))
                    );                    
                }
            return $this->render('FraterSoftPiaWebBundle:Pago:validartc.html.twig', array(
                        'entity' => $entity
            ));
        }
    }

    public function conciliarloteAction($data_json,$idevento){
        $ids="";
        $arry_erros_mails = array();
        $data_array=json_decode($data_json, $assoc = true);
        for($i=0;$i<count($data_array);$i++){
            $separador = ($i==count($data_array)-1)?"":",";
            $ids .= $data_array[$i].$separador;
        }
        
        $em = $this->getDoctrine()->getManager();
        
        //Actualiza a traves de sql el lote de secuencia de inscripciones 
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Pago')
                ->conciliarLote($ids,$idevento);       
        
        $inscritos = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->buscarSecuencias($ids,$idevento);      
        
        //Envia los correo a los inscritos conciliados
        foreach ($inscritos as $inscrito) {
            //Enumera los inscritos conciliados
            $numeracion=$this->get('app.numeracion_controller');
            $respuesta = $numeracion->enumerar($idevento);
            if($respuesta<0){
                $request = $this->getRequest();
                $referer = $request->headers->get('referer');   
                /*if($respuesta==commonPIAClass::NUMERACION_NO_CONFIGURADA)
                    return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                                'url' => $referer,
                                'texto' => 'Se concilio pero no se pudo enumerar. Falta configuracion de numeracion',
                    ));     */ 
                if($respuesta==commonPIAClass::NUMERACIONEXTERNA_NO_CONFIGURADA)
                    return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                                'url' => $referer,
                                'texto' => 'Se concilio pero no se pudo enumerar. No existen numeros externos disponibles',
                    ));                
            }
            
            try{
                $emails = array();
                if(!is_null($inscrito->getIdpia()->getEmail()) && filter_var($inscrito->getIdpia()->getEmail(), FILTER_VALIDATE_EMAIL))
                    array_push($emails,$inscrito->getIdpia()->getEmail());
                if(!is_null($inscrito->getIdpia()->getEmailpersonal()) && filter_var($inscrito->getIdpia()->getEmailpersonal(), FILTER_VALIDATE_EMAIL))
                    array_push($emails,$inscrito->getIdpia()->getEmailpersonal());
                
//                $mensaje = \Swift_Message::newInstance()
//                        ->setSubject("Confirmacion de Inscripcion " . $inscrito->getIdevento()->getNombre())
//                        ->setFrom("confirmacion@sistemapia.com.ve")
//                        ->setCharset('iso-8859-1')
//                        ->setContentType('text/html')
//                        ->setTo($emails)
//                        ->setBody(
//                        $this->renderView('FraterSoftPiaWebBundle:Inscrito:emailok.html.twig', array('inscrito' => $inscrito)
//                ));
//                $this->get('mailer')->send($mensaje);  
                $mailer = $this->get('app.mail_controller');
                $mailer->enviarConfirmacion(
                        "confirmacion@sistemapia.com.ve",
                        "Confirmacion de Inscripcion " . $inscrito->getIdevento()->getNombre(), 
                        $emails,
                        $this->renderView('FraterSoftPiaWebBundle:Inscrito:emailok.html.twig', array('inscrito' => $inscrito))
                );                    
            } catch(\Swift_TransportException $e){
                $mail_error = array();
                $mail_error['id']=$inscrito->getSecuencia();
                $mail_error['nombre']=$inscrito->getIdpia()->getNombre();
                $mail_error['apellido']=$inscrito->getIdpia()->getApellido();
                $mail_error['email']=$inscrito->getIdpia()->getEmailpersonal();
                $arry_erros_mails[$inscrito->getId()]=$mail_error;
            }                
        }
        return $this->render('FraterSoftPiaWebBundle:Pago:conciliadolote.html.twig', array(
            'inscritos' => $inscritos,));
    }
    
    public function tdcnoconciliadoAction($id) {
        $em = $this->getDoctrine()->getManager();

        $inscrito = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->findOneBy(array(
            'idpago' => $id,
        ));
        if (!$inscrito) {
            throw $this->createNotFoundException('Unable to find Pago entity.');
        }

        /*$precios=$this->buscarPrecio(
                $inscrito->getIdevento()->getId(),
                $inscrito->getIdcompetencia()->getId(),
                $inscrito->getIdcategoria()->getId()
        );
        
        $array_precios = array();
        foreach($precios as $precio){
            $array_precios[(string)$precio['precio']]= $precio['texto'] . " " . $precio['moneda'] . " " . $precio['precio'];
        }*/
        

        $editForm = $this->createEditForm($inscrito->getIdpago());
        $editForm
                /*->add('monto','choice',array(
                    'label'=>'Precios',
                    'choices' => $array_precios,
                    'empty_value' => 'Seleccione El Precio'
                ))*/
                ->add('monto','text',array(
                    'label'=>'Total a Pagar',
                    'data'=>$inscrito->getIdpago()->getMonto(),
                    'read_only'=>true,
                ))
                ->remove('conciliado')
                ->remove('conciliadoel')
        ;
        
        $arrayincrementos= array();
        $this->addFormasPago($editForm,$inscrito->getIdevento());  
        $formaspago=$em->getRepository('FraterSoftPiaWebBundle:Formaspagoevento')
                ->findBy(array(
            'idevento' => $inscrito->getIdevento(),
        ));
        foreach($formaspago as $formapago){
            $arrayincrementos[$formapago->getIdformapago()->getId()]=$formapago->getIncremento();
        }

        return $this->render('FraterSoftPiaWebBundle:Pago:tdcnoconciliado.html.twig', array(
                    'inscrito' => $inscrito,
                    'edit_form' => $editForm->createView(),
                    'incremento' => $arrayincrementos,
        ));
    }
    
    public function emailtdcnoconciliadosAction($idevento) {
        $num_notifiaciones = 0;
        $em = $this->getDoctrine()->getManager();
        $operacionestdc = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->listarOperacionesTDC($idevento);
        foreach ($operacionestdc as $operaciontdc) {
            try{
                if(!$operaciontdc->getIdpago()->getNotificado() && !$operaciontdc->getIdpago()->getConciliado()){
                    $date = new \DateTime('now');
                    $datetime = $date->format('Y-m-d H:i:s');
                    $operaciontdc->getIdPago()->setNotificado($date);
                    $em->persist($operaciontdc->getIdPago());
                    $em->flush();
                    
                    $emails = array();
                    if(!is_null($operaciontdc->getIdpia()->getEmail()) && filter_var($operaciontdc->getIdpia()->getEmail(), FILTER_VALIDATE_EMAIL))
                        array_push($emails,$operaciontdc->getIdpia()->getEmail());
                    if(!is_null($operaciontdc->getIdpia()->getEmailpersonal()) && filter_var($operaciontdc->getIdpia()->getEmailpersonal(), FILTER_VALIDATE_EMAIL))
                        array_push($emails,$operaciontdc->getIdpia()->getEmailpersonal());
                    
//                    $mensaje = \Swift_Message::newInstance()
//                            ->setSubject("Pago no Conciliado en " . $operaciontdc->getIdevento()->getNombre())
//                            ->setFrom("pagos@sistemapia.com.ve")
//                            ->setCharset('iso-8859-1')
//                            ->setContentType('text/html')
//                            //->setTo($operaciontdc->getIdpia()->getEmailpersonal())
//                            ->setTo($emails)
//                            ->setBody(
//                            $this->renderView('FraterSoftPiaWebBundle:Pago:email_tdcnoconciliados.html.twig', 
//                                    array('operaciontdc' => $operaciontdc)
//                    ));
//                    $this->get('mailer')->send($mensaje);   
                    $mailer = $this->get('app.mail_controller');
                    $mailer->enviarPago(
                            "Pago no Conciliado en " . $entity->getIdevento()->getNombre(), 
                            $emails,
                            $this->renderView('FraterSoftPiaWebBundle:Pago:email_tdcnoconciliados.html.twig', 
                                    array('operaciontdc' => $operaciontdc))
                    );                    
                    $num_notifiaciones++;
                }
            } catch(\Swift_TransportException $e){
                $mail_error = array();
                $mail_error['id']=$operaciontdc->getSecuencia();
                $mail_error['nombre']=$operaciontdc->getIdpia()->getNombre();
                $mail_error['apellido']=$operaciontdc->getIdpia()->getApellido();
                $mail_error['email']=$operaciontdc->getIdpia()->getEmailpersonal();
                $arry_erros_mails[$operaciontdc->getId()]=$mail_error;
            }                
        }
        $request = $this->getRequest();
        $referer = $request->headers->get('referer');         
        if($num_notifiaciones>0)
            return $this->render('FraterSoftPiaWebBundle:Default:progressbar.html.twig', array(
                        'url' => $referer,
                        'texto' => 'Se han enviado ' . $num_notifiaciones . ' notificaciones satisfactoriamente a los participante pendientes por pago',
            ));
        else{
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $referer,                    
                        'texto' => 'No existen Pagos No Conciliados por notificar',
            ));
        }
    }    
    
    public function newgrupoAction($idevento,$idcompetencia,$idgrupo) {
        $em = $this->getDoctrine()->getManager();

        $evento=$em->getRepository('FraterSoftPiaWebBundle:Evento')->find($idevento);
        
        $campos=["iddocumento","nombre","apellido"];
        $inscripcionesgrupo = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->inscritosGrupo($idevento,$idgrupo,$campos);
        $competencia=$em->getRepository('FraterSoftPiaWebBundle:Competencia')->find($idcompetencia);

        if(count($inscripcionesgrupo)<$competencia->getGrupo()->getIntegrantes())
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('competidor_findgrupo', array(
                            'idevento' => $idevento,
                            'idcompetencia' => $idcompetencia,
                            'idgrupo' => $idgrupo,)),
                        'texto' => 'El grupo no tiene instegrantes necesarios, sera redirigido para agregar el resto de los instegrantes',
                        'tema' => $evento->getTema()
            ));            
        
        $entity = new Pago();
        $form = $this->createCreateForm($entity,$idevento,$idgrupo);
        
        $preciosarray = array();
        $preciosarray=$this->preciosArray($emptyvalue_precio,$idevento, $idcompetencia);
//        $preciosarray = array();
//        $precios = $this->buscarPrecio($idevento, $idcompetencia, null);
//        foreach ($precios as $precio) {
//            $preciosarray[$precio['precio']] = $precio['texto'].' '.$precio['moneda'].' '.$precio['precio'];
//        }
//        $emptyvalue_precio = (count($preciosarray) > 1)?'Seleccione un Precio':null;

        $monto_total=0;
        /***********************************************************************
         * Este bloque de codigo esta exactamente igual en inscrito controller
         **********************************************************************/
        $categorias = $this->seleccionaCategoriasGrupo($idevento,$idcompetencia,$idgrupo);
        if ($categorias->count()==0) {
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                        'texto' => 'No existe una Categorias aplicable a este grupo',
                        'tema' => $evento->getTema()
            ));
        }
        $emptyvalue_categoria = (count($categorias) > 1)?'Seleccione una Categoría':null;
        /**********************************************************************/
        
        $arrayrecargas=array();
        $arrayrecargas=$this->EntitiesToArray($evento->getIdrecarga(),$this->getCampos($em,'Recarga'));        
        
        $form
                ->add('idcategoria', 'entity', array(
                    'mapped' => false,
                    'class' => 'FraterSoftPiaWebBundle:Categoria',
                    'label' => 'Categoria Grupo',
                    'choices' => $categorias,
                    'empty_value' => $emptyvalue_categoria,
                    'required' => true,                
                ))
                ->add('precio','text',array(
                    'mapped' => false,
                    'label'=>'Precio',
                    'required' => true,
                ))
                ->add('monto','text', array(
                    'label'=>'Total a Pagar',
                    'data'=>$monto_total,
                    'label_attr'=>array('style'=>'display:none'),
                    'read_only' =>'true',
                    'attr'=>array('style'=>'display:none'),
                ))
                ->add('idformapago')
                ->add('referencia','text', array(
                    'label'=>'Número Operación ',
                    'label_attr' => array('id' => 'label_pago_referencia')
                ))
                ->add('idbanco', 'entity', array(
                    'class' => 'FraterSoftPiaWebBundle:Banco',
                    'required' => true,
                    'empty_value' => "Seleccion un Banco",
                    'label' => 'Banco de donde pago',
                    'query_builder' => function (EntityRepository $b) {
                        return $b->createQueryBuilder('b')
                                ->where('b.idpais=:idpais')
                                ->setParameter('idpais', 115);
                    },
                ))
                ->add('submit', 'submit', array('label' => 'Inscribir'))                
        ;
        $this->addBotonRegresar($form,$this->generateUrl('competidor_find', array('idevento' => $idevento)));
        
        $default_moneda=null;
        $arraymonedas=$this->MonedasEvento($em,$default_moneda,$evento);
        
        $arrayincrementos= array();
        $this->addFormasPago($form,$evento,$default_moneda);  
        $formaspago=$em->getRepository('FraterSoftPiaWebBundle:Formaspagoevento')
                ->findBy(array(
            'idevento' => $idevento,
        ));
        foreach($formaspago as $formapago){
            $arrayincrementos[$formapago->getIdformapago()->getId()]=$formapago->getIncremento();
        }

        return $this->render('FraterSoftPiaWebBundle:Pago:newgrupo.html.twig', array(
            'idevento' => $idevento,
            'idcompetencia'=>$idcompetencia,
            'inscripcionesgrupo' => $inscripcionesgrupo,
            'campos'=>$this->getCampos($em,'Inscrito'),
            'form' => $form->createView(),
            'incremento' => $arrayincrementos,
            'recargas'=>$arrayrecargas,
        ));
    }
    
    /**
     * Creates a new Pago entity.
     *
     */
    public function createGrupoAction(Request $request,$idevento,$idgrupo) {
                
        $entity = new Pago();
        $form = $this->createCreateForm($entity,$idevento,$idgrupo);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            
            $em = $this->getDoctrine()->getManager();
            $inscripcionesgrupo = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->findBy(array(
                'idevento'=>$idevento,
                'idgrupo'=>$idgrupo
            ));
            $categoria = $em->getRepository('FraterSoftPiaWebBundle:Categoria')->find($form->get('idcategoria')->getData()->getId());
            foreach($inscripcionesgrupo as $inscripcion){
                $inscripcion->setIdpago($entity);
                $inscripcion->setIdcategoria($categoria);
                $inscripcion->setPrecio($request->request->get('fratersoft_piawebbundle_pago')['precio']/count($inscripcionesgrupo));
                $inscripcion->setStatus(commonPIAClass::ESTATUS_INSCRIPCION_ACTIVA);
                $em->persist($inscripcion);
            }
            
            $em->flush();

            $this->EnviarConfirmacion($inscripcionesgrupo,$em);
            
            return $this->redirect($this->generateUrl('pago_showgrupo', array(
                'idpago' => $entity->getId(),
                'idevento'=>$idevento,
                'idgrupo'=>$idgrupo
            )));
        }

        return $this->render('FraterSoftPiaWebBundle:Pago:newgrupo.html.twig', array(
                    'idevento' => $idevento,
                    'idcategoria' => $form->get('idcategoria')->getData()->getId(),
                    'idgrupo'=>$idgrupo
        ));
    }
    
    public function registroAction($id,$email) {
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Pago')->find($id);
                
        //Busca los atributos del evento y los envia al formulario
        $atributos = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')
                ->atributosEvento($entity->getIdinscrito()->getIdevento()->getId());
        if (!$atributos) {
            return new response("No hay atributos configurados para este evento");
        }    
        
        return $this->render('FraterSoftPiaWebBundle:Pago:registro.html.twig', array(
                    'pago' => $entity,
                    'atributos' => $atributos,
                    'parametros'=>json_decode($entity->getidformapago()->getparametros()),
                    'email'=>$email
        ));
    }    
    
    public function buscarFormasPagoAjaxAction($idevento,$idmoneda) {
        //print_r($idevento);
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $formaspagoselect = new ArrayCollection();

        $serializer = new Serializer($normalizers, $encoders);
        
        $formaspago = 0;
        $formaspago = $this->buscarFormasPago($idevento,$idmoneda);

        if ($formaspago) {
            $formaspagoselect->add($formaspago);
            $jsonContent = $serializer->serialize(['count'=>count($formaspago),'data'=>$formaspago], 'json');
            return new response($jsonContent);
        }
        return new response(-1);
    }
    
}
