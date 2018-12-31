<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;

use FraterSoft\PiaWebBundle\Entity\Inscrito;
use FraterSoft\PiaWebBundle\Entity\Competidor;
use FraterSoft\PiaWebBundle\Entity\Competencia;
use FraterSoft\PiaWebBundle\Entity\Evento;
use FraterSoft\PiaWebBundle\Entity\Estado;
use FraterSoft\PiaWebBundle\Entity\Preciosevento;
use FraterSoft\PiaWebBundle\Entity\Precioscompetencia;
use FraterSoft\PiaWebBundle\Entity\Precioscategoria;
use FraterSoft\PiaWebBundle\Entity\Pago;
use FraterSoft\PiaWebBundle\Form\InscritoType;
use FraterSoft\PiaWebBundle\Form\CompetidorType;

/**
 * Inscrito controller.
 *
 */
class InscritoController extends commonPIAClass {

    /**
     * Lists all Inscrito entities.
     *
     */
    public function indexAction($idevento) {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->listarNoConciliadas($idevento);

        foreach ($entities as $entity) {
            switch ($entity->getIdpago()->getTipo()) {
                case "1":
                    $entity->getIdpago()->setTipo("DEPO.");
                    break;
                case "2":
                    $entity->getIdpago()->setTipo("TRAN.");
                    break;
                case "3":
                    $entity->getIdpago()->setTipo("TACR.");
                    break;
                case "0":
                    $entity->getIdpago()->setTipo("EXON.");
                    break;                
            }
        }

        $conciliadas = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->listarConciliadas($idevento);

        $anuladas = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->listarAnuladas($idevento);

        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($idevento);

        return $this->render('FraterSoftPiaWebBundle:Inscrito:index.html.twig', array(
                    'entities' => $entities,
                    'conciliadas' => $conciliadas,
                    'anuladas' => $anuladas,
                    'emailcontacto' => $evento->getEmailContacto(),
        ));
    }

    public function listapreinscritosAction($idevento, $email) {
        $em = $this->getDoctrine()->getManager();
        
        //Busca los atributos criterios del evento y los envia al formulario
        $atributos = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')
                ->atributosEvento($idevento);
        if (!$atributos) {
            return new response("No hay atributos para este evento");
        }                 

        $request = $this->container->get('request');
        $routeURL = $request->getRequestUri();
        $this->get('session')->set('urlreturn',$routeURL);

        return $this->render('FraterSoftPiaWebBundle:Inscrito:lista_preinscritos.html.twig', array(
                    'idevento' => $idevento,
                    'atributos' => $atributos,
                    'email' => $email,
                    'nombreevento'=>$atributos[0]->getIdEvento()->getNombre()
        ));
    }    
    
    public function listapreinscritosajaxAction($idevento) {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();

        $conciliadas = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->listarNoConciliadasAjax($idevento);        

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($conciliadas),
            "data"=>$conciliadas)
                , 'json');
        return new response($jsonContent);            
    }      
    
    public function listaregistrosAction($idevento, $email) {
        $em = $this->getDoctrine()->getManager();
        
        //Busca los atributos criterios del evento y los envia al formulario
        $atributos = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')
                ->atributosEvento($idevento);
        if (!$atributos) {
            return new response("No hay atributos para este evento");
        }                 

        $request = $this->container->get('request');
        $routeURL = $request->getRequestUri();
        $this->get('session')->set('urlreturn',$routeURL);

        return $this->render('FraterSoftPiaWebBundle:Inscrito:lista_registros.html.twig', array(
                    'idevento' => $idevento,
                    'atributos' => $atributos,
                    'email' => $email,
                    'nombreevento'=>$atributos[0]->getIdEvento()->getNombre()
        ));
    }        
    
    public function listaregistrosajaxAction($idevento,$email) {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();

        $conciliadas = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->listarRegistrosAjax($idevento,$email);        

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($conciliadas),
            "data"=>$conciliadas)
                , 'json');
        return new response($jsonContent);            
    }      
    

    public function listainscritosAction($idevento,$email) {
        $em = $this->getDoctrine()->getManager();
        
        //Busca los atributos del evento y los envia al formulario
        $atributos = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')
                ->atributosEvento($idevento);
        if (!$atributos) {
            return new response("No hay atributos configurados para este evento");
        }    
        
        return $this->render('FraterSoftPiaWebBundle:Inscrito:lista_inscritos.html.twig', array(
                    'atributos' => $atributos,
                    'idevento' => $idevento,
                    'email' => $email,
                    'nombreevento'=>$atributos[0]->getIdEvento()->getNombre()
        ));
    }    
    
    public function listainscritosajaxAction($idevento) {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();

        $conciliadas = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->listarConciliadasAjax($idevento);        

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($conciliadas),
            "data"=>$conciliadas)
                , 'json');
        return new response($jsonContent);            
    }       

    public function listaoperacionestdcAction($idevento,$email) {
        $em = $this->getDoctrine()->getManager();

        $operacionestdc = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->listarOperacionesTDC($idevento);

        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($idevento);     

        return $this->render('FraterSoftPiaWebBundle:Inscrito:lista_operacionestdc.html.twig', array(
                    'operacionestdc' => $operacionestdc,
                    'idevento' => $idevento,
                    'email' => $email,
                    'nombreevento'=>$evento->getNombre()
        ));
    }   
    
    public function listaanuladosAction($idevento,$email) {
        $em = $this->getDoctrine()->getManager();

        $anuladas = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->listarAnuladas($idevento);


        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($idevento);       

        return $this->render('FraterSoftPiaWebBundle:Inscrito:lista_anulados.html.twig', array(
                    'anuladas' => $anuladas,
                    'idevento' => $idevento,
                    'email' => $email,
                    'nombreevento'=>$evento->getNombre()
        ));
    }    

    /**
     * Creates a new Inscrito entity.
     *
     */
    public function createAction(Request $request) {
        $entity = new Inscrito();
        $pago = new Pago();
        $competidor = new Competidor();
        $competencia = new Competencia();
        $accessor = PropertyAccess::createPropertyAccessor();
        
        $form = $this->createCreateForm($entity, null);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
                
        $competidor=$em->getRepository('FraterSoftPiaWebBundle:Competidor')
                ->findOneBy(array(
            'iddocumento' => $form->get('idpia')->getData()->getIddocumento()
        ));
        
        //Verifica si el evento esta configurado para control de cupo y valida si llego al maximo
        if ($form->get('idevento')->getData()->getCupocontrol()) {
            $cantidadinscritos = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                    ->cantidad($form->get('idevento')->getData()->getId());
            if ($cantidadinscritos >= $form->get('idevento')->getData()->getCupomaximo())
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $this->generateUrl('competidor_find', array('idevento' => $form->get('idevento')->getData()->getId())),
                            'texto' => 'Se ha alcanzado el cupo máximo de inscritos para este Evento',
                            'tema' => $entity->getIdevento()->getTema()
                ));
        }
        
        //Valida que el competidor no este inscrito
        if ($competidor)
            if ($em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                            ->buscarInscrito($form->get('idevento')->getData()->getId(), $competidor->getId()))
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $this->generateUrl('competidor_find', array('idevento' => $form->get('idevento')->getData()->getId())),
                            'texto' => 'Competidor ya está inscrito en este evento',
                            'tema' => $entity->getIdevento()->getTema()
                ));

        $competencia = $form->get('idcompetencia')->getData();
        
        if ($form->isValid()) {

            $emails = array();
            if(!is_null($entity->getIdpia()->getEmail()) && filter_var($entity->getIdpia()->getEmail(), FILTER_VALIDATE_EMAIL))
                array_push($emails,$entity->getIdpia()->getEmail());
            if(!is_null($entity->getIdpia()->getEmailpersonal()) && filter_var($entity->getIdpia()->getEmailpersonal(), FILTER_VALIDATE_EMAIL))
                array_push($emails,$entity->getIdpia()->getEmailpersonal());
                
            if ($competidor == null) {
                $competidor = new Competidor();
            }

            $competidorform = new Competidor();
            $competidorform = $form->get('idpia')->getData();            
            foreach($form->get('idpia') as $claveform =>$valorform){
                $accessor->setValue($competidor, $claveform, $accessor->getValue($competidorform, $claveform));
            }            

            $competidor->setTelefono(str_replace("(","",$this->desencriptarTelefono($competidor->getTelefono())));
            $competidor->setEmail($this->desencriptarEmail($competidor->getEmail()));                        
            $em->persist($competidor);
            
            //Resta el limite de los atributos configurados, 
            $atributoslista=$em->getRepository('FraterSoftPiaWebBundle:Listasevento')->atributosConLimites($form->get('idevento')->getData()->getId());
            foreach($atributoslista as $atributo){
                if($accessor->getValue($competidor, $atributo['nombre'])==$atributo['valor']){
                    $listaevento=$em->getRepository('FraterSoftPiaWebBundle:Listasevento')->findOneBy(array(
                        'idevento'=>$form->get('idevento')->getData()->getId(),
                        'idatributo'=>$atributo['idatributo'],
                        'codigo'=>$atributo['codigo']
                    ));
                    if($listaevento){
                        $listaevento->setLimite($listaevento->getLimite()-1);
                        $em->persist($listaevento);                                    
                    }
                }     
            }            

            if($entity->getIdevento()->getProceso()==1){
                $pago->setFechahora($form->get('idpago')->getData()->getFechahora());
                $pago->setMonto($form->get('idpago')->getData()->getMonto());
                $pago->setMoneda($form->get('idpago')->getData()->getMoneda());
                $pago->setReferencia($form->get('idpago')->getData()->getReferencia());
                $pago->setComprobante($form->get('idpago')->getData()->getComprobante());
                $pago->setTipo($form->get('idpago')->getData()->getTipo());
                if($form->get('idpago')->getData()->getTipo()==0){
                    $pago->setConciliado(true);
                    $pago->setConciliadoel(new \DateTime('now'));
                }
                $pago->setBanco($form->get('idpago')->getData()->getBanco());
                $em->persist($pago);
                $entity->setIdpago($pago);
            }else{
                $entity->setIdpago(null);
            }

            $entity->setIdcompetencia($competencia);
            $entity->setIdpia($competidor);

            $entity->setStatus(1);
            $entity->setFechahora(new \DateTime('now'));
            $maxsec = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->maximaSecuencia($entity->getIdevento()->getId());
            $entity->setSecuencia($maxsec ? $maxsec + 1 : 1);
            $em->persist($entity);
            $em->flush();

            if($entity->getIdevento()->getProceso()==1){
                if ($form->get('idpago')->getData()->getTipo() <> 3) { //PAGOS CON TDC
                    if ($form->get('idpago')->getData()->getTipo() <> 0) { //INSCRIPCIONES GRATIS
                        //Se envia el correo de confirmacion
                        $mailer = $this->get('app.mail_controller');
                        $mailer->enviarPreinscripcion(
                                "Pre-Inscripcion " . $entity->getIdevento()->getNombre(), 
                                //$competidor->getEmail(), 
                                $emails,
                                $this->renderView('FraterSoftPiaWebBundle:Inscrito:email.html.twig', array('entity' => $entity))
                        );         
                        return $this->redirect($this->generateUrl('inscrito_confirmacion', array('id' => $entity->getId())));
                    }
                    else{ //SI LA INSCRIPCION ES GRATUITA SE ENVIA LA CONFIRMACION
                        $mensaje = \Swift_Message::newInstance()
                                ->setSubject("Confirmacion de Inscripcion " . $entity->getIdevento()->getNombre())
                                ->setFrom("confirmacion@sistemapia.com.ve")
                                ->setCharset('iso-8859-1')
                                ->setContentType('text/html')
                                //->setTo($entity->getIdpia()->getEmail())
                                ->setTo($emails)
                                ->setBody(
                                $this->renderView('FraterSoftPiaWebBundle:Inscrito:emailok.html.twig', array('inscrito' => $entity)
                        ));
                        $this->get('mailer')->send($mensaje);

                        return $this->render('FraterSoftPiaWebBundle:Inscrito:conciliado.html.twig', array(
                                    'inscrito' => $entity,
                                    'idevento' => $entity->getIdevento()->getId()                        
                        ));                     
                    }
                } 
                else{
                    $formapago=$em->getRepository('FraterSoftPiaWebBundle:Formaspagoevento')
                                ->findBy(array('idevento'=>$entity->getIdevento()->getId(),'idformapago'=>3));                
                    return $this->render('FraterSoftPiaWebBundle:Inscrito:boton123pago.html.twig', array(
                                'entity' => $entity,
                                'incremento' => $formapago[0]->getIncremento(),
                                'boton123Pago' => $this->generarBoton123Pago($entity),
                    ));
                }		
            }
            else{ //Envia el correo si el proceso es tipo 2
                $mailer = $this->get('app.mail_controller');
                $mailer->enviarPreinscripcion(
                        "Pre-Inscripcion " . $entity->getIdevento()->getNombre(), 
                        $emails, 
                        $this->renderView('FraterSoftPiaWebBundle:Inscrito:email.html.twig', array('entity' => $entity))
                );         
                return $this->redirect($this->generateUrl('inscrito_confirmacion', array('id' => $entity->getId())));
            }
        }
        
        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                var_dump($child->getName());
                $errors[$child->getName()] = $this->getErrorMessages($child);
                print_r($errors);
            }
        }
        foreach ($form->get('idpia')->all() as $child) {
            if (!$child->isValid()) {
                var_dump($child->getName());
                $errors[$child->getName()] = $this->getErrorMessages($child);
                print_r($errors);
            }
        }        
        
        return new Response($form->getData()->getPrecio() . ' | ' . $form->getErrorsAsString());
    }

    /**
     * Creates a form to create a Inscrito entity.
     *
     * @param Inscrito $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Inscrito $entity, $idevento) {
        if ($idevento) {
            $em = $this->getDoctrine()->getManager();
            $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($idevento);
            $entity->setIdevento($evento);
        }
        //createForm
        $form = $this->createForm(new InscritoType(), $entity, array(
            'attr' => ['id' => 'inscrito-form', 'class' => 'cmxform'],
            'action' => $this->generateUrl('inscrito_create'),
            'method' => 'POST',
        ));

        //Agrega la Fecha y Hora de la inscripcion
        $form
                ->add('fechahora', 'datetime', array(
                    'widget' => 'single_text',
                    'data' => new \DateTime('now'),
                    'attr' => array('style' => 'display:none'),
                    'label' => ' ',
                ))
        ;
        $form->add('submit', 'submit', array(
            'attr' => ['class' => 'submit'],
            'label' => 'Inscribir'
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Inscrito entity.
     *
     */
    public function newAction($idevento, $idcompetidor, $iddocumento) {
        $entity = new Inscrito();
        $evento = new Evento();
        $precioevento = new Preciosevento();
        $em = $this->getDoctrine()->getManager();

        //Busca el evento 
        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')
                ->findOneBy(array('id' => $idevento));
        if ($evento == null) {
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                        'texto' => 'Evento no configurado',
                        'tema' => $evento->getTema()
            ));
        } 
        
        //Verifica si el competidor ya esta inscrito
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->buscarInscrito($idevento, $idcompetidor);
        $url = '';
        if ($entity != null) {
            
            //Si la inscripcion no posee pago, es un evento de tipo proceso 2
            if (is_null($entity[0]->getIdpago())){
                switch (true){
                    case $evento->getProceso()==2:
                        
                        $formasdepago = $em->getRepository('FraterSoftPiaWebBundle:Formaspagoevento')
                                ->listar($idevento);
                        $cantidadformaspago=0;
                        foreach ($formasdepago as $formadepago) {
                            $cantidadformaspago++;
                            if ($formadepago['id']==3) {
                                $formasdepagoarray[$formadepago['id']] = $formadepago['nombre'];
                            }
                        }       
                        
                        // revisar todo este blqoye
                        if($cantidadformaspago==1 and $formadepago['id']==3){
                            return $this->redirect($this->generateUrl('pago_newtdc', array('idinscripcion' => $entity[0]->getId()))); 
                        }
                        else{
                            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                                'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                                'texto' => 'Se ha encontrado una Pre-Inscripci&oacute;n para la cedula ingresada<br>'
                                    . 'Posteriormente estaremos informando de la fecha de inicio de pago',
                                'tema' => $evento->getTema()
                            ));
                        }
                    case $evento->getProceso()==1:
                        //Aqui debe crearse el pago para la inscripcion registrada, debe llamarse al newdel pago para crearlo
                        //pasar el id de la inscripcion para actualizar el 
                }
            }
            if ($entity[0]->getIdpago()->getTipo() == "3" and $entity[0]->getIdpago()->getConciliado() == false)
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                            'texto' => 'Usted posee una pre-inscripci&oacute;n pendiente de pago por Tarjeta de Cr&eacute;dito, '
                            . 'presione continuar para volver a intentar o elija otra forma de pago',
                            'urlcontinuar' => $this->generateUrl('pago_tdcnoconciliado', array('id' => $entity[0]->getIdpago()->getId())),
                            'tema' => $evento->getTema()
                ));
            else{
                if($entity[0]->getIdpago()->getConciliado()){
                    if($entity[0]->getIdpago()->getTipo() == "0"){                    
                        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                                    'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                                    'texto' => "El portador del Documento de Identidad Nro. " . $entity[0]->getIdpia()->getIdDocumento() . "<br>"
                                    . "est&aacute; oficialmente inscrito para el evento <br><b>" 
                                    . $evento->getNombre() . "</b><br><br>",
                                    'tema' => $evento->getTema()
                        ));
                    }else{
                        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                                    'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                                    'texto' => "El pago de la Pre-Inscripci&oacute;n Nro. " . $entity[0]->getSecuencia() . "<br>"
                                    . "fu&eacute; conciliado satisfactoriamente. <br>"
                                    . "El portador del Documento de Identidad Nro. " . $entity[0]->getIdpia()->getIdDocumento() . "<br>"
                                    . "est&aacute; oficialmente inscrito para el evento <br><b>" 
                                    . $evento->getNombre() . "</b><br><br>",
                                    'tema' => $evento->getTema()
                        ));
                    }
                }
                else
                    return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                                'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                                'texto' => 'Se ha encontrado una Pre-Inscripci&oacute;n para la cedula ingresada<br>'
                                . 'El organizador aun no ha conciliado la informacion de pago. Escriba a <b>' . $evento->getIdorganizador()->getEmail()
                        . "</b> para mayor informaci&oacute;n",
                                'tema' => $evento->getTema()
                    ));                
            }
        }

        //Busca el evento 
        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')
                ->findOneBy(array('id' => $idevento));
        if ($evento == null) {
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                        'texto' => 'No se ha configurado evento',
                        'tema' => $evento->getTema()
            ));
        }

        //Busca los datos del competidor si existen, lo asigna a la entidad del formulario
        $entity = new Inscrito();
        if ($idcompetidor != 0) { //Si el competidor existe
            $competidor = $em->getRepository('FraterSoftPiaWebBundle:Competidor')
                    ->findOneBy(array('id' => $idcompetidor));
            //Convierte a minuscula el correo si esta en mayuscula
            $competidor->setEmail($competidor->getEmail() ? strtolower($competidor->getEmail()) : null);

            //Calcula la edad actual del competidor
            if($evento->getCriteriocalculoedad()==1)
                $competidor->setEdad(date("Y") - $competidor->getFechanacimiento()->format("Y"));
            else{
                $mese=$evento->getFecha()->format("n");
                $mesn=$competidor->getFechanacimiento()->format("n");
                $diae=$evento->getFecha()->format("j");
                $dian=$competidor->getFechanacimiento()->format("j");
                if($mese > $mesn)
                    $competidor->setEdad(date("Y") - $competidor->getFechanacimiento()->format("Y"));
                else
                    if($mese == $mesn && $dian < $diae)
                        $competidor->setEdad($evento->getFecha()->format("Y") - $competidor->getFechanacimiento()->format("Y"));
                    else
                        $competidor->setEdad($evento->getFecha()->format("Y") - $competidor->getFechanacimiento()->format("Y") - 1);                
            }                

            //Busca el numero del competidor si el evento es tipo campeonato
            //Si el evento no es de tipo campeonato, no devuelve competidor
            $compcamp = $em->getRepository('FraterSoftPiaWebBundle:CampeonatoCompetidores')
                    ->findOneBy(array(
                'idcompetidor' => $idcompetidor,
                'idcampeonato' => $evento->getIdCampeonato(),
            ));

            //Si existe el competidor en el Campeonato, le asigno su Categoria
            if ($compcamp) {
                $entity->setNumero($compcamp->getNumero());
                $equipo = $competidor->setEquipo($compcamp->getIdclub()->getNombre());
                $idcategoria = $compcamp->getIdcategoria();
                $entity->setIdpia($competidor);
                $form = $this->createCreateForm($entity, $idevento);
                $this->ocultaCampos($idevento, $form);                
                $form
                        ->add('idcategoria', 'entity', array(
                            'class' => 'FraterSoftPiaWebBundle:Categoria',
                            'label' => 'Categoria',
                            'query_builder' => function (EntityRepository $er) use ( $idcategoria ) {
                                return $er->createQueryBuilder('c')
                                        ->where('c.id=:id')
                                        ->setParameter('id', $idcategoria);
                            },
                            'required' => true,
                        ))
                ;
                $form->get('idpia')
                        ->add('equipo', 'text', array(
                            'data' => $compcamp->getIdclub()->getNombre(),
                            'read_only' => true,
                        ))
                ;
                
                $desccategoria = $compcamp->getnombrecategoria();
                //Si no existe el competidor en el Campeonato, le calculo su Categoria                
            } else {                
                $entity->setIdpia($competidor);
                $form = $this->createCreateForm($entity, $idevento);
                $this->ocultaCampos($idevento, $form);
                //seleccional la categoria que le aplica al competidor
                $categorias = $this->seleccionaCategorias($idevento, $competidor);

                if ($categorias->count()==0) {
                    return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                                'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                                'texto' => 'No existe una Categorias aplicable a este participante',
                                'tema' => $evento->getTema()
                    ));
                }
                if ($categorias->count() > 1)
                    $emptyvalue = 'Seleccione una Categoría';
                else
                    $emptyvalue = null;
                $form->add('idcategoria', 'entity', array(
                    'class' => 'FraterSoftPiaWebBundle:Categoria',
                    'label' => 'Categoria',
                    'choices' => $categorias,
                    'empty_value' => $emptyvalue,
                    'required' => true,
                ));
                $form->add('numero', 'hidden');

                //Dependiendo del tipo de evento asigna el Equipo
                if ($evento->getIdcampeonato()) {
                    $form->get('idpia')
                            ->add('equipo', 'text', array(
                                'data' => 'INDEPENDIENTE',
                                'read_only' => true,
                            ))
                    ;
                } else {
                    $form->add('numero', 'hidden');
                }
            }
        }
        //Si el competidor no existe
        else {
            $form = $this->createCreateForm($entity, $idevento);
            $form
                    ->add('idcategoria', 'entity', array(
                        'class' => 'FraterSoftPiaWebBundle:Categoria',
                        'label' => 'Categoria',
                        'query_builder' => function (EntityRepository $er) use ( $idevento ) {
                            return $er->createQueryBuilder('c')
                                    ->innerJoin('c.idcompetencia', 'co')
                                    ->innerJoin('co.idevento','ev')
                                    //->addOrderBy('c.id', 'ASC')
                                    ->where('c.idcompetencia=co and co.idevento=:idevento')
                                    ->setParameter('idevento', $idevento);
                        },
                        'empty_value' => 'Seleccione Categoria',
                        'required' => true,
                    ))
            ;
            
            $this->ocultaCampos($idevento, $form);            
                        
            //Dependiendo del tipo de evento asigna el Equipo
            if ($evento->getIdcampeonato()) {
                $form->get('idpia')
                        ->add('equipo', 'text', array(
                            'data' => 'INDEPENDIENTE',
                            'read_only' => true,
                        ))
                ;
                $form->add('numero', 'hidden');
            } else {
                $form->add('numero', 'hidden');
            }

            $form->get('idpia')
                    ->add('telefono', 'text', array(
                        'label' => 'Telefono',
                        'required' => true,
            ));
        }

        //Busca la cantidad de competencias por eventos, si hay mas de 1 muestra el combo
        //sino, muestra el texto de la competencia
        //$competencias = $em->getRepository('FraterSoftPiaWebBundle:Competencia')->cantidad($idevento);
        $competencias = $em->getRepository('FraterSoftPiaWebBundle:Competencia')
                ->findBy(array(
            'idevento' => $idevento,
        ));
        if ($competencias) {
            if (count($competencias) > 1) {
                $form
                        ->add('idcompetencia', 'entity', array(
                            'class' => 'FraterSoftPiaWebBundle:Competencia',
                            'label' => 'Competencia',
                            'choices' => $competencias,
                            'required' => true,
                            'empty_value' => 'Seleccione una Competencia',
                        ))
                ;
            } else {
                $form
                        ->add('idcompetencia', 'entity', array(
                            'class' => 'FraterSoftPiaWebBundle:Competencia',
                            'label' => 'Competencia',
                            'choices' => $competencias,
                        ))
                ;
            }
        }

        //Agrega y oculta el campo del evento
        $form
                ->add('idevento', 'entity', array(
                    'class' => 'FraterSoftPiaWebBundle:Evento',
                    'label' => 'Evento',
                    'attr' => array('style' => 'display:none'), //Oculta el control
                    'query_builder' => function (EntityRepository $er) use ($idevento) {
                return $er->createQueryBuilder('e')
                        ->where('e.id=:id')
                        ->setParameter('id', $idevento);
            },
                ))
        ;

        //Configura el select para mostrar los precios
        $form->add('precio','choice',array(
            'label'=>'Precios',
            'empty_value' => 'Seleccione El Precio'
        ));
                        
        //Agrega las formas de pago del evento
        $formasdepagoarray = array();
        $formasdepago = $em->getRepository('FraterSoftPiaWebBundle:Formaspagoevento')
                ->listar($idevento);
        foreach ($formasdepago as $formadepago) {
            if ($formadepago['nombre']) {
                $formasdepagoarray[$formadepago['id']] = $formadepago['nombre'];
            }
        }
        if ($formasdepagoarray)
            $form
                    ->get('idpago')
                    ->add('tipo', 'choice', array(
                        'label' => 'Tipo de Pago',
                        'choices' => $formasdepagoarray,
                        'required' => true,
                        'empty_value' => 'Seleccione Tipo de Pago',
            ));
        else {
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                        'texto' => "No se han configurado las Formas De Pago para este Evento",
                        'tema' => $evento->getTema()
            ));            
        }
        
        if ($evento->getProceso()==2){
            $form
                    ->remove('precio')
                    ->remove('idpago')
            ;
        }

        //Muestra la etiqueta de la edad segun el tipo de calculo
        if ($evento->getCriteriocalculoedad() == 1) {
            $form->get('idpia')->add('edad', 'text', array(
                'label' => 'Edad Calendario',
                'read_only' => true,
            ));
        } else {
            $form->get('idpia')->add('edad', 'text', array(
                'label' => 'Edad al Evento',
                'read_only' => true,
            ));
        }

        //Busca los atributos criterios del evento y los envia al formulario, 
        //para las busqueda en ajax de las categorias
        $atributoscriterios = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')
                ->atributosCriterios($idevento);
        if (!$atributoscriterios) {
            return new response("No hay atributos criterios para este evento");
        }

        //$session = new Session();
        //if ($session->isStarted())
        //$session->start();
            //$session->set('form', $form);       

        $arrayincrementos= array();
        if($evento->getProceso()==1){
            $this->addFormasPago($form->get('idpago'),$entity->getIdevento());  
            $formaspago=$em->getRepository('FraterSoftPiaWebBundle:Formaspagoevento')
                    ->findBy(array(
                'idevento' => $entity->getIdevento(),
            ));
            foreach($formaspago as $formapago){
                $arrayincrementos[$formapago->getIdformapago()->getId()]=$formapago->getIncremento();
            }
        }   

        $this->addBotonRegresar($form,$this->generateUrl('competidor_find', array('idevento' => $idevento)));

        return $this->render('FraterSoftPiaWebBundle:Inscrito:new.html.twig', array(
            'entity' => $entity,
            'atributos' => $atributoscriterios,
            'form' => $form->createView(),
            'incremento' => $arrayincrementos,
        ));
    }

    /**
     * Finds and displays a Inscrito entity.
     *
     */
    public function showAction($id) {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->findIdArray($id);
        
        //Se convierte la entidad en arreglo json para poder accesar a traves del nombre de la propiedad
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        $jsonContent = $serializer->serialize($entity[0],'json');    
        print_r($jsonContent);
        //Se transforma a un array php porque se json crea un array de objectos
        $obj_php = json_decode($jsonContent);
        //Se transforma en array basico, porque el decode crea un array de objetos 
        //stdClass, y es necesacio un array con acceso a traves de los keys del array
        $x=$this->objectToArray($obj_php);
        
        //Busca los atributos del evento y los envia al formulario
        $atributos = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')
                ->atributosEvento($obj_php->idevento->id);
        if (!$atributos) {
            return new response("No hay atributos configurados para este evento");
        }    
        
        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($obj_php->idevento->id);

        return $this->render('FraterSoftPiaWebBundle:Inscrito:show.html.twig', array(
                    'entity' => $x,
                    'evento' => $evento,
                    'atributos' => $atributos,
        ));        
    }

    /**
     * Finds and displays a Inscrito entity.
     *
     */
    public function showcompetidorAction($idevento, $idpia) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->findOneBy(array(
            'idevento' => $idevento,
            'idpia' => $idpia,
        ));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inscrito entity.');
        }

        return $this->render('FraterSoftPiaWebBundle:Inscrito:showcompetidor.html.twig', array(
                    'entity' => $entity,
                    'idevento' => $entity->getIdEvento()->getId(),
        ));
    }

    /**
     * Finds and displays a Inscrito entity.
     *
     */
    public function listarrivalesAction($idevento, $idpia) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->findOneBy(array(
            'idevento' => $idevento,
            'idpia' => $idpia,
        ));

        if (!$entity) {
            throw $this->createNotFoundException('No se ha encontrado ningun rival.');
        }

        $rivales = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->listarRivales($idevento, $entity->getIdcategoria()->getId());

        return $this->render('FraterSoftPiaWebBundle:Inscrito:rivales.html.twig', array(
                    'entity' => $entity,
                    'rivales' => $rivales,
                    'idevento' => $entity->getIdEvento()->getId(),
        ));
    }

    /**
     * Finds and displays a Inscrito entity.
     *
     */
    public function confirmacionAction($id) {
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->findIdArray($id);
        
        //Se convierte la entidad en arreglo json para poder accesar a traves del nombre de la propiedad
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        $jsonContent = $serializer->serialize($entity[0],'json');    
        
        //Se transforma a un array php porque se json crea un array de objectos
        $obj_php = json_decode($jsonContent);
        //Se transforma en array basico, porque el decode crea un array de objetos 
        //stdClass, y es necesacio un array con acceso a traves de los keys del array
        $x=$this->objectToArray($obj_php);
        
        //Busca los atributos del evento y los envia al formulario
        $atributos = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')
                ->atributosEvento($obj_php->idevento->id);
        if (!$atributos) {
            return new response("No hay atributos configurados para este evento");
        }    
        
        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($obj_php->idevento->id);

        return $this->render('FraterSoftPiaWebBundle:Inscrito:confirmacion.html.twig', array(
                    'entity' => $x,
                    'evento' => $evento,
                    'atributos' => $atributos,
        ));
    }

    /**
     * Displays a form to edit an existing Inscrito entity.
     *
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inscrito entity.');
        }

        $editForm = $this->createEditForm($entity);

        //Dependiendo del tipo de evento asigna el Equipo
        if ($entity->getIdevento()->getIdcampeonato()) {
            $editForm->get('idpia')
                    ->add('equipo', 'text', array(
                        'data' => 'INDEPENDIENTE',
                        'read_only' => true,
                    ))
            ;
        } else {
            $editForm->add('numero', 'hidden');
        }
        
        //Selecciona las categoria aplicables al competidor
        $categorias = $this->seleccionaCategorias($entity->getIdevento()->getId(), $entity->getIdpia());
    
        if ($categorias->count()==0) {
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('competidor_find', array('idevento' => $entity->getIdevento()->getId())),
                        'texto' => 'No existe una Categorias aplicable a este participante',
                        'tema' => $evento->getTema()
            ));
        }
        if ($categorias->count() > 1)
            $emptyvalue = 'Seleccione una Categoría';
        else
            $emptyvalue = null;
        $editForm->add('idcategoria', 'entity', array(
            'class' => 'FraterSoftPiaWebBundle:Categoria',
            'label' => 'Categoria',
            'choices' => $categorias,
            'empty_value' => $emptyvalue,
            'required' => true,
        ));
        
        //Busca la cantidad de competencias por eventos, si hay mas de 1 muestra el combo
        //sino, muestra el texto de la competencia
        //$competencias = $em->getRepository('FraterSoftPiaWebBundle:Competencia')->cantidad($idevento);
        $competencias = $em->getRepository('FraterSoftPiaWebBundle:Competencia')
                ->findBy(array('idevento' => $entity->getIdevento()->getId(),
        ));
        if ($competencias) {
            if (count($competencias) > 1) {
                $editForm
                        ->add('idcompetencia', 'entity', array(
                            'class' => 'FraterSoftPiaWebBundle:Competencia',
                            'label' => 'Competencia',
                            'choices' => $competencias,
                            'required' => true,
                            'empty_value' => 'Seleccione una Competencia',
                        ))
                ;
            } else {
                $editForm
                        ->add('idcompetencia', 'entity', array(
                            'class' => 'FraterSoftPiaWebBundle:Competencia',
                            'label' => 'Competencia',
                            'choices' => $competencias,
                        ))
                ;
            }
        }
        
        /**************** Agrega las formas de pago del evento ****************/
        $formasdepagoarray = array();
        $formasdepago = $em->getRepository('FraterSoftPiaWebBundle:Formaspagoevento')
                ->listar($entity->getIdevento()->getId());
        foreach ($formasdepago as $formadepago) {
            if ($formadepago['nombre']) {
                $formasdepagoarray[$formadepago['id']] = $formadepago['nombre'];
            }
        }
        if ($formasdepagoarray)
            $editForm
                    ->get('idpago')
                    ->add('tipo', 'choice', array(
                        'label' => 'Tipo de Pago',
                        'choices' => $formasdepagoarray,
                        'required' => true,
                        'empty_value' => 'Seleccione Tipo de Pago',
            ));
        else {
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('competidor_find', array('idevento' => $entity->getIdevento()->getId())),
                        'texto' => "No se han configurado las Formas De Pago para este Evento",
                        'tema' => $evento->getTema()
            ));            
        }        
        if ($entity->getIdevento()->getProceso()==2){
            $editForm
                ->remove('precio')
                ->remove('idpago')
            ;
        }        

        $this->addBotonRegresar($editForm,$this->get('session')->get('urlreturn'));
        
        $this->ocultaCampos($entity->getIdevento()->getId(), $editForm);
        
        //Busca los atributos criterios del evento y los envia al formulario, 
        //para las busqueda en ajax de las categorias
        $atributoscriterios = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')
                ->atributosCriterios($entity->getIdevento()->getId());
        if (!$atributoscriterios) {
            return new response("No hay atributos criterios para este evento");
        }        

        return $this->render('FraterSoftPiaWebBundle:Inscrito:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'atributos' => $atributoscriterios,
        ));
    }

    /**
     * Creates a form to edit a Inscrito entity.
     *
     * @param Inscrito $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Inscrito $entity) {
        $form = $this->createForm(new InscritoType(), $entity, array(
            'action' => $this->generateUrl('inscrito_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Inscrito entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inscrito entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            if($editForm->getdata()->getIdevento()->getProceso()==2)
                $editForm->getdata()->setIdpago(null);
                
            $em->flush();

            return $this->redirect($this->generateUrl('inscrito_edit', array('id' => $id)));
        }

        return $this->render('FraterSoftPiaWebBundle:Inscrito:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Inscrito entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Inscrito entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('inscrito'));
    }

    /**
     * Creates a form to delete a Inscrito entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('inscrito_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

    public function cargarXMLOldAction() {
        $crawler = new Crawler();

        $em = $this->getDoctrine()->getManager();

        $crawler->addXmlContent(file_get_contents('c:\ftpfiles\PIAK02_DataPia.xml'));
        //$raiz = $crawler->filter('root');
        $grupo = $crawler->filter('root')->children();
        foreach ($grupo as $domElement) {
            $inscrito = new Inscrito();
            $idevento = $domElement->getElementsByTagName("IdEvento")->item(0)->nodeValue;
            $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($idevento);
            if ($evento->getActivo() == TRUE) {

                $inscrito->setIdevento($evento);

                $idpia = $domElement->getElementsByTagName("IdPia")->item(0)->nodeValue;
                $competidor = $em->getRepository('FraterSoftPiaWebBundle:Competidor')->find($idpia);
                $inscrito->setIdpia($competidor);

                if ($competidor) {

                    $idcompetencia = $domElement->getElementsByTagName("IdCompetencia")->item(0)->nodeValue;
                    $competencia = $em->getRepository('FraterSoftPiaWebBundle:Competencia')->find($idcompetencia);
                    $inscrito->setIdcompetencia($competencia);

                    $numero = $domElement->getElementsByTagName("Numero")->item(0);
                    if ($numero)
                        $inscrito->setNumero($domElement->getElementsByTagName("Numero")->item(0)->nodeValue);

                    $idcategoria = $domElement->getElementsByTagName("IdCategoria")->item(0)->nodeValue;
                    $categoria = $em->getRepository('FraterSoftPiaWebBundle:Categoria')->find($idcategoria);
                    $inscrito->setIdCategoria($categoria);

                    $inscrito->setFechahora($domElement->getElementsByTagName("FechaInscripcion")->item(0)->nodeValue);
                    $inscrito->setPunto($domElement->getElementsByTagName("PtoInscripcion")->item(0)->nodeValue);
                    $inscrito->setStatus($domElement->getElementsByTagName("Status")->item(0)->nodeValue);

                    print_r('IdEvento: ' . $inscrito->getIdevento()->getId());
                    if ($inscrito->getIdpia())
                        print_r(' | IdPia: ' . $inscrito->getIdpia()->getId());
                    print_r(' | Competencia: ' . $inscrito->getIdcompetencia()->getId());
                    print_r(' | Numero: ' . $inscrito->getNumero());
                    print_r(' | Categoria: ' . $inscrito->getIdcategoria()->getId());
                    print_r(' | FechaHora: ' . $inscrito->getFechahora());
                    print_r(' | Punto: ' . $inscrito->getPunto());
                    print_r(' | Status: ' . $inscrito->getStatus());

                    print_r("<br>");
                    $inscrito = null;
                }
            }
        }
        return new response('x');
    }

    public function cargarXMLAction() {
        $crawler = new Crawler();

        $em = $this->getDoctrine()->getManager();

        $crawler->addXmlContent(file_get_contents('c:\pia\publicacion\PIAK02_DataPia.xml'));
        $grupo = $crawler->filter('root')->children();
        foreach ($grupo as $domElement) {

            print_r("IdDocumento: " . $domElement->getElementsByTagName("IdDocumento")->item(0)->nodeValue . "<br>");
            print_r("Nombre: " . $domElement->getElementsByTagName("Nombre")->item(0)->nodeValue . "<br>");
            print_r("Apellido: " . $domElement->getElementsByTagName("Apellido")->item(0)->nodeValue . "<br>");
            print_r("Precio: " . $domElement->getElementsByTagName("Precio")->item(0)->nodeValue . "<br>");
        }
        return new response('x');
    }
      
    public function categoriasAction() {

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $criterios = array();
        $em = $this->getDoctrine()->getManager();
        $atributoscriterios = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')
                ->atributosCriterios($this->get('request')->query->get('idevento'));
        if ($atributoscriterios) {
            //crear un array de los campos y los valores pasados
            foreach ($atributoscriterios as $atruibutocriterio) {
                $valor = $this->get('request')->query->get(strtolower($atruibutocriterio->getIdatributo()->getNombre()));
                if ($valor) {
                    $criterios[strtolower($atruibutocriterio->getIdatributo()->getNombre())] = $valor;
                }
            }

            $categoriasselect = new ArrayCollection();

            $categorias = $em->getRepository('FraterSoftPiaWebBundle:Categoria')->arrayCategorias(
                    $this->get('request')->query->get('idcompetencia')
            );
            for($i=0;$i<count($categorias);$i++){
                $reglas = $em->getRepository('FraterSoftPiaWebBundle:CategoriaReglas')->findBy(array(
                    'idcategoria' => $categorias[$i]['id']
                ));
                $parametrok = false;
                foreach ($reglas as $regla) {
                    $parametrok = false;
                    if ($regla->getTipo() == 'R') {
                        if ($regla->getValor1() <= $criterios[strtolower($regla->getAtributo())] &&
                                $regla->getValor2() >= $criterios[strtolower($regla->getAtributo())])
                            $parametrok = true;
                    }
                    else {
                        if ($regla->getValor1() == $criterios[strtolower($regla->getAtributo())])
                            $parametrok = true;
                    }
                    if (!$parametrok)
                        break;
                }
                if ($parametrok) {
                    $categoriasselect->add($categorias[$i]);
                    $parametrok = false;
                }
            }
            $jsonContent = $serializer->serialize($categoriasselect, 'json');
            return new response($jsonContent);
        } else {
            return null;
        }
    }  

    public function anularAction($id) {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inscrito entity.');
        }

        $emails = array();
        if(!is_null($entity->getIdpia()->getEmail()) && filter_var($entity->getIdpia()->getEmail(), FILTER_VALIDATE_EMAIL))
            array_push($emails,$entity->getIdpia()->getEmail());
        if(!is_null($entity->getIdpia()->getEmailpersonal()) && filter_var($entity->getIdpia()->getEmailpersonal(), FILTER_VALIDATE_EMAIL))
            array_push($emails,$entity->getIdpia()->getEmailpersonal());
            
        $entity->setStatus(0);
        $em->persist($entity);
        $em->flush();

        //Se envia el correo de anulación
        $mailer = $this->get('app.mail_controller');
        $mailer->enviar(
                "Pre-Inscripcion Anulada " . $entity->getIdevento()->getNombre(), 
                //$entity->getIdpia()->getEmail(), 
                $emails,
                $this->renderView('FraterSoftPiaWebBundle:Inscrito:anulado.html.twig', array('inscrito' => $entity))
        );
        $request = $this->getRequest();
        $referer = $request->headers->get('referer');  
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => $referer,
                    'texto' => 'Inscripcion Nro ' . $entity->getSecuencia() . ' Anulada',
        ));
    }

    public function buscarPrecioAjaxAction() {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $precioselect = new ArrayCollection();

        $serializer = new Serializer($normalizers, $encoders);

        $idevento = $this->get('request')->query->get('idevento');
        $idcompetencia = $this->get('request')->query->get('idcompetencia');
        $idcategoria = $this->get('request')->query->get('idcategoria');

        $precio = 0;
        $precio = $this->buscarPrecio($idevento, $idcompetencia, $idcategoria);

        if ($precio) {
            $precioselect->add($precio);
            $jsonContent = $serializer->serialize($precioselect, 'json');
            return new response($jsonContent);
        }
        return new response(0);
    }
    
    /**
     * Finds and displays a Inscrito entity.
     *
     */
    public function estadisticasAction($idevento, $email) {
        $em = $this->getDoctrine()->getManager();
        
        //Busca los atributos del evento y los envia al formulario
        $atributos = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')
                ->atributosEvento($idevento);
        if (!$atributos) {
            return new response("No hay atributos configurados para este evento");
        }            

        /*$insxcomp = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->inscritosPorCompetencia($idevento);
        $insxstatus = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->inscritosPorEstatus($idevento);        
        $insxformapago = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->inscritosPorFormaPago($idevento);        
        $insxsexo = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->inscritosPorSexo($idevento);        
        $insxcategoria = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->inscritosPorCategoria($idevento);        
        $insxestado = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->inscritosPorEstado($idevento);     
        $insxprecio = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->inscritosPorPrecio($idevento);  
        $insxfecha = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->inscritosPorFecha($idevento);  */

        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($idevento);
        
        $total=0;
        /*foreach($insxstatus as $row){
            $total+=$row['cantidad'];
        } */
        return $this->render('FraterSoftPiaWebBundle:Inscrito:estadisticas.html.twig', array(
                    /*'insxcomp' => $insxcomp,
                    'insxstatus' => $insxstatus,
                    'insxformapago' => $insxformapago,
                    'insxsexo' => $insxsexo,
                    'insxcategoria' => $insxcategoria,
                    'insxestado' => $insxestado,
                    'insxprecio' => $insxprecio,                    
                    'insxfechahora' => $insxfecha,                    */
                    'idevento' => $idevento,
                    'email' => $email,            
                    'total' => $total,
                    'atributos'=>$atributos,
                    'nombreevento'=>$evento->getNombre(),
        ));
    }    
    
    public function estadisticasAjaxAction() {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $precioselect = new ArrayCollection();

        $serializer = new Serializer($normalizers, $encoders);

        $idevento = $this->get('request')->query->get('idevento');
        $entity = $this->get('request')->query->get('entity');

        $em = $this->getDoctrine()->getManager();
        $estadisticas = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->estadisticas($idevento,$entity);
        
        $sumaCantidad=0;
        foreach($estadisticas as $estadistica){
            $sumaCantidad+=$estadistica['cantidad'];
        }
        
        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($estadisticas),
            "sumaCantidad"=>$sumaCantidad,
            "data"=>$estadisticas)
                , 'json');
        return new response($jsonContent);          
    }    

    public function anulartdcnoconciliadosAction($idevento) {
        $num_anulados = 0;
        $em = $this->getDoctrine()->getManager();
        
        $request = $this->getRequest();
        $referer = $request->headers->get('referer'); 
        
        $noconciliadostdc_notificados = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->AnularNoConciliados($idevento);
        if(count($noconciliadostdc_notificados)<1){
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $referer,
                        'texto' => 'No existen Pagos No Conciliados por anular',
            ));
        }
            
        foreach ($noconciliadostdc_notificados as $row) {
            try{
                $emails = array();
                if(!is_null($row->getIdpia()->getEmail()) && filter_var($row->getIdpia()->getEmail(), FILTER_VALIDATE_EMAIL))
                    array_push($emails,$row->getIdpia()->getEmail());
                if(!is_null($row->getIdpia()->getEmailpersonal()) && filter_var($row->getIdpia()->getEmailpersonal(), FILTER_VALIDATE_EMAIL))
                    array_push($emails,$row->getIdpia()->getEmailpersonal());  
                if(count($emails)>0){
                    $row->setStatus(0);
                    $em->persist($row);
                    $em->flush();
                    $mensaje = \Swift_Message::newInstance()
                            ->setSubject("Pago no Conciliado en " . $row->getIdevento()->getNombre())
                            ->setFrom("pagos@sistemapia.com.ve")
                            ->setCharset('iso-8859-1')
                            ->setContentType('text/html')
                            //->setTo($row->getIdpia()->getEmail())
                            ->setTo($emails)
                            ->setBody(
                            $this->renderView('FraterSoftPiaWebBundle:Inscrito:anulado.html.twig', 
                                    array('inscrito' => $row)
                    ));
                    $this->get('mailer')->send($mensaje);   
                    $num_anulados++;
                }
            } catch(\Swift_TransportException $e){
                $mail_error = array();
                $mail_error['id']=$row->getSecuencia();
                $mail_error['nombre']=$row->getIdpia()->getNombre();
                $mail_error['apellido']=$row->getIdpia()->getApellido();
                $mail_error['email']=$row->getIdpia()->getEmail();
                $arry_erros_mails[$row->getId()]=$mail_error;
            }                
        }
        if($num_anulados>0)
            return $this->render('FraterSoftPiaWebBundle:Default:progressbar.html.twig', array(
                        'url' => $referer,                
                        'texto' => 'Se han anulado ' . $num_anulados . ' pre-inscripciones con pagos no conciliados por TDC',
            ));
        else
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $referer,
                        'texto' => 'No existen Pagos No Conciliados por anular',
            ));
    }      
    
   public function notificarconfirmaciontodosAction($idevento) {
        $num_notifiaciones = 0;
        $em = $this->getDoctrine()->getManager();

        $inscritos = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->listarConciliadas($idevento); 
        if (!$inscritos) {
            throw $this->createNotFoundException('Unable to find Inscrito entity.');
        }
        
        $num_notifiaciones = $this->EnviarConfirmacion($inscritos,$em);
        
        if($num_notifiaciones!=0){
            $request = $this->getRequest();
            $referer = $request->headers->get('referer');     
            return $this->render('FraterSoftPiaWebBundle:Default:progressbar.html.twig', array(
                        'url' => $referer,
                        'texto' => 'Se han enviado satisfactoriamente ' . $num_notifiaciones . ' notificaciones de confirmacion de inscripcion',
            ));
        }
        else{
            $request = $this->getRequest();
            $referer = $request->headers->get('referer');     
            return $this->render('FraterSoftPiaWebBundle:Default:progressbar.html.twig', array(
                        'url' => $referer,
                        'texto' => 'No exiten participantes por notificar',
            ));
        }

    }      
    
    public function notificarconfirmacionloteAction($data_json,$idevento){
        $num_notifiaciones = 0;     
        $ids="";
        $arry_erros_mails = array();
        $data_array=json_decode($data_json, $assoc = true);
        for($i=0;$i<count($data_array);$i++){
            $separador = ($i==count($data_array)-1)?"":",";
            $ids .= $data_array[$i].$separador;
        }
        
        $em = $this->getDoctrine()->getManager();      
        
        $inscritos = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->buscarSecuencias($ids,$idevento);      
        
        $num_notifiaciones = $this->EnviarConfirmacion($inscritos,$em);
        
        if($num_notifiaciones!=0){
            $request = $this->getRequest();
            $referer = $request->headers->get('referer');     
            return $this->render('FraterSoftPiaWebBundle:Default:progressbar.html.twig', array(
                        'url' => $referer,
                        'texto' => 'Se han enviado satisfactoriamente ' . $num_notifiaciones . ' notificaciones de confirmacion de inscripcion',
            ));
        }
        else{
            $request = $this->getRequest();
            $referer = $request->headers->get('referer');     
            return $this->render('FraterSoftPiaWebBundle:Default:progressbar.html.twig', array(
                        'url' => $referer,
                        'texto' => 'No exiten participantes por notificar',
            ));
        }
    }
    
    private function EnviarConfirmacion($inscritos,$em){
        //Envia los correo a los inscritos conciliados
        $num_notifiaciones = 0;        
        foreach ($inscritos as $inscrito) {

            $emails = array();
            if(!is_null($inscrito->getIdpia()->getEmail()) && filter_var($inscrito->getIdpia()->getEmail(), FILTER_VALIDATE_EMAIL))
                array_push($emails,$inscrito->getIdpia()->getEmail());
            if(!is_null($inscrito->getIdpia()->getEmailpersonal()) && filter_var($inscrito->getIdpia()->getEmailpersonal(), FILTER_VALIDATE_EMAIL))
                array_push($emails,$inscrito->getIdpia()->getEmailpersonal()); 
            
            //if($inscrito->getNotificado()!=true){ //OJO MOSCA, VALIDAR ESTO PARA QUE NO QUE CONSUMAN LOS RECURSOS AL REENVIAR MUCHAS VECES
                $subject = is_null($inscrito->getNumero())?
                        "Confirmacion de Inscripcion " . $inscrito->getIdevento()->getNombre():
                        "Dorsal Numero " . $inscrito->getNumero() . ". " . $inscrito->getIdevento()->getNombre();            
                $mailer = $this->get('app.mail_controller');
                $mailer->enviarConfirmacion(
                        $subject, 
                        //$inscrito->getIdpia()->getEmail(), 
                        $emails,
                        $this->renderView('FraterSoftPiaWebBundle:Inscrito:emailok.html.twig', array('inscrito' => $inscrito))
                );
                $num_notifiaciones++;
                $inscrito->setNotificado(true);
            //}            
        }
        $em->flush();
        return($num_notifiaciones);
    }
    
    public function importarAction(Request $request,$idevento){
        $accessor = PropertyAccess::createPropertyAccessor();        
        $em = $this->getDoctrine()->getManager();
        $camposCompetidor = $this->getCampos($em, 'Competidor');
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('inscrito_importar', array('idevento' => $idevento)))
            ->setMethod('POST')                
            ->add('archivo', 'text',array(
                'label'=>'Archivo',
            ))
            ->add('actualiza', 'checkbox',array(
                'label'=>'Actualiza existentes?',
                'required'=>false,
            ))
            ->add('cabecera', 'checkbox',array(
                'label'=>'Posee cabecera?',
                'required'=>false,
            ))
            ->add('Importar', 'button')
        ->getForm();     
        
        $this->addBotonRegresar($form,$this->generateUrl('inscrito_lista_inscritos', array('idevento' => $idevento,'email'=>'admin')));
        $competencias=$em->getRepository("FraterSoftPiaWebBundle:Competencia")->findBy(array('idevento'=>$idevento));
        $categorias=$em->getRepository("FraterSoftPiaWebBundle:Categoria")->listaCategorias($idevento);
        $estados=$em->getRepository("FraterSoftPiaWebBundle:Estado")->findAll();
        $paises=$em->getRepository("FraterSoftPiaWebBundle:Pais")->findAll();
        $formaspago=$em->getRepository("FraterSoftPiaWebBundle:Formaspagoevento")->listar($idevento);
        //$precios=$em->getRepository("FraterSoftPiaWebBundle:Preciosevento")->findBy(array('idevento'=>$idevento));
        
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $file = "bundles/fratersoftpiaweb/fine-uploader/files/" . $form->get('archivo')->getData();
            $folder= "bundles/fratersoftpiaweb/fine-uploader/files/" . 
                    substr($form->get('archivo')->getData(),0,strpos($form->get('archivo')->getData(),"/"));
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
                //print_r($csvcompetidor);
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
                                        $entity = $em->getRepository($valores['tipo'])->findOneBy(array('nombre'=>$valor));
                                        if($entity)
                                            $accessor->setValue($competidor,$campo,$entity);
                                    }
                                    break;
                                default:
                                    if($valor!="")
                                        /* Quitar ese codigo cuando se configure el campo idpais como clave foranea */
                                        if($campo=="idpais"){
                                            $entity = $em->getRepository("FraterSoftPiaWebBundle:Pais")->findOneBy(array('nombre'=>$valor));
                                            if($entity)
                                                $accessor->setValue($competidor,$campo,$entity->getId());
                                        }
                                        /*******************************************************************************/
                                        else
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
                    //Valida si el competidor es un usuario registrado pia, es
                    //decir, si posee email personal, no actualiza los datos.
                    //Solo el usuario puede actualizar sus datos personales
                    if($competidorfind->getEmailpersonal()==""){ 
                        $this->actualizaEntity($em, $competidor, $competidorfind);
                        $em->persist($competidorfind);
                        $count_competidores_a++;
                    }
                }
                
            }            
            try{
                $em->flush();
            }
            catch(\PDOException $e){
                print_r("error al guardar competidor");
            }
            
            $categorias=$em->getRepository("FraterSoftPiaWebBundle:Competencia")->categorias($idevento);
            if(!$categorias)
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => '',
                            'texto' => "Error, no se han configurado categorias para este evento.",
                ));             
            $evento=$categorias[0]->getIdcompetencia()->getIdevento();
            
            $count_inscritos_i=0;
            $count_inscritos_existentes=0;
            
            //Ingresamos la inscripcion y el pago
            foreach($csv as $csvcompetidor){
                //Valida si el iddocumento ya esta inscrito
                $idpia=$em->getRepository("FraterSoftPiaWebBundle:Competidor")->findOneBy(array(
                    'iddocumento'=>$csvcompetidor['iddocumento']
                ));                
                $inscrito=$em->getRepository("FraterSoftPiaWebBundle:Inscrito")->findOneBy(array(
                    'idpia'=>$idpia->getId(),'idevento'=>$idevento,'status'=>'1'
                ));
                if(!$inscrito){ //si no esta inscrito
                    //Validar si hay cupos
                    $cantidad=$em->getRepository("FraterSoftPiaWebBundle:Inscrito")->cantidad($idevento);                    
                    if($evento->getCupocontrol() && $evento->getCupomaximo()<$cantidad){
                        return $this->render('FraterSoftPiaWebBundle:Inscrito:reporteimport.html.twig', array(
                            'cantidad_registros'=>count($csv),
                            'competidores_i'=>$count_competidores_i,
                            'competidores_a'=>$count_competidores_a,
                            'inscritos_i'=>$count_inscritos_i,
                            'inscritos_existentes'=>$count_inscritos_existentes,
                            'mensaje'=>'SE HA ALCANZADO EL CUPO MAXIMO DE INSCRITOS'
                        ));            
                    }
                    //Ingresamos el pago                    
                    $pago = new Pago();
                    $pago->setFechahora(new \DateTime('now'));
                    $pago->setMonto($csvcompetidor['precio']);
                    $pago->setReferencia($csvcompetidor['referencia']);
                    $pago->setBanco($csvcompetidor['banco']);
                    $formapago=$em->getRepository("FraterSoftPiaWebBundle:Formaspagoevento")->
                            buscaPorNombre($idevento,$csvcompetidor['formapago']);
                    $pago->setTipo($formapago[0]['idformapago']['id']);
                    $pago->setConciliado(true);

                    $em->persist($pago);
                    
                    //Ingresamos la inscripcion
                    $inscrito= new Inscrito();
                    $inscrito->setIdevento($evento);
                    $inscrito->setIdpia($idpia);
                    $inscrito->setIdCompetencia($categorias[0]->getIdcompetencia());
                    $inscrito->setNumero($csvcompetidor['numero']==""?null:$csvcompetidor['numero']);
                    foreach($categorias as $categoria){
                        if($categoria->getDescripcion()==$csvcompetidor['categoria'])
                            $inscrito->setIdCategoria($categoria);
                    }
                    $inscrito->setFechahora(new \DateTime('now'));
                    $inscrito->setPunto('PIAK');
                    $inscrito->setEquipo($csvcompetidor['equipo']);
                    $inscrito->setPrecio($csvcompetidor['precio']);
                    $inscrito->setStatus(1);
                    $inscrito->setIdpago($pago);
                    $maxsec = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->maximaSecuencia($idevento);
                    $inscrito->setSecuencia($maxsec ? $maxsec + 1 : 1);
                    
                    $em->persist($inscrito);
                    $em->flush();                    
                    
                    $count_inscritos_i++;
                }
                else
                    $count_inscritos_existentes++;               
            }
            if($count_inscritos_i==0)
                $mensaje='TODAS LAS INSCRIPCIONES YA EXISTEN EN EL EVENTO. NO SE CARGO NINGUA INSCRIPCION.';
            else
                $mensaje='INSCRIPCIONES CARGADAS SATISFACTORIAMENTE';
            unlink($file);
            rmdir($folder);
            return $this->render('FraterSoftPiaWebBundle:Inscrito:reporteimport.html.twig', array(
                'cantidad_registros'=>count($csv),
                'competidores_i'=>$count_competidores_i,
                'competidores_a'=>$count_competidores_a,
                'inscritos_i'=>$count_inscritos_i,
                'inscritos_existentes'=>$count_inscritos_existentes,
                'mensaje'=>$mensaje,
                'url'=>''
            ));                 
        }        
        return $this->render('FraterSoftPiaWebBundle:Inscrito:importar.html.twig', array(
                    'form' => $form->createView(),
                    'campos'=> $camposCompetidor,
                    'idevento'=>$idevento,
                    'competencias'=>$competencias,
                    'categorias'=>$categorias,
                    'estados'=>$estados,
                    'paises'=>$paises,
                    'formaspago'=>$formaspago
        ));
    }  
    
    public function listaauditoriaAction($idevento,$email) {
        $em = $this->getDoctrine()->getManager();
        
        //Busca los atributos del evento y los envia al formulario
        $atributos = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')
                ->atributosEvento($idevento);
        if (!$atributos) {
            return new response("No hay atributos configurados para este evento");
        }    
        
        return $this->render('FraterSoftPiaWebBundle:Inscrito:lista_auditoria.html.twig', array(
                    'atributos' => $atributos,
                    'idevento' => $idevento,
                    'email' => $email,
                    'nombreevento'=>$atributos[0]->getIdEvento()->getNombre()
        ));
    }    
    
    public function listaauditoriaajaxAction($idevento) {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();

        $conciliadas = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->listarAuditoriaAjax($idevento);        

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($conciliadas),
            "data"=>$conciliadas)
                , 'json');
        return new response($jsonContent);            
    }       

 
}