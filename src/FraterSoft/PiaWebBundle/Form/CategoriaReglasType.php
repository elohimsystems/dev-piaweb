<?php

namespace FraterSoft\PiaWebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoriaReglasType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idcategoria')
            ->add('accion','choice',array(
                'label'=>'Accion',
                'choices' => array(
                    1 => 'Sumar', 
                    2 => 'Contar'
                 ),
                'empty_value' => 'Seleccione la Accion'
            ))
            ->add('atributo')
            ->add('cantidad')
            ->add('tipo')
            ->add('valor1')
            ->add('valor2')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FraterSoft\PiaWebBundle\Entity\CategoriaReglas'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fratersoft_piawebbundle_categoriareglas';
    }
}
