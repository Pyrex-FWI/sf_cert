<?php

namespace Cpyree\TagBundle\Entity;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Filesystem\Filesystem;
/**
 * TagData
 *
 */
class TagDataRepository extends BaseRepository{

    
    public function execBulkSqlQuery($file){
        
        $rsm = new ResultSetMapping();
        
        $this->getEntityManager()->createNativeQuery(
                $this->getSqlBulkInsert($file), 
                $rsm
                )->execute(); 

    }
    /**
     * 
     * @param type $page
     * @param type $limit
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getPage($page = 1, $limit = 20){
        /*$query = $this->getEntityManager()->createQuery("SELECT td, af FROM CpyreeTagBundle:TagData td join td.audioFile af ORDER BY td.id ASC")
                ->setFirstResult(($page-1) * $limit)
                ->setMaxResults($limit);*/
        $qB = $this->_em->createQueryBuilder();
        $qB->select(array('td', 'mf'))
                ->from($this->getEntityName(), 'td')
                ->join('td.mediaFile', 'mf')
                ->setFirstResult(($page-1) * $limit)
                ->setMaxResults($limit)
                ->orderBy('td.id', 'ASC');
        
        return new \Doctrine\ORM\Tools\Pagination\Paginator($qB->getQuery(), false);
                
    }
    

    /**
     * 
     * @return array()
     */
    public function countGenreWithAverage(){
        return $this->getEntityManager()->getConnection()->query(""
                . "SELECT td.genre , COUNT(*) as count,  "
                . "COUNT(1) / (select COUNT(1) FROM ".$this->getClassMetadata()->getTableName().") * 100 AS average "
                . "FROM ". $this->getClassMetadata()->getTableName()." td "
                . "group by td.genre order by count DESC")->fetchAll();
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
     * Get TagData wich have image but not assiated
     * @param type int
     * @return array
     */
    public function withoutCover($limit){
        $qB = $this->_em->createQueryBuilder();
        $qB->select(array('td'))
                ->from($this->getEntityName(), 'td')
                ->where(
                    $qB->expr()->andX(
                            $qB->expr()->eq('td.coverInTag', '?1')
                            ,$qB->expr()->isNull('td.cover')
                            )
                        )
            ->setParameter(1, 1)
            //->setParameter(2, null)
            ->setFirstResult(0)
            ->setMaxResults($limit)
            ->orderBy('td.id', 'ASC');
        //die($qB->getQuery()->getSQL());
        return $qB->getQuery()->getResult();
    }
    
    
    public function getSqlBulkInsert($file){
        $r = "LOAD DATA LOCAL INFILE \"$file\" ";
        $r .= "INTO TABLE tag_data ";
        $r .= "FIELDS ";
        $r .= "TERMINATED BY \";\" ";
        $r .= "ENCLOSED BY \"\\\"\" ";
        $r .= "LINES ";
        $r .= "TERMINATED BY \"\\n\" ";
        $r .= "(media_file_id,title,artist,genre,year,album,bpm,initial_key,comment, cover_in_tag, created);";
        $r .= "show warnings;";
        return $r;
 
    }
 
 
    
    public function persistCover($path, TagData $tagData){
        $id3 = $tagData->getMediaFile()->getId3();
        $hash_name = $id3->getCoverHash();

        if($cover = $this->getCoverRepo()->findOneByHash($hash_name)){
            $cover = $cover;
        }
        else{ 
            $filePathName = $path . $hash_name . $id3->getCoverExtension();
            $cover = new Cover();
            $cover->setMime($id3->getCoverMime());
            $cover->setHash($hash_name);
            $cover->setFilepath($filePathName);
            $coverFile = new Filesystem();
            $coverFile->dumpFile(
                $filePathName , 
                $id3->get('cover')
            );
            
            $this->_em->persist($cover);

        }
        $tagData->setCover($cover);
        $this->_em->persist($tagData);
        $this->_em->flush();

    }
    
   
    /**
     * Get TagData With ArtistName
     * @param \DateTime|null $bulkLastPass Date référence for restrict query (Fetch only Row with bulkLastPass date less than $bulkLastPass
     * @param type $limit Row limit
     * @param type $sortField default is "td.artist"
     * @param type $sortDir default "ASC"
     * @return Doctrine\ORM\Query
     */
    public function findTagaDataWithArtit(\DateTime $bulkLastPass = null, $limit = null, $sortField = "td.artist", $sortDir = "ASC"){
        
         if(is_null($bulkLastPass)){
            $bulkLastPass = new \DateTime('now');
        }
        $tagDataRep = $this->getTagDataRepo(); 
        $qB = $this->_em->createQueryBuilder();
        $qB->select("td")->from($tagDataRep->getEntityName(), "td");
        $qB->where(
                $qB->expr()->andX(
                    $qB->expr()->isNotNull("td.artist"),
                    $qB->expr()->neq("td.artist", "''"),
                        $qB->expr()->orX(
                             $qB->expr()->lt('td.bulkLastPass', "'".$bulkLastPass->format('Y-m-d H:i:s')."'"),
                             $qB->expr()->isNull('td.bulkLastPass')
                        )
                    )
                );
        if(!empty($sortField) && ! empty($sortDir)){ $qB->orderBy($sortField, $sortDir);}
        
        return $qB->getQuery();
    }    
    
    public function findTagaDataWithArtitCount(\DateTime $bulkLastPass = null){
        
         if(is_null($bulkLastPass)){
            $bulkLastPass = new \DateTime('now');
        }
        $tagDataRep = $this->getTagDataRepo(); 
        $qB = $this->_em->createQueryBuilder();
        $qB->select("count(td)")->from($tagDataRep->getEntityName(), "td");
        $qB->where(
                $qB->expr()->andX(
                    $qB->expr()->isNotNull("td.artist"),
                    $qB->expr()->neq("td.artist", "''"),
                        $qB->expr()->orX(
                             $qB->expr()->lt('td.bulkLastPass', "'".$bulkLastPass->format('Y-m-d H:i:s')."'"),
                             $qB->expr()->isNull('td.bulkLastPass')
                        )
                    )
                );
        return $qB->getQuery()->getSingleScalarResult();
    }      
    
}
