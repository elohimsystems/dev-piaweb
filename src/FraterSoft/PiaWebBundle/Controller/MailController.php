<?php

namespace FraterSoft\PiaWebBundle\Controller;

class MailController {

    private $mailer;

    public function __construct($mailer) {
        $this->mailer = $mailer;
    }

    private function crearMensaje($emailfrom, $subject, $to, $body, $adjuntos = array()) {
        $mensaje = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($emailfrom)
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
