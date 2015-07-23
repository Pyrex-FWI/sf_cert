<?php
/**
 * Created by PhpStorm.
 * User: chpyr
 * Date: 26/03/14
 * Time: 13:35
 */

namespace Cpyree\AudioDataBundle\Services\MusicInfo;


class SpotifyMusicInfoProvider extends AbstractMusicInfoService implements IMusicInfoProvider{


    public function search($search)
    {
        $this->term = $search;
        $this->buildURI();
        return $this->execQuery();
    }

    
        
    private function buildURI(){
        $this->queryURI = "http://ws.spotify.com/search/1/track.json?q=" . str_replace(' ', '+', $this->term);
    }
    
    public function ifResult(){
        
        if(isset($this->cacheResult['info']['num_results']) && $this->cacheResult['info']['num_results'] > 0 ){
            return true;
        }
        return false;
    }
    
    public function buildTagCollection() {
        if($this->ifResult() === false) return 0;
        foreach($this->cacheResult['tracks'] as $result){
            $t = new \Cpyree\AudioDataBundle\Libs\Tag();
            $t	->setAlbum(trim($result['album']['name']));
            foreach($result['artists'] as $artist){
                $t->addArtist(trim($artist['name']));
            }
            $t->setYear(trim($result['album']['released']));
            $t->setTitle(trim($result["name"]));
            $t->setSource($this->getName());

            $this->tagsCollection->add($t);
        }
    }
    public function getName()
    {
        return 'spotify';
    }
}