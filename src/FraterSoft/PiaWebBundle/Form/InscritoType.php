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
            ->add('idcompetencia')
            ->add('numero')
            ->add('idpia', new CompetidorType(),array(
                'label' => ' ',
            ))
//            ->add('fechahora')
//            ->add('punto','text',array(
//                'data'=>'WEB',
//                'attr'=> array('style'=>'display:none'),
//                'label'=>' ',
//            ))
            ->add('equipo','hidden')
            ->add('precio')
            ->add('idcategoria')
            ->add('idpago', new PagoType(), array(
                'label'=>' ',
            ))
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
