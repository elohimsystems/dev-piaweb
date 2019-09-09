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

use FraterSoft\PiaWebBundle\Entity\Inscrito;
use FraterSoft\PiaWebBundle\Entity\Competidor;

class DefaultController extends commonPIAClass 
{
  
    public function indexAction($name)
    {
        return $this->render('FraterSoftPiaWebBundle:Default:index.html.twig', array('name' => $name));
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

//        $definition = $container->getDefinition('app.user_config_manager');
//        $constructorArguments = $definition->getArguments();
//        print_r($constructorArguments);
//        $definition = new Definition(DoctrineConfigManager::class, array(
//            new Reference('app.mail_controller'), // a reference to another service
//            //'%app.database_name%',  // will be resolved to the value of a container parameter
//        ));
//        $mailer = $this->get('app.mail_controller');
//        $transport = $mailer->getTransport();
        
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
    
}
