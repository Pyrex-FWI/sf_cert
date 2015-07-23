<?php
/**
 * Created by PhpStorm.
 * User: chpyr
 * Date: 26/03/14
 * Time: 13:31
 */

namespace Cpyree\AudioDataBundle\Services\MusicInfo;
use Cpyree\AudioDataBundle\Libs\TagCollection;

/**
 * Classe de communication avec l'API itune
 * 
 */
class DeezerMusicInfoProvider extends AbstractMusicInfoService implements IMusicInfoProvider{

    /**
     * @param $search
     * @return \Cpyree\AudioDataBundle\Libs\TagCollection
     */
    public function search($search)
    {
        $this->term = $search;

        $this->buildURI();
        $r =  $this->execQuery();
        return $r;
        //return $this->term;
    }

    public function setQueryString()
    {
        // TODO: Implement setQueryString() method.
    }

    private function buildURI(){
        $this->queryURI  = "http://api.deezer.com/search/track?q=" . str_replace(' ', '+', $this->term) ."&limit=10";
    }

    
    public function ifResult(){
        if(isset($this->cacheResult['total']) && intval($this->cacheResult['total']) > 0){
            return true;
        }
        return false;
    }

    public function buildTagCollection() {
        if($this->ifResult() === false) return 0;
        foreach($this->cacheResult['data'] as $result){
            $t = new \Cpyree\AudioDataBundle\Libs\Tag();
            $t->setSource($this->getName());
            if(isset($result["artist"]['name'])){
                $t->addArtist(trim($result["artist"]['name']));
            }

            if(isset($result["title"])){
                $t->setTitle(trim(isset($result["title"])? $result["title"]: ''));
            }

            if(isset($result["album"]['cover'])){
                $t->addCover(trim($result["album"]['cover']));
            }
            $this->tagsCollection->add($t);
        }
    }


    public function getName()
    {
        return 'deezer';
    }
}