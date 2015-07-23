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
class ItunesMusicInfoProvider extends AbstractMusicInfoService implements IMusicInfoProvider{

    /**
     * @param $search
     * @return \Cpyree\AudioDataBundle\Libs\TagCollection
     */
    public function search($search)
    {
        $this->term = $search;

        $this->buildURI();
        return $this->execQuery();
        //return $this->term;
    }

    public function setQueryString()
    {
        // TODO: Implement setQueryString() method.
    }

    private function buildURI(){
        $this->queryURI  = "https://itunes.apple.com/search?term=" . str_replace(' ', '+', $this->term) ."&limit=10";
    }

    
    public function ifResult(){
        if(isset($this->cacheResult['resultCount']) && intval($this->cacheResult['resultCount']) > 0){
            return true;
        }
        return false;
    }

    public function buildTagCollection() {
        if($this->ifResult() === false) return 0;
        foreach($this->cacheResult['results'] as $result){
            $t = new \Cpyree\AudioDataBundle\Libs\Tag();
            $t->setSource($this->getName());
            if(isset($result["artistName"])){
                $t->addArtist(trim($result['artistName']));
            }

            if(isset($result["trackName"])){
                $t->setTitle(trim(isset($result["trackName"])? $result["trackName"]: ''));
            }
            if(isset($result["artworkUrl100"])){
                $t->addCover(trim($result['artworkUrl100']));
            }
            if(isset($result["artworkUrl60"])){
                $t->addCover(trim($result['artworkUrl60']));
            }
            if(isset($result["artworkUrl30"])){
                $t->addCover(trim($result['artworkUrl30']));
            }


            if(isset($result["releaseDate"])){
                $t->setYear(trim(substr($result['releaseDate'], 0, 4)));
            }
            $this->tagsCollection->add($t);
        }
    }


    public function getName()
    {
        return 'itune';
    }
}