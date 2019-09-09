<?php

/**
 * Description of EnviarSolicitudesCommand: Si se pasa al comando los parametros usuario y correo, se buscan las solicitudes 
 * en los estatus 'Editada','Creada','Revisada','Por Ajustar','Por Cancelar' para los centros de costos que tenga asignada el 
 * usuario. Si no se pasan los parametros se hace el mismo proceso pero para los usuarios con ROL GESTOR.
 * @author Freddy Garcia
 */


namespace FraterSoft\PiaWebBundle\Command;

use FraterSoft\PiaWebBundle\Controller\InscritoController;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;



class GuardaXMLCommand extends ContainerAwareCommand {

    //Configura el nombre y la descripcion del comando
    protected function configure() {
        $this
                ->setName('PiaWebBundle:GuardaXML')
                ->setDescription('Almacena en la base de datos, la informacion leida de un xml')
                //->addArgument('usuario', InputArgument::OPTIONAL, 'Usuario a buscar solicitudes')
                //->addArgument('email', InputArgument::OPTIONAL, 'Email donde seran enviadas el listado de las solicitudes')
        ;
    }
    
    //Ejecuta las acciones del comando
    protected function execute(InputInterface $input, OutputInterface $output) {
        $crawler = new Crawler();
        $ic = new InscritoController();
        $crawler->addXmlContent(file_get_contents('c:\ftpfiles\tblInscritos.xml'));
        $raiz = $crawler->filter( 'dataroot' );
        $grupo = $crawler->filter('dataroot')->children();
        
        $ic.salvax();
        //$inscrito.setNumero(10);
        foreach ($grupo as $domElement) {
            //$inscrito.setIdevento($domElement->getElementsByTagName( "IdEvento" )->item(0)->nodeValue);
            //$inscrito.setIdpia($domElement->getElementsByTagName( "IdPia" )->item(0)->nodeValue);
            //$inscrito.setNumero($domElement->getElementsByTagName( "Numero" )->item(0)->nodeValue);
        }
        print_r($inscrito);
    }

}
