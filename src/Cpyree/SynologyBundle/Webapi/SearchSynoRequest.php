<?php

namespace Cpyree\SynologyBundle\Webapi;

class SearchSynoRequest extends WebApiRequest{

    /** @var SearchStartSynoRequest $startRequest */
    private $startRequest;

    /** @var SearchListSynoResponse $listRequest */
    private $listRequest;

    /** @var SearchStopSynoRequest $stopRequest */
    private $stopRequest;

    public function __construct(\Cpyree\SynologyBundle\Services\SynologySession $session)
    {
        parent::__construct($session);
        $this->startRequest = new SearchStartSynoRequest($session);
        $this->stopRequest = new SearchStopSynoRequest($session);
        $this->listRequest  = new SearchListSynoRequest($session);
    }


    /**
     * @param $path
     * @param $pattern
     * @return $this
     */
    public function search($path, $pattern){
        $taskId = $this->startRequest->search($path, $pattern);
        return $this;
    }

    /**
     * @return SearchListSynoResponse
     */
    public function send(){
        $taskId = $this->startRequest->send()->get('taskid');
        $r = $this->listRequest->setTaskId($taskId)->send($this->loopWait);
        $this->stopRequest->setTaskId($taskId)->send();
        return $r;
    }


}
