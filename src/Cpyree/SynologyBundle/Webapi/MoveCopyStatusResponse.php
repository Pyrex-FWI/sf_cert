<?php

namespace Cpyree\SynologyBundle\Webapi;


class MoveCopyStatusResponse extends WebApiResponse{

    /**
     * @param SynoResponse $synoResponse
     */
    public function __construct(WebApiResponse $synoResponse)
    {
        $this->raw = $synoResponse->getRaw();
        $this->setData($synoResponse->getData());
        $this->setSuccess($this->raw['success']);
    }

    /**
     * @return array
     */
    public function getResult(){
        $rawResults = $this->get('files');
        $list = array();
        foreach($rawResults as $row){
            $list[$row['name']] = $row['additional']['real_path'];
        }
        return $list;
    }

    /**
     * @param $path
     * @return array
     */
    public function getResultRelativeTo($path){
        $path = (preg_match('/\/$/', $path) === true)? $path : $path ."/";
        $rawResults = $this->get('files');
        $list = array();
        foreach($rawResults as $row){
            $list[$row['name']] = str_replace($path, '', $row['path']);
        }
        return $list;
    }


}
