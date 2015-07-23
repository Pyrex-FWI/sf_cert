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
use Cpyree\SynologyBundle\Services\SynologySession;
use Cpyree\SynologyBundle\Webapi\DeleteRequest;
use Cpyree\SynologyBundle\Webapi\MoveCopyRequest;
use Cpyree\SynologyBundle\Webapi\SearchSynoRequest;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RemoveCommand
 * @package Cpyree\DigitalDjPoolBundle\Command
 */
class ApproveCleanCommand extends ContainerAwareCommand {

    /**
     * @var SynologySession
     */
    private static $session;

    /**
     * @var SearchSynoRequest
     */
    private $searchApi;

    /**
     * @var MoveCopyRequest
     */
    private $moveCopyApi;
    /**
     * @var DeleteRequest
     */
    private $deleteApi;

    /** @var  OutputInterface */
    private $output;

    /** @var  InputInterface */
    private $input;

	/**
	 * @var int
	 */
	private $countForDelete = 0;

	/**
	 * @var int
	 */
	private $totalPages = 1;

	/**
	 * @var int
	 */
	private $poolSize = 0;

	/**
	 * @var
	 */
	private $searchPath;

	/**
	 * @var
	 */
	private $recyclePath;

	/**
	 *
	 */
	protected function configure()
    {
        $this
            ->setName('ddp:clean');
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
     * @return SynologySession
     */
    private function getSession()
    {
        if(self::$session instanceof \Cpyree\SynologyBundle\Services\SynologySession){
            if(self::$session->isLogin() == false) self::$session->login();
        }else{
            self::$session = $this->getContainer()->get('cpyree_synology.session');
            self::$session->login();
            $this->searchApi = new SearchSynoRequest(self::$session);
            $this->moveCopyApi = new MoveCopyRequest(self::$session);
            $this->deleteApi = new DeleteRequest(self::$session);
        }
        return self::$session;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $trackRepository = $this->getTrackRepository();
        $this->setPoolSize($this->getParameter('ddp.console.pool_size'));
        $this->setSearchPath($this->getParameter('ddp.console.ddp_folder'));
        $this->setRecyclePath($this->getParameter('ddp.console.ddp_recycle_folder'));
        $this->setCountForDelete($trackRepository->countForDelete());
        $this->initTotalPages();

        if($this->getSession()->isLogin()){
            $this->doJob();
        }
        else{
			 $this->output->writeln("Error login on synology server");
        }
    }

    /**
     * @param $f
     * @param $recyclePath
     * @return bool
     */
    private function placeToTrash($f, $recyclePath)
    {
        if($this->moveCopyApi->copy($f, $recyclePath, true, true)){
            $this->output->writeln($f . " was succesfully copied/moved to ".$recyclePath);
            return $this->delete($f);
        }
        return false;
    }

    /**
     * @param $f
     * @return bool
     */
    private function delete($f)
    {
        if($this->deleteApi->setSource($f)->send()->is('success')) {
            $this->output->writeln($f . " was succesfully removed");
            return true;
        }
        return false;
    }

    /**
     * @param Track $track
     */
    private function markTrackAsDeleted(Track $track)
    {
        $eM = $this->getManager();
        $track->setHardDelete(true)->setHardDeleteDate(time());
        $eM->persist($track);
        $eM->flush();
    }

	/**
	 *
	 */
	private function initTotalPages()
    {
        $this->setTotalPages(ceil($this->getCountForDelete()/$this->getPoolSize()));
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     * @param int $totalPages
     */
    public function setTotalPages($totalPages)
    {
        $this->totalPages = $totalPages;
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
     * @return int
     */
    public function getCountForDelete()
    {
        return $this->countForDelete;
    }

    /**
     * @param int $countForDelete
     */
    public function setCountForDelete($countForDelete)
    {
        $this->countForDelete = $countForDelete;
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
     * @return mixed
     */
    public function getRecyclePath()
    {
        return $this->recyclePath;
    }

    /**
     * @param mixed $recyclePath
     */
    public function setRecyclePath($recyclePath)
    {
        $this->recyclePath = $recyclePath;
    }

	/**
	 * @param $set
	 */
	private function poolProcess($set)
    {
        $trashFiles = "";
        foreach ((array)$set as $data) {
            $track = $data['track'];
            if (strlen($trashFiles) > 0) $trashFiles .= ",";
            $trashFiles .= $data['path'];
        }
            /** @var Track $track */
            $this->placeToTrash($trashFiles, $this->getRecyclePath());

        foreach ((array)$set as $data) {
            $track = $data['track'];
            $this->markTrackAsDeleted($track);
        }
    }

    /**
     * @param $allSaparSearchFiles
     * @param $tracksCollection
     * @return mixed
     */
    private function getAssociatedTrack($allSaparSearchFiles, $tracksCollection)
    {
        for($i = 0; $i < count($allSaparSearchFiles); $i++){
            foreach($tracksCollection as $track){
                /** @var Track $track */
                $patern = "/^{$track->getTrackId()}_/";
                if(preg_match($patern, $allSaparSearchFiles[$i]['name']))  $allSaparSearchFiles[$i]['track'] = $track;
            }
        }

        return $allSaparSearchFiles;
    }

    /**
     * @param $all
     * @return string
     */
    private function buildSearchPatern($all)
    {
        $searchString = "";
        foreach($all as $one){
            /** @var Track $one */
            if(strlen($searchString) > 0){
                $searchString .=",";
            }
            $searchString .= $one->getTrackId()."_*";
        }
        return $searchString;
    }

    /**
     * @param $filesToRemove
     * @return array
     */
    private function removeFileAlreadyInTrash($filesToRemove)
    {
        $r = array();
        foreach((array)$filesToRemove as $file){
            if(preg_match("#{$this->getRecyclePath()}#", $file['path']) || (count($filesToRemove) > 0 && strrpos($file['path'],',') !== false)){
                $this->markTrackAsDeleted($file['track']);
                continue;
            }
            $r[] = $file;
        }
        return $r;
    }

	/**
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
	 *
	 */
	private function doJob()
	{
		for($i = 0; $i < $this->getTotalPages(); $i++){

			$this->showInfo();

			$tracksCollection = $this->getTrackRepository()->findForDeleted($this->getPoolSize());

			$searchString = $this->buildSearchPatern($tracksCollection);

			$r = $this->searchApi->search($this->getSearchPath(), $searchString)->send();

			$filesToRemove = (array)$r->get('files');

			$filesToRemove = $this->getAssociatedTrack($filesToRemove, $tracksCollection);

			$this->output->writeln(count($filesToRemove));

			$filesToRemove = $this->removeFileAlreadyInTrash($filesToRemove);

			$this->output->writeln(count($filesToRemove));

			if(count($filesToRemove)>0) {
				$this->poolProcess($filesToRemove);
			}
		}
	}


}