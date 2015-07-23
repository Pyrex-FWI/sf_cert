<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 25/10/2014
 * Time: 12:37
 */

namespace Cpyree\SynologyBundle\Webapi;


use Cpyree\SynologyBundle\Services\SynologySession;

class WebApiRequest {


    private $apiName;
    private $version;
    private $path;
    private $method;
    private $params = array();
    private $additional = array();
    protected $loopWait = 15; //sec

    /**
     * @var SynologySession
     */
    private $session;

    /**
     * @param SynologySession $session
     * @throws \Exception
     */
    public function __construct(SynologySession $session, $method = 'POST'){
            if($session instanceof \Cpyree\SynologyBundle\Services\SynologySession){
                $this->setSession($session);
                $this->session->setMethod($method);
            }else{
                throw new \Exception(get_class($this). ' excepted a \Cpyree\SynologyBundle\Services\SynologySession instance object '.get_class($session) . ' given.');
            }
    }

    /**
     * @param SynologySession $session
     * @return $this
     */
    public function setSession(SynologySession $session){
        $this->session = $session;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiName()
    {
        return $this->apiName;
    }

    /**
     * @param  $apiName
     * @throws Exception
     * @return $this
     */
    public function setApiName($apiName)
    {
        $this->apiName = $apiName;

        if($this->session->apiExist($apiName)){
            $this->setPath($this->session->getPath($apiName));
            $this->setVersion($this->session->getVersion($apiName));
        }else{
            throw new Exception("$apiName not available in current session");
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param $version
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    public function addParam($key, $val){
        $this->params[$key] = $val;
        return $this;
    }

    public function addAdditional($val){
        $this->additional[] = $val;
        return $this;
    }
    /**
     * GET
    /webapi/<CGI_PATH>?api=<API_NAME>&version=<VERSION>&method=<METHOD>[&<PARAMS>][&_si
    d=<SID>]
     * @return string
     */
    public function getUri(){
        $r = "".
            "/webapi/" . $this->getPath() .
            "?api=" . $this->getApiName() .
            "&version=" . $this->getVersion() .
            "&method=" . $this->getMethod();
        if(!empty($this->params)) $r .= '&' . http_build_query($this->params);
        if(!empty($this->additional)) $r .= '&additional=' . urlencode(implode(',', $this->additional));
        return $r;
    }

    /**
     * @return WebApiResponse
     */
    public function send(){

        //print_r("-->".$this->getUri()."<br/>");
        $r = $this->session->send(
             $this->getPath(),
             array_merge(
                 $this->params,
                 array(
                    "api"       =>  $this->getApiName(),
                    "version"   =>  $this->getVersion(),
                    "method"    =>  $this->getMethod(),
                    "additional"=>  implode(',', (array)$this->additional)
                    //"additional"=>  urlencode(implode(',', (array)$this->additional))
                 )
             ));

        return $r;
    }

    /**
     * @param int $seconds
     * @return int
     */
    protected function wait($seconds  = 0){
        return $seconds > 0 ? sleep($seconds) : sleep($this->loopWait);
    }


} 