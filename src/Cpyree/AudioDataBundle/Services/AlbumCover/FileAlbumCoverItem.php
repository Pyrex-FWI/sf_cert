<?php
/**
 * Created by PhpStorm.
 * User: Angie
 * Date: 21/02/2015
 * Time: 23:23
 */

namespace Cpyree\AudioDataBundle\Services\AlbumCover;

use Cpyree\TagBundle\Lib\Id3;
use Symfony\Component\Finder\SplFileInfo;

class FileAlbumCoverItem extends AbstractAlbumCoverItem implements AlbumCoverItemInterface{

    protected $id3;
    public function __construct(SplFileInfo $item){
        parent::__construct($item);
        $this->id3 = new Id3($item->getPathName());
    }
    public function getSearchTerm($param = array())
    {
        $this->id3->read();
        $text = $this->id3->get('artist') . ' '. $this->id3->get('title');
        return preg_replace('/\s?\([^)]+\)\s+/', '',$text);
    }

    public function getIdentifier()
    {
        return md5($this->item->getPathName());
    }
}


