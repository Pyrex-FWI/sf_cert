<?php
/**
 * Created by PhpStorm.
 * User: Angie
 * Date: 22/02/2015
 * Time: 10:30

 *
 */

namespace Cpyree\AudioDataBundle\Services;


use Cpyree\AudioDataBundle\Libs\TagCollection;
use Cpyree\AudioDataBundle\Services\MusicInfo\DeezerMusicInfoProvider;
use Cpyree\AudioDataBundle\Services\MusicInfo\IMusicInfoProvider;
use Cpyree\AudioDataBundle\Services\MusicInfo\ItunesMusicInfoProvider;
use Cpyree\AudioDataBundle\Services\MusicInfo\SpotifyMusicInfoProvider;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Config\Definition\Exception\Exception;

class MusicInfo{
    private $providers = array();
    /** @var Container */
    private $service_container;
    /** @var  Logger */
    private $logger;

    public function __construct($service_container){
        $this->service_container = $service_container;
        $this->logger =$this->service_container->get('logger');
        $this->logger->info('Init MusicInfo service');
        $this->addProvider($this->service_container->get('cpyree_audio_data.itunes_music_info'));
        $this->addProvider($this->service_container->get('cpyree_audio_data.deezer_music_info'));
        $this->addProvider($this->service_container->get('cpyree_audio_data.spotify_music_info'));
    }

    public function search($terms){
        $ite = new TagCollection();
        $terms = preg_replace('/\s?\([^)]+\)\s+/', ' ', $terms);
        $this->logger->info('Search with keywords: '. $terms);
        $send = false;
        foreach($this->providers as $pr){
            if($pr['selected'] == false) continue;
            $result = null;
            /** @var IMusicInfoProvider $provider */
            $provider = &$pr['provider'];
            $this->logger->info('search on: '.$provider->getName());
            $result = $provider->search($terms);
            $this->logger->info('founded : '.count($result));
            if(count($result)>0){
                $send = true;
                $ite->append($result->tags);
            }
        }
        return $send? $ite : array();
    }

    public function addProvider(MusicInfo\IMusicInfoProvider $provider, $priority = 0, $selected = false){
        $this->providers[$provider->getName()] = array(
            'provider'  =>  $provider,
            'priority'  =>  $priority,
            'selected'  =>  $selected,
        );

        $this->sortProvider();
    }
    public function setProvider($provider){
        $this->resetProviders();
        if(!isset($this->providers[$provider])){
            throw new Exception("Provider $provider not found");
        }
        $this->providers[$provider]['selected'] = true;
    }

    private function resetProviders(){
        foreach($this->providers as &$provider){
            $provider['selected'] = false;
        }
    }
    private function sortProvider()
    {
        uasort($this->providers, function (array $a, array $b) {
            /*if ($a['selected'] || $b['selected']) {
                return $a['selected'] ? -1 : 1;
            }*/

            return $a['priority'] > $b['priority'] ? -1 : 1;
        });

        return $this;
    }
}