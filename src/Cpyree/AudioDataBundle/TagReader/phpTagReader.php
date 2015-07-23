<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 24/01/15
 * Time: 17:06
 */

namespace Cpyree\AudioDataBundle\TagReader;


use Cpyree\TagBundle\Lib\Id3;

class phpTagReader implements TagReaderInterface{

    /**
     * @var Id3
     */
    private $id3;

    /**
     * @param string $pathFile
     * @return mixed
     */
    public function read($pathFile)
    {
        $this->id3 = new Id3($pathFile);
        $this->id3->read();
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->id3->get('title');
    }

    /**
     * @return string
     */
    public function getAlbum()
    {
        return $this->id3->get('album');
    }

    /**
     * @return string
     */
    public function getArtist()
    {
        return $this->id3->get('artist');
    }

    /**
     * @return integer
     */
    public function getYear()
    {
        // TODO: Implement getYear() method.
    }

    /**
     * @return string
     */
    public function getCover()
    {
        // TODO: Implement getCover() method.
    }
}