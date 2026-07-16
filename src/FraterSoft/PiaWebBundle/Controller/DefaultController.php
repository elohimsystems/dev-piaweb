<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Config\DoctrineConfigManager;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use AppBundle\Config\UserConfigManager;
use AppBundle\Config\CustomConfigManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;

use FraterSoft\PiaWebBundle\Entity\Inscrito;
use FraterSoft\PiaWebBundle\Entity\Competidor;
use FraterSoft\PiaWebBundle\Entity\Organizador;
use FraterSoft\PiaWebBundle\Entity\OrganizadorFormaspago;
use FraterSoft\PiaWebBundle\Form\OrganizadorType;

class DefaultController extends commonPIAClass 
{
   
    public function indexAction($name)
    {
        return $this->render('FraterSoftPiaWebBundle:Default:index.html.twig', array('name' => $name));
    }

    public function landingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $eventos = $em->getRepository('FraterSoftPiaWebBundle:Evento')->findProximosActivos();
        return $this->render('FraterSoftPiaWebBundle:Default:landing.html.twig', array('eventos' => $eventos));
    }

    public function sitemapAction()
    {
        $em = $this->getDoctrine()->getManager();
        $eventos = $em->getRepository('FraterSoftPiaWebBundle:Evento')->findBy(array('activo' => true), array('fecha' => 'DESC'));

        $response = $this->render('FraterSoftPiaWebBundle:Default:sitemap.xml.twig', array(
            'eventos' => $eventos,
            'site_base_url' => $this->container->getParameter('site_base_url'),
        ));
        $response->headers->set('Content-Type', 'application/xml');

        return $response;
    }

    public function eventosAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN') && !$user->getIdorganizador()) {
            return $this->redirect($this->generateUrl('fos_user_registration_confirmed'));
        }

        $request = $this->container->get('request');
        $routeURL = $request->getRequestUri();
        $this->get('session')->set('urllistaeventos', $routeURL);

        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $entities = $em->getRepository('FraterSoftPiaWebBundle:Evento')->enproceso('admin');
            $ejecutados = $em->getRepository('FraterSoftPiaWebBundle:Evento')->ejecutados('admin');
        } else {
            $organizador = $user->getIdorganizador();
            $entities = $em->getRepository('FraterSoftPiaWebBundle:Evento')->enprocesoPorOrganizador($organizador->getId());
            $ejecutados = $em->getRepository('FraterSoftPiaWebBundle:Evento')->ejecutadosPorOrganizador($organizador->getId());
        }

        $organizadorNombre = $this->get('security.context')->isGranted('ROLE_ADMIN') ? 'Admin' : $user->getIdorganizador()->getNombre();

        return $this->render('FraterSoftPiaWebBundle:Evento:listaporemail.html.twig', array(
            'entities' => $entities,
            'ejecutados' => $ejecutados,
            'email' => $this->get('security.context')->isGranted('ROLE_ADMIN') ? 'admin' : $user->getIdorganizador()->getEmail(),
            'nivel_seguridad' => 1,
            'organizador_nombre' => $organizadorNombre,
        ));
    }

    public function perfilAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $em = $this->getDoctrine()->getManager();
        $organizador = $user->getIdorganizador();

        if (!$organizador) {
            $organizador = new Organizador();
            $organizador->setEmail($user->getEmail());
        }

        $userForm = $this->get('fos_user.profile.form');
        $userForm->setData($user);

        $changePasswordForm = $this->get('fos_user.change_password.form');

        $organizadorForm = $this->createForm(new OrganizadorType(), $organizador, array(
            'method' => 'POST',
        ));
        $organizadorForm->add('submit', 'submit', array('label' => 'Guardar'));

        if ($request->isMethod('POST')) {
            $submitted = false;
            $tab = null;

            if ($request->request->has($userForm->getName())) {
                $userForm->bind($request);
                if ($userForm->isValid()) {
                    $this->get('fos_user.user_manager')->updateUser($user);
                    $this->get('session')->getFlashBag()->add('success', 'Perfil de usuario actualizado.');
                    $submitted = true;
                    $tab = 'tabs-1';
                }
            } elseif ($request->request->has($organizadorForm->getName())) {
                $organizadorForm->handleRequest($request);
                if ($organizadorForm->isValid()) {
                    $file = $organizadorForm->get('logo')->getData();
                    if ($file) {
                        $dir = $this->get('kernel')->getRootDir() . '/../web/bundles/fratersoftpiaweb/fine-uploader/files';
                        $filename = uniqid() . '.' . $file->guessExtension();
                        $file->move($dir, $filename);
                        $organizador->setLogo($filename);
                    }
                    $em->persist($organizador);
                    $em->flush();
                    if (!$user->getIdorganizador()) {
                        $user->setIdorganizador($organizador);
                        $em->persist($user);
                        $em->flush();
                    }
                    $this->get('session')->getFlashBag()->add('success', 'Datos del organizador actualizados.');
                    $submitted = true;
                    $tab = 'tabs-2';
                }
            } elseif ($request->request->has($changePasswordForm->getName())) {
                $handler = $this->get('fos_user.change_password.form.handler');
                if ($handler->process($user)) {
                    $this->get('session')->getFlashBag()->add('success', 'Contraseña actualizada correctamente.');
                    $submitted = true;
                    $tab = 'tabs-4';
                }
            } elseif ($request->request->has('formaspago_add')) {
                $idformapago = $request->request->get('idformapago');
                $observacion = $request->request->get('observacion');
                $qrFile = $request->files->get('qr');
                if ($organizador && $organizador->getId() && $idformapago) {
                    $formapago = $em->getRepository('FraterSoftPiaWebBundle:Formaspago')->find($idformapago);
                    if ($formapago) {
                        $qrFilename = null;
                        if ($qrFile) {
                            $dir = $this->get('kernel')->getRootDir() . '/../web/bundles/fratersoftpiaweb/fine-uploader/files';
                            $qrFilename = uniqid() . '.' . $qrFile->guessExtension();
                            $qrFile->move($dir, $qrFilename);
                        }

                        $existe = $em->getRepository('FraterSoftPiaWebBundle:OrganizadorFormaspago')
                            ->findOneBy(array('idorganizador' => $organizador, 'idformapago' => $formapago));
                        if (!$existe) {
                            $of = new OrganizadorFormaspago();
                            $of->setIdorganizador($organizador);
                            $of->setIdformapago($formapago);
                            $of->setObservacion($observacion);
                            if ($qrFilename) {
                                $of->setQr($qrFilename);
                            }
                            $em->persist($of);
                            $em->flush();
                            $this->get('session')->getFlashBag()->add('success', 'Forma de pago asignada al organizador.');
                        } else {
                            $existe->setObservacion($observacion);
                            if ($qrFilename) {
                                $existe->setQr($qrFilename);
                            }
                            $em->flush();
                            $this->get('session')->getFlashBag()->add('success', 'Observacion actualizada.');
                        }
                    }
                }
                $submitted = true;
                $tab = 'tabs-3';
            }

            if ($submitted) {
                $url = $this->generateUrl('frater_soft_pia_web_perfil');
                if ($tab) {
                    $url .= '#' . $tab;
                }
                return $this->redirect($url);
            }
        }

        $formaspagos = $em->getRepository('FraterSoftPiaWebBundle:Formaspago')->findBy(array('status' => 1));
        $asignaciones = array();
        if ($organizador && $organizador->getId()) {
            $asignaciones = $em->getRepository('FraterSoftPiaWebBundle:OrganizadorFormaspago')
                ->findBy(array('idorganizador' => $organizador));
        }

        return $this->render('FraterSoftPiaWebBundle:Default:perfil.html.twig', array(
            'user_form' => $userForm->createView(),
            'change_password_form' => $changePasswordForm->createView(),
            'organizador_form' => $organizadorForm->createView(),
            'user' => $user,
            'organizador' => $organizador,
            'formaspagos' => $formaspagos,
            'asignaciones' => $asignaciones,
        ));
    }
    
    //Oculta los campos que no estan configurados en la base de datos    
//    private function renombraLabels($idevento, $form) {
//        $em = $this->getDoctrine()->getManager();
//        $eventoatributos = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')->atributosEvento($idevento);
//        if ($eventoatributos) {
//            $atributo = null;
//            foreach ($form->get('idpia')->all() as $child) {
//                $existe = false;            
//                foreach ($eventoatributos as $atributo) {
////                    print_r(strtolower($atributo->getIdatributo()->getNombre()));
//                    if ($child->getName() == strtolower($atributo->getIdatributo()->getNombre())) {
//                        $existe = true;
////                        print_r(' existe'.'<br>');
//                        break;
//                    } else {
//                        $existe = false;
////                        print_r(' no existe'.'<br>');
//                    }
//                }
//                if ($existe == false && $child->getName() != 'idestado')
////                    print_r('borrar campo ' . $child->getName().'<br>');
//                    $form->get('idpia')->add($child->getName(), 'hidden');
//            }
//        }
//    }      
    
    public function sessionsAction(){
        $request = $this->getRequest();
        $cookies = $request->cookies;
        $user="";
        if ($cookies->has('username'))
            //$user=$this->get('session')->get('user');
            $user=$cookies->get('username');
            //$user=$cookies['username'];
        
        return $this->render('FraterSoftPiaWebBundle:Default:sessions.html.twig', array('user' => $user));
    }
    
    public function eviaremailAction(){
        $entity = new Inscrito();
        $competidor = new Competidor();
        $em = $this->getDoctrine()->getManager();        
        $competidor = $em->getRepository('FraterSoftPiaWebBundle:Competidor')->findOneBy(array('iddocumento' => '12660131'));
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->buscarInscrito(20,$competidor->getId());
        //print_r($entity);
        $mailer = $this->get('app.mail_controller');
        $mailer->enviarPreinscripcion(
                "Pre-Inscripcion " . $entity[0]->getIdevento()->getNombre(), 
                //$competidor->getEmail(), 
                $entity[0]->getIdpia()->getEmailpersonal(),
                $this->renderView('FraterSoftPiaWebBundle:Inscrito:email.html.twig', array('entity' => $entity[0]))
        );         
        return $this->render('FraterSoftPiaWebBundle:Inscrito:conciliado.html.twig', array(
                    'inscrito' => $entity[0],
                    'idevento' => $entity[0]->getIdevento()->getId()                        
        ));                     
        
    }     
    
    public function parametrosAction(){
        
        $transport = \Swift_SmtpTransport::newInstance('mail.retos.info', 465, 'ssl')
          ->setUsername('retosinf')
          ->setPassword('j-2013ab')
        ;        
        
        // Create the Mailer using your created Transport
        $mailer = \Swift_Mailer::newInstance($transport);

        // Create a message
        $message = \Swift_Message::newInstance('Prueba de Correo Retosinfo')
          ->setFrom(['pagos@retos.info' => 'Pagos Retosinfo'])
          ->setTo(['fdgcoronado@gmail.com' => 'Freddy Garcia'])
          ->setBody('Prueba de correo')
          ;

        // Send the message
        $result = $mailer->send($message);  
        
        print_r($result);

        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => '',
                    'texto' => 'No existe una Categorias aplicable a este participante',
        ));                 
    }      

    public function contactoAction(Request $request)
    {
        $enviado = false;
        $error = null;
        $nombre = '';
        $mensaje = '';

        if ($request->isMethod('POST')) {
            $nombre = trim($request->request->get('nombre'));
            $mensaje = trim($request->request->get('mensaje'));
            $metodo = $request->request->get('metodo');

            if ($nombre === '' || $mensaje === '') {
                $error = 'Debe completar su nombre y el mensaje.';
            } elseif (mb_strlen($mensaje) > 500) {
                $error = 'El mensaje no puede superar los 500 caracteres.';
            } elseif ($metodo === 'whatsapp') {
                $numero = preg_replace('/[^0-9]/', '', $this->container->getParameter('contacto_whatsapp'));
                $texto = "Nombre: {$nombre}\nMensaje: {$mensaje}";

                return $this->redirect('https://wa.me/' . $numero . '?text=' . rawurlencode($texto));
            } else {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Nuevo mensaje de contacto - ' . $nombre)
                    ->setFrom($this->container->getParameter('mailer_user'))
                    ->setTo($this->container->getParameter('contacto_email'))
                    ->setBody($mensaje . "\n\nDe: " . $nombre);
                $this->get('mailer')->send($message);
                $enviado = true;
                $nombre = '';
                $mensaje = '';
            }
        }

        return $this->render('FraterSoftPiaWebBundle:Default:contacto.html.twig', array(
            'enviado' => $enviado,
            'error' => $error,
            'nombre' => $nombre,
            'mensaje' => $mensaje,
        ));
    }

    public function detalleEventoAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($id);
        if (!$evento) {
            throw $this->createNotFoundException('Evento no encontrado.');
        }

        $competencias = $em->getRepository('FraterSoftPiaWebBundle:Competencia')->findBy(array('idevento' => $id));

        $categoriasPorCompetencia = array();
        foreach ($competencias as $c) {
            $cats = $em->getRepository('FraterSoftPiaWebBundle:Categoria')->findBy(array('idcompetencia' => $c));
            foreach ($cats as $cat) {
                $categoriasPorCompetencia[$c->getId()][] = $cat;
            }
        }

        $preciosEvento = $em->getRepository('FraterSoftPiaWebBundle:Preciosevento')
            ->findBy(array('idevento' => $evento));

        $preciosCategoria = $em->getRepository('FraterSoftPiaWebBundle:Precioscategoria')
            ->arrayPrecios($id);

        $publicidad = $em->getRepository('FraterSoftPiaWebBundle:Publicidad')
            ->arrayLista($id);

        $organizador = $evento->getIdorganizador();

        $cards = array();
        $cardsIdx = array();

        foreach ($preciosEvento as $pe) {
            $moneda = $pe->getIdmoneda();
            if (!$moneda) continue;
            $cod = $moneda->getCodigolocal();
            $nom = $moneda->getNombre();
            if (!$cod) continue;
            if (!isset($cardsIdx[$cod])) {
                $cardsIdx[$cod] = count($cards);
                $cards[] = array(
                    'monedaCodigo' => $cod,
                    'monedaNombre' => $nom,
                    'prices' => array(),
                    'formaspagos' => array(),
                );
            }
            $idx = $cardsIdx[$cod];
            $cards[$idx]['monedaNombre'] = $nom;
            $cards[$idx]['prices'][] = array(
                'texto' => $pe->getTexto(),
                'precio' => $pe->getPrecio(),
                'hasta' => $pe->getHasta(),
            );
        }

        foreach ($preciosCategoria as $pc) {
            $cod = $pc['moneda'];
            if (!$cod) continue;
            if (!isset($cardsIdx[$cod])) {
                $cardsIdx[$cod] = count($cards);
                $cards[] = array(
                    'monedaCodigo' => $cod,
                    'monedaNombre' => $cod,
                    'prices' => array(),
                    'formaspagos' => array(),
                );
            }
            $idx = $cardsIdx[$cod];
            $cards[$idx]['prices'][] = array(
                'texto' => $pc['texto'],
                'precio' => $pc['precio'],
            );
        }

        if ($organizador) {
            try {
                $fpes = $em->createQuery(
                    "SELECT fpe FROM FraterSoftPiaWebBundle:Formaspagoevento fpe "
                    . "JOIN fpe.idformapago fp "
                    . "WHERE fpe.idevento = " . $id . " AND fpe.status = 1"
                )->getResult();
                foreach ($fpes as $fpe) {
                    $fp = $fpe->getIdformapago();
                    if (!$fp || !$fp->getIdmoneda()) continue;
                    $cod = $fp->getIdmoneda()->getCodigolocal();
                    if (!$cod) continue;
                    if (!isset($cardsIdx[$cod])) {
                        $cardsIdx[$cod] = count($cards);
                        $cards[] = array(
                            'monedaCodigo' => $cod,
                            'monedaNombre' => $fp->getIdmoneda()->getNombre(),
                            'prices' => array(),
                            'formaspagos' => array(),
                        );
                    }
                    $idx = $cardsIdx[$cod];
                    $cards[$idx]['monedaNombre'] = $fp->getIdmoneda()->getNombre();
                    $ofp = $em->getRepository('FraterSoftPiaWebBundle:OrganizadorFormaspago')
                        ->findOneBy(array('idformapago' => $fp, 'idorganizador' => $organizador));
                    $cards[$idx]['formaspagos'][] = array(
                        'nombre' => $fp->getNombre(),
                        'icono' => $fp->getIcono(),
                        'observacion' => $ofp ? $ofp->getObservacion() : null,
                        'qr' => $ofp ? $ofp->getQr() : null,
                    );
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        return $this->render('FraterSoftPiaWebBundle:Default:detalle_evento.html.twig', array(
            'evento' => $evento,
            'organizador' => $organizador,
            'competencias' => $competencias,
            'categoriasPorCompetencia' => $categoriasPorCompetencia,
            'cards' => $cards,
            'preciosCompetencia' => $em->getRepository('FraterSoftPiaWebBundle:Precioscompetencia')
                ->arrayPrecios($id),
            'publicidad' => $publicidad,
        ));
    }

    public function postRegistrationOrganizadorAction(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        if ($user->getIdorganizador()) {
            return $this->redirect($this->generateUrl('frater_soft_pia_web_eventos'));
        }

        $em = $this->getDoctrine()->getManager();

        $organizador = new Organizador();
        $organizador->setEmail($user->getEmail());

        $form = $this->createForm(new OrganizadorType(), $organizador, array(
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Guardar'));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $file = $form->get('logo')->getData();
            if ($file) {
                $dir = $this->get('kernel')->getRootDir() . '/../web/bundles/fratersoftpiaweb/fine-uploader/files';
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($dir, $filename);
                $organizador->setLogo($filename);
            }

            $em->persist($organizador);
            $em->flush();

            $user->setIdorganizador($organizador);
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('frater_soft_pia_web_eventos'));
        }

        return $this->render('FraterSoftPiaWebBundle:Default:organizador_post_registration.html.twig', array(
            'form' => $form->createView(),
            'organizador' => $organizador,
            'user' => $user,
        ));
    }

    public function consultaTasaOficialAction($idmoneda){
        switch ($idmoneda) {
            case 1: 
                $url = "https://ve.dolarapi.com/v1/dolares/oficial";
                break;
            case 2:
                $url = "https://ve.dolarapi.com/v1/dolares/paralelo";
                break;
            case 3:
                $url = "https://ve.dolarapi.com/v1/dolares/bitcoin";
                break;
            default:
                return new Response("Moneda no válida");
        }
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // importante
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // opcional si hay problemas SSL

        $result = curl_exec($ch);

        if ($result === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return new Response("Error en cURL: " . $error);
        }

        curl_close($ch);

        $data = json_decode($result, true);

        return new Response('<pre>' . print_r($data, true) . '</pre>');
    }

    public function eliminarFormapagoOrganizadorAction($id)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $em = $this->getDoctrine()->getManager();
        $asignacion = $em->getRepository('FraterSoftPiaWebBundle:OrganizadorFormaspago')->find($id);

        if (!$asignacion) {
            throw $this->createNotFoundException('Asignacion no encontrada.');
        }

        $organizador = $user->getIdorganizador();
        if (!$organizador || $asignacion->getIdorganizador()->getId() !== $organizador->getId()) {
            throw new AccessDeniedException('No autorizado.');
        }

        $em->remove($asignacion);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Forma de pago eliminada del organizador.');

        return $this->redirect($this->generateUrl('frater_soft_pia_web_perfil') . '#tabs-3');
    }
    
}
