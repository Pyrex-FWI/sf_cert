<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 25/10/2014
 * Time: 08:52
 */

namespace Cpyree\DigitalDjPoolBundle\Command;


use Cpyree\DigitalDjPoolBundle\Entity\Track;
use Cpyree\DigitalDjPoolBundle\Entity\TrackRepository;
use Cpyree\DigitalDjPoolBundle\Service\FixPath;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * Class RemoveCommand
 * @package Cpyree\DigitalDjPoolBundle\Command
 */
class FixLocalPathCommand extends ContainerAwareCommand {

    /** @var  OutputInterface */
    private $output;

    /** @var  InputInterface */
    private $input;
    /**
     * @var int
     */
    private $poolSize = 0;
    private $maxPage = 0;
    private $subPathLimit = 0;

    /**
     * @var
     */
    private $searchPath;

	protected function configure()
    {
        $this
            ->setName('ddp:fix-path')
            ->addArgument('path', InputArgument::OPTIONAL, 'Path to search DigitalDjPool')
            ->addOption('subpath-limit',null, InputOption::VALUE_REQUIRED, 'Sub-directories Path limit');
    }

    /**
     * @return Registry
     */
    private function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
    }

    /**
     * @param string $name
     * @return EntityManager
     */
    private function getManager($name = "ddp_manager")
    {
        return $this->getDoctrine()->getManager($name);
    }

    /**
     * @return TrackRepository
     */
    private function getTrackRepository()
    {
        return $this->getManager()->getRepository('CpyreeDigitalDjPoolBundle:Track');
    }

	/**
	 * @param $val
	 * @return mixed
	 */
	private function getParameter($val)
    {
        return $this->getContainer()->getParameter($val);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        gc_enable();
        $this->input = $input;
        $this->output = $output;
        /** @var FixPath $fixDir */
        $fixDir = $this->getContainer()->get('cpyree_digital_dj_pool.fix_path');
        $fixDir->execute();
    }

    public function convert($size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }


    /**
     * @return int
     */
    public function getPoolSize()
    {
        return $this->poolSize;
    }

    /**
     * @param int $poolSize
     */
    public function setPoolSize($poolSize)
    {
        $this->poolSize = $poolSize;
    }



    /**
     * @return mixed
     */
    public function getSearchPath()
    {
        return $this->searchPath;
    }

    /**
     * @param mixed $searchPath
     * @return $this
     */
    public function setSearchPath($searchPath)
    {
        $this->searchPath = $searchPath;
        return $this;
    }


	/**
     *
	 *
	 */
	private function showInfo()
	{
		/** @var Table $table */
		$table = $this->getHelperSet()->get('table');
		$table->setRows(array(
			array("Track",$this->getTrackRepository()->count()),
			array("Approved",$this->getTrackRepository()->countApproved()),
			array("Not approved",$this->getTrackRepository()->countNotApproved()),
			array("Must delete",$this->getTrackRepository()->countForDelete()),
			array("Already delete",$this->getTrackRepository()->countDeleted()),
			array("Iteration",$this->getTotalPages()),
			array("ddp.console.pool_size",$this->getParameter('ddp.console.pool_size')),
			array("ddp.console.ddp_folder",$this->getParameter('ddp.console.ddp_folder')),
			array("ddp.console.ddp_recycle_folder",$this->getParameter('ddp.console.ddp_recycle_folder')),
		));
		$table->render($this->output);
	}

    /**
     * @return int
     */
    public function getMaxPage()
    {
        return $this->maxPage;
    }

    /**
     * @param int $allItemCount
     */
    public function setMaxPage($allItemCount)
    {
        if($this->getPoolSize() > 0) {
            $this->maxPage = ceil($allItemCount / $this->getPoolSize());
        }
    }

    /**
     * @return int
     */
    public function getSubPathLimit()
    {
        return $this->subPathLimit;
    }

    /**
     * @param int $subPathLimit
     */
    public function setSubPathLimit($subPathLimit)
    {
        $this->subPathLimit = $subPathLimit;
    }



    /**
	 *
	 */


}