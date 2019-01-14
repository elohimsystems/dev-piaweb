<?php

namespace FraterSoft\PiaWebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PagoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('monto','text', array(
                'label'=>'Total a Pagar',
                'label_attr'=>array('style'=>'display:none'),
                'read_only' =>'true',
                'attr'=>array('style'=>'display:none'),
            ))
            ->add('moneda','hidden')
            ->add('tipo','choice',array(
                'label'=>'Forma de Pago',
                'choices' => array(
                    0 => 'Exonerado', 
                    1 => 'Depósito', 
                    2 => 'Transferencia',
                    3 => 'Tarjetas de Credito',
                    5 => 'Cortesia'),
                'required' => true,
                'empty_value' => 'Seleccione Forma de Pago'
            ))
            ->add('referencia','text', array(
                'label'=>'Número Operación ',
                'label_attr' => array('id' => 'label_pago_referencia')
            ))
            ->add('comprobante','hidden')
            ->add('banco','choice',array(
                'label'=>'Banco de donde pago',
                'choices' => array(
                    'BANCO INDUSTRIAL DE VENEZUELA' => 'BANCO INDUSTRIAL DE VENEZUELA', 
                    'BANCO VENEZUELA' => 'BANCO VENEZUELA',
                    'BANCO VENEZOLANO DE CREDITO' => 'BANCO VENEZOLANO DE CREDITO',
                    'BANCO MERCANTIL' => 'BANCO MERCANTIL',
                    'BANCO PROVINCIAL' => 'BANCO PROVINCIAL',
                    'BANCO DEL CARIBE C.A.' => 'BANCO DEL CARIBE C.A.',
                    'BANCO EXTERIOR' => 'BANCO EXTERIOR',
                    'BANCO OCCIDENTAL DE DESCUENTO' => 'BANCO OCCIDENTAL DE DESCUENTO',
                    'BANCO CORP BANCA' => 'BANCO CORP BANCA',
                    'BANCO CARONI' => 'BANCO CARONI',
                    'BANCO BANESCO' => 'BANCO BANESCO',
                    'BANCO SOFITASA' => 'BANCO SOFITASA',
                    'BANCO PLAZA' => 'BANCO PLAZA',
                    'TOTAL BANK C.A. BANCO COMERCIA' => 'TOTAL BANK C.A. BANCO COMERCIA',
                    'FONDO COMUN' => 'FONDO COMUN',
                    'BANDES' => 'BANDES',
                    '100% BANCO BANCO COMERCIAL C.A' => '100% BANCO BANCO COMERCIAL C.A',
                    'BANCO DEL TESORO' => 'BANCO DEL TESORO',
                    'BANCO AGRICOLA DE VENEZUELA' => 'BANCO AGRICOLA DE VENEZUELA',
                    'BANCRECER' => 'BANCRECER',
                    'MI BANCO' => 'MI BANCO',
                    'BANCO ACTIVO C.A.' => 'BANCO ACTIVO C.A.',
                    'BANCAMIGA,MICROFINANCIERO C.A.' => 'BANCAMIGA,MICROFINANCIERO C.A.',
                    'BANPLUS BANCO COMERCIAL' => 'BANPLUS BANCO COMERCIAL',
                    'BANCO BICENTENARIO' => 'BANCO BICENTENARIO',
                    'NOVO BCO S.A.BCO.UNIVERSAL,C.A' => 'NOVO BCO S.A.BCO.UNIVERSAL,C.A',
                    'CITIBANK C.A.' => 'CITIBANK C.A.',
                    'BANCO NACIONAL DE CREDITO' => 'BANCO NACIONAL DE CREDITO',
                    'INSTITUTO MUNICIPAL DE CREDITO' => 'INSTITUTO MUNICIPAL DE CREDITO',
                    'BANCO DELSUR' => 'BANCO DELSUR',
                    'OTRO' => 'OTRO'
                 ),
                'required' => true,
                'empty_value' => 'Seleccione un Banco',
            ))
            ->add('fechahora','datetime', array(
                'label'=>false,
                'widget' => 'single_text',
                'data' => new \DateTime('now'),
                'attr'=> array('style'=>'display:none'),
            ))                
            ->add('conciliado','hidden')
            ->add('conciliadoel','datetime', array(
                'widget' => 'single_text',
                'data' => new \DateTime('now'),
                'attr'=> array('style'=>'display:none'),
                'label'=>false,
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
