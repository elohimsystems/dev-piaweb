<?php

namespace FraterSoft\PiaWebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CompetidorType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('foto')
            ->add('iddocumento')
            ->add('nombre')
            ->add('apellido')
            ->add('fechanacimiento','date',array(
                'attr' => ['class' => 'fechaESP'],
                'label'=>'Fecha de Nacimiento',
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
            ))
            ->add('sexo')
            ->add('edad')
            ->add('peso')
            ->add('estatura')
            ->add('gruposanguineo')
            ->add('estadocivil')
            ->add('alergicoa')
            ->add('direccion')
            ->add('idpais')
            ->add('idestado')
            ->add('localidad')
            ->add('tallafranela')
            ->add('tallamono')
            ->add('tallazapatos')
            ->add('tallachaqueta')
            ->add('tallachemis')
            ->add('tallaguantes')
            ->add('equipo')
            ->add('entrenador')
            ->add('idrepresentante')                
            ->add('nombrerepresentante')                
            ->add('emailpersonal')
            ->add('email')
            ->add('telefono')
            ->add('fileiddocumento')
            ->add('filepartidanacimiento')
            ->add('filecertificadodeportivo')
            ->add('textodorsal')
            ->add('telefonoemergencia')
            ->add('seguromedico')
            ->add('discapacitado')
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
