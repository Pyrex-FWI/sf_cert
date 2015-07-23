<?php
/**
 * Created by PhpStorm.
 * User: Angie
 * Date: 21/02/2015
 * Time: 19:01
 */

namespace Cpyree\AudioDataBundle\Services\AlbumCover;


interface AlbumCoverItemInterface {

    public function getSearchTerm($param = array());
    public function getIdentifier();

}