<?php

namespace FraterSoft\PiaWebBundle\EventListener;

use Symfony\Bundle\SwiftmailerBundle\EventListener\EmailSenderListener as BaseEmailSenderListener;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\IntrospectableContainerInterface;

/**
 * Igual al listener original de SwiftmailerBundle, pero registra en el logger
 * los fallos al vaciar el spool en memoria (kernel.terminate), ya que por
 * defecto esas excepciones no quedan registradas en ningun lado.
 */
class EmailSenderListener extends BaseEmailSenderListener
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
    }

    public function onTerminate()
    {
        if (!$this->container->has('mailer')) {
            return;
        }

        $mailers = array_keys($this->container->getParameter('swiftmailer.mailers'));
        foreach ($mailers as $name) {
            if ($this->container instanceof IntrospectableContainerInterface ? $this->container->initialized(sprintf('swiftmailer.mailer.%s', $name)) : true) {
                if ($this->container->getParameter(sprintf('swiftmailer.mailer.%s.spool.enabled', $name))) {
                    $mailer = $this->container->get(sprintf('swiftmailer.mailer.%s', $name));
                    $transport = $mailer->getTransport();
                    if ($transport instanceof \Swift_Transport_SpoolTransport) {
                        $spool = $transport->getSpool();
                        if ($spool instanceof \Swift_MemorySpool) {
                            $logger = $this->container->has('logger') ? $this->container->get('logger') : null;
                            $failedRecipients = array();
                            try {
                                $sent = $spool->flushQueue(
                                    $this->container->get(sprintf('swiftmailer.mailer.%s.transport.real', $name)),
                                    $failedRecipients
                                );
                                if ($logger) {
                                    if ($failedRecipients) {
                                        $logger->error(sprintf(
                                            'Correo enviado con el mailer "%s" pero rechazado para: %s',
                                            $name,
                                            implode(', ', $failedRecipients)
                                        ));
                                    } else {
                                        $logger->info(sprintf(
                                            'Mailer "%s": %d correo(s) enviado(s) desde el spool.',
                                            $name,
                                            $sent
                                        ));
                                    }
                                }
                            } catch (\Exception $e) {
                                if ($logger) {
                                    $logger->error(sprintf(
                                        'Fallo el envio de correo con el mailer "%s": %s',
                                        $name,
                                        $e->getMessage()
                                    ), array('exception' => $e));
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
