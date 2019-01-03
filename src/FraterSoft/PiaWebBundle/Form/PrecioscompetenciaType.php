<?php

namespace FraterSoft\PiaWebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PrecioscompetenciaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idcompetencia')
            ->add('precio')
            ->add('cantidad')
            ->add('hasta', null, array(
                'label'=>'Hasta',
                'widget' => 'single_text',
                'format' => 'dd/MM/y HH:mm',
            ))
            ->add('prioridad')
            ->add('texto')
            ->add('imagen')
            ->add('moneda')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FraterSoft\PiaWebBundle\Entity\Precioscompetencia'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fratersoft_piawebbundle_precioscompetencia';
    }
}
