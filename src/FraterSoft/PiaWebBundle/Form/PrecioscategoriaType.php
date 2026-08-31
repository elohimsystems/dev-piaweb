<?php

namespace FraterSoft\PiaWebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PrecioscategoriaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idcategoria')
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
            ->add('idmoneda')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FraterSoft\PiaWebBundle\Entity\Precioscategoria'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fratersoft_piawebbundle_precioscategoria';
    }
}
