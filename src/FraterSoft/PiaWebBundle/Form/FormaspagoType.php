<?php

namespace FraterSoft\PiaWebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FormaspagoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', null, array(
                'label' => 'Nombre',
            ))
            ->add('idmoneda', 'entity', array(
                'class' => 'FraterSoftPiaWebBundle:Moneda',
                'label' => 'Moneda',
                'empty_value' => 'Seleccione una moneda',
                'required' => false,
            ))
            ->add('status', 'choice', array(
                'label' => 'Estatus',
                'choices' => array(1 => 'Activo', 0 => 'Inactivo'),
                'required' => true,
            ))
            ->add('verificable', 'checkbox', array(
                'label' => 'Requiere verificacion del pago',
                'required' => false,
            ))
            ->add('icono', null, array(
                'label' => 'Icono (nombre de archivo)',
                'required' => false,
            ))
            ->add('parametros', 'textarea', array(
                'label' => 'Parametros (JSON de campos requeridos)',
                'required' => false,
            ))
            ->add('programa', null, array(
                'label' => 'Programa (clase de pasarela)',
                'required' => false,
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FraterSoft\PiaWebBundle\Entity\Formaspago'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fratersoft_piawebbundle_formaspago';
    }
}
