<?php

namespace FraterSoft\PiaWebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class PagoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder          
            ->add('info',null,array(
                'mapped' => false,
                'label'=>'INFORMACION DE PAGO',
                'label_attr'=>array('class'=>'group_fields'),
                'attr'=> array('style'=>'display:none'),
            ))
            ->add('message',null,array(
                'mapped' => false,
                'label'=>'USTED POSEE UN CREDITO DE X Para este evento. El precio de lainscripcion sera ',
                // 'label_attr'=>array('class'=>'message_pago'),
                'attr'=> array('style'=>'display:none'),
            ))            
            ->add('idmoneda')
            ->add('precio','text', array(
                'read_only' => true,
            ))
            ->add('monto','text', array(
                'label'=>'Total a Pagar',
                'label_attr'=>array('style'=>'display:none'),
                'read_only' =>'true',
                'attr'=>array('style'=>'display:none'),
            ))
            ->add('idformapago')             
            ->add('idbanco','entity', array(
                'class' => 'FraterSoftPiaWebBundle:Banco',
                'empty_value' => 'Seleccione un Banco',
                'label' => 'Banco de donde pago',
                'query_builder' => function (EntityRepository $b) {
                    return $b->createQueryBuilder('b')
                            ->where('b.idpais=:idpais')
                            ->setParameter('idpais', 115);
                },
            ))                
            ->add('idpagador')
            ->add('nombrepagador')
            ->add('correopagador')
            ->add('idcuenta')
            ->add('texto','hidden')                
            ->add('moneda','hidden')
            ->add('referencia','text', array(
                'label'=>'N&uacute;mero Operaci&oacute;n ',
                'label_attr' => array('id' => 'label_pago_referencia')
            ))
            ->add('comprobante','hidden')
            ->add('fechahora','datetime', array(
                'label'=>false,
                'widget' => 'single_text',
                'data' => new \DateTime('now'),
                'attr'=> array('style'=>'display:none'),
            ))                
            ->add('conciliado','hidden')
            //->add('conciliadoel','hidden')
            ->add('conciliadoel','datetime', array(
                'widget' => 'single_text',
                'data' => new \DateTime('now'),
                'attr'=> array('style'=>'display:none'),
                'label'=>false,
            ))
            ->add('fechapago','date',array(
                'attr' => ['class' => 'fechaESP'],
                'label'=>'Fecha del Pago',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FraterSoft\PiaWebBundle\Entity\Pago'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fratersoft_piawebbundle_pago';
    }
}
