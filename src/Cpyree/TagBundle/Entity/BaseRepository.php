<?php

namespace Cpyree\TagBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Cpyree\TagBundle\Entity\DbsFile;
use Cpyree\TagBundle\Entity\MediaFile;
use Cpyree\TagBundle\Manager\TagBundleManager;

class BaseRepository extends EntityRepository{
    
    /**
     * 
     * @return integer
     */
    public function count(){
        
        $config = new \Doctrine\ORM\Configuration();
        $config->setQueryCacheImpl(new \Doctrine\Common\Cache\ApcCache());
        $config->setResultCacheImpl(new \Doctrine\Common\Cache\ApcCache());
        
        $query = $this
                ->getEntityManager()
                ->createQuery("SELECT count(af) FROM ".$this->getEntityName()." af");
        $query->useResultCache(true, 3600*24, $this->getEntityName()."_count");
        $query->useQueryCache(true);
        return $query->getSingleScalarResult();
    }
    
    public function getTagedAverage(){
        $total = $this->count();
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
                ->select(
                        "mf.tagPass", 
                        "count(mf.id)",
                        "(100*(count(mf.id)/".$total.")) as percent"
                )
                ->from($this->getEntityName(), "mf")
                ->where($queryBuilder->expr()->eq('mf.tagPass', 1))
                ->groupBy("mf.tagPass");
        
        return $queryBuilder->getQuery()->getSingleResult();
    }
    
    public function getCountArtist(){
        
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select("count(DISTINCT a.id)")
            ->from($this->getEntityName(), "mf")
            ->leftJoin("mf.tagData",'td')
            ->join("td.artists", 'a');
                //->groupBy("a.name");
        
        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
    
    public function getCountRadio(){
        $total = $this->count();
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select(
                    "count(td.id)",
                    "(100*(count(td.id)/".$total.")) as percent"
                    )
            ->from($this->getTagDataRepo()->getEntityName(), "td")
            ->where($queryBuilder->expr()->orX(
                        $queryBuilder->expr()->like("td.comment", "?1"),
                        $queryBuilder->expr()->like("td.comment", "?2"),
                        $queryBuilder->expr()->like("td.comment", "?3")
                    ))
            ->setParameter("1", "%radio%")
            ->setParameter("2", "%hot%")
            ->setParameter("3", "%loop%");
        return $queryBuilder->getQuery()->getSingleResult();
    }
    
    /**
     * 
     * @param type $page
     * @param type $limit
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getPage($page = 1, $limit = 20){
        
        
        $qb = $this->_em->createQueryBuilder();
        $qb->select('mf', 'td')
                ->from($this->getEntityName(), 'mf')
                ->leftJoin('mf.tagData', 'td')
                //->leftJoin('mf.context', 'c')
                ->orderBy('mf.id', 'ASC')
                ->setFirstResult(($page-1) * $limit)
                ->setMaxResults($limit);
          
        $query = $qb;
        return new \Doctrine\ORM\Tools\Pagination\Paginator($query, false);
                
    }
 
    
    
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\DbsFileRepository
     */
    public function getDbsFileRepo(){
        return $this->_em->getRepository("CpyreeTagBundle:DbsFile");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\DbsFileRepository
     */
    public function getSaparFileRepo(){
        return $this->_em->getRepository("CpyreeTagBundle:SaparFile");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\DbsFileRepository
     */
    public function getTagDataRepo(){
        return $this->_em->getRepository("CpyreeTagBundle:TagData");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\MediaFileRepository
     */
    public function getMediaFileRepo(){
        return $this->_em->getRepository("CpyreeTagBundle:MediaFile");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\CoverRepository
     */
    public function getCoverFileRepo(){
        return $this->em->getRepository("CpyreeTagBundle:Cover");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\ArtistRepository
     */
    public function getArtistRepo(){
        return $this->_em->getRepository("CpyreeTagBundle:Artist");
    }    
    
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\CoverRepository
     */
    public function getCoverRepo(){
        return $this->_em->getRepository("CpyreeTagBundle:Cover");
    }    
    
}