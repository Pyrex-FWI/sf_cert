<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 25/10/2014
 * Time: 23:20
 */

namespace Cpyree\SynologyBundle\Webapi;


class MoveCopyRequest extends WebApiRequest{

    /** @var MoveCopyStartRequest $startRequest */
    private $startRequest;

    /** @var MoveCopyStatusRequest $listRequest */
    private $statusRequest;

    /** @var MoveCopyStopRequest $stopRequest */
    private $stopRequest;

    /** @var MoveCopyCleanRequest $cleanRequest */
    private $cleanRequest;

    private $source;

    private $destination;

    private $overwrite = false;

    private $removeSource = false;

    private $apiErrorCode = [
        1000  =>  "Failed to copy files/folders. More information in errors object",
        1001  =>  "Failed to move files/folders. More information in errors object",
        1002  =>  "An error occurred at the destination. More information in errors object.",
        1003  =>  "Cannot overwrite or skip the existing file because no overwrite parameter is given.",
        1004  =>  "File cannot overwrite a folder with the same name, or folder cannot overwrite a file with the same name.",
        1006  =>  "Cannot copy/move file/folder with special characters to a FAT32 file system",
        1007  =>  "Cannot copy/move a file bigger than 4G to a FAT32 file system",

    ];

    public function __construct(\Cpyree\SynologyBundle\Services\SynologySession $session)
    {
        parent::__construct($session);
        $this
            ->setStartRequest($session)
            ->setStatusRequest($session)
            ->setStopRequest($session);
            //->setCleanRequest($session);
    }

    /**
     * @param \Cpyree\SynologyBundle\Services\SynologySession $session
     * @return $this
     */
    private function setStartRequest(\Cpyree\SynologyBundle\Services\SynologySession $session)
    {
        $this->startRequest = new MoveCopyStartRequest($session);
        return $this;
    }
    private function setStatusRequest(\Cpyree\SynologyBundle\Services\SynologySession $session)
    {
        $this->statusRequest = new MoveCopyStatusRequest($session);
        return $this;
    }
    private function setStopRequest(\Cpyree\SynologyBundle\Services\SynologySession $session)
    {
        $this->stopRequest = new MoveCopyStopRequest($session);
        return $this;
    }
    private function setCleanRequest(\Cpyree\SynologyBundle\Services\SynologySession $session)
    {
        $this->cleanRequest = new MoveCopyCleanRequest($session);
        return $this;
    }

    public function setSource($fileOrDir)
    {
        $this->source = $fileOrDir;
        $this->startRequest->setSource($fileOrDir);
        return $this;
    }

    /**
     * @param $dest
     * @return $this
     */
    public function setDestination($dest)
    {
        $this->destination = $dest;
        $this->startRequest->setDestination($dest);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return mixed
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @return boolean
     */
    public function getOverwrite()
    {
        return $this->overwrite;
    }

    /**
     * @param boolean $overwrite
     * @return $this
     */
    public function setOverwrite($overwrite)
    {
        $this->overwrite = $overwrite;
        $this->startRequest->setOverwrite($overwrite);
        return $this;

    }

    /**
     * @return boolean
     */
    public function getRemoveSource()
    {
        return $this->removeSource;
    }

    /**
     * @param boolean $removeSource
     * @return $this
     */
    public function setRemoveSource($removeSource)
    {
        $this->removeSource = $removeSource;
        $this->startRequest->setRemoveSource($removeSource);
        return $this;

    }

    /**
     * @param $source
     * @param $dest
     * @param bool $removeSource
     * @return bool
     */
    public function copy($source, $dest, $removeSource = false, $overwrite = false)
    {
        $this
            ->setSource($source)
            ->setDestination($dest)
            ->setRemoveSource($removeSource)
            ->setOverwrite($overwrite);
        $start = $this->startRequest->send();
        //print_r($start);
        $taskId = $start->get('taskid');
        if($taskId == ""){
            throw new \Exception("no task Id");
        }
        $r = $this->statusRequest->setTaskId($taskId)->send($this->loopWait);
        $this->stopRequest->setTaskId($taskId)->send();
        return $r->is('success')? true : false;
    }

    public function move($source, $dest, $removeSource = true)
    {

    }




} 