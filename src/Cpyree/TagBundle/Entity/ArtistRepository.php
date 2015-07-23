<?php

namespace Cpyree\TagBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Filesystem\Filesystem;
/**
 * TagData
 *
 */
class ArtistRepository extends BaseRepository{


    public function getOrCreateNewArtist($name){
        $artist = $this->_em->getRepository($this->getEntityName())->findOneByName($name);
        if(is_null($artist)){
            $artist = new Artist($name);
        }
        return $artist;
    }
  
    
    public function getSqlBulkInsert($file){
        $r = "LOAD DATA LOCAL INFILE \"$file\" ";
        $r .= "INTO TABLE  ".$this->getClassMetadata()->getTableName(). " ";
        $r .= "FIELDS ";
        $r .= "TERMINATED BY \";\" ";
        $r .= "ENCLOSED BY \"\\\"\" ";
        $r .= "LINES ";
        $r .= "TERMINATED BY \"\\n\" ";
        $r .= "(name, created);";
        $r .= "show warnings;";
        return $r;
    }
     public function getConsolBulkInsertCMD($file){
        $user = $this->getEntityManager()->getConnection()->getUsername();
        $password = $this->getEntityManager()->getConnection()->getPassword();
        $dbname = $this->getEntityManager()->getConnection()->getDatabase();
        $sql = "mysql --local-infile -u" . $user . " " . $dbname . " --execute='" . $this->getSqlBulkInsert($file) ."'";
        if($password){
            $sql = "mysql --local-infile -u" . $user . "  -p". $password ." " . $dbname . " --execute='" . $this->getSqlBulkInsert($file) ."'";
        }
    
        return $sql;
    }
    
    /**
     * 
     * @param type $page
     * @param type $limit
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getPage($page = 1, $limit = 20){
        
        
        $qb = $this->_em->createQueryBuilder();
        $qb->select('a')
                ->from($this->getEntityName(), 'a')
                ->orderBy('a.id', 'ASC')
                ->setFirstResult(($page-1) * $limit)
                ->setMaxResults($limit);
          
        $query = $qb;
        return new \Doctrine\ORM\Tools\Pagination\Paginator($query, false);
                
    }
    
    public function findTagDatas($id){
        $qB = $this->_em->createQueryBuilder();
        $qB->select("a,td,mf")
                ->from($this->getEntityName(), "a")
                ->join("a.tagDatas", "td")
                ->join("td.mediaFile", "mf")
                ->where($qB->expr()->eq("a.id", $id))
                ->addOrderBy("td.year", "DESC")
                ->addOrderBy("td.album", "ASC");
        return ($qB->getQuery()->getSingleResult());
    }
    
}
