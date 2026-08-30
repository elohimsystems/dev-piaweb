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
use FraterSoft\PiaWebBundle\Entity\InscritoCompetencia;
use FraterSoft\PiaWebBundle\Entity\Competidor;
use FraterSoft\PiaWebBundle\Entity\Competencia;
use FraterSoft\PiaWebBundle\Entity\Evento;
use FraterSoft\PiaWebBundle\Entity\Estado;
use FraterSoft\PiaWebBundle\Entity\Preciosevento;
use FraterSoft\PiaWebBundle\Entity\Precioscompetencia;
use FraterSoft\PiaWebBundle\Entity\Precioscategoria;
use FraterSoft\PiaWebBundle\Entity\Pago;
use FraterSoft\PiaWebBundle\Entity\Grupo;
use FraterSoft\PiaWebBundle\Form\InscritoType;
use FraterSoft\PiaWebBundle\Form\CompetidorType;
use FraterSoft\PiaWebBundle\Form\PagoType;

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
    
    private function getNivelSeguridad($em,$email,$idevento){
        $nivel_seguridad = 1;
        $listaEventos = $em->getRepository('FraterSoftPiaWebBundle:Organizador')
                ->listaIdsEventosPatrocinadosPorEmail($email);
        foreach($listaEventos as $evento){
            if($evento['id'] == $idevento)
                $nivel_seguridad = 2;
        }
        return($nivel_seguridad);
    }

    public function listapreinscritosAction($idevento) {
        $em = $this->getDoctrine()->getManager();
        $email = $this->getUser()->getEmail();

        $nivel_seguridad = $this->getNivelSeguridad($em,$email,$idevento);

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
                    'nombreevento'=>$atributos[0]->getIdEvento()->getNombre(),
                    'titulocompetencias'=>$atributos[0]->getIdEvento()->getTitulocompetencias(),
                    'nivel_seguridad' => $nivel_seguridad
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
                    'nombreevento'=>$atributos[0]->getIdEvento()->getNombre(),
                    'nivel_seguridad' => $nivel_seguridad
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
    

    public function listainscritosAction($idevento) {
        $em = $this->getDoctrine()->getManager();
        $email = $this->getUser()->getEmail();
        
        $nivel_seguridad = $this->getNivelSeguridad($em,$email,$idevento);

        //Busca los atributos del evento y los envia al formulario
        $atributos = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')
                ->atributosEvento($idevento);
        if (!$atributos) {
            return new response("No hay atributos configurados para este evento");
        }    
        
        $request = $this->container->get('request');
        $routeURL = $request->getRequestUri();
        $this->get('session')->set('urlreturn',$routeURL);
        
        return $this->render('FraterSoftPiaWebBundle:Inscrito:lista_inscritos.html.twig', array(
                    'atributos' => $atributos,
                    'idevento' => $idevento,
                    'email' => $email,
                    'nombreevento'=>$atributos[0]->getIdEvento()->getNombre(),
                    'titulocompetencias'=>$atributos[0]->getIdEvento()->getTitulocompetencias(),
                    'nivel_seguridad' => $nivel_seguridad
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
    
    public function listaanuladosAction($idevento) {
        $em = $this->getDoctrine()->getManager();
        $email = $this->getUser()->getEmail();

        $nivel_seguridad = $this->getNivelSeguridad($em,$email,$idevento);

        $anuladas = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->listarAnuladas($idevento);


        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($idevento);       

        return $this->render('FraterSoftPiaWebBundle:Inscrito:lista_anulados.html.twig', array(
                    'anuladas' => $anuladas,
                    'idevento' => $idevento,
                    'email' => $email,
                    'nombreevento'=>$evento->getNombre(),
                    'titulocompetencias'=>$evento->getTitulocompetencias(),
                    'nivel_seguridad' => $nivel_seguridad
        ));
    }    

    private function getControlParentalEmailData($evento, $em) {
        $data = null;
        $adjuntos = array();
        if ($evento->getControlparental()) {
            $controlParental = $em->getRepository('FraterSoftPiaWebBundle:ControlParental')
                ->findOneBy(array('idevento' => $evento->getId()));
            if ($controlParental) {
                $data = array(
                    'mensaje' => $controlParental->getMensaje(),
                    'titulo_documento' => $controlParental->getTituloDocumento(),
                    'documento_pdf' => $controlParental->getDocumentoPdf(),
                );
                if ($controlParental->getDocumentoPdf()) {
                    $pdfPath = __DIR__ . '/../../../../web/' . $controlParental->getDocumentoPdf();
                    if (file_exists($pdfPath)) {
                        $adjuntos[] = $pdfPath;
                    }
                }
            }
        }
        return array($data, $adjuntos);
    }

    /**
     * Creates a new Inscrito entity.
     *
     */
    public function createAction(Request $request,$idcompetencia,$idgrupo) {
        
        $entity = new Inscrito();
        $competidor = new Competidor();
        $competencia = new Competencia();
        $accessor = PropertyAccess::createPropertyAccessor();
        
        $form = $this->createCreateForm($entity, null);
        $em = $this->getDoctrine()->getManager();

        //Si el evento tiene multicompetencia activo, el campo 'idcompetencia' mapeado se
        //reemplaza por uno no mapeado 'idcompetencias' (checkboxes) ANTES de procesar el
        //submit, para que handleRequest capture la seleccion. El evento se determina leyendo
        //el POST crudo porque el formulario aun no ha sido vinculado en este punto.
        $esMulticompetencia = false;
        $datosSubmit = $request->request->get($form->getName());
        $idEventoSubmit = is_array($datosSubmit) && !empty($datosSubmit['idevento']) ? $datosSubmit['idevento'] : null;
        if ($idEventoSubmit) {
            $eventoSubmit = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($idEventoSubmit);
            if ($eventoSubmit && $eventoSubmit->getMulticompetencia()) {
                $competenciasSubmit = $em->getRepository('FraterSoftPiaWebBundle:Competencia')
                        ->findBy(array('idevento' => $idEventoSubmit));
                if (count($competenciasSubmit) > 1) {
                    $esMulticompetencia = true;
                    if ($form->has('idcompetencia')) {
                        $form->remove('idcompetencia');
                    }
                    $form->add('idcompetencias', 'entity', array(
                        'class' => 'FraterSoftPiaWebBundle:Competencia',
                        'choices' => $competenciasSubmit,
                        'multiple' => true,
                        'expanded' => true,
                        'mapped' => false,
                        'required' => true,
                    ));
                }
            }
        }

        $form->handleRequest($request);

        //En multicompetencia debe marcarse al menos una modalidad
        if ($esMulticompetencia) {
            $seleccion = $form->get('idcompetencias')->getData();
            if ($seleccion === null || count($seleccion) === 0) {
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $this->generateUrl('competidor_find', array('idevento' => $idEventoSubmit)),
                            'texto' => 'Debe seleccionar al menos una ' . ($eventoSubmit->getTitulocompetencias() ?: 'Competencia'),
                            'tema' => $eventoSubmit->getTema()
                ));
            }
        }

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

        //Si el evento tiene multicompetencia activo, el campo no mapeado 'idcompetencias'
        //trae la seleccion (una o varias competencias); si no existe o esta vacio, se
        //mantiene el comportamiento actual de una sola competencia.
        $idcompetenciasSeleccionadas = $form->has('idcompetencias') ? $form->get('idcompetencias')->getData() : null;
        if ($idcompetenciasSeleccionadas != null && count($idcompetenciasSeleccionadas) > 0) {
            $competencia = $idcompetenciasSeleccionadas->first();
        } else {
            $idcompetenciasSeleccionadas = null;
            $competencia = $form->get('idcompetencia')->getData();
        }

        //Valida cupos: rechaza si alguna competencia elegida ya llego a su cupo maximo
        $competenciasAValidar = ($idcompetenciasSeleccionadas !== null)
            ? $idcompetenciasSeleccionadas->toArray()
            : ($competencia ? array($competencia) : array());
        $llenas = $this->competenciasSinCupo($competenciasAValidar, $form->get('idevento')->getData()->getId(), $em);
        if (!empty($llenas)) {
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('competidor_find', array('idevento' => $form->get('idevento')->getData()->getId())),
                        'texto' => 'Ya no hay cupos disponibles en: ' . implode(', ', $llenas),
                        'tema' => $entity->getIdevento()->getTema()
            ));
        }

        if ($form->isSubmitted()) {
            
            $emails = array();
            if(!is_null($entity->getIdpia()->getEmail()) && filter_var($entity->getIdpia()->getEmail(), FILTER_VALIDATE_EMAIL))
                array_push($emails,$entity->getIdpia()->getEmail());
            if(!is_null($entity->getIdpia()->getEmailpersonal()) && filter_var($entity->getIdpia()->getEmailpersonal(), FILTER_VALIDATE_EMAIL))
                array_push($emails,$entity->getIdpia()->getEmailpersonal());
                
            $emailfrom="";
            
            if(!is_null($entity->getIdevento()->getIdorganizador()->getEmail()) && filter_var($entity->getIdevento()->getIdorganizador()->getEmail(), FILTER_VALIDATE_EMAIL))
                $emailfrom = $entity->getIdevento()->getIdorganizador()->getEmail();
                //$emailfrom = "inscripciones@sistemapia.com";
            else
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $this->generateUrl('competidor_find', array('idevento' => $form->get('idevento')->getData()->getId())),
                            'texto' => 'El correo del Organizador no posee en formato adecuado',
                            'tema' => $entity->getIdevento()->getTema()
                ));
                
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

            //La inscripcion grupal (Grupo/relay) es una feature ortogonal a multicompetencia,
            //no diseñada para interoperar con ella: si hay varias competencias marcadas, se omite.
            $grupo = null;
            if ($idcompetenciasSeleccionadas === null) {
                $grupo=new Grupo();
                $grupo=$em->getRepository('FraterSoftPiaWebBundle:Grupo')->findOneBy(array(
                            'idcompetencia'=>$form->get('idcompetencia')->getData()->getId(),
                        ));
                if($grupo){
                    if($idgrupo==null){
                        $secuenciagrupo=$grupo->getSecuencia();
                        $em->getConnection()->beginTransaction();
                        $idgrupo='C'.$entity->getIdCompetencia()->getId().'G'.$secuenciagrupo;
                        $idcompetencia=$form->get('idcompetencia')->getData()->getId();
                        try{
                            $grupo->setSecuencia($secuenciagrupo+1);
                            $em->persist($grupo);
                            $em->flush();
                            $em->getConnection()->commit();
                        }
                        catch (Exception $e) {
                            $em->getConnection()->rollback();
                            throw $e;
                        }
                    }
                }
            }

            if($entity->getIdevento()->getProceso()==1 && $grupo==null){
                
                foreach($form->get('pagos')->getData() as $pago){
                    $pago->setFechahora(new \DateTime('now'));
                    if ($form->get('pagos')[0]->getData()->getIdformapago()->getId() == 14) {
                        $pago->setConciliado(true);
                        $pago->setConciliadoel(new \DateTime('now'));
                    }
                    $pago->setIdinscrito($entity);
                }                
            }

            //Modo multicompetencia: idcompetencia/idcategoria del Inscrito quedan vacios
            //(no hay una unica competencia/categoria que representen la inscripcion); la
            //relacion real vive en InscritoCompetencia, una fila por competencia marcada.
            if ($idcompetenciasSeleccionadas === null) {
                $entity->setIdcompetencia($competencia);
            }
            $entity->setIdpia($competidor);

            if ($idcompetenciasSeleccionadas !== null) {
                $totalMulticompetencia = $this->reconstruirInscritoCompetencias($entity, $idcompetenciasSeleccionadas, $request, $em);
                foreach ($form->get('pagos')->getData() as $pagoMulti) {
                    $pagoMulti->setPrecio($totalMulticompetencia);
                }
            }

            if($grupo==null)
                $entity->setStatus(1);
            else{
                $entity->setStatus(3);
                $entity->setIdgrupo($idgrupo);
            }
                
            $entity->setFechahora(new \DateTime('now'));

            $credito = $em->getRepository('FraterSoftPiaWebBundle:Creditos')
                    ->findOneBy(array(
                        'cedula' => $entity->getIdpia()->getIddocumento(),
                        'idevento' => $entity->getIdevento()->getId(),
                        'disponible' => true,
                    ));
            if($credito){
                $credito->setDisponible(false);
                $credito->setUsadoel(new \DateTime('now'));
            }

            $em->getConnection()->beginTransaction();            
            try{
                $maxsec = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->maximaSecuencia($entity->getIdevento()->getId());
                $entity->setSecuencia($maxsec ? $maxsec + 1 : 1);
                $em->persist($entity);
                $em->flush();            

                if($credito){
                    $em->persist($credito);
                    $em->flush();
                }

                $em->getConnection()->commit();
            } 
            catch (Exception $e) {
                $em->getConnection()->rollback();
                throw $e;
            }

            //Notifica al organizador si alguna de las competencias de esta inscripcion
            //llego a su cupo maximo
            $competenciasInscritas = ($idcompetenciasSeleccionadas !== null)
                ? $idcompetenciasSeleccionadas->toArray()
                : ($competencia ? array($competencia) : array());
            $this->notificarCupoMaximoCompetencias($entity, $competenciasInscritas, $em);


            if($entity->getIdevento()->getProceso()==1 && $grupo==null){
                if(count($form->get('pagos')->getData())==1){
                    if ($form->get('pagos')[0]->getData()->getIdformapago()->getVerificable() == true) { //PAGOS CON TDC
                        //if ($form->get('pagos')->getData()->getTipo() <> 0) { //INSCRIPCIONES GRATIS
                            //Se envia el correo de confirmacion de preinscripcion
                            $mailer = $this->get('app.mail_controller');
                            list($cpData, $cpAdjuntos) = $this->getControlParentalEmailData($entity->getIdevento(), $em);
                            $mailer->enviar(
                                    $emailfrom,
                                    "Pre-Inscripcion " . $entity->getIdevento()->getNombre(), 
                                    $emails,
                                    $this->renderView('FraterSoftPiaWebBundle:Inscrito:email.html.twig', array('entity' => $entity, 'controlParental' => $cpData)),
                                    $cpAdjuntos
                            );
                            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                                        'url' => $this->generateUrl('inscrito_confirmacion', array('id' => $entity->getId())),
                                        'texto' => "Se ha enviado la confirmacion de inscripcion a su correo, por favor verifique",
                            ));
                            //return $this->redirect($this->generateUrl('inscrito_confirmacion', array('id' => $entity->getId())));
                        /*}
                        else{ //SI LA INSCRIPCION ES GRATUITA SE ENVIA LA CONFIRMACION
                            $mailer = $this->get('app.mail_controller');
                            $mailer->enviarConfirmacion(
                                    $emailfrom,
                                    "Confirmacion de Inscripcion " . $entity->getIdevento()->getNombre(), 
                                    $emails,
                                    $this->renderView('FraterSoftPiaWebBundle:Inscrito:emailok.html.twig', array('inscrito' => $entity))
                            );         
                            return $this->render('FraterSoftPiaWebBundle:Inscrito:conciliado.html.twig', array(
                                        'inscrito' => $entity,
                                        'idevento' => $entity->getIdevento()->getId()                        
                            ));                     
                        }*/
                    } 
                    else{
                        //SI LA INSCRIPCION ES GRATUITA SE ENVIA LA CONFIRMACION
                        if ($form->get('pagos')[0]->getData()->getIdformapago()->getId() == 14) { //FORMA DE PAGO EXONERADA o gratis, DEBE EXISTIR EN LA BD
                            $mailer = $this->get('app.mail_controller');
                            list($cpData, $cpAdjuntos) = $this->getControlParentalEmailData($entity->getIdevento(), $em);
                            $mailer->enviarConfirmacion(
                                    $emailfrom,
                                    "Confirmacion de Inscripcion " . $entity->getIdevento()->getNombre(), 
                                    $emails,
                                    $this->renderView('FraterSoftPiaWebBundle:Inscrito:emailok.html.twig', array('inscrito' => $entity, 'controlParental' => $cpData)),
                                    $cpAdjuntos
                            );         
                            return $this->render('FraterSoftPiaWebBundle:Inscrito:conciliado.html.twig', array(
                                        'inscrito' => $entity,
                                        'idevento' => $entity->getIdevento()->getId()                        
                            ));                     
                        }
                        else{
                            $formapago=$em->getRepository('FraterSoftPiaWebBundle:Formaspagoevento')
                                        ->findBy(array('idevento'=>$entity->getIdevento()->getId(),'idformapago'=>3));                
                            if($formapago){
                                return $this->render('FraterSoftPiaWebBundle:Inscrito:boton123pago.html.twig', array(
                                            'entity' => $entity,
                                            'incremento' => $formapago[0]->getIncremento(),
                                            'boton123Pago' => $this->generarBoton123Pago($entity,$formapago[0]->getIncremento()),
                                ));
                            }
                        }
                    }		
                }
            }
            else{ //Envia el correo si el proceso es tipo 2
                if($grupo==null){
                    //En multicompetencia, idcompetencia/idcategoria del Inscrito quedan
                    //vacios; el detalle de precios por competencia ya viaja en
                    //entity.competencias, usado por el twig del correo.
                    $precio = ($idcompetenciasSeleccionadas !== null) ? array() : $this->buscarPrecio(
                                $entity->getIdevento()->getid(),
                                $entity->getIdcompetencia()->getid(),
                                $entity->getIdcategoria()->getid(),
                                null
                            );
                    $mailer = $this->get('app.mail_controller');
                    list($cpData, $cpAdjuntos) = $this->getControlParentalEmailData($entity->getIdevento(), $em);
                    $mailer->enviar(
                        $emailfrom,
                        "Pre-Inscripcion " . $entity->getIdevento()->getNombre(), 
                        $emails,
                        $this->renderView('FraterSoftPiaWebBundle:Inscrito:emailproceso2.html.twig', array(
                            'entity' => $entity,
                            'precios'=>$precio,
                            'controlParental' => $cpData,
                        )),
                        $cpAdjuntos
                    );
                    return $this->redirect($this->generateUrl('inscrito_confirmacion', array('id' => $entity->getId())));
                }
                else{ //si la inscripcion es grupal
                    $integrantes=$em->getRepository('FraterSoftPiaWebBundle:Inscrito')->findBy(array(
                        'idevento' => $entity->getIdevento()->getId(),
                        'idgrupo'=>$idgrupo,
                        'status'=>3,
                    ));                    
                    if(count($integrantes)<$grupo->getIntegrantes())
                        return $this->redirect($this->generateUrl('inscrito_confirmaciongrupo', array(
                            'idevento' => $entity->getIdevento()->getId(),
                            'idcompetencia' => $idcompetencia,
                            'idgrupo'=> $idgrupo,
                        )));
                    else
                        return $this->redirect($this->generateUrl('pago_newgrupo', array(
                            'idevento' => $entity->getIdevento()->getId(),
                            'idcompetencia' => $idcompetencia,
                            'idgrupo'=> $idgrupo
                        )));
                }
            }
        }
        
        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                var_dump($child->getName());
                $errors[$child->getName()] = $this->getErrorMessages($child);
                print_r($errors);
                print_r($child->getData());
            }
        }

//        foreach ($form->get('idpia')->all() as $child) {
//            if (!$child->isValid()) {
//                var_dump($child->getName());
//                $errors[$child->getName()] = $this->getErrorMessages($child);
//                print_r($errors);
//            }
//        }        
        
        return new Response($form->getErrorsAsString());
    }

    /**
     * Precio ESPECIFICO de una competencia/categoria (cascada categoria -> competencia),
     * SIN caer al precio de evento. Devuelve 0 si no hay precio propio configurado.
     */
    private function buscarPrecioCompetenciaCategoria($idcompetencia, $idcategoria, $idmoneda)
    {
        $em = $this->getDoctrine()->getManager();
        $precio = new ArrayCollection();
        if ($idcategoria) {
            $precio = $em->getRepository('FraterSoftPiaWebBundle:Precioscategoria')
                    ->BuscaPreciosCategoria($idcategoria, $idmoneda);
        }
        if ($idcompetencia && count($precio) == 0) {
            $precio = $em->getRepository('FraterSoftPiaWebBundle:Precioscompetencia')
                    ->BuscaPreciosCompetencia($idcompetencia, $idmoneda);
        }
        return (count($precio) > 0) ? $precio[0]['precio'] : 0;
    }

    /**
     * Precio configurado a nivel de evento para la moneda dada. 0 si no hay.
     */
    private function buscarPrecioEvento($idevento, $idmoneda)
    {
        $em = $this->getDoctrine()->getManager();
        $precio = $em->getRepository('FraterSoftPiaWebBundle:Preciosevento')
                ->BuscaPreciosEvento($idevento, $idmoneda);
        return (count($precio) > 0) ? $precio[0]['precio'] : 0;
    }

    /**
     * Reconstruye las filas InscritoCompetencia de un inscrito multicompetencia a partir de
     * las competencias seleccionadas y de los <select> "idcategoria_<idcompetencia>" del POST.
     * Elimina el detalle anterior (si lo hay) y deja idcompetencia/idcategoria del Inscrito en
     * null.
     *
     * Regla de precios: cada InscritoCompetencia guarda su precio ESPECIFICO (de competencia
     * o categoria; 0 si no tiene). El precio total del Inscrito = suma de los precios
     * especificos + el precio de evento UNA sola vez, y solo si alguna modalidad no tiene
     * precio propio (el precio de evento actua como tarifa base, no se cobra por modalidad).
     *
     * @return float El precio total.
     */
    private function reconstruirInscritoCompetencias(Inscrito $entity, $competenciasSeleccionadas, Request $request, $em)
    {
        //Elimina el detalle anterior (en el alta la coleccion viene vacia y no hace nada)
        foreach ($entity->getCompetencias() as $inscritoCompetenciaPrevia) {
            $em->remove($inscritoCompetenciaPrevia);
        }
        $entity->getCompetencias()->clear();

        //Moneda seleccionada en el POST (radio pagos[0][idmoneda])
        $datosPost = $request->request->get('fratersoft_piawebbundle_inscrito');
        $idMoneda = (is_array($datosPost) && isset($datosPost['pagos'][0]['idmoneda']) && $datosPost['pagos'][0]['idmoneda'] !== '')
            ? $datosPost['pagos'][0]['idmoneda'] : null;

        $sumaEspecificos = 0;
        $algunaSinPrecioEspecifico = false;
        $cantidad = 0;

        if ($competenciasSeleccionadas != null) {
            foreach ($competenciasSeleccionadas as $comp) {
                $cantidad++;
                $idCategoriaComp = $request->request->get('idcategoria_' . $comp->getId());
                $categoriaComp = $idCategoriaComp
                    ? $em->getRepository('FraterSoftPiaWebBundle:Categoria')->find($idCategoriaComp)
                    : null;

                $montoEspecifico = $this->buscarPrecioCompetenciaCategoria($comp->getId(), $idCategoriaComp, $idMoneda);

                $inscritoCompetencia = new InscritoCompetencia();
                $inscritoCompetencia->setIdcompetencia($comp);
                $inscritoCompetencia->setIdcategoria($categoriaComp);
                $inscritoCompetencia->setPrecio($montoEspecifico);
                $inscritoCompetencia->setIdinscrito($entity);
                $entity->addCompetencia($inscritoCompetencia);

                if ($montoEspecifico > 0) {
                    $sumaEspecificos += $montoEspecifico;
                } else {
                    $algunaSinPrecioEspecifico = true;
                }
            }
        }

        if ($cantidad == 0) {
            $precioTotal = 0;
        } else {
            $precioEvento = $this->buscarPrecioEvento($entity->getIdevento()->getId(), $idMoneda);
            $precioTotal = $sumaEspecificos + ($algunaSinPrecioEspecifico ? $precioEvento : 0);
        }

        $entity->setIdcompetencia(null);
        $entity->setIdcategoria(null);
        $entity->setPrecio($precioTotal);

        return $precioTotal;
    }

    /**
     * De una lista de Competencia, devuelve las descripciones de las que ya alcanzaron su
     * cupo maximo (si lo tienen configurado). Array vacio si todas tienen cupo.
     *
     * @param array $competencias  Competencia[]
     * @return string[]
     */
    private function competenciasSinCupo(array $competencias, $idevento, $em)
    {
        if (empty($competencias)) {
            return array();
        }
        $cupos = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->cantidadPorCompetencia($idevento);
        $llenas = array();
        foreach ($competencias as $comp) {
            if ($comp === null) {
                continue;
            }
            $max = $comp->getCupomaximo();
            if (!$max) {
                continue;
            }
            $actual = isset($cupos[$comp->getId()]) ? $cupos[$comp->getId()] : 0;
            if ($actual >= $max) {
                $llenas[] = $comp->getDescripcion();
            }
        }
        return $llenas;
    }

    /**
     * AJAX: dado el evento y una lista de ids de competencia (csv), devuelve cuales ya no
     * tienen cupo disponible.  {"llenas":[{"id":..,"descripcion":".."}]}
     */
    public function cupocompetenciasAjaxAction()
    {
        $query = $this->get('request')->query;
        $idevento = $query->get('idevento');
        $ids = array_filter(array_map('trim', explode(',', $query->get('idcompetencias', ''))));

        $em = $this->getDoctrine()->getManager();
        $llenas = array();
        if ($idevento && $ids) {
            $cupos = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->cantidadPorCompetencia($idevento);
            foreach ($ids as $id) {
                $comp = $em->getRepository('FraterSoftPiaWebBundle:Competencia')->find($id);
                if (!$comp) {
                    continue;
                }
                $max = $comp->getCupomaximo();
                if (!$max) {
                    continue;
                }
                $actual = isset($cupos[$comp->getId()]) ? $cupos[$comp->getId()] : 0;
                if ($actual >= $max) {
                    $llenas[] = array('id' => $comp->getId(), 'descripcion' => $comp->getDescripcion());
                }
            }
        }
        return new Response(json_encode(array('llenas' => $llenas)));
    }

    /**
     * Tras guardar una inscripcion, revisa las competencias involucradas: si alguna tiene
     * cupo maximo configurado y ya se alcanzo (o superó), envia un correo al organizador
     * (email y email de contacto) avisando que esa competencia llego a su cupo.
     *
     * @param Inscrito $entity
     * @param array    $competencias  competencias (Competencia) de esta inscripcion
     */
    private function notificarCupoMaximoCompetencias(Inscrito $entity, array $competencias, $em)
    {
        if (empty($competencias)) {
            return;
        }

        $evento = $entity->getIdevento();
        $organizador = $evento->getIdorganizador();
        if ($organizador === null) {
            return;
        }

        $destinatarios = array();
        foreach (array($organizador->getEmail(), $organizador->getEmailcontacto()) as $mail) {
            if (!is_null($mail) && filter_var($mail, FILTER_VALIDATE_EMAIL) && !in_array($mail, $destinatarios)) {
                $destinatarios[] = $mail;
            }
        }
        if (empty($destinatarios)) {
            return;
        }

        $emailfrom = filter_var($organizador->getEmail(), FILTER_VALIDATE_EMAIL)
            ? $organizador->getEmail()
            : 'inscripciones@sistemapia.com';

        $cupos = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->cantidadPorCompetencia($evento->getId());

        $mailer = $this->get('app.mail_controller');
        $notificadas = array();
        foreach ($competencias as $comp) {
            if ($comp === null || in_array($comp->getId(), $notificadas)) {
                continue;
            }
            $max = $comp->getCupomaximo();
            if (!$max) {
                continue;
            }
            $actual = isset($cupos[$comp->getId()]) ? $cupos[$comp->getId()] : 0;
            if ($actual < $max) {
                continue;
            }
            $notificadas[] = $comp->getId();
            try {
                $mailer->enviar(
                    $emailfrom,
                    'Cupo maximo alcanzado - ' . $comp->getDescripcion() . ' - ' . $evento->getNombre(),
                    $destinatarios,
                    $this->renderView('FraterSoftPiaWebBundle:Inscrito:email_cupomaximo.html.twig', array(
                        'evento' => $evento,
                        'competencia' => $comp,
                        'cupomaximo' => $max,
                        'inscritos' => $actual,
                    ))
                );
            } catch (\Exception $e) {
                // No interrumpe el flujo de la inscripcion si falla el correo al organizador
            }
        }
    }

    /**
     * Creates a form to create a Inscrito entity.
     *
     * @param Inscrito $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Inscrito $entity, $idevento,$idcompetencia=null,$idgrupo=null) {
        if ($idevento) {
            $em = $this->getDoctrine()->getManager();
            $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($idevento);
            $entity->setIdevento($evento);
        }
        //createForm
        if($idgrupo==null)
            $form = $this->createForm(new InscritoType(), $entity, array(
                'attr' => ['id' => 'inscrito-form', 'class' => 'cmxform'],
                'action' => $this->generateUrl('inscrito_create'),
                'method' => 'POST',
            ));
        else
            $form = $this->createForm(new InscritoType(), $entity, array(
                'attr' => ['id' => 'inscrito-form', 'class' => 'cmxform'],
                'action' => $this->generateUrl('inscrito_create',array(
                    'idcompetencia'=>$idcompetencia,
                    'idgrupo'=>$idgrupo
                )),
                'method' => 'POST',
            ));

        //Agrega la Fecha y Hora de la inscripcion
        $form
                ->add('fechahora', 'datetime', array(
                    'widget' => 'single_text',
                    'data' => new \DateTime('now'),
                    'attr' => array('style' => 'display:none'),
                    'label' => false,
                ))
        ;
//        $form->add('submit', 'submit', array(
//            'attr' => ['class' => 'submit'],
//            'label' => 'Inscribir'
//        ));

        return $form;
    }

    /**
     * Displays a form to create a new Inscrito entity.
     *
     */
    public function newAction($idevento, $idcompetidor, $iddocumento,$idcompetencia,$idgrupo) {
        $entity = new Inscrito();
        $evento = new Evento();
        $precioevento = new Preciosevento();
        $em = $this->getDoctrine()->getManager();
        $controlParentalData = null;

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
        
        //Carga configuracion de Control Parental si el evento la tiene activa
        $controlParentalConfig = null;
        if ($evento->getControlparental()) {
            $controlParental = $em->getRepository('FraterSoftPiaWebBundle:ControlParental')
                ->findOneBy(array('idevento' => $idevento));
            if ($controlParental) {
                $controlParentalConfig = array(
                    'edad_control' => $controlParental->getEdadControl(),
                    'mensaje' => $controlParental->getMensaje(),
                    'titulo_documento' => $controlParental->getTituloDocumento(),
                    'documento_pdf' => $controlParental->getDocumentoPdf(),
                );
            }
        }

        //Busca la cantidad de competencias por eventos, si hay mas de 1 muestra el combo
        $competencias = $em->getRepository('FraterSoftPiaWebBundle:Competencia')
                ->findBy(array(
            'idevento' => $idevento,
        ));

        //No se muestran las competencias que ya alcanzaron su cupo maximo (no aplica a
        //campeonatos, donde la competencia se asigna segun el competidor).
        if (!$evento->getIdCampeonato()) {
            $cuposcompetencia = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                    ->cantidadPorCompetencia($idevento);
            $competencias = array_values(array_filter($competencias, function ($comp) use ($cuposcompetencia) {
                $max = $comp->getCupomaximo();
                if (!$max) {
                    return true;
                }
                $actual = isset($cuposcompetencia[$comp->getId()]) ? $cuposcompetencia[$comp->getId()] : 0;
                return $actual < $max;
            }));

            if (count($competencias) === 0) {
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                            'texto' => 'No hay cupos disponibles en las ' . ($evento->getTitulocompetencias() ?: 'competencias') . ' de este evento',
                            'tema' => $evento->getTema()
                ));
            }
        }

        //Si el evento tiene activo multicompetencia y hay mas de 1 competencia configurada,
        //el campo de competencia se muestra como checkboxes (seleccion multiple)
        $multicompetenciaActivo = $evento->getMulticompetencia() && count($competencias) > 1;

        //Titulo del campo de competencia (configurable en el evento) y, si tras el filtro de
        //cupos queda una sola competencia, la referencia a ella para acotar las categorias.
        $labelCompetencia = $evento->getTitulocompetencias() ?: 'Competencia';
        $competenciaUnica = (count($competencias) === 1) ? $competencias[0] : null;

        $cantidad_integrantes=($idgrupo)?$em->getRepository('FraterSoftPiaWebBundle:Inscrito')->cantidadIntegrantesGrupo($idgrupo)+1:1;
                
        //Verifica si el competidor ya esta inscrito
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->buscarInscrito($idevento, $idcompetidor);
        $url = '';

        $default_moneda=null;
        $arraymonedas=$this->MonedasEvento($em,$default_moneda,$evento);
        
        if ($entity != null) { //Si el competidor esta inscrito
            if (is_null($entity[0]->getPagos()[0])){//Si la inscripcion no posee pago
                switch (true){
                    case $evento->getProceso()==2:
                        $formasdepago = $em->getRepository('FraterSoftPiaWebBundle:Formaspagoevento')
                                ->listarPublicos($idevento,$default_moneda);
                        $cantidadformaspago=0;
                        foreach ($formasdepago as $formadepago) {
                            $cantidadformaspago++;
                            if ($formadepago->getId()==3) {
                                $formasdepagoarray[$formadepago->getId()] = $formadepago->getNombre();
                            }
                        }       
                        
                        // revisar todo este blqoye
                        if($cantidadformaspago==1 and $formadepago->getId()==3){
                            return $this->redirect($this->generateUrl('pago_newtdc', array('idinscripcion' => $entity[0]->getId()))); 
                        }
                        else{
                            $pago=$em->getRepository('FraterSoftPiaWebBundle:Pago')->findBy(array('idinscrito'=>$entity[0]->getId()));
                            if($pago)//SI YA POSEE PAGO
                                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                                            'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                                            'texto' => 'Se ha encontrado una Pre-Inscripci&oacute;n para la cedula ingresada<br>'
                                            . 'El organizador aun no ha conciliado la informacion de pago. Escriba a <b>' . $evento->getIdorganizador()->getEmailcontacto()
                                    . "</b> para mayor informaci&oacute;n",
                                            'tema' => $evento->getTema()
                                ));                
                            else//SI NO POSEE PAGO
                                if($evento->getRegistropago()==1 || is_null($evento->getRegistropago()))
                                    return $this->redirect($this->generateUrl('pago_new', array(
                                        'idinscripcion' => $entity[0]->getId())
                                    )); 
                                else
                                    return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                                                'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                                                'texto' => 'Se ha encontrado una Pre-Inscripci&oacute;n sin registro de pago para la cedula ingresada<br>'
                                                . 'Si aun no ha pagado, por favor realice el pago y envie la informaci&oacute;n del mismo al correo <b>' . $evento->getIdorganizador()->getEmail()
                                        . "</b>.",
                                                'tema' => $evento->getTema()
                                    ));                
                        }
                        break;
                    case $evento->getProceso()==1:
                        switch(true){
                            case $entity[0]->getIdgrupo()!=null && $entity[0]->getStatus()==3:
                                    $cantidad_integrantes=$em->getRepository('FraterSoftPiaWebBundle:Inscrito')->cantidadIntegrantesGrupo($entity[0]->getIdgrupo());
                                    if($cantidad_integrantes>=$entity[0]->getIdcompetencia()->getGrupo()->getIntegrantes())
                                        return $this->redirect($this->generateUrl('pago_newgrupo', array(
                                            'idevento' => $idevento,
                                            'idcompetencia' => $entity[0]->getIdcompetencia()->getId(),
                                            'idgrupo'=> $entity[0]->getIdgrupo(),
                                        )));                                        
                                    else
                                        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                                            'url' => null,
                                            'texto' => 'El participante esta registrado en una inscripcion grupal sin finalizar,'
                                            . ' presione <b>Continuar</b> para finalizar con el proceso de inscripcion de dicho grupo o <b>Regresar</b> para anular el registro.',
                                            'urlcontinuar' => $this->generateUrl('inscrito_confirmaciongrupo', array(
                                                'idevento' => $idevento,
                                                'idcompetencia'=>$entity[0]->getIdcompetencia()->getId(),
                                                'idgrupo'=>$entity[0]->getIdgrupo(),
                                            )),
                                        ));
                                break;
                            case $entity[0]->getIdgrupo()!=null && $entity[0]->getStatus()==1:
                                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                                            'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                                            'texto' => 'El participante esta registrado y pertenece a un grupo.'
                                ));
                            break;
                        }
                        break;                                    
                }
            }
            if ($entity[0]->getPagos()[0]->getTipo() == "3" and $entity[0]->getPagos()[0]->getConciliado() == false)
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                            'texto' => 'Usted posee una pre-inscripci&oacute;n pendiente de pago por Tarjeta de Cr&eacute;dito, '
                            . 'presione continuar para volver a intentar o elija otra forma de pago',
                            'urlcontinuar' => $this->generateUrl('pago_tdcnoconciliado', array('id' => $entity[0]->getPagos()[0]->getId())),
                            'tema' => $evento->getTema()
                ));
            else{
                if($entity[0]->getPagos()[0]->getConciliado()){
//                    if($entity[0]->getPagos()[0]->getIdformapago() == 0){  

                        // Busca los rivales del atleta y los muesta en una tabla
                        //$this->mostrarInfoInscrito($em,$entity[0]);
                         $rivales=$em->getRepository('FraterSoftPiaWebBundle:Inscrito')->listarRivales(
                            $idevento,
                            $entity[0]->getIdcompetencia()->getId(),
                            $entity[0]->getIdcategoria()->getId(),
                            $entity[0]->getIdpia()->getSexo()
                        );
                        if(count($rivales) > 0){
                            //print_r(count($rivales));
                            $cuadrorivales = "<div class='infoTable'><div class='infoTableTitle'>Rivales</div>"
                                . "<div class='infoTableHeader'>" 
                                    . "<div class='infoTableCell'>ATLETA</div>"
                                    //. "<div class='infoTableCell'>CLUB</div>"
                                . "</div>";
                            foreach($rivales as $rival){
                                $cuadrorivales .= "<div class='infoTableRow'><div class='infoTableCell'>" . strtoupper($rival->getIdpia()->getApellido()) . " " . strtoupper($rival->getIdpia()->getNombre()) . "</div>";
                                //$cuadrorivales .= "<div class='infoTableCell'>" . strtoupper($rival->getIdpia()->getEquipo()) . "</div>";
                                $cuadrorivales .= "</div>";
                            }  
                            $cuadrorivales .= "</div>";
                        }
                        $dorsal="";
                        if($idevento == 7209814356 && $entity[0]->getNumero()<>""){
                            $desc_competencia = $entity[0]->getIdcategoria()->getIdCompetencia()->getDescripcion();
                            $color = "black";
                            $top_numero_dorsal = "75%";
                            if($desc_competencia == "5K") {
                                $color = "white";
                                $top_numero_dorsal = "55%";
                            }
                            else if ($desc_competencia == "10K") {
                                $color = "white";
                                $top_numero_dorsal = "56%";
                            }
                            $dorsal = "<div class='contenedor-dorsal'><div class='titulo-dorsal'>Tu Dorsal</div>"
                                . "<div class='imagen-dorsal'><img src='/piaweb/web/bundles/fratersoftpiaweb/images/numero-" . $desc_competencia . ".jpg' width='700px' border:'1px solid black'></div>"
                                . "<div class='texto-dorsal' style='color:" . $color . ";top:" . $top_numero_dorsal  . "'>" . str_pad($entity[0]->getNumero(), 4, "0", STR_PAD_LEFT) . "</div>"
                                . "</div>";
                        }                            
                        $numero = ($entity[0]->getNumero() == null)?"":"<div class='infoTableRow'><div class='infoTableHeaderVertical infoTableCell'>N&uacute;mero de Participaci&oacute;n</div><div class='infoTableCell'>" . $entity[0]->getNumero() . "</div></div>"; //
                        $genero = ($entity[0]->getIdpia()->getSexo()=='M')?'Masculino':'Femenino';
                        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                                    'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                                    'texto' => "El portador del Documento de Identidad Nro. " . $entity[0]->getIdpia()->getIdDocumento() . "<br>"
                                    . "est&aacute; oficialmente inscrito para el evento <br><b>" 
                                    . $evento->getNombre() . "</b><br><br>"
                                    . "<div class='infoTable'>"
                                    . "<div class='infoTableRow'><div class='infoTableHeaderVertical infoTableCell'>Categor&iacute;a</div>" 
                                    . "<div class='infoTableCell'>" . $entity[0]->getIdcategoria()->getDescripcion() . "</div></div>" 
                                    . "<div class='infoTableRow'><div class='infoTableHeaderVertical infoTableCell'>G&eacute;nero</div>" 
                                    . "<div class='infoTableCell'>" . $genero . "</div></div></div>" 
                                    //. $numero
                                    . $dorsal
                                    . $cuadrorivales,
                                    'tema' => $evento->getTema()
                        ));
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

            $controlParentalData = null;
            if ($controlParentalConfig) {
                $menor_edad = $competidor->getEdad() < $controlParentalConfig['edad_control'];
                if ($menor_edad) {
                    $controlParentalData = $controlParentalConfig;
                }
            } else {
                $menor_edad = false;
            }

            //Busca el numero del competidor si el evento es tipo campeonato
            //Si el evento no es de tipo campeonato, no devuelve competidor
            $compcamp = $em->getRepository('FraterSoftPiaWebBundle:CampeonatoCompetidores')
                    ->findOneBy(array(
                'idcompetidor' => $idcompetidor,
                'idcampeonato' => $evento->getIdCampeonato(),
            ));

            if($evento->getProceso()==1){
                $pagos = new Pago();
                $entity->addPago($pagos);
            }

            //Si existe el competidor en el Campeonato, le asigno su Categoria
            if ($compcamp) {
    			//Busca la cantidad de competencias por eventos, si hay mas de 1 muestra el combo        
    			$competencias = $em->getRepository('FraterSoftPiaWebBundle:Competencia')
    					->findBy(array(
    				'idevento' => $idevento,'id' => $compcamp->getIdCompetencia()
    			));                  
                
                $entity->setNumero($compcamp->getNumero());
                $equipo = $competidor->setEquipo($compcamp->getIdclub()->getNombre());
                $idcategoria = $compcamp->getIdcategoria();
                $entity->setIdpia($competidor);
                if($idgrupo==null)
                    $form = $this->createCreateForm($entity, $idevento);
                else
                    $form = $this->createCreateForm($entity, $idevento,$idcompetencia,$idgrupo);
                    
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
                            'read_only' => true,
                        ))
                        ->add('numero', 'text', array(
                            'data' => $compcamp->getNumero(),
                            'read_only' => true,
                        ))
                ;
                $form
                    ->add('numero', 'text', array(
                        'read_only' => true,
                        'required' => false,
                    ))
                    ->get('idpia')
                        ->add('equipo', 'text', array(
                            'data' => $compcamp->getIdclub()->getNombre(),
                            'read_only' => true,
                        ))
                        ->add('nombre', 'text', array(
                            'read_only' => true,
                        ))
                        ->add('apellido', 'text', array(
                            'read_only' => true,
                        ))
                        ->add('fechanacimiento', 'date', array(
                            'attr' => ['class' => 'fechaESP'],
                            'label'=>'Fecha de Nacimiento',
                            'required' => true,
                            'widget' => 'single_text',
                            'format' => 'dd/MM/yyyy',                            
                            'read_only' => true,
                        ))
                        ->add('sexo', 'text', array(
                            'read_only' => true,
                        ))
                        /*->add('emailpersonal', 'text', array(
                            'read_only' => true,
                            'required' => false,
                        ))
                        ->add('telefono', 'text', array(
                            'read_only' => true,
                        ))*/
                ;
                
                $desccategoria = $compcamp->getnombrecategoria();
                //Si no existe el competidor en el Campeonato, le calculo su Categoria                
            } else {                
                $entity->setIdpia($competidor);

                if($idgrupo==null)
                    $form = $this->createCreateForm($entity, $idevento);
                else
                    $form = $this->createCreateForm($entity, $idevento,$idcompetencia,$idgrupo);
                $this->ocultaCampos($idevento, $form);
                //seleccional la categoria que le aplica al competidor
                $categorias = $this->seleccionaCategorias($idevento, $competidor);

                //Si queda una sola competencia, las categorias se acotan a las de esa competencia
                if ($competenciaUnica) {
                    $categorias = $categorias->filter(function ($cat) use ($competenciaUnica) {
                        return $cat->getIdcompetencia() && $cat->getIdcompetencia()->getId() == $competenciaUnica->getId();
                    });
                }

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

                //Si la cantidad de competencias es mayor a 1, no se muestran las categorias
                //Si multicompetencia esta activo, la categoria se selecciona por cada competencia marcada (ver twig/JS), no aqui
                if ($multicompetenciaActivo) {
                    // no se agrega idcategoria: se resuelve por competencia en createAction.
                    // Se remueve el campo base (agregado por InscritoType) para que
                    // "form.idcategoria is defined" sea false en el twig, igual que idcompetencia.
                    if ($form->has('idcategoria')) {
                        $form->remove('idcategoria');
                    }
                } elseif(count($competencias)==1)
                    $form->add('idcategoria', 'entity', array(
                        'class' => 'FraterSoftPiaWebBundle:Categoria',
                        'label' => 'Categoria',
                        'choices' => $categorias,
                        'empty_value' => $emptyvalue,
                        'required' => true,
                    ));
                else
                    $form->add('idcategoria', 'entity', array(
                        'class' => 'FraterSoftPiaWebBundle:Categoria',
                        'label' => 'Categoria',
                        'choices' => array(),
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
            $menor_edad = false;
            if($evento->getProceso()==1){
                $pagos = new Pago();
                $entity->addPago($pagos);
            }
            
            if($idgrupo==null)
                $form = $this->createCreateForm($entity, $idevento);
            else
                $form = $this->createCreateForm($entity, $idevento,$idcompetencia,$idgrupo);
            if ($multicompetenciaActivo) {
                //Se remueve el campo base (agregado por InscritoType) para que
                //"form.idcategoria is defined" sea false en el twig, igual que idcompetencia.
                if ($form->has('idcategoria')) {
                    $form->remove('idcategoria');
                }
            } else {
                //Si multicompetencia esta activo, la categoria se selecciona por cada competencia marcada (ver twig/JS), no aqui
                $idcompetenciaUnica = $competenciaUnica ? $competenciaUnica->getId() : null;
                $form
                        ->add('idcategoria', 'entity', array(
                            'class' => 'FraterSoftPiaWebBundle:Categoria',
                            'label' => 'Categoria',
                            'query_builder' => function (EntityRepository $er) use ( $idevento, $idcompetenciaUnica ) {
                                $qb = $er->createQueryBuilder('c')
                                        ->innerJoin('c.idcompetencia', 'co')
                                        ->innerJoin('co.idevento','ev')
                                        //->addOrderBy('c.id', 'ASC')
                                        ->where('c.idcompetencia=co and co.idevento=:idevento')
                                        ->setParameter('idevento', $idevento);
                                if ($idcompetenciaUnica) {
                                    $qb->andWhere('co.id = :idcompunica')->setParameter('idcompunica', $idcompetenciaUnica);
                                }
                                return $qb;
                            },
                            'empty_value' => 'Seleccione Categoria',
                            'required' => true,
                        ))
                ;
            }

            $this->ocultaCampos($idevento, $form,array('clave'=>'iddocumento','dato'=>$iddocumento));

            
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
        
        $arraycompetencias=array();
        foreach($competencias as $competencia){
            if($competencia->getGrupo()!=null){
                array_push($arraycompetencias,$competencia->getId());
            }
        }
            
        if ($competencias) {
            if ($multicompetenciaActivo) {
                //Campo no mapeado: permite marcar varias competencias a la vez. Se procesa
                //manualmente en createAction para crear una InscritoCompetencia por cada una.
                //Se remueve el campo mapeado 'idcompetencia' (agregado por InscritoType) para
                //que no se siga mostrando el combobox original junto a los checkboxes.
                if ($form->has('idcompetencia')) {
                    $form->remove('idcompetencia');
                }
                $form
                    ->add('idcompetencias', 'entity', array(
                        'class' => 'FraterSoftPiaWebBundle:Competencia',
                        'label' => $evento->getTitulocompetencias() ?: 'Competencias',
                        'label_attr' => array('class' => 'multicompetencia-titulo'),
                        'choices' => $competencias,
                        'multiple' => true,
                        'expanded' => true,
                        'mapped' => false,
                        'required' => true,
                    ))
                ;
            } elseif (count($competencias) > 1) {
                $form
                    ->add('idcompetencia', 'entity', array(
                        'class' => 'FraterSoftPiaWebBundle:Competencia',
                        'label' => $labelCompetencia,
                        'choices' => $competencias,
                        'required' => true,
                        'empty_value' => 'Seleccione una ' . $labelCompetencia,
                    ))
                ;
            } else {
                $form
                    ->add('idcompetencia', 'entity', array(
                        'class' => 'FraterSoftPiaWebBundle:Competencia',
                        'label' => $labelCompetencia,
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
        //$form->add('precio','choice',array(
        //    'label'=>'Precios',
        //    'empty_value' => 'Seleccione El Precio'
        //));
                        
        //Muestra la etiqueta de la edad segun el tipo de calculo
        $etiquetaEdad = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')
                ->findOneBy(array('idatributo'=>6,'idevento'=>$idevento));
        if($etiquetaEdad->getEtiqueta()!=''){
            $labelEdad = $etiquetaEdad->getEtiqueta();
        }
        else if ($evento->getCriteriocalculoedad() == 1) {
            $labelEdad = 'Edad Calendario';
        } else {
            $labelEdad = 'Edad al Evento';
        }
        $form->get('idpia')->add('edad', 'text', array(
            'label' => $labelEdad,
            'read_only' => true,
        ));
        
        //Busca los atributos criterios del evento y los envia al formulario, 
        //para las busqueda en ajax de las categorias
        $atributoscriterios = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')
                ->atributosCriterios($idevento);
        if (!$atributoscriterios) {
            return new response("No hay atributos criterios para este evento");
        }

        $arrayincrementos= array();
        $arrayrecargas=array();
        $formaspago=null;
        if($evento->getProceso()==1){
            $default_moneda=null;
            $arraymonedas=$this->MonedasEvento($em,$default_moneda,$entity->getIdevento());    
            if(is_null($arraymonedas)){
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $this->generateUrl('competidor_find', array('idevento' => $evento->getId())),
                            'texto' => "No se han configurado la moneda por defecto del Organizador",
                            'tema' => $evento->getTema()
                ));                
            }

            //Agrega las formas de pago del evento
            $formasdepagoarray = array();
            $formasdepago=null;
            $formasdepago = $em->getRepository('FraterSoftPiaWebBundle:Formaspagoevento')
                    ->listarPublicos($idevento,$default_moneda);
            foreach ($formasdepago as $formadepago) {
               if ($formadepago->getIdFormapago()->getNombre()) {
                    $formasdepagoarray[$formadepago->getIdFormapago()->getId()] = $formadepago->getIdFormapago()->getNombre();
                }
            }
            
            if ($formasdepagoarray){
                //print_r(count($entity->getPagos()));
                $form
                        /*->add('info',null,array(
                            'mapped' => false,
                            'label'=>'INFORMACION DE PAGO',
                            'label_attr'=>array('class'=>'group_fields'),
                            'attr'=> array('style'=>'display:none'),
                        ))*/
                        /*->add('pagos', new PagoType(), array(
                            'label'=>false,
                        ))*/
                        ->get('pagos')[0]
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
                            'read_only' => true,
                        ))                        
                        ->add('idformapago', 'choice', array(
                            'label' => 'Forma  de Pago',
                            'choices' => $formasdepagoarray,
                            'required' => true,
                            'empty_value' => 'Seleccione Forma de Pago',
                        ))
                ;
            }
            else {
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                            'texto' => "No se han configurado las Formas De Pago para este Evento",
                           'tema' => $evento->getTema()
                ));       
            }
            
            //$this->addFormasPago($form->get('pagos'),$entity->getIdevento(),$default_moneda);  
            //$formaspago=$em->getRepository('FraterSoftPiaWebBundle:Formaspagoevento')
            //        ->findBy(array(
            //    'idevento' => $entity->getIdevento(),
            //));
            //foreach($formaspago as $formapago){
            //    $arrayincrementos[$formapago->getIdformapago()->getId()]=$formapago->getIncremento();
            //}
            if(method_exists($evento->getIdrecarga(),'getId'))
                $arrayrecargas=$this->EntitiesToArray($evento->getIdrecarga(),$this->getCampos($em,'Recarga'));
                        
            $form->add('submit', 'submit', array(
                'attr' => ['class' => 'submit'],
                'label' => 'Inscribir'
            ));
        }
        else{
            $form
                ->add('submit', 'submit', array(
                    'attr' => ['class' => 'submit'],
                    'label' => 'Pre-Inscribir'))
                ->remove('info')
                ->remove('precio')
                ->remove('idpago')
            ;            
        }
        /* 
         * Busca si existen grupos configurados y elimina los campos de pagos y precios
         * Y se determina si la configuracion de grupos es total, parcial o ninguna
         */
        if($idcompetencia!=null and $idgrupo!=null){
            $this->addBotonRegresar($form,$this->generateUrl('competidor_find', array('idevento' => $idevento)));
            $form
                ->add('submit', 'submit', array(
                    'attr' => ['class' => 'submit'],
                    'label' => 'Agregar'
                )
            );
        }
        else
            $this->addBotonRegresar($form,$this->generateUrl('competidor_find', array('idevento' => $idevento)));

        $message_credito = '';
        $creditoArray = null;
        $credito = $em->getRepository('FraterSoftPiaWebBundle:Creditos')
                ->findOneBy(array('idevento'=>$idevento,'cedula'=>$iddocumento,'disponible'=>true));
        if($credito){
            $moneda_credito = $em->getRepository('FraterSoftPiaWebBundle:Moneda')->find($credito->getIdMoneda());
            if($moneda_credito==null){
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                            'texto' => "No se han configurado la moneda del credito",
                            'tema' => $evento->getTema()
                ));                
            }
            //Si la moneda del credito es diferente a la moneda del evento, se busca la tasa de cambio del dia para mostrar el mensaje del credito al competidor
            $monto_credito = $credito->getMonto();
            $texto_moneda_credito = $moneda_credito->getCodigolocal();
            if($credito->getIdmoneda()!=$evento->getIdOrganizador()->getIdmoneda()->getId()){
                $tasadia = $em->getRepository('FraterSoftPiaWebBundle:HistoricoTasas')->findOneBy(array('idmoneda'=>$credito->getIdmoneda(),'publicadoel'=>new \DateTime("now")));
                if($tasadia==null){
                    return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                                'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                                'texto' => "No se han configurado la tasa de cambio del credito",
                                'tema' => $evento->getTema()
                    ));
                }
                $monto_credito = $credito->getMonto() * $tasadia->getMonto();
                $texto_moneda_credito = $evento->getIdOrganizador()->getIdmoneda()->getCodigolocal();
            }
            $message_credito = 'USTED POSEE UN CREDITO DE ' . $texto_moneda_credito . ' ' . number_format($monto_credito, 2, ',', '')   
                        . ' PARA ESTE EVENTO. EL MISMO YA FUE DESCONTADO DEL PRECIO DE LA INSCRIPCION';
            $creditoArray = [
                'cedula' => $credito->getCedula(),
                'monto' => $monto_credito,
                'disponible' => $credito->getDisponible(),
                'idevento' => $credito->getIdevento(),
                'idmoneda' => $credito->getIdmoneda(),
            ];
        }
        $form
            ->get('pagos')[0]
                ->add('message',null,array(
                    'mapped' => false,
                    'label'=>$message_credito,
                    'attr'=> array('style'=>'display:none'),
                ))
        ;

                
        return $this->render('FraterSoftPiaWebBundle:Inscrito:new.html.twig', array(
            'entity' => $entity,
            'atributos' => $atributoscriterios,
            'form' => $form->createView(),
            'incremento' => $arrayincrementos,
            'recargas'=>$arrayrecargas,
            'competenciasgrupo'=>$arraycompetencias,
            'idcompetencia'=>$idcompetencia,
            'idgrupo'=>$idgrupo,
            'integrante'=>$cantidad_integrantes,
            'formasdepago'=>$formaspago==null?null:$this->EntitiesToArray($formasdepago,$this->getCampos($em,'Formaspagoevento')),
            'creditox'=>$creditoArray,
            'menor_edad' => $menor_edad,
            'controlParentalData' => $controlParentalData,
            'controlParentalConfig' => $controlParentalConfig,
            'multicompetenciaActivo' => $multicompetenciaActivo,
            'competencias' => $competencias
        ));
    }

    /**
     * Finds and displays a Inscrito entity.
     *
     */
    public function showAction($id) {

        $em = $this->getDoctrine()->getManager();

//        $entity = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->findIdArray($id);
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->find($id);
        
        //Se convierte la entidad en arreglo json para poder accesar a traves del nombre de la propiedad
//        $encoders = array(new XmlEncoder(), new JsonEncoder());
//        $normalizers = array(new GetSetMethodNormalizer());  
//        $serializer = new Serializer($normalizers, $encoders);  
//        $jsonContent = $serializer->serialize($entity[0],'json');    
//        //Se transforma a un array php porque se json crea un array de objectos
//        $obj_php = json_decode($jsonContent);
//        //Se transforma en array basico, porque el decode crea un array de objetos 
//        //stdClass, y es necesacio un array con acceso a traves de los keys del array
//        $x=$this->objectToArray($obj_php);
//        
//        //Busca los atributos del evento y los envia al formulario
//        $atributos = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')
//                ->atributosEvento($obj_php->idevento->id);
//        if (!$atributos) {
//            return new response("No hay atributos configurados para este evento");
//        }    
        
//        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($obj_php->idevento->id);

        $parametros=null;
        if(is_null($entity->getPagos()))
            $parametros=$entity->getPagos()[0]->getidformapago()->getparametros();
        return $this->render('FraterSoftPiaWebBundle:Inscrito:show.html.twig', array(
                    'entity' => $entity,
                    'parametros'=>json_decode($parametros),
//                    'evento' => $evento,
//                    'atributos' => $atributos,
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
    
    public function confirmaciongrupoAction($idevento,$idcompetencia,$idgrupo) {
        $em = $this->getDoctrine()->getManager();

        $integrantes = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->inscritosGrupo($idevento,$idgrupo);
        
//        $integrantes=$em->getRepository('FraterSoftPiaWebBundle:Inscrito')->findBy(array(
//            'idevento' => $idevento,
//            'idgrupo'=>$idgrupo,
//        ));                        
        return $this->render('FraterSoftPiaWebBundle:Inscrito:confirmaciongrupo.html.twig', array(
            'integrantes' => $integrantes,
            'idevento' => $idevento,
            'idcompetencia' => $idcompetencia,
            'idgrupo' => $idgrupo,
        ));
    }

    /**
     * Displays a form to edit an existing Inscrito entity.
     *
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->find($id);

        $idevento = $entity->getIdevento()->getId();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inscrito entity.');
        }

        // var_dump($entity->getPagos()[0]);

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
        //Busca la cantidad de competencias por eventos, si hay mas de 1 muestra el combo
        //sino, muestra el texto de la competencia
        $competencias = $em->getRepository('FraterSoftPiaWebBundle:Competencia')
                ->findBy(array('idevento' => $entity->getIdevento()->getId(),
        ));

        //Si el evento tiene activo multicompetencia y hay mas de 1 competencia configurada,
        //la modalidad se muestra como checkboxes (seleccion multiple) y la categoria se
        //selecciona por cada competencia marcada (ver twig/JS), igual que en el alta.
        $multicompetenciaActivo = $entity->getIdevento()->getMulticompetencia() && count($competencias) > 1;

        $labelCompetencia = $entity->getIdevento()->getTitulocompetencias() ?: 'Competencia';
        $competenciaUnica = (count($competencias) === 1) ? $competencias[0] : null;
        //Si hay una sola competencia, las categorias se acotan a las de esa competencia
        if ($competenciaUnica) {
            $categorias = $categorias->filter(function ($cat) use ($competenciaUnica) {
                return $cat->getIdcompetencia() && $cat->getIdcompetencia()->getId() == $competenciaUnica->getId();
            });
            if ($categorias->count() == 0) {
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $this->generateUrl('competidor_find', array('idevento' => $entity->getIdevento()->getId())),
                            'texto' => 'No existe una Categoria aplicable a este participante',
                            'tema' => $entity->getIdevento()->getTema()
                ));
            }
        }

        //Mapa idcompetencia => idcategoria de la seleccion guardada (para precargar el twig)
        $categoriasActuales = array();
        foreach ($entity->getCompetencias() as $inscritoCompetencia) {
            $categoriasActuales[$inscritoCompetencia->getIdcompetencia()->getId()] =
                $inscritoCompetencia->getIdcategoria();
        }

        if ($multicompetenciaActivo) {
            //Se remueven los campos base mapeados (agregados por InscritoType) para que
            //"edit_form.idcategoria is defined" / "edit_form.idcompetencia is defined" sean
            //false en el twig, y se agrega el campo no mapeado de checkboxes con la
            //seleccion actual precargada.
            if ($editForm->has('idcompetencia')) {
                $editForm->remove('idcompetencia');
            }
            if ($editForm->has('idcategoria')) {
                $editForm->remove('idcategoria');
            }
            $competenciasActuales = array();
            foreach ($entity->getCompetencias() as $inscritoCompetencia) {
                $competenciasActuales[] = $inscritoCompetencia->getIdcompetencia();
            }
            $editForm->add('idcompetencias', 'entity', array(
                'class' => 'FraterSoftPiaWebBundle:Competencia',
                'label' => $entity->getIdevento()->getTitulocompetencias() ?: 'Competencias',
                'choices' => $competencias,
                'multiple' => true,
                'expanded' => true,
                'mapped' => false,
                'required' => true,
                'data' => $competenciasActuales,
            ));
        } else {
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

            if ($competencias) {
                if (count($competencias) > 1) {
                    $editForm
                            ->add('idcompetencia', 'entity', array(
                                'class' => 'FraterSoftPiaWebBundle:Competencia',
                                'label' => $labelCompetencia,
                                'choices' => $competencias,
                                'required' => true,
                                'empty_value' => 'Seleccione una ' . $labelCompetencia,
                            ))
                    ;
                } else {
                    $editForm
                            ->add('idcompetencia', 'entity', array(
                                'class' => 'FraterSoftPiaWebBundle:Competencia',
                                'label' => $labelCompetencia,
                                'choices' => $competencias,
                            ))
                    ;
                }
            }
        }

        $default_moneda=null;
        $arraymonedas=$this->MonedasEvento($em,$default_moneda,$entity->getIdevento());        
        
        /**************** Agrega las formas de pago del evento ****************/
        // $formasdepagoarray = array();
        // $formasdepago = $em->getRepository('FraterSoftPiaWebBundle:Formaspagoevento')
        //         ->listarPublicos($entity->getIdevento()->getId(),$default_moneda);
        // foreach ($formasdepago as $formadepago) {
        //     if ($formadepago->getIdformapago()->getNombre()) {
        //         $formasdepagoarray[$formadepago->getId()] = $formadepago->getIdformapago()->getNombre();
        //     }
        // }

        // $editForm->add('numero');

        $this->addBotonRegresar($editForm,$this->get('session')->get('urlreturn'));
        
        $this->ocultaCampos($entity->getIdevento()->getId(), $editForm);
        
        //Busca los atributos criterios del evento y los envia al formulario, 
        //para las busqueda en ajax de las categorias
        $atributoscriterios = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')
                ->atributosCriterios($entity->getIdevento()->getId());
        if (!$atributoscriterios) {
            return new response("No hay atributos criterios para este evento");
        }        

        $default_moneda=null;
        $arraymonedas=$this->MonedasEvento($em,$default_moneda,$entity->getIdevento());    
        if(is_null($arraymonedas)){
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('competidor_find', array('idevento' => $evento->getId())),
                        'texto' => "No se han configurado la moneda por defecto del Organizador",
                        'tema' => $evento->getTema()
            ));                
        }

        //Agrega las formas de pago del evento
        $formasdepagoarray = array();
        $formasdepago=null;
        $formasdepago = $em->getRepository('FraterSoftPiaWebBundle:Formaspagoevento')
                ->listarPublicos($idevento,$default_moneda);
        foreach ($formasdepago as $formadepago) {
            if ($formadepago->getIdFormapago()->getNombre()) {
                $formasdepagoarray[$formadepago->getIdFormapago()->getId()] = $formadepago->getIdFormapago()->getNombre();
            }
        }
        // var_dump();
        if ($formasdepagoarray){
                $editForm
                        ->get('pagos')[0]
                        ->add('idmoneda', 'choice', array(
                            'choices' => $arraymonedas,
                            'data'=>'idmoneda',
                            'label' => 'Moneda',
                            'expanded' => true,
                            'data'=>$entity->getPagos()[0]->getIdmoneda()->getId(),
                        ))                     
                        ->add('precio','text',array(
                            'label'=>'Precio',
                            'read_only' => true,
                        ))  
                        ->add('message',null,array(
                            'mapped' => false,
                            'label'=>'',
                            'attr'=> array('style'=>'display:none'),
                        ))
                        //->add('idbanco','text',array(
                        //    'required' => false,
                        //))                                               
                        // ->add('idformapago', 'choice', array(
                        //     'label' => 'Forma  de Pago',
                        //     'choices' => $formasdepagoarray,
                        //     'required' => true,
                        //     'empty_value' => 'Seleccione Forma de Pago',
                        // ))
                ;
            }


        return $this->render('FraterSoftPiaWebBundle:Inscrito:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'atributos' => $atributoscriterios,
                    'multicompetenciaActivo' => $multicompetenciaActivo,
                    'competencias' => $competencias,
                    'categoriasActuales' => $categoriasActuales,
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
        
        $form
                ->add('fechahora', 'datetime', array(
                    'widget' => 'single_text',
                    'data' => new \DateTime('now'),
                    'attr' => array('style' => 'display:none'),
                    'label' => false,
                ))
        ;

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

        //Competencias que el inscrito ya tiene ANTES de editar (no cuentan como nuevas para
        //la validacion de cupo, ya que su registro actual ya ocupa lugar).
        $competenciasPreviasIds = array();
        if ($entity->getIdcompetencia()) {
            $competenciasPreviasIds[] = $entity->getIdcompetencia()->getId();
        }
        foreach ($entity->getCompetencias() as $icPrevia) {
            $competenciasPreviasIds[] = $icPrevia->getIdcompetencia()->getId();
        }

        $editForm = $this->createEditForm($entity);

        //Si el evento tiene multicompetencia activo (y >1 competencia), los campos mapeados
        //'idcompetencia'/'idcategoria' se reemplazan por el campo no mapeado 'idcompetencias'
        //(checkboxes) ANTES de procesar el submit, igual que en createAction.
        $competencias = $em->getRepository('FraterSoftPiaWebBundle:Competencia')
                ->findBy(array('idevento' => $entity->getIdevento()->getId()));
        $multicompetenciaActivo = $entity->getIdevento()->getMulticompetencia() && count($competencias) > 1;
        if ($multicompetenciaActivo) {
            if ($editForm->has('idcompetencia')) {
                $editForm->remove('idcompetencia');
            }
            if ($editForm->has('idcategoria')) {
                $editForm->remove('idcategoria');
            }
            $editForm->add('idcompetencias', 'entity', array(
                'class' => 'FraterSoftPiaWebBundle:Competencia',
                'choices' => $competencias,
                'multiple' => true,
                'expanded' => true,
                'mapped' => false,
                'required' => true,
            ));
        }

        $editForm->handleRequest($request);

        //En multicompetencia debe marcarse al menos una modalidad
        if ($multicompetenciaActivo) {
            $seleccion = $editForm->get('idcompetencias')->getData();
            if ($seleccion === null || count($seleccion) === 0) {
                $this->get('session')->getFlashBag()->add('error',
                    'Debe seleccionar al menos una ' . ($entity->getIdevento()->getTitulocompetencias() ?: 'Competencia'));
                return $this->redirect($this->generateUrl('inscrito_edit', array('id' => $id)));
            }
        }

        //Valida cupos: solo se rechaza si se AGREGA una competencia (que antes no tenia) y
        //esa competencia ya llego a su cupo maximo.
        if ($multicompetenciaActivo) {
            $seleccionCupo = $editForm->get('idcompetencias')->getData();
            $seleccionCupo = ($seleccionCupo !== null) ? $seleccionCupo->toArray() : array();
        } else {
            $seleccionCupo = $entity->getIdcompetencia() ? array($entity->getIdcompetencia()) : array();
        }
        $nuevasCompetencias = array();
        foreach ($seleccionCupo as $comp) {
            if ($comp && !in_array($comp->getId(), $competenciasPreviasIds)) {
                $nuevasCompetencias[] = $comp;
            }
        }
        $llenasEdit = $this->competenciasSinCupo($nuevasCompetencias, $entity->getIdevento()->getId(), $em);
        if (!empty($llenasEdit)) {
            $this->get('session')->getFlashBag()->add('error',
                'Ya no hay cupos disponibles en: ' . implode(', ', $llenasEdit));
            return $this->redirect($this->generateUrl('inscrito_edit', array('id' => $id)));
        }

        if ($editForm->isValid()) {

            if($editForm->getdata()->getIdevento()->getProceso()==2)
                $editForm->getdata()->setIdpago(null);

            if ($multicompetenciaActivo) {
                //Reconstruye el detalle InscritoCompetencia y recalcula el total; actualiza
                //tambien el precio del Pago asociado para mantener la consistencia.
                $total = $this->reconstruirInscritoCompetencias(
                    $entity,
                    $editForm->get('idcompetencias')->getData(),
                    $request,
                    $em
                );
                if (count($entity->getPagos()) > 0) {
                    $entity->getPagos()[0]->setPrecio($total);
                }
            }

            $em->flush();

            //Notifica al organizador si alguna competencia llego a su cupo maximo
            if ($multicompetenciaActivo) {
                $competenciasInscritas = $editForm->get('idcompetencias')->getData();
                $competenciasInscritas = ($competenciasInscritas !== null) ? $competenciasInscritas->toArray() : array();
            } else {
                $competenciasInscritas = $entity->getIdcompetencia() ? array($entity->getIdcompetencia()) : array();
            }
            $this->notificarCupoMaximoCompetencias($entity, $competenciasInscritas, $em);

            $this->get('session')->getFlashBag()->add('success', 'Guardado satisfactoriamente');
            return $this->redirect($this->generateUrl('inscrito_edit', array('id' => $id)));
        }

        //En fallo de validacion se vuelve a la pantalla de edicion (editAction rearma todas
        //las variables que el twig necesita).
        $this->get('session')->getFlashBag()->add('error', 'No se pudo guardar, verifique los datos');
        return $this->redirect($this->generateUrl('inscrito_edit', array('id' => $id)));
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
            //$categoriasselect = new ArrayCollection();
            $categoriasselect = array();

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
                    switch($regla->getTipo()){
                        case '[]':
                            if ($regla->getValor1() <= $criterios[strtolower($regla->getAtributo())] &&
                                    $regla->getValor2() >= $criterios[strtolower($regla->getAtributo())])
                                $parametrok = true;
                            break;
                        case '=':
                            if ($regla->getValor1() == $criterios[strtolower($regla->getAtributo())])
                                $parametrok = true;
                            break;
                    }
                    if (!$parametrok)
                        break;
                }
                if ($parametrok) {
                    //$categoriasselect->add($categorias[$i]);
                    array_push($categoriasselect,$categorias[$i]);
                    $parametrok = false;
                }
            }
            $jsonContent = $serializer->serialize($categoriasselect, 'json');
            return new response($jsonContent);
        } else {
            return null;
        }
    }  

    public function anularAction($id,$idgrupo=null) {

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
        $emailfrom="";
        if(!is_null($entity->getIdevento()->getIdorganizador()->getEmail()) && filter_var($entity->getIdevento()->getIdorganizador()->getEmail(), FILTER_VALIDATE_EMAIL))
            $emailfrom = $entity->getIdevento()->getIdorganizador()->getEmail();
            // $emailfrom = "inscripciones@sistemapia.com";
        else
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('competidor_find', array('idevento' => $entity->getIdevento()->getId())),
                        'texto' => 'El correo del Organizador no posee en formato adecuado',
                        'tema' => $entity->getIdevento()->getTema()
            ));
            
        $entity->setStatus(0);
        $em->persist($entity);
        $em->flush();

        //Se envia el correo de anulación
        $mailer = $this->get('app.mail_controller');
        $mailer->enviar(
                $emailfrom,
                "Pre-Inscripcion Anulada " . $entity->getIdevento()->getNombre(), 
                $emails,
                $this->renderView('FraterSoftPiaWebBundle:Inscrito:anulado.html.twig', array(
                    'inscrito' => $entity,
                    'idgrupo' => $idgrupo
                ))
        );
        if($idgrupo){
            $url=$this->redirect($this->generateUrl('competidor_find', array('idevento' => $entity->getIdevento()->getid())));
            $texto='Integrante anulado satisfactoriamente del grupo';
        }
        else{
            $request = $this->getRequest();
            $referer = $request->headers->get('referer');  
            $url=$referer;
            $texto='Inscripcion Nro ' . $entity->getSecuencia() . ' Anulada';
        }
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => $url,
                    'texto' => $texto,
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
        $idmoneda = $this->get('request')->query->get('idmoneda');

        $precio = 0;
        $precio = $this->buscarPrecio($idevento, $idcompetencia, $idcategoria,$idmoneda);

        if ($precio) {
            $precioselect->add($precio);
            $jsonContent = $serializer->serialize($precioselect, 'json');
            return new response($jsonContent);
        }
        return new response(0);
    }

    /**
     * Devuelve el precio configurado a nivel de EVENTO para la moneda dada, como
     * {"precio": <numero>} (0 si no hay). Usado por el formulario multicompetencia.
     */
    public function precioeventoAjaxAction() {
        $idevento = $this->get('request')->query->get('idevento');
        $idmoneda = $this->get('request')->query->get('idmoneda');
        $monto = 0;
        if ($idevento && $idmoneda) {
            $monto = $this->buscarPrecioEvento($idevento, $idmoneda);
        }
        return new Response(json_encode(array('precio' => $monto)));
    }

    /**
     * Devuelve el precio ESPECIFICO de una competencia/categoria (sin caer al precio de
     * evento) para la moneda dada, como {"precio": <numero>} (0 si no tiene precio propio).
     * Usado por el formulario multicompetencia para saber si cada modalidad tiene tarifa
     * propia o debe usar la tarifa base del evento.
     */
    public function preciocompetenciaAjaxAction() {
        $idcompetencia = $this->get('request')->query->get('idcompetencia');
        $idcategoria = $this->get('request')->query->get('idcategoria');
        $idmoneda = $this->get('request')->query->get('idmoneda');
        $monto = 0;
        if ($idcompetencia && $idmoneda) {
            $monto = $this->buscarPrecioCompetenciaCategoria($idcompetencia, $idcategoria, $idmoneda);
        }
        return new Response(json_encode(array('precio' => $monto)));
    }

    /**
     * Finds and displays a Inscrito entity.
     *
     */
    public function estadisticasAction($idevento) {
        $em = $this->getDoctrine()->getManager();
        $email = $this->getUser()->getEmail();
        
        $nivel_seguridad = $this->getNivelSeguridad($em,$email,$idevento);
        
        //Busca los atributos del evento y los envia al formulario
        $atributos = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')
                ->atributosEstadistica($idevento);

        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($idevento);
        
        $total=0;
        return $this->render('FraterSoftPiaWebBundle:Inscrito:estadisticas.html.twig', array(
                    'idevento' => $idevento,
                    'email' => $email,            
                    'total' => $total,
                    'atributos'=>$atributos,
                    'nombreevento'=>$evento->getNombre(),
                    'nivel_seguridad' => $nivel_seguridad
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
            
        $emailfrom="";
        if(!is_null($entity->getIdevento()->getIdorganizador()->getEmail()) && filter_var($entity->getIdevento()->getIdorganizador()->getEmail(), FILTER_VALIDATE_EMAIL))
            $emailfrom = $entity->getIdevento()->getIdorganizador()->getEmail();
            // $emailfrom = "inscripciones@sistemapia.com";
        else
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('competidor_find', array('idevento' => $form->get('idevento')->getData()->getId())),
                        'texto' => 'El correo del Organizador no posee en formato adecuado',
                        'tema' => $entity->getIdevento()->getTema()
            ));
            
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
                    $mailer = $this->get('app.mail_controller');
                    $mailer->enviarPago(
                            $emailfrom,                            
                            "Pago no Conciliado en " . $entity->getIdevento()->getNombre(), 
                            $emails,
                            $this->renderView('FraterSoftPiaWebBundle:Inscrito:anulado.html.twig', 
                                    array('inscrito' => $row))
                    );                    
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
    
//   public function notificarpreinscritostodosAction($idevento) {
//        $num_notifiaciones = 0;
//        $em = $this->getDoctrine()->getManager();
//
//        $inscritos = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->listarNoConciliadas($idevento); 
//        if (!$inscritos) {
//            throw $this->createNotFoundException('Unable to find Inscrito entity.');
//        }
//        
//        $num_notifiaciones = $this->EnviarConfirmacion($inscritos,$em);
//        
//        if($num_notifiaciones!=0){
//            $request = $this->getRequest();
//            $referer = $request->headers->get('referer');     
//            return $this->render('FraterSoftPiaWebBundle:Default:progressbar.html.twig', array(
//                        'url' => $referer,
//                        'texto' => 'Se han enviado satisfactoriamente ' . $num_notifiaciones . ' notificaciones de confirmacion de inscripcion',
//            ));
//        }
//        else{
//            $request = $this->getRequest();
//            $referer = $request->headers->get('referer');     
//            return $this->render('FraterSoftPiaWebBundle:Default:progressbar.html.twig', array(
//                        'url' => $referer,
//                        'texto' => 'No exiten participantes por notificar',
//            ));
//        }
//
//    }      

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
        
    public function notificarpreinscripcionloteAction($data_json,$idevento){
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
        if(!$inscritos)
            throw $this->createNotFoundException('No se ubico id de inscripcion');
        
        $num_notifiaciones = $this->EnviarConfirmacionPreinscritos($inscritos,$em);
        
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

        //Busca el evento 
        $evento = new Evento();
        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')
                ->findOneBy(array('id' => $idevento));
        $arraymonedas=$this->MonedasEvento($em,$default_moneda,$evento);
        
        $this->addBotonRegresar($form,$this->generateUrl('inscrito_lista_inscritos', array('idevento' => $idevento,'email'=>'admin')));
        $competencias=$em->getRepository("FraterSoftPiaWebBundle:Competencia")->findBy(array('idevento'=>$idevento));
        $categorias=$em->getRepository("FraterSoftPiaWebBundle:Categoria")->listaCategorias($idevento);
        $estados=$em->getRepository("FraterSoftPiaWebBundle:Estado")->findAll();
        /*foreach($estados as $estado){
            print_r($estado->nombre);
        }*/
        $paises=$em->getRepository("FraterSoftPiaWebBundle:Pais")->findAll();
        $formaspago=$em->getRepository("FraterSoftPiaWebBundle:Formaspagoevento")->listarPublicos($idevento,$default_moneda);
        /*foreach($formaspago as $formapago){
            print_r($formapago['nombre']);print_r(',');
        }*/
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
    //            print_r($csvcompetidor);
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
                    //if($competidorfind->getEmailpersonal()==""){ 
                        //$this->actualizaEntity($em, $competidor, $competidorfind);
                        //$em->persist($competidorfind);
                        //$count_competidores_a++;
                    //}
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
                    
                    //Ingresamos la inscripcion
                    $inscrito= new Inscrito();
                    $inscrito->setIdevento($evento);
                    $inscrito->setIdpia($idpia);
                    //$inscrito->setIdCompetencia($categorias[0]->getIdcompetencia());
                    foreach($categorias as $categoria){
                        if($categoria->getDescripcion()==$csvcompetidor['categoria']){
                            $inscrito->setIdCategoria($categoria);
                            $inscrito->setIdCompetencia($categoria->getIdcompetencia());
                        }
                    }
                    $inscrito->setFechahora(new \DateTime('now'));
                    $inscrito->setPunto('PIAK');
                    $inscrito->setEquipo($csvcompetidor['equipo']);
                    $inscrito->setPrecio($csvcompetidor['precio']);
                    $inscrito->setStatus(1);
                    $inscrito->setNumero($csvcompetidor['numero']==""?null:$csvcompetidor['numero']);
                    //$inscrito->setIdpago($pago);
                    $maxsec = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->maximaSecuencia($idevento);
                    $inscrito->setSecuencia($maxsec ? $maxsec + 1 : 1);
                    
                    $em->persist($inscrito);
                    
                    //Ingresamos el pago                    
                    $pago = new Pago();
                    $pago->setFechahora(new \DateTime('now'));
                    $pago->setMonto($csvcompetidor['precio']);
                    $pago->setReferencia($csvcompetidor['referencia']);
                    $pago->setBanco($csvcompetidor['banco']);
                    if($csvcompetidor['conciliadoel']=="")
                        $pago->setConciliadoEl(new \DateTime('now'));
                    else
                        $pago->setConciliadoEl(new \DateTime($csvcompetidor['conciliadoel']));
                    $formapago=$em->getRepository("FraterSoftPiaWebBundle:Formaspagoevento")->buscaPorNombre($idevento,$csvcompetidor['formapago']);
                    $pago->setTipo($formapago[0]['idformapago']['id']);
                    $pago->setConciliado(true);
                    $pago->setIdInscrito($inscrito);

                    $em->persist($pago);
                    
                    $count_inscritos_i++;
                }
                else{
                    $count_inscritos_existentes++;
                    $inscrito->setNumero($csvcompetidor['numero']==""?null:$csvcompetidor['numero']);
                    $em->persist($inscrito); //actualiza el numero por defecto
                    $pago=$em->getRepository("FraterSoftPiaWebBundle:Pago")->findOneBy(array(
                        'idinscrito'=>$inscrito->getId()
                    ));                    
                    if($csvcompetidor['conciliadoel']=="")
                        $pago->setConciliadoEl(new \DateTime('now'));
                    else
                        $pago->setConciliadoEl(new \DateTime($csvcompetidor['conciliadoel']));
                    $em->persist($pago);
                }
                $em->flush();                  
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
    
    public function listaauditoriaAction($idevento) {
        $em = $this->getDoctrine()->getManager();
        $email = $this->getUser()->getEmail();
        
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
                    'nombreevento'=>$atributos[0]->getIdEvento()->getNombre(),
                    'titulocompetencias'=>$atributos[0]->getIdEvento()->getTitulocompetencias()
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