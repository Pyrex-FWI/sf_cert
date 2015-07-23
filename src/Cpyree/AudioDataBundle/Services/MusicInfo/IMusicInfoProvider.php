<?php
/**
 * Created by PhpStorm.
 * User: chpyr
 * Date: 26/03/14
 * Time: 13:09
 */

namespace Cpyree\AudioDataBundle\Services\MusicInfo;


interface IMusicInfoProvider {

    /**
     * @param $serch
     * @return \Cpyree\AudioDataBundle\Libs\TagCollection()
     */
    public function search($serch);

    public function buildTagCollection();

    public function getName();
}