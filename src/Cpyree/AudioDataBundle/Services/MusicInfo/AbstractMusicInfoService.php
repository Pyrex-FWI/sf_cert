<?php
/**
 * Created by PhpStorm.
 * User: chpyr
 * Date: 26/03/14
 * Time: 13:26
 */

namespace Cpyree\AudioDataBundle\Services\MusicInfo;
use Cpyree\AudioDataBundle\Libs;
use Cpyree\AudioDataBundle\Libs\TagCollection;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;


abstract class AbstractMusicInfoService {

    /**
     * List options for curl ressource
     * @var array
     */
    var $curlOptArray = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10
    );
    var $ch = null;
    
    /**
     *
     * @var string 
     */
    var $queryURI;
    /**
     * Type de retour du service distant
     * @var string
     */
    public $responseProvider = "json";

    /**
     *Input user search
     * @var string
     */
    protected $term = null;
    
    private $artists = array();

    private $name;

    private $title;

    private $album;

    private $genre;

    private $year;

    private $coverUrl;
    
    protected $cacheResult = null;
    /** @var  Container */
    private $service_container;

    /** @var  Logger */
    protected $logger;

    /**
     *
     * @var \Cpyree\AudioDataBundle\Libs\TagCollection
     */
    public $tagsCollection;
    
    public function __construct($service_container){
        $this->service_container = $service_container;
        $this->logger = $this->service_container->get('logger');
        $this->logger->info("init: ". get_class($this));
        $this->ch = curl_init();
        $this->tagsCollection = new \Cpyree\AudioDataBundle\Libs\TagCollection();
    }
    
    static public function cleanTerm($term){
        //return str_replace()
    }

    /**
     * 
     * @return \Cpyree\AudioDataBundle\Libs\TagCollection
     */
    protected function execQuery(){
        $this->tagsCollection = new TagCollection();
        $this->logger->info("Fetch data from : ". $this->queryURI);
        curl_setopt_array($this->ch,
            array(
                CURLOPT_URL => $this->queryURI,
                //CURLOPT_COOKIESESSION => true
            )
            +
            $this->curlOptArray
        );

        $result =  curl_exec($this->ch);
        $this->cacheResult = $this->beforeSendResult($result);
        $this->buildTagCollection();
        return $this->tagsCollection;

    }

    public function __destruct(){
        if($this->ch != null)
            curl_close($this->ch);
    }
    
    public function ifResult(){
        return false;
    }

    public function beforeSendResult($result){
        switch ($this->responseProvider) {
            case "json":
                $result = json_decode($result, true);

                break;

            default:
                break;
        }
        
        return $result;
    }

    public function buildTagCollection() {
        
    }

} 