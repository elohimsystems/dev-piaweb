<?php

namespace FraterSoft\PiaWebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array(
                'label' => 'Usuario',
            ))
            ->add('email', null, array(
                'label' => 'Correo',
            ))
            ->add('plainPassword', 'password', array(
                'label' => 'Contraseña',
                'mapped' => false,
                'required' => false,
                'always_empty' => true,
            ))
            ->add('roles', 'choice', array(
                'label' => 'Roles',
                'choices' => array(
                    'ROLE_USER' => 'Usuario',
                    'ROLE_ADMIN' => 'Administrador',
                    'ROLE_SUPER_ADMIN' => 'Super Administrador',
                ),
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ))
            ->add('enabled', null, array(
                'label' => 'Activo',
                'required' => false,
            ))
            ->add('idorganizador', 'entity', array(
                'class' => 'FraterSoftPiaWebBundle:Organizador',
                'label' => 'Organizador',
                'empty_value' => 'Sin organizador asociado',
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
            'data_class' => 'FraterSoft\PiaWebBundle\Entity\User',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fratersoft_piawebbundle_user';
    }
}
