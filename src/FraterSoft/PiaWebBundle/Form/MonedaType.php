<?php

namespace FraterSoft\PiaWebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MonedaType extends AbstractType
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
            ->add('codigolocal', null, array(
                'label' => 'Codigo (texto que se muestra al competidor)',
                'attr' => array('maxlength' => 5),
            ))
            ->add('codigointernacional', null, array(
                'label' => 'Codigo Internacional',
                'required' => false,
                'attr' => array('maxlength' => 5),
            ))
            ->add('estatus', 'choice', array(
                'label' => 'Estatus',
                'choices' => array(1 => 'Activo', 0 => 'Inactivo'),
                'required' => true,
            ))
            ->add('icono', null, array(
                'label' => 'Icono (nombre de archivo)',
                'required' => false,
            ))
            ->add('urlConsultaTasaOficial', null, array(
                'label' => 'URL Consulta Tasa Oficial',
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
            'data_class' => 'FraterSoft\PiaWebBundle\Entity\Moneda'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fratersoft_piawebbundle_moneda';
    }
}
