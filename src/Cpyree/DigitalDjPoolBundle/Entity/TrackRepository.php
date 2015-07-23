<?php

namespace Cpyree\DigitalDjPoolBundle\Entity;

use Cpyree\AdminLTEBundle\Form\DataObject\Range;
use Cpyree\AdminLTEBundle\Form\DataObject\DateRange;
use Cpyree\DigitalDjPoolBundle\Form\Type\TrackExpertSearchType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\OrderBy;

class TrackRepository extends EntityRepository{


    /**
     * @param TrackExpertSearchType|\Symfony\Component\Form\Form $form
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getTrackExpertSearchQuery(\Symfony\Component\Form\Form $form){
        $qb = $this->createQueryBuilder("t");

        $conditions = array();
        /*if($form->has('isApproval')){
            $conditions[] = $qb->expr()->eq('t.approvalDate','0');
        }*/
        if($form->has('title') && strlen($form->get('title')->getData()) > 0){
            $conditions[] = $qb->expr()->like('t.fulltitle', "'%".$form->get('title')->getData()."%'");
        }
        if($form->get("uidRange")->getData()){
            /** @var Range $bounds */
            $bounds = $form->get("uidRange")->getData();
            $conditions[] = $qb->expr()->between('t.uid', $bounds->getMin(), $bounds->getMax());
        }
        if($form->get("trackIdRange")->getData()){
            $bounds = $form->get("trackIdRange")->getData();
            $conditions[] = $qb->expr()->between('t.trackId', $bounds->getMin(), $bounds->getMax());
        }
        if($form->get("releaseDateRange")->getData()){
            $bounds = $form->get("releaseDateRange")->getData();
            /** @var DateRange $bounds */
            $conditions[] = $qb->expr()->between('t.releaseDate', $bounds->getMin()->getTimestamp(), $bounds->getMax()->getTimestamp());
        }

        if($form->get('approbationType')->getData()){
            $approbationType = $form->get('approbationType')->getData();

            switch($approbationType){
                case 'APPROVED':
                    $conditions[] = $qb->expr()->eq('t.approval','1');
                    break;
                case 'NEUTRAL':
                    $conditions[] = $qb->expr()->gt('t.approvalDate','0');
                    $conditions[] = $qb->expr()->eq('t.approval','0');
                    break;
                case 'NOT_APPROVED':
                    $conditions[] = $qb->expr()->eq('t.approval','-1');
                    break;
                case 'NOT_PROCESSED':
                    $conditions[] = $qb->expr()->eq('t.approvalDate','0');
                    $conditions[] = $qb->expr()->eq('t.approvalDate','0');
                    break;
                default:
                    break;
            }
        }

        if($form->get('deleted')->getData()){
            $conditions[] = $qb->expr()->eq('t.deleted',$form->get('deleted')->getData());
        }else{
            $conditions[] = $qb->expr()->eq('t.deleted',0);
        }


        if(!empty($conditions)){
            $conditions = call_user_func_array(array($qb->expr(), 'andx'), $conditions);
        }
        $qb->where($conditions);
        return $qb;
    }
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
    /**
     * Count element from entity
     * @return integer
     */
    public function maxUid(){
        return $this->createQueryBuilder('t')
            ->select('MAX(t.uid)')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }
    /**
     * Count element from entity
     * @return integer
     */
    public function minUid(){
        return $this->createQueryBuilder('t')
            ->select('MIN(t.uid)')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }
    /**
     * Count element from entity
     * @return integer
     */
    public function maxTrackId(){
        return $this->createQueryBuilder('t')
            ->select('MAX(t.trackId)')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }
    /**
     * Count element from entity
     * @return integer
     */
    public function minTrackId(){
        return $this->createQueryBuilder('t')
            ->select('MIN(t.trackId)')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    /**
     * Count element from entity
     * @return integer
     */
    public function minReleaseDate(){
        return $this->createQueryBuilder('t')
            ->select('MIN(t.releaseDate)')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }
    /**
     * Count element from entity
     * @return integer
     */
    public function maxReleaseDate(){
        return $this->createQueryBuilder('t')
            ->select('MAX(t.releaseDate)')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function approbationAverage(){
        $total = $this->count();
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select(
                "count(t.uid)",
                "(100*(count(t.uid)/".$total.")) as percent"
            )
            ->from($this->getEntityName(), "t")
            ->where($queryBuilder->expr()->neq('t.approvalDate', 0))
            //->groupBy("mf.tagPass")
        ;

        $r = $queryBuilder->getQuery()->getSingleResult();
        return $r['percent'];
    }

    /**
     * @return mixed
     */
    public function countApproved(){

        $query = $this
                ->getEntityManager()
                ->createQuery("SELECT count(t) FROM ".$this->getEntityName()." t where t.approval = 1");
        return $query->getSingleScalarResult();
    }
    /**
     * @return mixed
     */
    public function countNotApproved(){

        $query = $this
                ->getEntityManager()
                ->createQuery("SELECT count(t) FROM ".$this->getEntityName()." t where t.approval = -1");
        return $query->getSingleScalarResult();
    }
    /**
     * @return mixed
     */
    public function countNeutralApproved(){

        $query = $this
                ->getEntityManager()
                ->createQuery("SELECT count(t) FROM ".$this->getEntityName()." t where t.approval = 0 and t.approvalDate <> 0");
        return $query->getSingleScalarResult();
    }
    /**
     * @return mixed
     */
    public function countNotYetApproved(){

        $query = $this
                ->getEntityManager()
                ->createQuery("SELECT count(t) FROM ".$this->getEntityName()." t where t.approval = 0 and t.approvalDate = 0");
        return $query->getSingleScalarResult();
    }
    /**
     * @return mixed
     */
    public function countForDelete(){

        $query = $this
                ->getEntityManager()
                ->createQuery("SELECT count(t) FROM ".$this->getEntityName()." t where t.approval = -1 AND t.hardDelete = 0");
        return $query->getSingleScalarResult();
    }

    /**
     * @param int $limit
     * @return array
     */
    public function findForDeleted($limit = 10){

        $query = $this
                ->getEntityManager()
                ->createQuery("SELECT t FROM ".$this->getEntityName()." t where t.approval = -1 AND t.hardDelete = 0")
                ->setMaxResults(intval($limit));
        return $query->getResult();
    }
    /**
     * @return mixed
     */
    public function countDeleted(){

        $query = $this
                ->getEntityManager()
                ->createQuery("SELECT count(t) FROM ".$this->getEntityName()." t where t.hardDeleteDate > 0 AND t.hardDelete = 0");
        return $query->getSingleScalarResult();
    }

    /**
     * @param array $data
     * @return \Doctrine\ORM\Query
     */
    public function searchQuery($data = array()){
        $queryBuilder = $this->createQueryBuilder('t');
        //$queryBuilder->select('t')->from($this->getEntityName(), " t");
        if(isset($data['text']) && !empty($data['text'])){
            $queryBuilder->where("t.fulltitle LIKE :fulltitle_text");
            $queryBuilder->setParameter("fulltitle_text", "%".$data['text']."%");
        }
        return $queryBuilder->getQuery();
        return $queryBuilder->getDQL();
    }

}