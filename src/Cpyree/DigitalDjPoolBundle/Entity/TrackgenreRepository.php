<?php

namespace Cpyree\DigitalDjPoolBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TrackgenreRepository extends EntityRepository{
    
    /**
     * Count element from entity
     * @return integer
     */
    public function count(){
        return $this->createQueryBuilder('id')
            ->select('COUNT(id)')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

}