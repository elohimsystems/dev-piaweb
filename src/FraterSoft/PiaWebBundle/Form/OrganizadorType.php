<?php

namespace FraterSoft\PiaWebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrganizadorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('abreviado')
            ->add('logo', 'file', array(
                'required' => false,
                'mapped' => false,
                'label' => 'Logo',
            ))
            ->add('email')
            ->add('rif')
            ->add('contacto')
            ->add('telefonocontacto')
            ->add('emailcontacto')
            ->add('idmoneda', 'entity', array(
                'class' => 'FraterSoftPiaWebBundle:Moneda',
                'empty_value' => 'Seleccione una moneda',
                'required' => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FraterSoft\PiaWebBundle\Entity\Organizador'
        ));
    }

    public function getName()
    {
        return 'fratersoft_piawebbundle_organizador';
    }
}
