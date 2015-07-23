<?php

namespace Cpyree\SynologyBundle\Webapi;

use Cpyree\SynologyBundle\Services\SynologySession;

class DeleteRequest extends WebApiRequest{

	private $source;

	/**
	 * @return mixed
	 */
	public function getSource()
	{
		return $this->source;
	}

	/**
	 * @param mixed $source
	 * @return $this
	 */
	public function setSource($source)
	{
		$this->source = $source;
		return $this;
	}
    /**
     * @param SynologySession $session
     * @return $this
     * @throws Exception
     */
    public function setSession(SynologySession $session)
    {
        parent::setSession($session);
        $this->setApiName('SYNO.FileStation.Delete');
        return $this;
    }


	/**
	 * @return WebApiResponse
	 */
	public function send(){
		$this
			->setMethod('delete')
			->addParam('path', $this->getSource());
		return parent::send();
	}


}
