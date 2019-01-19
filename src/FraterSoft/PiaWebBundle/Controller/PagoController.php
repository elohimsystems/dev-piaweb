<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use FraterSoft\PiaWebBundle\Entity\Pago;
use FraterSoft\PiaWebBundle\Form\PagoType;
use FraterSoft\PiaWebBundle\Entity\Preciosevento;
use FraterSoft\PiaWebBundle\Entity\Precioscompetencia;
use FraterSoft\PiaWebBundle\Entity\Inscrito;
use FraterSoft\PiaWebBundle\Entity\Formaspagoevento;
use FraterSoft\PiaWebBundle\Entity\Numeracion;


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
     * Creates a new Pago entity.
     *
     */
    public function createAction(Request $request) {
        $entity = new Pago();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('pago_show', array('id' => $entity->getId())));
        }

        return $this->render('FraterSoftPiaWebBundle:Pago:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Pago entity.
     *
     * @param Pago $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Pago $entity) {
        $form = $this->createForm(new PagoType(), $entity, array(
            'action' => $this->generateUrl('pago_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Pago entity.
     *
     */
    public function newAction($idinscripcion) {
        $inscrito = new \FraterSoft\PiaWebBundle\Entity\Inscrito();
        $em = $this->getDoctrine()->getManager();

        $inscripcion = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->findOneBy(array(
            'id' => $idinscripcion,
        ));

        $entity = new Pago();
        $form = $this->createCreateForm($entity);

        return $this->render('FraterSoftPiaWebBundle:Pago:new.html.twig', array(
                    'entity' => $entity,
                    'inscripcion' => $inscripcion,
                    'form' => $form->createView(),
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
                $mensaje = \Swift_Message::newInstance()
                        ->setSubject("Confirmacion de Inscripcion " . $inscrito->getIdevento()->getNombre())
                        ->setFrom("confirmacion@sistemapia.com.ve")
                        ->setCharset('iso-8859-1')
                        ->setContentType('text/html')
                        //->setTo($inscrito->getIdpia()->getEmailpersonal())
                        ->setTo($emails)
                        ->setBody(
                        $this->renderView('FraterSoftPiaWebBundle:Inscrito:emailok.html.twig', array('inscrito' => $inscrito)
                ));
                $this->get('mailer')->send($mensaje);
                
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
                
                $mensaje = \Swift_Message::newInstance()
                        ->setSubject("Pre-Inscripcion " . $inscrito->getIdevento()->getNombre())
                        ->setFrom("pre-inscripcion@sistemapia.com.ve")
                        ->setCharset('iso-8859-1')
                        ->setContentType('text/html')
                        //->setTo(array($inscrito->getIdpia()->getEmailpersonal()))
                        ->setTo($emails)
                        ->setBody(
                        $this->renderView('FraterSoftPiaWebBundle:Inscrito:email.html.twig', array('entity' => $inscrito)
                ));
                $this->get('mailer')->send($mensaje);
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
                        
                    $mensaje = \Swift_Message::newInstance()
                            ->setSubject("Confirmacion de Inscripcion " . $entity->getIdevento()->getNombre())
                            ->setFrom("confirmacion@sistemapia.com.ve")
                            ->setCharset('iso-8859-1')
                            ->setContentType('text/html')
                            //->setTo($entity->getIdpia()->getEmailpersonal())
                            ->setTo($emails)
                            ->setBody(
                            $this->renderView('FraterSoftPiaWebBundle:Inscrito:emailok.html.twig', array('inscrito' => $entity)
                    ));
                    $this->get('mailer')->send($mensaje);
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
                
                $mensaje = \Swift_Message::newInstance()
                        ->setSubject("Confirmacion de Inscripcion " . $inscrito->getIdevento()->getNombre())
                        ->setFrom("confirmacion@sistemapia.com.ve")
                        ->setCharset('iso-8859-1')
                        ->setContentType('text/html')
                        ->setTo($emails)
                        ->setBody(
                        $this->renderView('FraterSoftPiaWebBundle:Inscrito:emailok.html.twig', array('inscrito' => $inscrito)
                ));
                $this->get('mailer')->send($mensaje);  
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
    
//    public function buscarPrecio($idevento, $idcompetencia, $idcategoria) {
//        $precioevento = new Preciosevento();
//        $preciocompetencia = new Precioscompetencia();
//        $preciocategoria = new Precioscompetencia();
//
//        $em = $this->getDoctrine()->getManager();
//
//        $precio = 0;
//
//        if ($idcategoria)
//            $preciocategoria = $em->getRepository('FraterSoftPiaWebBundle:Precioscategoria')
//                    ->buscarPrecioActivo($idcategoria);
//        else
//            $preciocategoria = 0;
//
//        if ($idcompetencia)
//            $preciocompetencia = $em->getRepository('FraterSoftPiaWebBundle:Precioscompetencia')
//                    ->buscarPrecioActivo($idcompetencia);
//        else
//            $preciocompetencia = 0;
//
//        $precioevento = $em->getRepository('FraterSoftPiaWebBundle:Preciosevento')
//                ->buscarPrecioActivo($idevento);
//
//        if ($precioevento)
//            $precio = $precioevento[0]['precio'];
//        if ($preciocompetencia)
//            $precio = $preciocompetencia[0]['precio'];
//        if ($preciocategoria)
//            $precio = $preciocategoria[0]['precio'];
//        return $precio;
//    }    

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
                    
                    $mensaje = \Swift_Message::newInstance()
                            ->setSubject("Pago no Conciliado en " . $operaciontdc->getIdevento()->getNombre())
                            ->setFrom("pagos@sistemapia.com.ve")
                            ->setCharset('iso-8859-1')
                            ->setContentType('text/html')
                            //->setTo($operaciontdc->getIdpia()->getEmailpersonal())
                            ->setTo($emails)
                            ->setBody(
                            $this->renderView('FraterSoftPiaWebBundle:Pago:email_tdcnoconciliados.html.twig', 
                                    array('operaciontdc' => $operaciontdc)
                    ));
                    $this->get('mailer')->send($mensaje);   
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
}
