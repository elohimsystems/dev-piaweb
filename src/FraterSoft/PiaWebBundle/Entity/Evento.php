<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Evento
 */
class Evento
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $nombre;

    /**
     * @var string
     */
    private $logo;

    /**
     * @var \DateTime
     */
    private $fecha;

    /**
     * @var \DateTime
     */
    private $hora;

    /**
     * @var string
     */
    private $lugar;

    /**
     * @var string
     */
    private $ruta;

    /**
     * @var string
     */
    private $organizador;

    /**
     * @var string
     */
    private $nombrecontacto;

    /**
     * @var string
     */
    private $telefonocontacto;

    /**
     * @var string
     */
    private $puntoinscripcion;

    /**
     * @var \DateTime
     */
    private $fechacierre;

    /**
     * @var \DateTime
     */
    private $fechacierreefectivo;

    /**
     * @var \DateTime
     */
    private $horacierre;

    /**
     * @var string
     */
    private $equipo;

    /**
     * @var boolean
     */
    private $rifa;

    /**
     * @var string
     */
    private $tipo;

    /**
     * @var boolean
     */
    private $cupocontrol;

    /**
     * @var integer
     */
    private $cupomaximo;

    /**
     * @var boolean
     */
    private $activo;

    /**
     * @var integer
     */
    private $idcampeonato;

    /**
     * @var integer
     */
    private $criteriocalculoedad;

    /**
     * @var boolean
     */
    private $controlamenores16;

    /**
     * @var string
     */
    private $emailcontacto;

    /**
     * @var \DateTime
     */
    private $fechainicio;

    /**
     * @var string
     */
    private $zonahoraria;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Organizador
     */
    private $idorganizador;
    
    /**
     * @var boolean
     */
    private $inforepresentante;    
    
    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Tema
     */
    private $tema;    
    
    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Numeracion
     */
    private $numeracion;      
    
    /**
     * @var integer
     */
    private $proceso;    
    
    /**
     * @var string
     */
    private $mapalugar;    

    /**
     * @var string
     */
    private $rutamapa;    

    /**
     * @var string
     */
    private $rutaperfil;    

    /**
     * @var string
     */
    private $rutavideo;    

    /**
     * @var string
     */
    private $externo;    

    /**
     * @var string
     */
    private $url;    

    /**
     * @var string
     */
    private $clasificacion;
    
    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Ciudad
     */
    private $idciudad;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $idrecarga;    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idrecarga = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Evento
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set logo
     *
     * @param string $logo
     * @return Evento
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Evento
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set hora
     *
     * @param \DateTime $hora
     * @return Evento
     */
    public function setHora($hora)
    {
        $this->hora = $hora;

        return $this;
    }

    /**
     * Get hora
     *
     * @return \DateTime 
     */
    public function getHora()
    {
        return $this->hora;
    }

    /**
     * Set lugar
     *
     * @param string $lugar
     * @return Evento
     */
    public function setLugar($lugar)
    {
        $this->lugar = $lugar;

        return $this;
    }

    /**
     * Get lugar
     *
     * @return string 
     */
    public function getLugar()
    {
        return $this->lugar;
    }

    /**
     * Set ruta
     *
     * @param string $ruta
     * @return Evento
     */
    public function setRuta($ruta)
    {
        $this->ruta = $ruta;

        return $this;
    }

    /**
     * Get ruta
     *
     * @return string 
     */
    public function getRuta()
    {
        return $this->ruta;
    }

    /**
     * Set organizador
     *
     * @param string $organizador
     * @return Evento
     */
    public function setOrganizador($organizador)
    {
        $this->organizador = $organizador;

        return $this;
    }

    /**
     * Get organizador
     *
     * @return string 
     */
    public function getOrganizador()
    {
        return $this->organizador;
    }

    /**
     * Set nombrecontacto
     *
     * @param string $nombrecontacto
     * @return Evento
     */
    public function setNombrecontacto($nombrecontacto)
    {
        $this->nombrecontacto = $nombrecontacto;

        return $this;
    }

    /**
     * Get nombrecontacto
     *
     * @return string 
     */
    public function getNombrecontacto()
    {
        return $this->nombrecontacto;
    }

    /**
     * Set telefonocontacto
     *
     * @param string $telefonocontacto
     * @return Evento
     */
    public function setTelefonocontacto($telefonocontacto)
    {
        $this->telefonocontacto = $telefonocontacto;

        return $this;
    }

    /**
     * Get telefonocontacto
     *
     * @return string 
     */
    public function getTelefonocontacto()
    {
        return $this->telefonocontacto;
    }

    /**
     * Set puntoinscripcion
     *
     * @param string $puntoinscripcion
     * @return Evento
     */
    public function setPuntoinscripcion($puntoinscripcion)
    {
        $this->puntoinscripcion = $puntoinscripcion;

        return $this;
    }

    /**
     * Get puntoinscripcion
     *
     * @return string 
     */
    public function getPuntoinscripcion()
    {
        return $this->puntoinscripcion;
    }

    /**
     * Set fechacierre
     *
     * @param \DateTime $fechacierre
     * @return Evento
     */
    public function setFechacierre($fechacierre)
    {
        $this->fechacierre = $fechacierre;

        return $this;
    }

    /**
     * Get fechacierre
     *
     * @return \DateTime 
     */
    public function getFechacierre()
    {
        return $this->fechacierre;
    }

    /**
     * Set fechacierreefectivo
     *
     * @param \DateTime $fechacierreefectivo
     * @return Evento
     */
    public function setFechacierreefectivo($fechacierreefectivo)
    {
        $this->fechacierreefectivo = $fechacierreefectivo;

        return $this;
    }

    /**
     * Get fechacierreefectivo
     *
     * @return \DateTime 
     */
    public function getFechacierreefectivo()
    {
        return $this->fechacierreefectivo;
    }

    /**
     * Set horacierre
     *
     * @param \DateTime $horacierre
     * @return Evento
     */
    public function setHoracierre($horacierre)
    {
        $this->horacierre = $horacierre;

        return $this;
    }

    /**
     * Get horacierre
     *
     * @return \DateTime 
     */
    public function getHoracierre()
    {
        return $this->horacierre;
    }

    /**
     * Set equipo
     *
     * @param string $equipo
     * @return Evento
     */
    public function setEquipo($equipo)
    {
        $this->equipo = $equipo;

        return $this;
    }

    /**
     * Get equipo
     *
     * @return string 
     */
    public function getEquipo()
    {
        return $this->equipo;
    }

    /**
     * Set rifa
     *
     * @param boolean $rifa
     * @return Evento
     */
    public function setRifa($rifa)
    {
        $this->rifa = $rifa;

        return $this;
    }

    /**
     * Get rifa
     *
     * @return boolean 
     */
    public function getRifa()
    {
        return $this->rifa;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     * @return Evento
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set cupocontrol
     *
     * @param boolean $cupocontrol
     * @return Evento
     */
    public function setCupocontrol($cupocontrol)
    {
        $this->cupocontrol = $cupocontrol;

        return $this;
    }

    /**
     * Get cupocontrol
     *
     * @return boolean 
     */
    public function getCupocontrol()
    {
        return $this->cupocontrol;
    }

    /**
     * Set cupomaximo
     *
     * @param integer $cupomaximo
     * @return Evento
     */
    public function setCupomaximo($cupomaximo)
    {
        $this->cupomaximo = $cupomaximo;

        return $this;
    }

    /**
     * Get cupomaximo
     *
     * @return integer 
     */
    public function getCupomaximo()
    {
        return $this->cupomaximo;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return Evento
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean 
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set idcampeonato
     *
     * @param integer $idcampeonato
     * @return Evento
     */
    public function setIdcampeonato($idcampeonato)
    {
        $this->idcampeonato = $idcampeonato;

        return $this;
    }

    /**
     * Get idcampeonato
     *
     * @return integer 
     */
    public function getIdcampeonato()
    {
        return $this->idcampeonato;
    }

    /**
     * Set criteriocalculoedad
     *
     * @param integer $criteriocalculoedad
     * @return Evento
     */
    public function setCriteriocalculoedad($criteriocalculoedad)
    {
        $this->criteriocalculoedad = $criteriocalculoedad;

        return $this;
    }

    /**
     * Get criteriocalculoedad
     *
     * @return integer 
     */
    public function getCriteriocalculoedad()
    {
        return $this->criteriocalculoedad;
    }

    /**
     * Set controlamenores16
     *
     * @param boolean $controlamenores16
     * @return Evento
     */
    public function setControlamenores16($controlamenores16)
    {
        $this->controlamenores16 = $controlamenores16;

        return $this;
    }

    /**
     * Get controlamenores16
     *
     * @return boolean 
     */
    public function getControlamenores16()
    {
        return $this->controlamenores16;
    }

    /**
     * Set emailcontacto
     *
     * @param string $emailcontacto
     * @return Evento
     */
    public function setEmailcontacto($emailcontacto)
    {
        $this->emailcontacto = $emailcontacto;

        return $this;
    }

    /**
     * Get emailcontacto
     *
     * @return string 
     */
    public function getEmailcontacto()
    {
        return $this->emailcontacto;
    }

    /**
     * Set fechainicio
     *
     * @param \DateTime $fechainicio
     * @return Evento
     */
    public function setFechainicio($fechainicio)
    {
        $this->fechainicio = $fechainicio;

        return $this;
    }

    /**
     * Get fechainicio
     *
     * @return \DateTime 
     */
    public function getFechainicio()
    {
        return $this->fechainicio;
    }

    /**
     * Set zonahoraria
     *
     * @param string $zonahoraria
     * @return Evento
     */
    public function setZonahoraria($zonahoraria)
    {
        $this->zonahoraria = $zonahoraria;

        return $this;
    }

    /**
     * Get zonahoraria
     *
     * @return string 
     */
    public function getZonahoraria()
    {
        return $this->zonahoraria;
    }

    /**
     * Set idorganizador
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Organizador $idorganizador
     * @return Evento
     */
    public function setIdorganizador(\FraterSoft\PiaWebBundle\Entity\Organizador $idorganizador = null)
    {
        $this->idorganizador = $idorganizador;

        return $this;
    }

    /**
     * Get idorganizador
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Organizador 
     */
    public function getIdorganizador()
    {
        return $this->idorganizador;
    }
    
    /**
     * to string
     *
     * @return string 
     */
    public function __toString() {
        return $this->nombre;
    }        
    
    /**
     * Set inforepresentante
     *
     * @param boolean $inforepresentante
     * @return Evento
     */
    public function setInforepresentante($inforepresentante)
    {
        $this->inforepresentante = $inforepresentante;

        return $this;
    }

    /**
     * Get inforepresentante
     *
     * @return boolean 
     */
    public function getInforepresentante()
    {
        return $this->inforepresentante;
    }    
    
    /**
     * Set Tema
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Tema $tema
     * @return Evento
     */
    public function setTema(\FraterSoft\PiaWebBundle\Entity\Tema $tema = null)
    {
        $this->tema = $tema;
        return $this;
    }

    /**
     * Get Tema
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Tema 
     */
    public function getTema()
    {
        return $this->tema;
    }
    
    /**
     * Set numeracion
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Numeracion $numeracion
     * @return Evento
     */
    public function setNumeracion(\FraterSoft\PiaWebBundle\Entity\Numeracion $numeracion = null)
    {
        $this->numeracion = $numeracion;

        return $this;
    }

    /**
     * Get numeracion
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Numeracion 
     */
    public function getNumeracion()
    {
        return $this->numeracion;
    }
    
    /**
     * Set proceso
     *
     * @param integer $proceso
     * @return Evento
     */
    public function setProceso($proceso)
    {
        $this->proceso = $proceso;
        return $this;
    }

    /**
     * Get proceso
     *
     * @return integer 
     */
    public function getProceso()
    {
        return $this->proceso;
    }    
    
    /**
     * Set mapalugar
     *
     * @param string $mapalugar
     * @return Evento
     */
    public function setMapalugar($mapalugar)
    {
        $this->mapalugar = $mapalugar;

        return $this;
    }

    /**
     * Get mapalugar
     *
     * @return string 
     */
    public function getMapalugar()
    {
        return $this->mapalugar;
    }    

    /**
     * Set rutamapa
     *
     * @param string $rutamapa
     * @return Evento
     */
    public function setRutamapa($rutamapa)
    {
        $this->rutamapa = $rutamapa;
        return $this;
    }

    /**
     * Get rutamapa
     *
     * @return string 
     */
    public function getRutamapa()
    {
        return $this->rutamapa;
    }       
    
    /**
     * Set rutaperfil
     *
     * @param string $rutamapa
     * @return Evento
     */
    public function setRutaperfil($rutaperfil)
    {
        $this->rutaperfil = $rutaperfil;
        return $this;
    }

    /**
     * Get rutaperfil
     *
     * @return string 
     */
    public function getRutaperfil()
    {
        return $this->rutaperfil;
    }       

    /**
     * Set rutavideo
     *
     * @param string $rutavideo
     * @return Evento
     */
    public function setRutavideo($rutavideo)
    {
        $this->rutavideo = $rutavideo;
        return $this;
    }

    /**
     * Get rutavideo
     *
     * @return string 
     */
    public function getRutavideo()
    {
        return $this->rutavideo;
    }       

    /**
     * Set externo
     *
     * @param string $externo
     * @return Evento
     */
    public function setExterno($externo)
    {
        $this->externo = $externo;
        return $this;
    }

    /**
     * Get externo
     *
     * @return string 
     */
    public function getExterno()
    {
        return $this->externo;
    }       

    /**
     * Set url
     *
     * @param string $url
     * @return Evento
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }       

    /**
     * Set clasificacion
     *
     * @param string $clasificacion
     * @return Evento
     */
    public function setClasificacion($clasificacion)
    {
        $this->clasificacion = $clasificacion;
        return $this;
    }

    /**
     * Get clasificacion
     *
     * @return string 
     */
    public function getClasificacion()
    {
        return $this->clasificacion;
    }           
    
    /**
     * Set idciudad
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Ciudad $idciudad
     * @return Evento
     */
    public function setIdciudad(\FraterSoft\PiaWebBundle\Entity\Ciudad $idciudad = null)
    {
        $this->idciudad = $idciudad;
        return $this;
    }

    /**
     * Get idciudad
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Ciudad 
     */
    public function getIdciudad()
    {
        return $this->idciudad;
    }
    
    /**
     * Set idrecarga
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Recarga $idrecarga
     * @return Club
     */
    public function setIdrecarga($idrecarga = null)
    {
        $this->idrecarga = $idrecarga;

        return $this;
    }

    /**
     * Add idrecarga
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Recarga $idrecarga
     * @return Club
     */
    public function addIdrecarga(\FraterSoft\PiaWebBundle\Entity\Recarga $disciplina)
    {
        $this->idrecarga[] = $disciplina;
        return $this;
    }
    
    /**
     * Remove idrecarga
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Recarga $idrecarga
     */
    public function removeIdrecarga(\FraterSoft\PiaWebBundle\Entity\Recarga $idrecarga)
    {
        $this->idrecarga->removeElement($idrecarga);
    }
    
    /**
     * Remove idrecarga
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Recarga $idrecarga
     */
    public function removeAlldisciplinas()
    {
        unset($this->idrecarga);
    }
    /**
     * Get idrecarga
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Recarga 
     */
    public function getIdrecarga()
    {
        return $this->idrecarga;
    }
    
}
