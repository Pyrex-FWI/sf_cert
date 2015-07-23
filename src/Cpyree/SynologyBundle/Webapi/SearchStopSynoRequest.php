<?php

namespace Cpyree\SynologyBundle\Webapi;
use Cpyree\DeejayBundle\Services\Synology\SynologyApi;
use Cpyree\SynologyBundle\Services\SynologySession;

class SearchStopSynoRequest extends WebApiRequest{

	private $taskId;

    /**
     * @param SynologySession $session
     * @return $this
     * @throws Exception
     */
    public function setSession(SynologySession $session)
    {
        parent::setSession($session); // TODO: Change the autogenerated stub
        $this->setApiName('SYNO.FileStation.Search');
        return $this;
    }

	/**
	 * @return mixed
	 */
	public function getTaskId()
	{
		return $this->taskId;
	}

	/**
	 * @param mixed $taskId
	 */
	public function setTaskId($taskId)
	{
		if(strlen($taskId) == 0){
			throw new \Exception("Task id must be not empty");
		}
		$this->taskId = $taskId;
		return $this;
	}


	public function send(){
		$this
			->setMethod('stop')
			->addParam('taskid', $this->getTaskId());
		parent::send();
	}


}
