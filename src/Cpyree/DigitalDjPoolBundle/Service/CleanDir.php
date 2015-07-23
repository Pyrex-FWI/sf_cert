<?php
/**
 * Created by PhpStorm.
 * User: Angie
 * Date: 14/03/2015
 * Time: 19:57
 */

namespace Cpyree\DigitalDjPoolBundle\Service;


use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @property  allFiles
 */
class CleanDir {
    /** @var Container */
    private $service_container;
    /** @var  array */
    private $allDirs;

    /** @var  Logger */
    private $logger;
    /** @var  string */
    private $allFiles;

    public function __construct($service_container)
    {
        $this->service_container = $service_container;


    }

    private static function getFiles($rootDir)
    {
        $f = new Finder();
        $dirs = (array_map(function(SplFileInfo $file){ return $file->getFilename();},iterator_to_array($f->in($rootDir)->depth(0)->name('/^[0-9]*_.*\.mp3$/')->files())));
        return array_values($dirs);
    }

    public function clean(){
        $rootDir = $this->service_container->getParameter('ddp.files_path');
        $this->allDirs = (self::getDirName($rootDir));
        $this->allFiles = self::getFiles($rootDir);
        foreach($this->allFiles as $file){
            preg_match('/^(?P<dir>[\d]*)_*/',$file, $matches);
            $dirOfSong = (str_pad(substr($matches['dir'], 0, 2),5,0,STR_PAD_RIGHT));
            $this->creaDirIfNotExist($rootDir, $dirOfSong);
            $this->moveFile($rootDir . DIRECTORY_SEPARATOR . $file, $rootDir . DIRECTORY_SEPARATOR .$dirOfSong . DIRECTORY_SEPARATOR . $file);
        }
    }


    public function creaDirIfNotExist($rootDir, $dirOfSong){
        if(!in_array($dirOfSong, $this->allDirs)){
            mkdir($rootDir . DIRECTORY_SEPARATOR . $dirOfSong);
        }
    }
    /**
     * @param $rootDir
     * @return array
     */
    static public function getDirName($rootDir){
        $f = new Finder();
        $dirs = (array_map(function(SplFileInfo $file){ return $file->getFilename();},iterator_to_array($f->in($rootDir)->depth(0)->name('/^[0-9]*$/')->directories())));
        asort($dirs);
        return array_values($dirs);
    }

    private function moveFile($src, $dest)
    {
        $fs = new Filesystem();
        return $fs->rename($src, $dest);

    }
}