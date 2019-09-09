<?php

namespace FraterSoft\PiaWebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CampeonatoCompetidoresType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idcampeonato')
            ->add('idcompetidor')
            ->add('idcategoria')
            ->add('idclub')
            ->add('numero')
            ->add('afiliadoel', null, array(
                'label'=>'Afiliado el',
                'widget' => 'single_text',
                'format' => 'dd/MM/y HH:mm',
            ))              
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FraterSoft\PiaWebBundle\Entity\CampeonatoCompetidores'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fratersoft_piawebbundle_campeonatocompetidores';
    }
}
