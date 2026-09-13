<?php

namespace FraterSoft\PiaWebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('externo', null, array(
                'label' => 'Inscripciones Externas',
                'required' => false,
            ))
            ->add('logo','hidden')
            ->add('nombre')
            ->add('fecha', null, array(
                'label'=>'Fecha y Hora',
                'widget' => 'single_text',
                'format' => 'dd/MM/y HH:mm',
            ))
            ->add('lugar')
            ->add('mapalugar','hidden')                
            ->add('proceso','choice',array(
                'label'=>'Proceso',
                'choices' => array(
                    1 => 'Pago -> PreInscripcion', 
                    2 => 'PreInscripcion -> Pago'
                 ),
                'required' => true,
                'empty_value' => 'Seleccione Tipo de Evento'
            ))                
            ->add('registropago','choice',array(
                'label'=>'Registro del pago',
                'choices' => array(
                    1 => 'Registro por el Participantes', 
                    2 => 'Registro por el Organizador'
                 ),
                'required' => true,
                'empty_value' => 'Seleccione Tipo de Evento'
            ))                
            ->add('fechainicio', null, array(
                'label'=>'Inicio Inscripciones',
                'widget' => 'single_text',
                'format' => 'dd/MM/y HH:mm',
                'required' => true,
            ))
            ->add('fechacierre', null, array(
                'label'=>'Cierre Inscripciones',
                'widget' => 'single_text',
                'format' => 'dd/MM/y HH:mm',
            ))            
            ->add('tipo','choice',array(
                'label'=>'Tipo',
                'choices' => array(
                    'U' => 'Unico', 
                    'C' => 'Campeonato'
                 ),
                'required' => true,
                'empty_value' => 'Seleccione Tipo de Evento'
            ))
            ->add('idcampeonato','entity',array(
                'class' => 'FraterSoftPiaWebBundle:Campeonato',
                'label'=>'Campeonato',
                'required' => false,
                'empty_value' => 'Seleccione Campeonato',
            ))
            ->add('cupocontrol', null, array(
                'label'=>'Control de Cupos',
            ))
            ->add('cupomaximo', null, array(
                'label'=>'Cupo Maximo',
            ))
            ->add('criteriocalculoedad','choice',array(
                'label'=>'Criterio Edad',
                'choices' => array(
                    '1' => 'Edad Calendario', 
                    '2' => 'Edad al Evento',
                 ),
                'required' => true,
                'empty_value' => 'Seleccione el Criterio'
            ))               
            ->add('rutamapa','hidden')
            ->add('rutaperfil','hidden')
            ->add('rutavideo','hidden')
            ->add('activo')
            ->add('emailcontacto', null, array(
                'label'=>'Email Contacto',
            ))
            ->add('zonahoraria')
            ->add('idorganizador')
            ->add('url','textarea', array(
                'label' => 'Url' ,
                'max_length' => 255 ,
                'required' => false,
                'attr' => array(
                    'cols' => '25',
                    'rows' => '4',
                    'title' => 'URL donde se realizaran las inscripciones del evento',),
            ))
            ->add('clasificacion','choice',array(
                'label'=>'Clasificacion',
                'choices' => array(
                    'RUNNING' => 'Running', 
                    'TRAIL' => 'Trail',
                    'CAMINATA' => 'Caminata',
                    'MOUNTAINBIKE' => 'MountainBike',
                    'RUTA' => 'Ruta',                    
                    'BMX' => 'Bmx',
                    'PISCINA' => 'Piscina',
                    'AGUAS ABIERTAS' => 'Aguas Abiertas',
                    'TRIATLON' => 'Triatlon',
                    'DUATLON' => 'Duatlon',
                 ),
                'required' => true,
                'empty_value' => 'Seleccione el Criterio'
            ))
            ->add('inforepresentante', null, array(
                'label'=>'Info Representante',
                'attr' => array ('title' => 'Activa la solicitud del Representante en el Formulario de Inscripcion'),
            ))
            ->add('controlparental', null, array(
                'label'=>'Control Parental',
                'required' => false,
            ))
            ->add('multicompetencia', null, array(
                'label'=>'Multi Competencia',
                'required' => false,
                'attr' => array('title' => 'Permite que el competidor se inscriba en varias Modalidades/Competencias en un solo registro'),
            ))
            ->add('titulocompetencias', null, array(
                'label'=>'Titulo de Competencias',
                'required' => false,
                'attr' => array('title' => 'Texto que se mostrara como titulo del campo de seleccion de competencias en el formulario de inscripcion. Si se deja vacio se usa "Competencia"'),
            ))
            ->add('pais','entity', array(
                'class' => 'FraterSoftPiaWebBundle:Pais',
                'mapped'=>false,
                'empty_value' => 'Seleccione el Pais'
            ))
            ->add('idestado','entity', array(
                'class' => 'FraterSoftPiaWebBundle:Estado',
                'label' => 'Estado',
                'empty_value' => 'Seleccione el Estado'
            ))
            ->add('localidad', null, array(
                'label'=>'Localidad',
                'required' => true,
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FraterSoft\PiaWebBundle\Entity\Evento'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fratersoft_piawebbundle_evento';
    }
}
