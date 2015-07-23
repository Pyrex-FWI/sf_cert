<?php
/**
 * Created by PhpStorm.
 * User: chpyr
 * Date: 20/02/15
 * Time: 09:44
 */

namespace Cpyree\AudioDataBundle\Services\AlbumCover;


use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class AlbumCoverDatabaseItemIterator implements \Iterator, \Countable{

	/**
	 * @var EntityManager
	 */
	private $em;

	private $entityName = null;

	private $itemsPortion = array();

	private $portionSize = 1000;

	private $maxPage = 0;

	private $page = 1;

    private $itemName = 0;

    private $itemsCount;
    /** @var  \Doctrine\ORM\Mapping\ClassMetadata */
    private $classMetaData;

	private $currentPortionPosition = 0;

	public function __construct(EntityManager $em, $entityName){
		$this->em = $em;
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->classMetaData = $this->em->getClassMetadata($entityName);
		$this->entityName = $entityName;
        $this->itemsCount = $this->count();
        $this->maxPage = ceil($this->itemsCount/$this->portionSize);
	//	$this->init();
	}
	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the current element
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 */
	public function current()
	{
		return $this->itemsPortion[$this->currentPortionPosition];
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	public function next()
	{
		if($this->currentPortionPosition == ($this->portionSize -1)){
			$this->currentPortionPosition  = 0;

			$this->itemsPortion = $this->getData();
            $this->page++;
		}
		else{
			$this->currentPortionPosition++;
		}

	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 */
	public function key()
	{
		$this->currentPortionPosition;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Checks if current position is valid
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 */
	public function valid()
	{
		return isset($this->itemsPortion[$this->currentPortionPosition]);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 */
	public function rewind()
	{
		$this->init();
	}

	private function init()
	{
		$this->currentPortionPosition = 0;

		$this->itemsPortion = $this->getData();
	}

    /**
     * @return array
     */
    private function getData(){
        gc_enable();
		$arrayObject = array();
		$qB = $this->em->createQueryBuilder();
		$qB->from($this->entityName, 't');
		$qB->select('t');
		$qB->setMaxResults($this->portionSize);
		$qB->setFirstResult(($this->page - 1) * $this->portionSize);
		foreach($qB->getQuery()->getResult() as $item){
			$arrayObject[] = new DatabaseAlbumCoverItem($item, $this->classMetaData);
		}
		$this->em->clear();
        gc_collect_cycles();
		return $arrayObject;
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Count elements of an object
	 * @link http://php.net/manual/en/countable.count.php
	 * @return int The custom count as an integer.
	 * </p>
	 * <p>
	 * The return value is cast to an integer.
	 */
	public function count()
	{
		$portion =  $this->em->createQueryBuilder()
			->select('COUNT(cnt)')
			->from($this->entityName, 'cnt')
			->getQuery()
			->getSingleScalarResult()
			;
		return $portion;
	}

}