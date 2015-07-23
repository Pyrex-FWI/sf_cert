<?php
/**
 * Created by PhpStorm.
 * User: chpyr
 * Date: 17/02/15
 * Time: 13:09
 */

namespace Cpyree\AudioDataBundle\Services;


use Cpyree\AudioDataBundle\Services\AlbumCover\AlbumCoverDatabaseItemIterator;
use Cpyree\AudioDataBundle\Services\AlbumCover\AlbumCoverItemInterface;
use Cpyree\AudioDataBundle\Services\AlbumCover\CoverAdapter;
use Cpyree\AudioDataBundle\Services\AlbumCover\DatabaseAlbumCoverItem;
use Cpyree\AudioDataBundle\Services\AlbumCover\FileAlbumCoverAdapter;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Test\Fixture\Entity\Shop\Tag;

class AlbumCover
{


	/** @var Container */
	private $service_container;
	private $cachePath = null;
	private $confName;
    private $currentSource;

	private $sources = array();
	/** @var  MusicInfo */
	public $musicInfo;

    /** @var  Logger */
    private $logger;
	/**
	 * @param Container $service_container
	 * @param null $confName
	 */
	public function __construct(Container $service_container, $confName = null)
	{
		$this->service_container = $service_container;
        $this->logger = $this->service_container->get('logger');
        $this->logger->info("Init AlbumCover service");
		$this->confName = $confName;
		if ($confName) {
			$this->setConf($confName);
		}
		$this->musicInfo = $this->service_container->get('cpyree_audio_data.music_info');
		if (!is_dir($this->cachePath)) {
			mkdir($this->cachePath, 0775, true);
		}
	}

	public function    setConf($confName)
	{
		$albumCoverAllConf = $this->service_container->getParameter('album_cover');
		if (!in_array($confName, array_keys($albumCoverAllConf))) {
			throw new Exception($confName . " n'a pas été trouvé dans la configuration sémantique pour cpyree_audio_data.album_cover");
		}
        $this->logger->info("Set AlbumCover configuration " . $confName);
        //$this->sources = $albumCoverAllConf[$confName]['sources'];
		$this->setSources($albumCoverAllConf[$confName]['sources']);
		$this->setCachePath($albumCoverAllConf[$confName]['cache_path']);
	}

	/**
	 * @param array $sources
	 * @return $this
	 */
	private function setSources($sources = array())
	{
		$this->sources = $sources;
		return $this;
	}

	private function setCachePath($val)
	{
		$this->cachePath = $val;
		return $this;
	}


    function coverExist($item){
        $data = $this->prepareSearchConfForItem($item);
        if (file_exists($data['file'])) return true;
        return false;
    }

	public function run()
	{
		foreach ($this->sources as $sourceName => $source) {
            $this->setCurrentSource($sourceName);

            $itr = $this->getAlbumCoverIterator();
            foreach ($itr as $item) {
                /** @var AlbumCoverItemInterface $item */
                $data = $this->prepareSearchConfForItem($item);
                if($this->coverExist($item)) continue;

                $this->searchAndSaveCover($data);
            }
	    }
	}

	/**
	 * @return \Traversable
	 */
	private function getAlbumCoverIterator()
	{
		if ($this->currentSource['type'] == 'repository') {
			return new AlbumCoverDatabaseItemIterator(
				$this->service_container->get('doctrine')->getManager($this->currentSource['em']),
                $this->currentSource['entityname']
			);
		} else {
			$fs = new Finder();
			$fs->addAdapter(new CoverAdapter());
			$fs->setAdapter('cover');
			return $fs->files()->in($this->currentSource['path'])->name('/mp3$/');
		}
	}

	/**
	 * @param AlbumCoverItemInterface $item
	 * @return array
	 */
	public function prepareSearchConfForItem(AlbumCoverItemInterface $item)
	{
		return array(
			'term' => $item->getSearchTerm($this->currentSource),
			'id' => $this->confName . '_' . $item->getIdentifier(),
			'file' => $this->cachePath . $this->confName . '_' . $item->getIdentifier() . '.jpg'
		);
	}

	/**
     * @param $item
	 */
	public function findOne($item)
	{
		if (is_a($item, 'Symfony\Component\Finder\SplFileInfo')) {
			//return $this->getSearchTermFromFileObject($item);
		} else {
            $classMetaData = $this->service_container->get('doctrine')->getManager($this->currentSource['em'])->getClassMetadata($this->currentSource['entityname']);

            $dbItem  = new DatabaseAlbumCoverItem($item,$classMetaData);
            $data =  $this->prepareSearchConfForItem($dbItem);
            if($this->coverExist($dbItem)){
                return $data['file'];
            }elseif($this->searchAndSaveCover($data)){
                return $data['file'];
            }else{
                return "dummy.png";
            }
        }

	}

    public function setCurrentSource($source = null)
    {
        if($source && isset($this->sources[$source])){
            $this->currentSource = $this->sources[$source];
        }
    }

    public function searchAndSaveCover($data)
    {
        $c = $this->musicInfo->search($data['term']);
        foreach ($c as $info) {
            /** @var \Cpyree\AudioDataBundle\Libs\Tag $info */
            $covers = $info->getCovers();
            if (!empty($covers)) {
                try {
                    file_put_contents($data['file'], file_get_contents($covers[0]));
                    return true;
                    break;
                } catch (Exception $e) {
                    print_r($e->getMessage());
                }
            }
        }
    }


}