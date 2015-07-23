<?php
/**
 * Created by PhpStorm.
 * User: Angie
 * Date: 21/02/2015
 * Time: 23:45
 */

namespace Cpyree\AudioDataBundle\Services\AlbumCover;

use Cpyree\TagBundle\Lib\Id3;
use Symfony\Component\Finder\Iterator\FileTypeFilterIterator;

class CoverFileTypeFilterIterator extends  FileTypeFilterIterator{
    private $mode;
    /**
     * Constructor.
     *
     * @param \Iterator $iterator The Iterator to filter
     * @param int       $mode     The mode (self::ONLY_FILES or self::ONLY_DIRECTORIES)
     */
    public function __construct(\Iterator $iterator, $mode)
    {
        parent::__construct($iterator, $mode);
        $this->mode = $mode;
    }
    /**
     * Filters the iterator values.
     *
     * @return bool true if the value should be kept, false otherwise
     */
    public function accept()
    {
        $fileinfo = parent::current();
        if (self::ONLY_DIRECTORIES === (self::ONLY_DIRECTORIES & $this->mode) && $fileinfo->isFile()) {
            return false;
        } elseif (self::ONLY_FILES === (self::ONLY_FILES & $this->mode) && $fileinfo->isDir()) {
            return false;
        }

        return true;
    }

    public function current()
    {
        return new FileAlbumCoverItem(parent::current());
    }

}