<?php

namespace FraterSoft\PiaWebBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsultarTasasCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tasas:consultar')
            ->setDescription('Consulta las tasas de todas las monedas activas y guarda el histórico');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        // 1. Obtener todas las monedas activas
        $monedas = $em->getRepository('FraterSoftPiaWebBundle:Moneda')->findBy(['estatus' => 1]);

        if (!$monedas) {
            $output->writeln("No hay monedas activas para consultar.");
            return 0;
        }

        foreach ($monedas as $moneda) {

            $url = $moneda->getUrlConsultaTasaOficial();
            if (!$url) {
                $output->writeln("La moneda " . $moneda->getNombre() . " no tiene URL de consulta configurada.");
                continue;
            }
            $output->writeln("Consultando: " . $moneda->getNombre() . " => " . $url);

            // 2. Ejecutar cURL
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $result = curl_exec($ch);

            if ($result === false) {
                $output->writeln("Error cURL: " . curl_error($ch));
                curl_close($ch);
                continue;
            }

            curl_close($ch);

            // 3. Decodificar JSON
            $data = json_decode($result, true);

            if (!is_array($data)) {
                $output->writeln("Respuesta inválida para " . $moneda->getNombre());
                continue;
            }

            // 4. Extraer valor (ajusta según tu API)
            $valor = isset($data['promedio']) ? $data['promedio'] : null;

            if ($valor === null) {
                $output->writeln("No se encontró 'promedio' en la respuesta.");
                continue;
            }

            $publicadoEl = new \DateTime();
            $existe = $em->getRepository('FraterSoftPiaWebBundle:HistoricoTasas')->findOneBy([
                'publicadoel' => $publicadoEl,
                'idmoneda' => $moneda->getId(),
            ]);
            if ($existe) {
                $output->writeln('La tasa ya existe para esta fecha y moneda.');
            } else {
                // 5. Guardar en histórico
                $historicotasas = new \FraterSoft\PiaWebBundle\Entity\HistoricoTasas();
                $historicotasas->setMonto($valor);
                $historicotasas->setPublicadoel($publicadoEl);
                $historicotasas->setIdmoneda($moneda->getId());

                $em->persist($historicotasas);
                $em->flush();

                $output->writeln("Guardado: " . $moneda->getNombre() . " => " . $valor);
            }
        }

        $output->writeln("Proceso completado.");
        return 0;
    }
}