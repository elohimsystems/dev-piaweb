<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FraterSoft\PiaWebBundle\Entity\HistoricoTasas;
use FraterSoft\PiaWebBundle\Form\HistoricoTasasType;

/**
 * HistoricoTasas controller.
 *
 */
class HistoricoTasasController extends Controller
{
    public function consultaTasaOficialDiaAjaxAction() {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $precioselect = new ArrayCollection();

        $serializer = new Serializer($normalizers, $encoders);

        $idmoneda = $this->get('request')->query->get('idmoneda');

        $precio = 0;
        $precio = $this->buscarPrecio($idevento, $idcompetencia, $idcategoria,$idmoneda);

        if ($precio) {
            $precioselect->add($precio);
            $jsonContent = $serializer->serialize($precioselect, 'json');
            return new response($jsonContent);
        }
        return new response(0);
    }
}