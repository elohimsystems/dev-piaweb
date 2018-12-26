<?php

namespace FraterSoft\PiaWebBundle\Controller;

class MailController {

    private $mailer;

    public function __construct($mailer) {
        $this->mailer = $mailer;
    }

    public function enviar($subject, $to, $body) {
        //Se envia el correo de confirmacion
        $mensaje = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom("pre-inscripcion@sistemapia.com.ve")
                ->setCharset('iso-8859-1')
                ->setContentType('text/html')
                ->setTo($to)
                ->setBody($body);
        $this->mailer->send($mensaje);
        return(1);
    }

    public function enviarConfirmacion($subject, $to, $body) {
        //Se envia el correo de confirmacion
        $mensaje = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom("confirmacion@sistemapia.com.ve")
                ->setCharset('iso-8859-1')
                ->setContentType('text/html')
                ->setTo($to)
                ->setBody($body);
        $this->mailer->send($mensaje);
        return(1);
    }    
    
    public function enviarPreinscripcion($subject, $emails, $body) {
        //Se envia el correo de confirmacion
        $mensaje = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom("pre-inscripcion@sistemapia.com.ve")
                ->setCharset('iso-8859-1')
                ->setContentType('text/html')
                ->setTo($emails)
                ->setBody($body);
        $this->mailer->send($mensaje);
        return(1);
    }        
}
