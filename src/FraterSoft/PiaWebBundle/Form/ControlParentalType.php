<?php

namespace FraterSoft\PiaWebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ControlParentalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titulo_documento', null, array(
                'label' => 'Titulo del Documento',
                'required' => false,
            ))
            ->add('edad_control', 'integer', array(
                'label' => 'Edad de Control',
                'required' => false,
            ))
            ->add('mensaje', 'textarea', array(
                'label' => 'Mensaje',
                'required' => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FraterSoft\PiaWebBundle\Entity\ControlParental'
        ));
    }

    public function getName()
    {
        return 'fratersoft_piawebbundle_controlparental';
    }
}
