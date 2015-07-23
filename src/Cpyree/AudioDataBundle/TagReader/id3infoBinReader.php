<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 25/01/15
 * Time: 16:56
 */

namespace Cpyree\AudioDataBundle\TagReader;


class id3infoBinReader extends BinTagReader implements BinTagReaderInterface{


    /**
     * @param string $pathFile
     * @return mixed
     */
    public function read($pathFile)
    {
        $this->setFileOrDir($pathFile);
        $this->executeCmd();
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        // TODO: Implement getTitle() method.
    }

    /**
     * @return string
     */
    public function getAlbum()
    {
        // TODO: Implement getAlbum() method.
    }

    /**
     * @return string
     */
    public function getArtist()
    {
        // TODO: Implement getArtist() method.
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

    /**
     * @return string
     */

    public function getCommandLine()
    {
        return $this->getBin() . " \"" . $this->getFileOrDir()."\"";
    }
}