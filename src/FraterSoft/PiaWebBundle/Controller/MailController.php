<?php

namespace FraterSoft\PiaWebBundle\Controller;

class MailController {

    private $mailer;
    private $mailerUser;

    public function __construct($mailer, $mailerUser = null) {
        $this->mailer = $mailer;
        $this->mailerUser = $mailerUser;
    }

    /**
     * El From siempre es la cuenta configurada en mailer_user (parameters.yml): es la
     * unica autenticada ante el SMTP, y usar otra direccion (p.ej. la del organizador)
     * hace que el correo sea rechazado o marcado como spam por SPF/DKIM. El $emailfrom
     * recibido (organizador) no se usa: las respuestas al correo del organizador no le
     * llegan de todas formas.
     */
    private function crearMensaje($emailfrom, $subject, $to, $body, $adjuntos = array()) {
        $mensaje = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($this->mailerUser ?: $emailfrom)
                ->setCharset('iso-8859-1')
                ->setContentType('text/html')
                ->setTo($to)
                ->setBody($body);
        foreach ($adjuntos as $adjunto) {
            if (file_exists($adjunto)) {
                $mensaje->attach(\Swift_Attachment::fromPath($adjunto));
            }
        }
        return $mensaje;
    }

    public function enviar($emailfrom, $subject, $to, $body, $adjuntos = array()) {
        $mensaje = $this->crearMensaje($emailfrom, $subject, $to, $body, $adjuntos);
        $this->mailer->send($mensaje);
        return 1;
    }

    public function enviarConfirmacion($emailfrom, $subject, $to, $body, $adjuntos = array()) {
        $mensaje = $this->crearMensaje($emailfrom, $subject, $to, $body, $adjuntos);
        $this->mailer->send($mensaje);
        return 1;
    }    
    
    public function enviarPreinscripcion($emailfrom, $subject, $emailsto, $body, $adjuntos = array()) {
        $mensaje = $this->crearMensaje($emailfrom, $subject, $emailsto, $body, $adjuntos);
        $this->mailer->send($mensaje);
        return 1;
    }        
    
    public function enviarPago($emailfrom, $subject, $emails, $body, $adjuntos = array()) {
        $mensaje = $this->crearMensaje($emailfrom, $subject, $emails, $body, $adjuntos);
        $this->mailer->send($mensaje);
        return 1;
    }        
    
}
