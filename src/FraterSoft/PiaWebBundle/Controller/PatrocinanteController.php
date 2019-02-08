<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use FraterSoft\PiaWebBundle\Entity\Patrocinante;
use FraterSoft\PiaWebBundle\Form\PatrocinanteType;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Competidor controller.
 *
 */
class PatrocinanteController extends commonPIAClass {
    
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
            return $this->render('FraterSoftPiaWebBundle:Patrocinante:numerar.html.twig', array(
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
                            'texto' => 'No se ha configurado patrocinante para este evento',
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
        $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->resetearPatrocinante($idevento);
        $patrocinante=$em->getRepository('FraterSoftPiaWebBundle:Patrocinante')->findOneBy(array('idevento'=>$idevento));
        if($patrocinante){
            $em->getRepository('FraterSoftPiaWebBundle:Patrocinanteexterna')->resetear($patrocinante->getId());
            $patrocinante->setSiguiente($patrocinante->getInicio());
            $patrocinanteatributos=$em->getRepository('FraterSoftPiaWebBundle:Patrocinanteatributo')->findBy(array('idpatrocinante'=>$patrocinante->getId()));
            foreach ($patrocinanteatributos as $patrocinanteatributo){
                $patrocinanteatributo->setSiguiente($patrocinanteatributo->getInicio());
            }
            $em->flush();     
        }
        $request = $this->getRequest();
        $referer = $request->headers->get('referer');
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => $referer,
                    'texto' => 'Reseteada toda la patrocinante del evento',
        ));          
    }
    
    public function listapatrocinanteAction($idevento,$email) {
        $em = $this->getDoctrine()->getManager();

        $inscritos = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')
                ->listarConciliadas($idevento);

        $evento = $em->getRepository('FraterSoftPiaWebBundle:Evento')->find($idevento);     

        return $this->render('FraterSoftPiaWebBundle:Patrocinante:lista_patrocinante.html.twig', array(
                    'inscritos' => $inscritos,
                    'idevento' => $idevento,
                    'email' => $email,
        ));
    }      
    
    public function enumerar($idevento){
        $em = $this->getDoctrine()->getManager();
        $inscrito=null;
        $contador=1;
        $patrocinante = $em->getRepository('FraterSoftPiaWebBundle:Patrocinante')->findOneBy(array('idevento' => $idevento));
        if(!$patrocinante) //si no hay patrocinante configurada
            return(-1);
        $patrocinanteatributos = $em->getRepository('FraterSoftPiaWebBundle:Patrocinanteatributo')->findBy(array('idpatrocinante' => $patrocinante->getId()));
        if(!$patrocinanteatributos){ //Si no hay atributos configurados se usa configuracion por evento
            $this->numerados=array(0);
            $this->inicial=array(0);
            $this->final=array(0);
            $inscritos = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->listaParaNumerar(
                    $idevento,null,$patrocinante->getOrdenarpor()
            );
            foreach ($inscritos as $inscrito){
                $inscripcion=$em->getRepository('FraterSoftPiaWebBundle:Inscrito')->find($inscrito);
                if($patrocinante->getSecuencial()){
                    $this->numerados[0]=$contador++;
                    if($patrocinante->getSiguiente()==$patrocinante->getInicio())
                        $this->inicial[0]=$patrocinante->getSiguiente();
                    $inscripcion->setNumero($patrocinante->getSiguiente());
                    $this->final[0]=$patrocinante->getSiguiente();
                    $patrocinante->setSiguiente($patrocinante->getSiguiente()+1);
                }
                else{
                    $patrocinanteexterna = $em->getRepository('FraterSoftPiaWebBundle:Patrocinanteexterna')->findOneBy(
                            array('idpatrocinante'=>$patrocinante->getId(),'asignado'=>false),
                            array('numero'=>'ASC'),
                            1 //parametro limit (devuelve 1 registro de los seleccionados)
                    );
                    if($patrocinanteexterna){ 
                        $this->numerados[0]=$contador++;
                        $inscripcion->setNumero($patrocinanteexterna->getNumero());
                        $patrocinanteexterna->setAsignado(true);
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
            foreach ($patrocinanteatributos as $patrocinanteatributo){
                switch (true){
                    case $patrocinante->getAtributo()=="competencia":
                        $filtro="tmcompetencias.descripcion in (" . $patrocinanteatributo->getValoratributo() . ")";
                        break;
                    case $patrocinante->getAtributo()=="categortia":
                        $filtro="tmcategorias.descripcion in (" . $patrocinanteatributo->getValoratributo() . ")";
                        break;                
                    case $patrocinante->getAtributo()=="precio":
                        $filtro="tminscritos.precio in (" . $patrocinanteatributo->getValoratributo() . ")";
                        break;        
                    default:
                        $filtro="tmcompetidores." . $patrocinante->getAtributo() . " in (" . $patrocinanteatributo->getValoratributo() . ")";
                }
                $idinscritos = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->listaParaNumerar(
                        $idevento,$filtro,$patrocinante->getOrdenarpor()
                );
                $this->inicial[$patrocinanteatributo->getValoratributo()]=$patrocinanteatributo->getSiguiente(); 
                $contador=1;
                foreach ($idinscritos as $idinscrito){
                    $this->numerados[$patrocinanteatributo->getValoratributo()]=$contador++;
                    $inscripcion=$em->getRepository('FraterSoftPiaWebBundle:Inscrito')->find($idinscrito);
                    $inscripcion->setNumero($patrocinanteatributo->getSiguiente());
                    $this->final[$patrocinanteatributo->getValoratributo()]=$patrocinanteatributo->getSiguiente();
                    $patrocinanteatributo->setSiguiente($patrocinanteatributo->getSiguiente()+1);
                }
                $em->flush();     
            }
        }
        return($contador);
    }
    
    /**
     * Creates a new Patrocinante entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Patrocinante();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('patrocinante_gestion', array(
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
    * Creates a form to create a Patrocinante entity.
    *
    * @param Patrocinante $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Patrocinante $entity)
    {
        $form = $this->createForm(new PatrocinanteType(), $entity, array(
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Patrocinante entity.
     *
     */
    public function gestionAction($idorganizador)
    {
        $entity = new Patrocinante();
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
        
        return $this->render('FraterSoftPiaWebBundle:Patrocinante:gestion.html.twig', array(
            'form'   => $form->createView(),
            'idevento' => $idevento,
            'atributos' => $atributos, 
            'campos' => $this->getCampos($em,'Patrocinante'),
        ));
    }

    public function listaAjaxAction($idevento){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());  
        $serializer = new Serializer($normalizers, $encoders);  
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:Patrocinante')->arrayListas($idevento);

        $jsonContent = $serializer->serialize(array(
            "recordsTotal"=> count($entities),
            "data"=>$entities)
                , 'json');
        
        return new response($jsonContent);                    
    }

    /**
    * Creates a form to edit a Patrocinante entity.
    *
    * @param Patrocinante $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Patrocinante $entity)
    {
        $form = $this->createForm(new PatrocinanteType(), $entity, array(
            'method' => 'POST',
        ));

        return $form;
    }
    
    /**
     * Edits an existing Patrocinante entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FraterSoftPiaWebBundle:Patrocinante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Patrocinante entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('patrocinante_gestion', array(
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
     * Deletes a Patrocinante entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:Patrocinante')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Patrocinante entity.');
        }
        $em->remove($entity);
        $em->flush();
            return $this->redirect($this->generateUrl('patrocinante_gestion', array(
                'idevento' => $entity->getIdevento()->getId(),
                'estado' => 1
            )));            
    }
    
}
