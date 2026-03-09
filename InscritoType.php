<?php

namespace FraterSoft\PiaWebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class InscritoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idevento')
            ->add('numero')
            ->add('idpia', new CompetidorType(),array(
                'label' => '',
            ))
            ->add('equipo','hidden')
            ->add('idcompetencia')
            ->add('idcategoria')
            ->add('pagos', 'collection', array(
                'type' => new PagoType(),
                'allow_add'    => true,
                'by_reference' => false,
                'attr' => array('class'=>'pagos'),
                'label_attr' => array('style'=>'display:none;'),
            ))
            
//            ->add('info',null,array(
//                'mapped' => false,
//                'label'=>'INFORMACION DE PAGO',
//                'label_attr'=>array('class'=>'group_fields'),
//                'attr'=> array('style'=>'display:none'),
//            ))
//            ->add('precio')
//            ->add('idpago', new PagoType(), array(
//                'label'=>false,
//            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FraterSoft\PiaWebBundle\Entity\Inscrito'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fratersoft_piawebbundle_inscrito';
    }
}
