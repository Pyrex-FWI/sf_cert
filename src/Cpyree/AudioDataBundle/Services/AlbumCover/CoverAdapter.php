<?php
/**
 * Created by PhpStorm.
 * User: Angie
 * Date: 21/02/2015
 * Time: 23:44
 */

namespace Cpyree\AudioDataBundle\Services\AlbumCover;


use Cpyree\TagBundle\Lib\Id3;
use Symfony\Component\Finder\Adapter\PhpAdapter;

class CoverAdapter extends PhpAdapter{


    public function getName()
    {
        return 'cover';
    }

    public function searchInDirectory($dir)
    {
        $iterator = parent::searchInDirectory($dir);
        if ($this->mode) {
            $iterator = new CoverFileTypeFilterIterator($iterator, $this->mode);
        }

        return $iterator;
    }

}