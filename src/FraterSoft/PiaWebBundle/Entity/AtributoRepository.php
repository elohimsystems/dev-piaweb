<?php

namespace FraterSoft\PiaWebBundle\Entity;

use Doctrine\ORM\EntityRepository;

class AtributoRepository extends EntityRepository
{
    public function tipoAtributo($idatributo)
    {    
        $em = $this->getEntityManager();
        $sql = "select nombre,tipodato from piaaccess.tmatributos "
                . "where id=" . $idatributo;
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute(); 
        return $stmt->fetchAll();  
    }
}