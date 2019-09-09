<?php

namespace FraterSoft\PiaWebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CompetidorTypeOld extends AbstractType
{
   
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('iddocumento')
            ->add('nombre','text',array( 
                'required' => true,
            ))
            ->add('apellido','text',array(
                'required' => true,
            ))
            ->add('foto','hidden')
            ->add('fechanacimiento','date',array(
                'attr' => ['class' => 'fechaESP'],
                'label'=>'Fecha de Nacimiento',
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
            ))
            ->add('sexo','choice',array(
                'choices' => array('M' => 'Masculino', 'F' => 'Femenino'),
                'required' => true,
                'empty_value' => 'Seleccione Sexo',
            ))
            ->add('equipo','text',array(
                'read_only' => true,
            ))
            ->add('idrepresentante')                
            ->add('nombrerepresentante')                
            ->add('email','text',array(
                'required' => true,
            ))
            ->add('edad')
            ->add('peso')
            ->add('condicion')
            ->add('telefono')
            ->add('idestado','entity',array(
                'class' => 'FraterSoftPiaWebBundle:Estado',
                'label'=>'Estado',
                'empty_value' => 'Seleccione Estado',
            ))
            ->add('idpais')
            ->add('tallafranela')
            ->add('certificado','hidden')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FraterSoft\PiaWebBundle\Entity\Competidor'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fratersoft_piawebbundle_competidor';
    }
}
