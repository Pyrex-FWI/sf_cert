<?php
/**
 * Created by PhpStorm.
 * User: Angie
 * Date: 15/03/2015
 * Time: 09:20
 */

namespace Cpyree\DigitalDjPoolBundle\Service;


use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Finder\Finder;

class FixPath {

    /**
     * @var int
     */
    private $poolSize = 0;
    private $maxPage = 0;
    private $subPathLimit = 0;
    /** @var Container */
    private $service_container;
    /**
     * @var
     */
    private $searchPath;

    public function __construct($service_container){
        $this->service_container = $service_container;
    }

    /**
     * @return Container
     */
    public function getContainer(){
        return $this->service_container;
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
     * @param int $subPathLimit
     */
    public function execute($subPathLimit = 2)
    {
        gc_enable();
        $trackRepository = $this->getTrackRepository();
        $this->setPoolSize($this->getParameter('ddp.console.pool_size'));
        $this->setSearchPath($this->getParameter('ddp.console.ddp_folder'));
        $this->setSubPathLimit($subPathLimit) ? $subPathLimit : 0;
        $this->setMaxPage($trackRepository->count());

        /** @var CleanDir $cd */
        $cd = $this->getContainer()->get('cpyree_digital_dj_pool.clean_dir');
        $cd->clean();


        $count = 1;
        $pack = array();
        $pathsToAnalyze = $this->getRequestedPaths();

        $regex = '/(?P<trackId>[\d]{4,6})_.*\.mp3$/';
        foreach($pathsToAnalyze as $path){
            //$this->output->writeln($path);
            //$this->output->writeln($this->convert(memory_get_usage()));
            $f = new Finder();
            foreach($f->files()->size(' >= 1M')->depth(' == 0')->name($regex)->in($path) as $item){

                if(preg_match($regex,$item->getPathName(), $match) !== 1){
                    //$this->output->writeln('trackId not found for '. $item->getPathName());
                    continue;
                }
                //$this->output->writeln($item->getPathName());

                $pack[] = array('fullPath' => $item->getPathName(), 'trackId' => $match['trackId']);
                if($count % $this->getPoolSize() == 0){
                    $this->updatePath($pack);
                    $pack = array();
                    $count = 0;
                }
                $count++;
            }
        }

        if(count($pack) > 0){
            $this->updatePath($pack);
        }
    }

    public function updatePath($files){
        //$this->output->writeln("Update ".count($files)." files.");
        $em = $this->getManager();
        foreach($files as $file){
            /** @var Track $track */
            //$this->output->writeln('Search : '.$file['trackId']);
            $track = $this->getTrackRepository()->findOneByTrackId($file['trackId']);
            if(!is_object($track)) continue;
            //$this->output->writeln("search :". $file['trackId']. ', found with uid:' . $track->getUid());

            $track->setFullPath($file['fullPath']);
            $em->persist($track);
            unset($track);
        }
        $em->flush();
        $em->clear();
        unset($em);
    }

    public function convert($size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    /**
     * @param $page
     * @param int $limit
     * @return mixed
     */
    public function getTrackItems($page, $limit = 20){
        $em    = $this->getManager();
        $dql   = "SELECT t FROM CpyreeDigitalDjPoolBundle:Track t ORDER BY t.uid DESC";
        $query = $em->createQuery($dql);

        $paginator  = $this->getContainer()->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page,
            $limit
        );
        //To reduce memory usage
        $em->flush();
        //To reduce memory usage
        $em->clear();
        return $pagination;
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

    private function getRequestedPaths()
    {
        $f = new Finder();
        $paths = array();
        foreach($f->directories()->name('/\d{3,7}$/')->depth(' == 0')->in($this->getSearchPath())->sortByName() as $dir) {
            $paths[] = $dir->getPathName();
        }
        $paths = array_reverse($paths);
        if($this->getSubPathLimit()>0){
            $paths = array_slice($paths,0, $this->getSubPathLimit());
        }
        return $paths;
    }

}