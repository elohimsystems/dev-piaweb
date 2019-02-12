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
    
    public function buscarPrecio($idevento, $idcompetencia, $idcategoria) {
        $precioevento = new Preciosevento();
        $preciocompetencia = new Precioscompetencia();
        $preciocategoria = new Precioscategoria();

        $em = $this->getDoctrine()->getManager();

        $precio = new ArrayCollection();

        if($idcategoria)
            $precio = $em->getRepository('FraterSoftPiaWebBundle:Precioscategoria')
                    ->BuscaPreciosCategoria($idcategoria);            
        if($idcompetencia && count($precio)==0)
            $precio = $em->getRepository('FraterSoftPiaWebBundle:Precioscompetencia')
                    ->BuscaPreciosCompetencia($idcompetencia);
        if(count($precio)==0)
            $precio = $em->getRepository('FraterSoftPiaWebBundle:Preciosevento')
                    ->BuscaPreciosEvento($idevento);
        return $precio;
    }   
    
    //Funcion que genera el boton de pago para 123pago, hay que estar pendiente que el numero
    //de pedido no exedad los 45 caracteres
    public function generarBoton123Pago($entity) {

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
        //$porcentaje=($entity->getIdevento()->getId()==21)?0.15:0.10;
        $porcentaje=0.15;
        //$porcentaje=$incremento / 100;

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
    public function getCampos($em, $entidad){
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
        $AssociationNames=$em->getClassMetadata('FraterSoft\PiaWebBundle\Entity\\' . $entidad)->getAssociationNames();
        /** Se llena el array con los campos foraneos */
        for($i=0;$i<count($AssociationNames);$i++){
            $campos[$AssociationNames[$i]] = array(
                'nombre'=>$AssociationNames[$i],
                'tipo'=>$metadata->getAssociationMapping($AssociationNames[$i])['targetEntity'],
                'constraint'=>'foreingkey',
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
        return($campos);
    }
    public function addBotonRegresar($form,$url){

        $form->add('regresar', 'button', array(
            'label' => 'Regresar',
            'attr' => array('onclick'=>'window.location.href="'.$url.'";')
        ));
    }
    
    public function addFormasPago($formulario,$evento){
        //Agrega las formas de pago del evento
        $em = $this->getDoctrine()->getManager();
        $formasdepagoarray = array();
        $formasdepago = $em->getRepository('FraterSoftPiaWebBundle:Formaspagoevento')
                ->listar($evento->getId());
        foreach ($formasdepago as $formadepago) {
            if ($formadepago['nombre']) {
                $formasdepagoarray[$formadepago['id']] = $formadepago['nombre'];
            }
        }
        if ($formasdepagoarray)
            $formulario
                    ->add('tipo', 'choice', array(
                        'label' => 'Forma de Pago',
                        'choices' => $formasdepagoarray,
                        'required' => true,
                        'empty_value' => 'Seleccione Forma de Pago',
            ));
        else {
            return $this->render('FraterSoftPiaWebBundle:Default:mensaje.html.twig', array(
                        'url' => $this->generateUrl('competidor_find', array('idevento' => $idevento)),
                        'texto' => "No se han configurado las Formas De Pago para este Evento",
                        'tema' => $evento->getTema()
            ));            
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
}
