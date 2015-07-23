<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 24/01/15
 * Time: 16:12
 */

namespace Cpyree\AudioDataBundle\TagReader;

interface TagReaderInterface {
    /**
     * @param string $pathFile
     * @return mixed
     */
    public function read($pathFile);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getAlbum();

    /**
     * @return string
     */
    public function getArtist();

    /**
     * @return integer
     */
    public function getYear();

    /**
     * @return string
     */
    public function getCover();
}