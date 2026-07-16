<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ControlParental
{
    private $id;

    private $edad_control;

    private $titulo_documento;

    private $documento_pdf;

    private $mensaje;

    private $idevento;

    private $file;

    public function getId()
    {
        return $this->id;
    }

    public function setEdadControl($edadControl)
    {
        $this->edad_control = $edadControl;
        return $this;
    }

    public function getEdadControl()
    {
        return $this->edad_control;
    }

    public function setTituloDocumento($tituloDocumento)
    {
        $this->titulo_documento = $tituloDocumento;
        return $this;
    }

    public function getTituloDocumento()
    {
        return $this->titulo_documento;
    }

    public function setDocumentoPdf($documentoPdf)
    {
        $this->documento_pdf = $documentoPdf;
        return $this;
    }

    public function getDocumentoPdf()
    {
        return $this->documento_pdf;
    }

    public function setMensaje($mensaje)
    {
        $this->mensaje = $mensaje;
        return $this;
    }

    public function getMensaje()
    {
        return $this->mensaje;
    }

    public function setIdevento(\FraterSoft\PiaWebBundle\Entity\Evento $idevento = null)
    {
        $this->idevento = $idevento;
        return $this;
    }

    public function getIdevento()
    {
        return $this->idevento;
    }

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }
}
