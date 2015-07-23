<?php
/**
 * Created by PhpStorm.
 * User: Angie
 * Date: 22/02/2015
 * Time: 00:00
 */

namespace Cpyree\AudioDataBundle\Services\AlbumCover;


abstract class AbstractAlbumCoverItem {
    /** @var Object  */
    protected $item;

    public function __construct($item){
        $this->item = $item;
    }
}