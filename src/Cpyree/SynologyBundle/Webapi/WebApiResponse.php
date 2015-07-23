<?php

namespace Cpyree\SynologyBundle\Webapi;

class WebApiResponse{

    protected $raw;

    protected $data = array();

    private $success  = false;

    public function __construct($raw){
        $this->raw = $raw;
        if(isset($raw['data'])){ $this->setData($raw['data']);}
        $this->setSuccess($raw['success']);
    }
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    protected function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    protected function getRaw()
    {
        return $this->raw;
    }


    /**
     * @return mixed
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * @param mixed $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }

    public function is($value){
        if($this->keyExistInData($value)){
            return ($this->data[$value] === true ) ? true : false;
        }elseif(isset($this->raw[$value])){
            return $this->raw[$value] === true ? true : false;
        }
        return false;
    }

    public function get($key){
        if($this->keyExistInData($key)) return $this->data[$key];
        if(isset($this->raw[$key])) return $this->raw[$key];
    }

    private function keyExistInData($key){
        if(isset($this->data[$key])) return true;
        return false;
    }
    private function dataExist(){
        if(!empty($this->data)) return true;
        return false;
    }


}
