<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Competidor
 */
class CompetidorOld
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $iddocumento;

    /**
     * @var string
     */
    private $nombre;

    /**
     * @var string
     */
    private $apellido;

    /**
     * @var string
     */
    private $foto;

    /**
     * @var \DateTime
     */
    private $fechanacimiento;

    /**
     * @var string
     */
    private $sexo;

    /**
     * @var string
     */
    private $equipo;

    /**
     * @var integer
     */
    private $edad;

    /**
     * @var float
     */
    private $peso;

    /**
     * @var string
     */
    private $condicion;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $telefono;

    /**
     * @var integer
     */
    private $idestado;

    /**
     * @var integer
     */
    private $idpais;

    /**
     * @var string
     */
    private $tallafranela;

    /**
     * @var boolean
     */
    private $certificado;

    /**
     * @var string
     */
    private $idrepresentante;
    
    /**
     * @var string
     */
    private $nombrerepresentante;

    /**
     * Set id
     *
     * @return integer 
     */
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
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
     * Set iddocumento
     *
     * @param string $iddocumento
     * @return Competidor
     */
    public function setIddocumento($iddocumento)
    {
        $this->iddocumento = $iddocumento;

        return $this;
    }

    /**
     * Get iddocumento
     *
     * @return string 
     */
    public function getIddocumento()
    {
        return $this->iddocumento;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Competidor
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
     * Set apellido
     *
     * @param string $apellido
     * @return Competidor
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido
     *
     * @return string 
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set foto
     *
     * @param string $foto
     * @return Competidor
     */
    public function setFoto($foto)
    {
        $this->foto = $foto;

        return $this;
    }

    /**
     * Get foto
     *
     * @return string 
     */
    public function getFoto()
    {
        return $this->foto;
    }

    /**
     * Set fechanacimiento
     *
     * @param \DateTime $fechanacimiento
     * @return Competidor
     */
    public function setFechanacimiento($fechanacimiento)
    {
        $this->fechanacimiento = $fechanacimiento;

        return $this;
    }

    /**
     * Get fechanacimiento
     *
     * @return \DateTime 
     */
    public function getFechanacimiento()
    {
        return $this->fechanacimiento;
    }

    /**
     * Set sexo
     *
     * @param string $sexo
     * @return Competidor
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;

        return $this;
    }

    /**
     * Get sexo
     *
     * @return string 
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * Set equipo
     *
     * @param string $equipo
     * @return Competidor
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
     * Set edad
     *
     * @param integer $edad
     * @return Competidor
     */
    public function setEdad($edad)
    {
        $this->edad = $edad;

        return $this;
    }

    /**
     * Get edad
     *
     * @return integer 
     */
    public function getEdad()
    {
        return $this->edad;
    }

    /**
     * Set peso
     *
     * @param float $peso
     * @return Competidor
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;

        return $this;
    }

    /**
     * Get peso
     *
     * @return float 
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * Set condicion
     *
     * @param string $condicion
     * @return Competidor
     */
    public function setCondicion($condicion)
    {
        $this->condicion = $condicion;

        return $this;
    }

    /**
     * Get condicion
     *
     * @return string 
     */
    public function getCondicion()
    {
        return $this->condicion;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Competidor
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     * @return Competidor
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string 
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set idestado
     *
     * @param integer $idestado
     * @return Competidor
     */
    public function setIdestado($idestado)
    {
        $this->idestado = $idestado;

        return $this;
    }

    /**
     * Get idestado
     *
     * @return integer 
     */
    public function getIdestado()
    {
        return $this->idestado;
    }

    /**
     * Set idpais
     *
     * @param integer $idpais
     * @return Competidor
     */
    public function setIdpais($idpais)
    {
        $this->idpais = $idpais;

        return $this;
    }

    /**
     * Get idpais
     *
     * @return integer 
     */
    public function getIdpais()
    {
        return $this->idpais;
    }

    /**
     * Set tallafranela
     *
     * @param string $tallafranela
     * @return Competidor
     */
    public function setTallafranela($tallafranela)
    {
        $this->tallafranela = $tallafranela;

        return $this;
    }

    /**
     * Get tallafranela
     *
     * @return string 
     */
    public function getTallafranela()
    {
        return $this->tallafranela;
    }

    /**
     * Set certificado
     *
     * @param boolean $certificado
     * @return Competidor
     */
    public function setCertificado($certificado)
    {
        $this->certificado = $certificado;

        return $this;
    }

    /**
     * Get certificado
     *
     * @return boolean 
     */
    public function getCertificado()
    {
        return $this->certificado;
    }
    
    /**
     * to string
     *
     * @return string 
     */
    public function __toString() {
        return $this->iddocumento;
    }        
    
    /**
     * Get certificado
     *
     * @return boolean 
     */
    public function getValorCampo($campo)
    {
        return $this->$campo;
    }    
    
    /**
     * Set idrepresentante
     *
     * @param string $idrepresentante
     * @return Competidor
     */
    public function setIdrepresentante($idrepresentante)
    {
        $this->idrepresentante = $idrepresentante;

        return $this;
    }

    /**
     * Get idrepresentante
     *
     * @return string 
     */
    public function getIdrepresentante()
    {
        return $this->idrepresentante;
    }    
    
    /**
     * Set nombrerepresentante
     *
     * @param string $nombrerepresentante
     * @return Competidor
     */
    public function setnombrerepresentante($nombrerepresentante)
    {
        $this->nombrerepresentante = $nombrerepresentante;

        return $this;
    }

    /**
     * Get nombrerepresentante
     *
     * @return string 
     */
    public function getNombrerepresentante()
    {
        return $this->nombrerepresentante;
    }    
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $iddocumento;

    /**
     * @var string
     */
    private $nombre;

    /**
     * @var string
     */
    private $apellido;

    /**
     * @var string
     */
    private $foto;

    /**
     * @var \DateTime
     */
    private $fechanacimiento;

    /**
     * @var string
     */
    private $sexo;

    /**
     * @var string
     */
    private $equipo;

    /**
     * @var integer
     */
    private $edad;

    /**
     * @var float
     */
    private $peso;

    /**
     * @var string
     */
    private $condicion;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $telefono;

    /**
     * @var integer
     */
    private $idpais;

    /**
     * @var string
     */
    private $tallafranela;

    /**
     * @var boolean
     */
    private $certificado;

    /**
     * @var string
     */
    private $idrepresentante;

    /**
     * @var string
     */
    private $nombrerepresentante;

    /**
     * @var \FraterSoft\PiaWebBundle\Entity\Estado
     */
    private $idestado;


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
     * Set iddocumento
     *
     * @param string $iddocumento
     * @return CompetidorOld
     */
    public function setIddocumento($iddocumento)
    {
        $this->iddocumento = $iddocumento;

        return $this;
    }

    /**
     * Get iddocumento
     *
     * @return string 
     */
    public function getIddocumento()
    {
        return $this->iddocumento;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return CompetidorOld
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
     * Set apellido
     *
     * @param string $apellido
     * @return CompetidorOld
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido
     *
     * @return string 
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set foto
     *
     * @param string $foto
     * @return CompetidorOld
     */
    public function setFoto($foto)
    {
        $this->foto = $foto;

        return $this;
    }

    /**
     * Get foto
     *
     * @return string 
     */
    public function getFoto()
    {
        return $this->foto;
    }

    /**
     * Set fechanacimiento
     *
     * @param \DateTime $fechanacimiento
     * @return CompetidorOld
     */
    public function setFechanacimiento($fechanacimiento)
    {
        $this->fechanacimiento = $fechanacimiento;

        return $this;
    }

    /**
     * Get fechanacimiento
     *
     * @return \DateTime 
     */
    public function getFechanacimiento()
    {
        return $this->fechanacimiento;
    }

    /**
     * Set sexo
     *
     * @param string $sexo
     * @return CompetidorOld
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;

        return $this;
    }

    /**
     * Get sexo
     *
     * @return string 
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * Set equipo
     *
     * @param string $equipo
     * @return CompetidorOld
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
     * Set edad
     *
     * @param integer $edad
     * @return CompetidorOld
     */
    public function setEdad($edad)
    {
        $this->edad = $edad;

        return $this;
    }

    /**
     * Get edad
     *
     * @return integer 
     */
    public function getEdad()
    {
        return $this->edad;
    }

    /**
     * Set peso
     *
     * @param float $peso
     * @return CompetidorOld
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;

        return $this;
    }

    /**
     * Get peso
     *
     * @return float 
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * Set condicion
     *
     * @param string $condicion
     * @return CompetidorOld
     */
    public function setCondicion($condicion)
    {
        $this->condicion = $condicion;

        return $this;
    }

    /**
     * Get condicion
     *
     * @return string 
     */
    public function getCondicion()
    {
        return $this->condicion;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return CompetidorOld
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     * @return CompetidorOld
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string 
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set idpais
     *
     * @param integer $idpais
     * @return CompetidorOld
     */
    public function setIdpais($idpais)
    {
        $this->idpais = $idpais;

        return $this;
    }

    /**
     * Get idpais
     *
     * @return integer 
     */
    public function getIdpais()
    {
        return $this->idpais;
    }

    /**
     * Set tallafranela
     *
     * @param string $tallafranela
     * @return CompetidorOld
     */
    public function setTallafranela($tallafranela)
    {
        $this->tallafranela = $tallafranela;

        return $this;
    }

    /**
     * Get tallafranela
     *
     * @return string 
     */
    public function getTallafranela()
    {
        return $this->tallafranela;
    }

    /**
     * Set certificado
     *
     * @param boolean $certificado
     * @return CompetidorOld
     */
    public function setCertificado($certificado)
    {
        $this->certificado = $certificado;

        return $this;
    }

    /**
     * Get certificado
     *
     * @return boolean 
     */
    public function getCertificado()
    {
        return $this->certificado;
    }

    /**
     * Set idrepresentante
     *
     * @param string $idrepresentante
     * @return CompetidorOld
     */
    public function setIdrepresentante($idrepresentante)
    {
        $this->idrepresentante = $idrepresentante;

        return $this;
    }

    /**
     * Get idrepresentante
     *
     * @return string 
     */
    public function getIdrepresentante()
    {
        return $this->idrepresentante;
    }

    /**
     * Set nombrerepresentante
     *
     * @param string $nombrerepresentante
     * @return CompetidorOld
     */
    public function setNombrerepresentante($nombrerepresentante)
    {
        $this->nombrerepresentante = $nombrerepresentante;

        return $this;
    }

    /**
     * Get nombrerepresentante
     *
     * @return string 
     */
    public function getNombrerepresentante()
    {
        return $this->nombrerepresentante;
    }

    /**
     * Set idestado
     *
     * @param \FraterSoft\PiaWebBundle\Entity\Estado $idestado
     * @return CompetidorOld
     */
    public function setIdestado(\FraterSoft\PiaWebBundle\Entity\Estado $idestado = null)
    {
        $this->idestado = $idestado;

        return $this;
    }

    /**
     * Get idestado
     *
     * @return \FraterSoft\PiaWebBundle\Entity\Estado 
     */
    public function getIdestado()
    {
        return $this->idestado;
    }
}
