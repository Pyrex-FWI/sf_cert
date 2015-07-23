<?php

namespace Cpyree\TagBundle\Entity;

use Doctrine\ORM\EntityRepository;


class MediaFileRepository extends EntityRepository{
    
    /**
     * Get The count of non tagged object
     * @return int
     */
    public function nbUntaged(Date $beforeDate = null){
        $query = $this
                ->getEntityManager()
                ->createQuery("SELECT count(mf) FROM ". $this->getEntityName() ." mf where mf.exist = :exist and mf.tagPass <> :tag_pass");
        
        $query->setParameters(array(
            'exist' => 1,
            'tag_pass' => 1,
        )); 
        
        return $query->getSingleScalarResult();
    }
    
    public function getUntaged( $limit = 500){
        $query = $this
                ->getEntityManager()
                ->createQuery("SELECT mf FROM ".$this->getEntityName()."  mf where mf.exist = :exist and mf.tagPass <> :tag_pass");
                
         $query->setParameters(array(
                    'exist' => 1,
                    'tag_pass' => 1,
                ));
        $query->setMaxResults($limit);
        
        return $query->getResult();
    }

    
    public function getCsvHeader($enclose ="", $separator = ";", $endLine = "\n"){
        
        $sm = $this->getEntityManager()->getConnection()->getSchemaManager();
        $out = "";
        
        foreach($this->getClassMetadata()->columnNames as $fieldName => $colName){
            if($this->getClassMetadata()->isIdentifier($fieldName) == $colName) continue;
            $fieldMap = $this->getClassMetadata()->getFieldMapping($fieldName);
            if(in_array($fieldMap['type'], array('date', 'datetime'))) continue;
            $out .= (strlen($out)>0)? $separator : "" ;
            $out .= $endLine . $enclose . $colName . $enclose;
        }
        
        return $out;
    }
    
    public function getCsvEntity($Obj, $enclose ="", $separator = ";", $endLine = "\n"){
        $sm = $this->getEntityManager()->getConnection()->getSchemaManager();
        $out = "";

        foreach($this->getClassMetadata()->columnNames as $fieldName => $colName){
            if($this->getClassMetadata()->isIdentifier($fieldName) == $colName) continue;
            $fieldMap = $this->getClassMetadata()->getFieldMapping($fieldName);
            if(in_array($fieldMap['type'], array('date', 'datetime'))) continue;
            
            $out .= (strlen($out)>0)? $separator  : "" ;
            $out .= $endLine . $enclose . call_user_method('get'.$fieldName, $Obj) . $enclose;
        }
        return $out;
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

    public function getSqlBulkInsert($file){
        $r = "LOAD DATA LOCAL INFILE \"$file\" IGNORE ";
        $r .= "INTO TABLE ".$this->getClassMetadata()->getTableName() ." ";
        $r .= "FIELDS ";
        $r .= "TERMINATED BY \";\" ";
        $r .= "ENCLOSED BY \"\\\"\" ";
        $r .= "LINES ";
        $r .= "TERMINATED BY \"\\n\" ";
        $r .= "(hash,filepath,created,exist,tag_pass, context_id);";
        $r .= "show warnings;";
        return $r;
 
    }
    
   
    
    
}