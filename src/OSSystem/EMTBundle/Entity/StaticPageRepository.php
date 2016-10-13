<?php

namespace OSSystem\EMTBundle\Entity;

use Doctrine\ORM\EntityRepository;
use OSSystem\EMTBundle\Entity\StaticPage;
use Doctrine\ORM\Query;

class StaticPageRepository extends EntityRepository
{
    public function findContent($url){
        $em = $this->getEntityManager();
        $query =  $em->createQuery("SELECT s "
                                . "FROM OSSystemEMTBundle:StaticPage s "
                                . "WHERE s.url = :url"
                        );
        $query->setParameter('url', $url);
        $res = $query->getResult();
        if (count($res))
            return $res[0];
        else
            return false;        
    }
    
}