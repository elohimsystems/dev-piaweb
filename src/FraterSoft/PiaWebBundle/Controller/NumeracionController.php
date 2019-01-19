<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use FraterSoft\PiaWebBundle\Entity\Competidor;
use FraterSoft\PiaWebBundle\Entity\Inscrito;
use FraterSoft\PiaWebBundle\Form\CompetidorType;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Competidor controller.
 *
 */
class NumeracionController extends Controller {
    
    private $numerados;
    private $inicial; 
    private $final;

    /**
     * Lists all Competidor entities.
     *
     */
    public function asignarAction($idevento) {
        $respuesta=$this->enumerar($idevento);
        if($respuesta>0){
            return $this->render('FraterSoftPiaWebBundle:Numeracion:numerar.html.twig', array(
                        'numerados' => $this->numerados,
                        'inicial' => $this->inicial,
                        'final' => $this->final
            ));
        }
        else{
            $request = $this->getRequest();
            $referer = $request->headers->get('referer');   
            if($respuesta==-1)
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => $referer,
                            'texto' => 'No se ha configurado numeracion para este evento',
                ));      
            if($respuesta==-2)
                return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                            'url' => null,
                            'texto' => 'No existen numeros externos disponibles',
                ));                
        }
    }  
    
    public function resetearAction($idevento) {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->resetearNumeracion($idevento);
        $numeracion=$em->getRepository('FraterSoftPiaWebBundle:Numeracion')->findOneBy(array('idevento'=>$idevento));
        if($numeracion){
            $em->getRepository('FraterSoftPiaWebBundle:Numeracionexterna')->resetear($numeracion->getId());
            $numeracion->setSiguiente($numeracion->getInicio());
            $numeracionatributos=$em->getRepository('FraterSoftPiaWebBundle:Numeracionatributo')->findBy(array('idnumeracion'=>$numeracion->getId()));
            foreach ($numeracionatributos as $numeracionatributo){
                $numeracionatributo->setSiguiente($numeracionatributo->getInicio());
            }
            $em->flush();     
        }
        $request = $this->getRequest();
        $referer = $request->headers->get('referer');
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => $referer,
                    'texto' => 'Reseteada toda la numeracion del evento',
        ));          
    }
    
    public function listanumeracionAction($idevento,$email) {
        $em = $this->getDoctrine()->getManager();

        $inscritos = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->listarConciliadas($idevento);

        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($idevento);     

        return $this->render('FraterSoftPiaWebBundle:Numeracion:lista_numeracion.html.twig', array(
                    'inscritos' => $inscritos,
                    'idevento' => $idevento,
                    'email' => $email,
        ));
    }      
    
    public function enumerar($idevento){
        $em = $this->getDoctrine()->getManager();
        $inscrito=null;
        $contador=1;
        $numeracion = $em->getRepository('FraterSoftPiaWebBundle:Numeracion')->findOneBy(array('idevento' => $idevento));
        if(!$numeracion) //si no hay numeracion configurada
            return(-1);
        $numeracionatributos = $em->getRepository('FraterSoftPiaWebBundle:Numeracionatributo')->findBy(array('idnumeracion' => $numeracion->getId()));
        if(!$numeracionatributos){ //Si no hay atributos configurados se usa configuracion por evento
            $this->numerados=array(0);
            $this->inicial=array(0);
            $this->final=array(0);
            $inscritos = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->listaParaNumerar(
                    $idevento,null,$numeracion->getOrdenarpor()
            );
            foreach ($inscritos as $inscrito){
                $inscripcion=$em->getRepository('FraterSoftPiaWebBundle:Inscrito')->find($inscrito);
                if($numeracion->getSecuencial()){
                    $this->numerados[0]=$contador++;
                    if($numeracion->getSiguiente()==$numeracion->getInicio())
                        $this->inicial[0]=$numeracion->getSiguiente();
                    $inscripcion->setNumero($numeracion->getSiguiente());
                    $this->final[0]=$numeracion->getSiguiente();
                    $numeracion->setSiguiente($numeracion->getSiguiente()+1);
                }
                else{
                    $numeracionexterna = $em->getRepository('FraterSoftPiaWebBundle:Numeracionexterna')->findOneBy(
                            array('idnumeracion'=>$numeracion->getId(),'asignado'=>false),
                            array('numero'=>'ASC'),
                            1 //parametro limit (devuelve 1 registro de los seleccionados)
                    );
                    if($numeracionexterna){ 
                        $this->numerados[0]=$contador++;
                        $inscripcion->setNumero($numeracionexterna->getNumero());
                        $numeracionexterna->setAsignado(true);
                    }
                    else{
                         return(commonPIAClass::NUMERACIONEXTERNA_NO_CONFIGURADA);
                    }
                    $em->flush();
                }
            }
            $em->flush();
        }
        else{
            $this->numerados=array();
            $this->inicial=array();
            $this->final=array();                
            foreach ($numeracionatributos as $numeracionatributo){
                switch (true){
                    case $numeracion->getAtributo()=="competencia":
                        $filtro="tmcompetencias.descripcion in (" . $numeracionatributo->getValoratributo() . ")";
                        break;
                    case $numeracion->getAtributo()=="categortia":
                        $filtro="tmcategorias.descripcion in (" . $numeracionatributo->getValoratributo() . ")";
                        break;                
                    case $numeracion->getAtributo()=="precio":
                        $filtro="tminscritos.precio in (" . $numeracionatributo->getValoratributo() . ")";
                        break;        
                    default:
                        $filtro="tmcompetidores." . $numeracion->getAtributo() . " in (" . $numeracionatributo->getValoratributo() . ")";
                }
                $idinscritos = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->listaParaNumerar(
                        $idevento,$filtro,$numeracion->getOrdenarpor()
                );
                $this->inicial[$numeracionatributo->getValoratributo()]=$numeracionatributo->getSiguiente(); 
                $contador=1;
                foreach ($idinscritos as $idinscrito){
                    $this->numerados[$numeracionatributo->getValoratributo()]=$contador++;
                    $inscripcion=$em->getRepository('FraterSoftPiaWebBundle:Inscrito')->find($idinscrito);
                    $inscripcion->setNumero($numeracionatributo->getSiguiente());
                    $this->final[$numeracionatributo->getValoratributo()]=$numeracionatributo->getSiguiente();
                    $numeracionatributo->setSiguiente($numeracionatributo->getSiguiente()+1);
                }
                $em->flush();     
            }
        }
        return($contador);
    }
}
