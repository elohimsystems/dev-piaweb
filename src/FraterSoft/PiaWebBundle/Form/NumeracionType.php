<?php

namespace FraterSoft\PiaWebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NumeracionType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idevento')            
            ->add('tipo','choice',array(
                'label'=>'Tipo',
                'choices' => array(
                    1 => 'Secuencial', 
                    2 => 'Externa'
                 ),
                'required' => true,
                'empty_value' => 'Seleccione Tipo de Numeracion'
            ))                       
            ->add('atributo')
            ->add('automatica')
            ->add('inicio')
            ->add('fin')
            ->add('siguiente')            
            ->add('ordenarpor')            
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FraterSoft\PiaWebBundle\Entity\Numeracion'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fratersoft_piawebbundle_numeracion';
    }
}
