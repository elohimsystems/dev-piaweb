<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use FraterSoft\PiaWebBundle\Entity\Numeracion;
use FraterSoft\PiaWebBundle\Form\NumeracionType;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Competidor controller.
 *
 */
class NumeracionController extends commonPIAClass {
    
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
    
    /**
     * Creates a new Numeracion entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Numeracion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('numeracion_gestion', array(
                'idevento' => $entity->getIdevento()->getId(),
                'estado' => 2
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
    * Creates a form to create a Numeracion entity.
    *
    * @param Numeracion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Numeracion $entity)
    {
        $form = $this->createForm(new NumeracionType(), $entity, array(
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Numeracion entity.
     *
     */
    public function gestionAction($idevento)
    {
        $entity = new Numeracion();
        $form   = $this->createCreateForm($entity);
        
        $em = $this->getDoctrine()->getManager();
        
        //llena el Select con el evento y Oculta el control
//        $form->add('idevento','entity',array(
//                'class' => 'FraterSoftPiaWebBundle:Evento',
//                'query_builder' => function (EntityRepository $er) use ( $idevento ) {
//                    return $er->createQueryBuilder('e')
//                            ->where('e.id=:idevento')
//                            ->setParameter('idevento',$idevento);
//                },
//            ));
        

        $atributos = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')->atributosOptions($idevento);
        
        return $this->render('FraterSoftPiaWebBundle:Numeracion:gestion.html.twig', array(
            'form'   => $form->createView(),
            'idevento' => $idevento,
            'atributos' => $atributos, 
            'campos' => $this->getCampos($em,'Numeracion'),
        ));
    }

    public function listaAjaxAction($idevento){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:Numeracion')->arrayListas($idevento);

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($entities),
            "data"=>$entities)
                , 'json');
        
        return new response($jsonContent);                    
    }

    /**
    * Creates a form to edit a Numeracion entity.
    *
    * @param Numeracion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Numeracion $entity)
    {
        $form = $this->createForm(new NumeracionType(), $entity, array(
            'method' => 'POST',
        ));

        return $form;
    }
    
    /**
     * Edits an existing Numeracion entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Numeracion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Numeracion entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('numeracion_gestion', array(
                'idevento' => $entity->getIdevento()->getId(),
                'estado' => 3
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
     * Deletes a Numeracion entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Numeracion')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Numeracion entity.');
        }
        $em->remove($entity);
        $em->flush();
            return $this->redirect($this->generateUrl('numeracion_gestion', array(
                'idevento' => $entity->getIdevento()->getId(),
                'estado' => 1
            )));            
    }
    
}
