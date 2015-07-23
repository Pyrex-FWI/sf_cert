<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 12/10/2014
 * Time: 16:06
 */

namespace Cpyree\DigitalDjPoolBundle\Twig\Extension;


use Cpyree\AudioDataBundle\Libs\Tag;
use Cpyree\AudioDataBundle\Services\AlbumCover;
use Cpyree\AudioDataBundle\Services\ItunesAbstractMusicInfoProvider;
use Cpyree\DigitalDjPoolBundle\Entity\Track;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Router;

class CoverExtension extends \Twig_Extension{

    /** @var Container  */
    private $container;
    public function __construct(Container $service_container){
        $this->container = $service_container;
    }
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter(
                'cover',
                array($this, 'coverFilter'),
                array(
                    'pre_escape' => 'html',
                    'is_safe' => array('html')
                )
            ),
        );
    }


    /**
     * @param Track $track
     * @return string
     */
    public function coverFilter(Track $track)
    {
        /** @var AlbumCover $albumCover */
        $albumCover = $this->container->get('cpyree_audio_data.album_cover.digitaldjpool');
        $albumCover->setConf('digitaldjpool');
        $albumCover->setCurrentSource('ddp_track');
        $albumCover->musicInfo->setProvider('itune');


        $coverFile = $albumCover->findOne($track);
        $pathInfo = pathinfo($coverFile);
        return "bundles/cpyreedigitaldjpool/images/covers/" . $pathInfo['basename'];
        /** @var ItunesAbstractMusicInfoProvider $musicInfo */
        //$musicInfo = $this->container->get('cpyree_audio_data.itunes_music_info');
        /*$c = $musicInfo->getInfo($track->getFulltitle());
        if(isset($c->tags[0])) {
            $c = $c->tags[0]->getCovers();
            $c = $c[2];
        }else{
            $c = null;
        }

        $link = !is_null($c) ? sprintf('<img src="%s" />',$c) : "<!-- no cover -->";

        return $link;*/
    }
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return str_replace("\\",'_','Cpyree\DigitalDjPoolBundle\Twig\Cover');
    }
}