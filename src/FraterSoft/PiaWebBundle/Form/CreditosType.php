<?php

namespace FraterSoft\PiaWebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreditosType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cedula')
            ->add('monto')
            ->add('idevento')
            ->add('usadoel')
            ->add('disponible')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FraterSoft\PiaWebBundle\Entity\Creditos'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fratersoft_piawebbundle_creditos';
    }
}
