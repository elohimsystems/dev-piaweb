<?php

namespace FraterSoft\PiaWebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CompetenciaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idevento')
            ->add('descripcion')
            ->add('fechacierre', null, array(
                'label'=>'Fecha y Hora',
                'widget' => 'single_text',
                'format' => 'dd/MM/y HH:mm',
            ))
            ->add('cupomaximo')
            ->add('grupal', null, array(
                'mapped' => false,                
                'label'=>'Inscripcion Grupal?, ingrese la cantidad de:',
                'label_attr'=>array('class'=>'group_fields'),
                'attr'=> array('style'=>'display:none'),
            ))
            ->add('grupo', new GrupoType(),array(
                'label_attr' => array('style'=>'display:none'),
            ))                
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FraterSoft\PiaWebBundle\Entity\Competencia'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fratersoft_piawebbundle_competencia';
    }
}
