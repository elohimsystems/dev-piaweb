<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;

use FraterSoft\PiaWebBundle\Entity\Preciosevento;
use FraterSoft\PiaWebBundle\Entity\Precioscompetencia;
use FraterSoft\PiaWebBundle\Entity\Precioscategoria;


class commonPIAClass extends Controller
{

    const NUMERACION_NO_CONFIGURADA = -1;
    const NUMERACIONEXTERNA_NO_CONFIGURADA = -2;
    const CONFIGURACION_GRUPO_TOTAL = 1;
    const CONFIGURACION_GRUPO_PARCIAL = 2;
    const CONFIGURACION_GRUPO_NINGUNA = 0;
    const RELACION_ONE_TO_ONE = 1;
    const RELACION_MANY_TO_ONE = 2;
    const RELACION_MANY_TO_MANY = 8;
    const ESTATUS_INSCRIPCION_ACTIVA = 1;
    const ESTATUS_INSCRIPCION_ANULADA = 0;
    
    //Oculta los campos que no estan configurados en la base de datos    
    public function renombraLabels($idevento, $form) {
        $em = $this->getDoctrine()->getManager();
        $eventoatributos = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')->atributosEvento($idevento);
        if ($eventoatributos) {
            $atributo = null;
            foreach ($form->get('idpia')->all() as $child) {
                $existe = false;            
                foreach ($eventoatributos as $atributo) {
                    if ($child->getName() == strtolower($atributo->getIdatributo()->getNombre())) {
                        if($atributo->getEtiqueta())
                            $form->get('idpia')->add($child->getName(), 'text',array('label'=>$atributo->getEtiqueta()));
                    }
                }
            }
        }
    } 
    
    //Oculta los campos que no estan configurados en la base de datos    
    public function ocultaCampos($idevento, $form,$campobusqueda=array('clave'=>NULL,'dato'=>NULL)) {
        $em = $this->getDoctrine()->getManager();
        $eventoatributos = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')->atributosEvento($idevento);
        if ($eventoatributos) {
            $atributo = null;
            foreach ($form->get('idpia')->all() as $child) {
                $existe = false;
                foreach ($eventoatributos as $atributo) {
                    if ($child->getName() == strtolower($atributo->getIdatributo()->getNombre())) {
                        $existe = true;
                        break;
                    } else {
                        $existe = false;
                    }
                }
                if ($existe == false && $child->getName() != 'idestado'){
                    $form->get('idpia')->add($child->getName(), 'hidden');
                }
                if ($existe == true)
                    switch($atributo->getIdatributo()->getTipodato()){
                        case "S":
                            $propiedades=[];
                            switch(true){
                                case $child->getName()=='iddocumento' && $campobusqueda['clave']=='iddocumento':
                                    $propiedades['data']=$campobusqueda['dato'];
                                    break;
                                case $child->getName()=='emailpersonal' && $campobusqueda['clave']=='emailpersonal':
                                    $propiedades['data']=$campobusqueda['dato'];
                                    break;
                            }
                            $propiedades['required']=($atributo->getRequerido()==true)?true:false;
                            $propiedades['label']=(is_null($atributo->getEtiqueta()))?$atributo->getIdatributo()->getNombre():$atributo->getEtiqueta();
                            $propiedades['read_only']=($atributo->getBusqueda()==true)?true:false;
                            $tipocampo='text';
                            $form->get('idpia')->add(
                                $child->getName(), $tipocampo,$propiedades//array(
                                //'required'=>($atributo->getRequerido()==true)?true:false,
                                //'label'=>(is_null($atributo->getEtiqueta()))?$atributo->getIdatributo()->getNombre():$atributo->getEtiqueta(),
                                //'read_only' => ($atributo->getBusqueda()==true)?true:false,
                            );                     
                            break;
                        case "N":
                            $tipocampo='number';
                            $form->get('idpia')->add(
                                $child->getName(), $tipocampo,array(
                                'required'=>($atributo->getRequerido()==true)?true:false,
                                'label'=>(is_null($atributo->getEtiqueta()))?$atributo->getIdatributo()->getNombre():$atributo->getEtiqueta(),
                                'read_only' => ($atributo->getBusqueda()==true)?true:false,
                            ));                     
                            break;
                        case "F":
                            $tipocampo='text';
                            $form->get('idpia')->add(
                                $child->getName(), $tipocampo,array(
                                'required'=>($atributo->getRequerido()==true)?true:false,
                                'label'=>(is_null($atributo->getEtiqueta()))?$atributo->getIdatributo()->getNombre():$atributo->getEtiqueta(),
                                'read_only' => ($atributo->getBusqueda()==true)?true:false,
                            ));                     
                            break;
                        case "I":
                            $tipocampo='text';
                            $form->get('idpia')->add(
                                $child->getName(), $tipocampo,array(
                                'required'=>($atributo->getRequerido()==true)?true:false,
                                'label'=>(is_null($atributo->getEtiqueta()))?$atributo->getIdatributo()->getNombre():$atributo->getEtiqueta(),
                                'read_only' => ($atributo->getBusqueda()==true)?true:false,
                            ));                     
                            break;
                        case "A":
                            $tipocampo='textarea';
                            $form->get('idpia')->add(
                                $child->getName(), $tipocampo,array(
                                'required'=>($atributo->getRequerido()==true)?true:false,
                                'label'=>(is_null($atributo->getEtiqueta()))?$atributo->getIdatributo()->getNombre():$atributo->getEtiqueta(),
                            ));                            
                            break;
                        case "O":
                            $tipocampo='choice';
                            $lista = $em->getRepository('FraterSoftPiaWebBundle:Listasevento')->getArrayValores($idevento,$atributo->getIdatributo()->getId());
                            $form->get('idpia')->add(
                                $child->getName(), $tipocampo,array(
                                'choices' => $lista,
                                'required'=>($atributo->getRequerido()==true)?true:false,
                                'label'=>(is_null($atributo->getEtiqueta()))?$atributo->getIdatributo()->getNombre():$atributo->getEtiqueta(),
                                'empty_value' => (is_null($atributo->getEtiqueta()))?
                                    'Seleccione ' . $atributo->getIdatributo()->getNombre():
                                    'Seleccione ' . $atributo->getEtiqueta(),
                            ));                            
                            break;                    
                        case "E":
                            $tipocampo='entity';
                            $form->get('idpia')->add(
                                $child->getName(), $tipocampo,array(
                                'class'=>'FraterSoftPiaWebBundle:' . ucwords(substr($child->getName(),2)),
                                'required'=>($atributo->getRequerido()==true)?true:false,
                                'label'=>(is_null($atributo->getEtiqueta()))?$atributo->getIdatributo()->getNombre():$atributo->getEtiqueta(),
                                'empty_value' => (is_null($atributo->getEtiqueta()))?
                                    'Seleccione ' . $atributo->getIdatributo()->getNombre():
                                    'Seleccione ' . $atributo->getEtiqueta(),
                            ));                            
                            break;                    
//                        case "D":
//                            $tipocampo='date';
//                            $form->get('idpia')->add(
//                                $child->getName(), $tipocampo,array(
//                                'attr' => ['class' => 'fechaESP'],
//                                'required'=>($atributo->getRequerido()==true)?true:false,
//                                'label'=>(is_null($atributo->getEtiqueta()))?$atributo->getIdatributo()->getNombre():$atributo->getEtiqueta(),
//                                'widget' => 'single_text',
//                                'format' => 'dd/MM/yyyy',
//                            ));                            
//                            break;                    
                    }
            }
        }
    }    
    
    public function desencriptarTelefono($telefono) {
        if (substr($telefono, 0, 8) == 'crypted:')
            return $this->decrypt(substr($telefono, 8, strlen($telefono)), '$fDGc2401');
        else
            return $telefono;
    }    
    
    public function encrypt($string, $key) {
        $result = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result.=$char;
        }
        return base64_encode($result);
    }

    public function decrypt($string, $key) {
        $result = '';
        $string = base64_decode($string);
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result.=$char;
        }
        return $result;
    }
    
    public function desencriptarEmail($emaile) {
        if (substr($emaile, 0, 8) == 'crypted:')
            return $this->decrypt(substr($emaile, 8, strlen($emaile)), '$fDGc2401');
        else
            return $emaile;
    }

    public function mensaje($texto, $url) {
        return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                    'url' => $url,
                    'texto' => $texto,
        ));
    }   
    
    public function seleccionaCategorias($idevento, $competidor) {
        $criterios = array();
        $em = $this->getDoctrine()->getManager();
        $atributoscriterios = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')->atributosCriterios($idevento);
        if ($atributoscriterios) {
            foreach ($atributoscriterios as $atruibutocriterio) {
                $valor = $competidor->getValorCampo(strtolower($atruibutocriterio->getIdatributo()->getNombre()));
                if ($valor) {
                    $criterios[strtolower($atruibutocriterio->getIdatributo()->getNombre())] = $valor;
                }
            }

            $categoriasselect = new ArrayCollection();

            $categorias = $em->getRepository('FraterSoftPiaWebBundle:Categoria')->listaCategorias($idevento);

            foreach ($categorias as $categoria) {
                $reglas = $em->getRepository('FraterSoftPiaWebBundle:CategoriaReglas')->findBy(array(
                    'idcategoria' => $categoria->getId()
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
                    $categoriasselect->add($categoria);
                    $parametrok = false;
                }
            }
            return $categoriasselect;
        } else {
            return null;
        }
    }    

    public function seleccionaCategoriasGrupo($idevento,$idcompetencia,$idgrupo) {
        $criterios = array();
        $em = $this->getDoctrine()->getManager();
        $atributoscriterios = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')->atributosCriterios($idevento);
        if ($atributoscriterios) {

            $categoriasselect = new ArrayCollection();

            $categorias = $em->getRepository('FraterSoftPiaWebBundle:Categoria')->listaCategoriasGrupos($idevento,$idcompetencia);
            
            foreach ($categorias as $categoria) {
                $reglas = $em->getRepository('FraterSoftPiaWebBundle:CategoriaReglas')->findBy(array(
                    'idcategoria' => $categoria->getId()
                ));
                $parametrok = false;
                foreach ($reglas as $regla) {
                    $parametrok = false;
                    //print_r($categoria->getDescripcion());
                    switch($regla->getAccion()){
                        case 1: // Accion sumar
                            $suma = $em->getRepository('FraterSoftPiaWebBundle:CategoriaReglas')->SumarAccion(array(
                                'idevento' => $idevento,
                                'idgrupo' => $idgrupo,
                                'recla' => $regla,
                            ));
                            switch($regla->getTipo()){
                                case '[]':
                                    if ($regla->getValor1() <= $suma &&
                                            $regla->getValor2() >= $suma)
                                        $parametrok = true;
                                    break;
                                case '=':
                                    if ($regla->getValor1() == $suma)
                                        $parametrok = true;
                                    break;                                    
                            }
                            break;
                        case 2: // Accion contar
                            $atributo=$em->getRepository('FraterSoftPiaWebBundle:Atributo')->findOneBy(array('nombre'=>strtolower($regla->getAtributo())));
                            $cuenta = $em->getRepository('FraterSoftPiaWebBundle:Inscrito')->ContarReglaGrupo($idevento,$idgrupo,$regla,$atributo);
                            //echo('cuenta:');print_r($cuenta[0]['Cantidad']);print_r(' cantidad:'.$regla->getCantidad());echo("<br>");
                            if ($regla->getCantidad() == $cuenta[0]['Cantidad'])
                                $parametrok = true;
                            break;
                    }
                }
                if ($parametrok) {
                    $categoriasselect->add($categoria);
                    $parametrok = false;
                    break; //rompo el for de categorias
                }
            }
            return $categoriasselect;
        } else {
            return null;
        }
    }    
    
    public function buscarPrecio($idevento, $idcompetencia, $idcategoria,$idmoneda=null) {
        $precioevento = new Preciosevento();
        $preciocompetencia = new Precioscompetencia();
        $preciocategoria = new Precioscategoria();

        $em = $this->getDoctrine()->getManager();

        $precio = new ArrayCollection();

        if($idcategoria)
            $precio = $em->getRepository('FraterSoftPiaWebBundle:Precioscategoria')
                    ->BuscaPreciosCategoria($idcategoria,$idmoneda);
        if($idcompetencia && count($precio)==0)
            $precio = $em->getRepository('FraterSoftPiaWebBundle:Precioscompetencia')
                    ->BuscaPreciosCompetencia($idcompetencia,$idmoneda);
        if(count($precio)==0)
            $precio = $em->getRepository('FraterSoftPiaWebBundle:Preciosevento')
                    ->BuscaPreciosEvento($idevento,$idmoneda);
        return $precio;
    }   
    
    //Funcion que genera el boton de pago para 123pago, hay que estar pendiente que el numero
    //de pedido no exedad los 45 caracteres
    public function generarBoton123Pago($entity,$incremento) {

        $date = new \DateTime('now');
        //$post_url = "http://190.153.48.117/msBotonDePago/index.jsp"; // (TEST)
        $post_url = "https://123pago.net/msBotonDePago/index.jsp"; // (PRODUCCION)

        $numpedido = 'E';
        $numpedido.=$entity->getIdevento()->getId();
        $numpedido.='C';
        $numpedido.=$entity->getIdcompetencia()->getId();
        $numpedido.='S';
        $numpedido.=$entity->getSecuencia();
        $numpedido.='P';
        $numpedido.=$entity->getIdpago()->getId();
        $numpedido.='F';
        $numpedido.=$date->format('YmdHis');
        $porcentaje=$incremento / 100;

        $post_values = array(
            "nbproveedor" => "INVERSIONES DEPORTIVAS GARCIA",
            "nb" => $entity->getIdpia()->getNombre(),
            "ap" => $entity->getIdpia()->getApellido(),
            "ci" => $entity->getIdpia()->getIddocumento(),
            "em" => $entity->getIdpia()->getEmailpersonal(),
            "cs" => "7dc7504a0705cb3c12f9d5993578bc56",
            "nai" => $numpedido, //NUMERO DE PEDIDO 
            "co" => "INSCRIPCION " . $entity->getIdevento()->getNombre(),
            "tl" => "0424-9642982", //Telefono Soporte PIA
            "mt" => number_format($entity->getIdpago()->getMonto() * (1 + $porcentaje), 2, ".", ""), //Monto
            "ancho" => "190px"
        );

        // esta sección toma los valores de entrada requeridos por 123Pago y los
        // convierte en el formato correspondiente en el protocolo http post.
        $post_string = "";
        foreach ($post_values as $key => $value) {
            $post_string .= "$key=" . urlencode($value) . "&";
        }
        $post_string = rtrim($post_string, "& ");

        $request = curl_init($post_url); // instancia el objeto curl
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // retorna data de respuesta TRUE(1)
        curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // usa HTTP POST para enviar data de la forma.
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // descomente esta línea si no quiere obtener //respuesta de gateway
        if (!$post_response = curl_exec($request)) { // ejecuta el curl post y almacena el resultado en in $post_response
            $post_response = "Error en Host, intente otro metodo de pago"; //trigger_error(curl_error($request)); 
        }
        // es posible que se requiera el uso de opciones adicionales a las indicadas dependiendo de la //configuración de su servidor
        // Puede encontrar documentación de las opciones de curl en http://www.php.net/curl_setopt
        curl_close($request); // cierra el objeto curl

        return new response($post_response);
    }    
    
    //Convierte de forma recursiva un array de objetos stdClass a un array normal, para que la entidad pueda ser 
    //referenciada por nombre de propiedad
    public function objectToArray($objeto){
        $array_objeto=get_object_vars($objeto);
        foreach ($array_objeto as $clave => $valor){
            if(is_object($valor))
                $array_objeto[$clave]=$this->objectToArray($valor);
        } 
        return($array_objeto);
    }
    
    /*
     * Funcion recursiva para transformar los datos de una entidad 1 a 1 a un array de datos, 
     * la funcion solo recorre dos niveles de relacion 1 a 1, el base de la entidad y el 
     * siguiente segun la relacion que 1 a 1 que exista. Si consigue relaciones mucho a uno
     * solo devuelve el valor del campo id de la relacion.
     */
    public function EntitiesToArray($rows,$campos){
        $accessor = PropertyAccess::createPropertyAccessor();  
        $arrayrows=array();
        if(!is_array($rows)){
            throw $this->createNotFoundException('Parametro rows no es un array');
            return(null);
        }
        foreach($rows as $i=>$row){
            $arrayrow=array();
            foreach($campos as $campo => $valor){
//                print_r($campo.":");print_r($valor);echo("<br>");echo("<br>");
                if($campo!="entity")
                    switch(true){
                        case $valor["constraint"]==null || $valor["constraint"]=="primarykey":
                            switch(true){
                                case $valor["tipo"]=="bigint":
                                    $arrayrow[$campo]=intval($accessor->getValue($row,$campo));
                                    break;
                                case $valor["tipo"]=="datetime":
                                    $datetime=$accessor->getValue($row,$campo);
                                    if(!is_null($datetime))
                                        $arrayrow[$campo]=$datetime->format('Y-m-d H:i:s');
                                    break;
                                default:
                                    $arrayrow[$campo]=$accessor->getValue($row,$campo);
                            }
                            break;
                        case $valor["constraint"]=="foreingkey":
//                            print_r($campo.":");print_r($valor);echo("<br>");
                            switch(true){
                                case $valor["tipoRelacion"]==$this::RELACION_ONE_TO_ONE:
                                    $arrayrow[$campo]=is_null($accessor->getValue($row,$campo))?null:intval($accessor->getValue($row,$campo)->getId());
                                    break;
                                case $valor["tipoRelacion"]==$this::RELACION_MANY_TO_ONE:
                                    if($accessor->getValue($row,$campo))
                                        $arrayrow[$campo]=is_null($valor["campos"])?intval($accessor->getValue($row,$campo)->getId()):$this->EntitiesToArray([$accessor->getValue($row,$campo)],$valor["campos"]);
                                    else
                                        $arrayrow[$campo]=null;
                                    break;
                            }
                            break;
                    }
            }
            $arrayrows[$i]=$arrayrow;
        }
        return($arrayrows);
    }
    
    public function getErrorMessages(\Symfony\Component\Form\Form $form) {
        $errors = array();

        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }  
    
    /*
     * Devuelve un array con los campos de la entidad en el siguiente orden:
     *      primero los ids 
     *      luegos las claves foraneas
     *      y luego los demas campos. 
     */
    public function getCamposEntidad($em, $entidad){
        $campos = array();
        $index = 0;
        $IdentifierColumnNames=$em->getClassMetadata('FraterSoft\PiaWebBundle\Entity\\' . $entidad)->getIdentifierColumnNames();
        for($i=0;$i<count($IdentifierColumnNames);$i++){
            $campos[$index++]=$IdentifierColumnNames[$i];
        }
        $AssociationNames=$em->getClassMetadata('FraterSoft\PiaWebBundle\Entity\\' . $entidad)->getAssociationNames();
        for($i=0;$i<count($AssociationNames);$i++){
            $campos[$index++]=$AssociationNames[$i];
        }
        $FieldNames=$em->getClassMetadata('FraterSoft\PiaWebBundle\Entity\\' . $entidad)->getColumnNames();
        for($i=count($IdentifierColumnNames);$i<count($FieldNames);$i++){
            $campos[$index++]=$FieldNames[$i];
        }        
        return($campos);
    }
    
    function isDate($value) 
    {
        if (!$value) {
            return false;
        }

        try {
            new \DateTime($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }   
        
    /*
     * Devuelve un array de arrays con los campos de la entidad en el siguiente orden:
     *      primero los ids 
     *      luegos las claves foraneas
     *      y luego los demas campos. 
     * La clave de array principal son los nombres de los campos, cuyo elemento es un
     * array con el nombre del campo, el tipo de dato del campo, tipo de constrain y la
     * longitd del mismo. Si el campo es una relacion a una entidad, el tipo contendra 
     * la ruta de ubicacion de la Entidad Acme\Bundle\Entity\Clase
     */
    public function getCampos($em, $entidad,$nivel=null){
        if($nivel==null) $nivel=0;
        if($nivel==2) return (null);
        $campos = array();
        $index = 0;
        $metadata = $em->getClassMetadata('FraterSoft\PiaWebBundle\Entity\\' . $entidad);
        $campos['entity']='FraterSoft\PiaWebBundle\Entity\\' . $entidad;
        $IdentifierColumnNames=$metadata->getIdentifierColumnNames();
        /** Se llena el array con los campos de clave primaria */
        for($i=0;$i<count($IdentifierColumnNames);$i++){
            $campos[$IdentifierColumnNames[$i]] = array(
                'nombre'=>$IdentifierColumnNames[$i],
                'tipo'=>$metadata->getTypeOfColumn($IdentifierColumnNames[$i]),
                'constraint'=>'primarykey',
                'longitud'=>null,
            );
        }

        $FieldNames=$em->getClassMetadata('FraterSoft\PiaWebBundle\Entity\\' . $entidad)->getColumnNames();
        //Se llena el array con el restp de los campos
        
        for($i=count($IdentifierColumnNames);$i<count($FieldNames);$i++){
            $campos[$FieldNames[$i]] = array(
                'nombre'=>$FieldNames[$i],
                'tipo'=>$metadata->getTypeOfColumn($FieldNames[$i]),
                'constraint'=>$metadata->isUniqueField($FieldNames[$i])?'unique':'',
                'longitud'=>$metadata->getTypeOfColumn($FieldNames[$i])=='string'?$metadata->getFieldMapping($FieldNames[$i])['length']:null,
            );
        }        
        
        $AssociationNames=$em->getClassMetadata('FraterSoft\PiaWebBundle\Entity\\' . $entidad)->getAssociationNames();        
        /** Se llena el array con los campos foraneos */
        for($i=0;$i<count($AssociationNames);$i++){
                $targetEntity=$metadata->getAssociationMapping($AssociationNames[$i])['targetEntity'];
                $campos[$AssociationNames[$i]] = array(
                    'nombre'=>$AssociationNames[$i],
                    'tipo'=>$metadata->getAssociationMapping($AssociationNames[$i])['targetEntity'],
                    'constraint'=>'foreingkey',
                    'longitud'=>null,
                    'tipoRelacion'=>$metadata->getAssociationMapping($AssociationNames[$i])['type'],
                    'campos'=>$this->getCampos($em,substr($targetEntity,strlen('FraterSoft\PiaWebBundle\Entity\\'),strlen($targetEntity)),$nivel+1)
                );
        }
        
        return($campos);
    }
    public function addBotonRegresar($form,$url){

        $form->add('regresar', 'button', array(
            'label' => 'Regresar',
            'attr' => array('onclick'=>'window.location.href="'.$url.'";')
        ));
    }
    
    public function addFormasPago($formulario,$evento,$idmoneda=null){
        //Agrega las formas de pago del evento
        $em = $this->getDoctrine()->getManager();
        $formasdepagoarray = array();
        $formasdepago = $em->getRepository('FraterSoftPiaWebBundle:Formaspagoevento')
                ->listarPublicos($evento->getId(),$idmoneda);
        foreach ($formasdepago as $formadepago) {
            if ($formadepago->getIdFormapago()->getNombre()) {
                $formasdepagoarray[$formadepago->getIdFormapago()->getId()] = $formadepago->getIdFormapago()->getNombre();
            }
        }
        if ($formasdepagoarray){
            if(count($formasdepagoarray)==1)
                $formulario
                        ->add('idformapago', 'choice', array(
                            'label' => 'Forma de Pago',
                            'choices' => $formasdepagoarray,
                            'required' => true,
                ));
            else
                $formulario
                        ->add('idformapago', 'choice', array(
                            'label' => 'Forma de Pago',
                            'choices' => $formasdepagoarray,
                            'required' => true,
                            'empty_value' => 'Seleccione Forma de Pago',
                ));
            return($formasdepago);
        }
        else {
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                        'texto' => "No se han configurado las Formas De Pago para este Evento",
                        'tema' => $evento->getTema()
            ));            
        }        
    }      
    
    public function clearFormPago($formulario){
        foreach ($formulario->all() as $child) {
            $formulario->remove($child->getName());
        }
    }
    
    public function seleccionaCategoriasCampeonato($idcampeonato, $competidor) {
        $criterios = array();
        $em = $this->getDoctrine()->getManager();
        $atributoscriterios = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')->atributosCriterios($idevento);
        if ($atributoscriterios) {
            foreach ($atributoscriterios as $atruibutocriterio) {
                $valor = $competidor->getValorCampo(strtolower($atruibutocriterio->getIdatributo()->getNombre()));
                if ($valor) {
                    $criterios[strtolower($atruibutocriterio->getIdatributo()->getNombre())] = $valor;
                }
            }

            $categoriasselect = new ArrayCollection();

            $categorias = $em->getRepository('FraterSoftPiaWebBundle:Categoria')->listaCategoriasCampeonato($idcampeonato);

            foreach ($categorias as $categoria) {
                $reglas = $em->getRepository('FraterSoftPiaWebBundle:CategoriaReglas')->findBy(array(
                    'idcategoria' => $categoria->getId()
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
                    $categoriasselect->add($categoria);
                    $parametrok = false;
                }
            }
            return $categoriasselect;
        } else {
            return null;
        }
    }    
    
    public function actualizaEntity($em,$origen,$destino)    {
        $accessor = PropertyAccess::createPropertyAccessor();  
        $rutaenity=$em->getClassMetadata(get_class($origen))->getName();
        if($em->getClassMetadata(get_class($origen))->getName()==$em->getClassMetadata(get_class($destino))->getName()){
            $camposentity=$this->getCampos($em, substr($rutaenity,strrpos($rutaenity,"\\")+1,strlen($rutaenity)));
            $reg_actualizados=0;
            foreach($camposentity as $campo => $valores){
                if($campo!="id" && $campo!="entity")
                    $accessor->setValue($destino, $campo, $accessor->getValue($origen,$campo));
                    $reg_actualizados++;
                }
            return $reg_actualizados;
        }
        else 
            return false;
    }
    
    public function OpcionesEstatus(){
        return( array(
                'choices' => array(
                    0 => 'Inactivo', 
                    1 => 'Activo',
                 ),
            )
        );
    }
    
    public function getStrSqlCampos($idevento,$campos){
        $em = $this->getDoctrine()->getManager();
        $atributos = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')->arrayEtiquetasAtributos($idevento,$campos);
        $strcampos="";
        $cantidad_atributos=count($atributos);
        $i=1;
        foreach($atributos as $atributo){
            if(is_null($atributo['etiqueta']))
                $strcampos.="co." . $atributo['atributo'] . ",";
            else
                $strcampos.="co." . $atributo['atributo'] . ' as ' . $atributo['etiqueta'] . ",";
        }
        return($strcampos);
    }
    
    public function getStrSqlCriterios($idevento){
        $atributoscriterios = $em->getRepository('FraterSoftPiaWebBundle:EventoAtributos')->atributosCriterios($idevento);
        $criterios="";
        $cantidad_criterios=count($atributoscriterios);
        $i=1;
        foreach ($atributoscriterios as $atruibutocriterio) {
            $criterios.= "co." . $atruibutocriterio->getidatributo()->getNombre();
            $criterios.=($i++<$cantidad_criterios)?",":"";
        }
        return($criterios);
    }
    
    public function EnviarConfirmacion($inscritos,$em){
        //Envia los correo a los inscritos conciliados
        $num_notifiaciones = 0;        
        foreach ($inscritos as $inscrito) {

            $emails = array();
            if(!is_null($inscrito->getIdpia()->getEmail()) && filter_var($inscrito->getIdpia()->getEmail(), FILTER_VALIDATE_EMAIL))
                array_push($emails,$inscrito->getIdpia()->getEmail());
            if(!is_null($inscrito->getIdpia()->getEmailpersonal()) && filter_var($inscrito->getIdpia()->getEmailpersonal(), FILTER_VALIDATE_EMAIL))
                array_push($emails,$inscrito->getIdpia()->getEmailpersonal()); 
            
            //if($inscrito->getNotificado()!=true){ //OJO MOSCA, VALIDAR ESTO PARA QUE NO QUE CONSUMAN LOS RECURSOS AL REENVIAR MUCHAS VECES
            if(count($emails)>=1){
                $subject = is_null($inscrito->getNumero())?
                        "Confirmacion de Inscripcion " . $inscrito->getIdevento()->getNombre():
                        "Dorsal Numero " . $inscrito->getNumero() . ". " . $inscrito->getIdevento()->getNombre();            
                $mailer = $this->get('app.mail_controller');
                $mailer->enviarConfirmacion(
                        $subject, 
                        $emails,
                        $this->renderView('FraterSoftPiaWebBundle:Inscrito:emailok.html.twig', array('inscrito' => $inscrito))
                );
                $num_notifiaciones++;
                $inscrito->setNotificado(true);
            }
        }
        $em->flush();
        return($num_notifiaciones);
    }
    
    public function EnviarConfirmacionPreinscritos($inscritos,$em){
        //Envia los correo a los inscritos conciliados
        $num_notifiaciones = 0;        
        if($inscritos[0]->getIdevento()->getProceso()==1)
            $templateemail='FraterSoftPiaWebBundle:Inscrito:email.html.twig';
        else{
            $this->MonedasEvento($em,$default_moneda,$inscritos[0]->getIdevento());
            $templateemail='FraterSoftPiaWebBundle:Inscrito:emailproceso2.html.twig';
        }
        foreach ($inscritos as $inscrito) {
            if($inscrito->getIdevento()->getProceso()==1)
                $parametros=array('entity' => $inscrito);
            else{
                $precio=$this->buscarPrecio(
                            $inscrito->getIdevento()->getid(), 
                            $inscrito->getIdcompetencia()->getid(), 
                            $inscrito->getIdcategoria()->getid(),
                            $default_moneda
                        );                        
                $parametros=array('entity' => $inscrito,'precios'=>$precio);
            }
            $emails = array();
            if(!is_null($inscrito->getIdpia()->getEmail()) && filter_var($inscrito->getIdpia()->getEmail(), FILTER_VALIDATE_EMAIL))
                array_push($emails,$inscrito->getIdpia()->getEmail());
            if(!is_null($inscrito->getIdpia()->getEmailpersonal()) && filter_var($inscrito->getIdpia()->getEmailpersonal(), FILTER_VALIDATE_EMAIL))
                array_push($emails,$inscrito->getIdpia()->getEmailpersonal()); 
            
            //if($inscrito->getNotificado()!=true){ //OJO MOSCA, VALIDAR ESTO PARA QUE NO QUE CONSUMAN LOS RECURSOS AL REENVIAR MUCHAS VECES
            if(count($emails)>=1){
                $subject = is_null($inscrito->getNumero())?
                        "Confirmacion de Pre-Inscripcion " . $inscrito->getIdevento()->getNombre():
                        "Dorsal Numero " . $inscrito->getNumero() . ". " . $inscrito->getIdevento()->getNombre();            
                $mailer = $this->get('app.mail_controller');
                $mailer->enviarPreinscripcion(
                        $subject, 
                        $emails,
                        $this->renderView($templateemail, $parametros)
                );
                $num_notifiaciones++;
                $inscrito->setNotificado(true);
            }
        }
        $em->flush();
        return($num_notifiaciones);
    }

    public function MonedasEvento($em,&$default_moneda,$evento){
        $monedas = $em->getRepository('FraterSoftPiaWebBundle:Moneda')->MonedasEnEvento($evento->getId());
        $arraymonedas=array();
        //print_r();
        foreach ($monedas as $moneda){
            if($moneda['id']==$evento->getIdorganizador()->getIdmoneda()->getId())
                $default_moneda=$moneda['id'];
            $arraymonedas[$moneda['id']]=$moneda['nombre'];
        }        
        return($arraymonedas);
    }
    
    public function preciosArray(&$emptyvalue_precio,$idevento,$idcompetencia){
        $preciosarray = array();
        $precios = $this->buscarPrecio($idevento, $idcompetencia, null);
        foreach ($precios as $precio) {
            $preciosarray[$precio['precio']] = $precio['texto'].' '.$precio['moneda'].' '.$precio['precio'];
        }
        $emptyvalue_precio = (count($preciosarray) > 1)?'Seleccione un Precio':null;
        return($preciosarray);
    }

    public function ValidarEmail($email,$emailpersonal){
        $emails = array();
        if(!is_null($email) && filter_var($email, FILTER_VALIDATE_EMAIL))
            array_push($emails,$email);
        if(!is_null($emailpersonal) && filter_var($emailpersonal, FILTER_VALIDATE_EMAIL))
            array_push($emails,$emailpersonal);
        return($emails);
    }
    
    public function buscarFormasPago($idevento,$idmoneda){
        //Agrega las formas de pago del evento
        $em = $this->getDoctrine()->getManager();
        $formasdepagoarray = array();
        $formasdepago = $em->getRepository('FraterSoftPiaWebBundle:Formaspagoevento')
                ->listarPublicos($idevento,$idmoneda);
        foreach ($formasdepago as $formadepago) {
            if ($formadepago->getIdFormapago()->getNombre()) {
                $formasdepagoarray[$formadepago->getIdFormapago()->getId()] = [
                    'nombre'=>$formadepago->getIdFormapago()->getNombre(),
                    'parametros'=>$formadepago->getIdFormapago()->getParametros()
                ];
            }
        }
        return($formasdepagoarray);
    }      
    
}
