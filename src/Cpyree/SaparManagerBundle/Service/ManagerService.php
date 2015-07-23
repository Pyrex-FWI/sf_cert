<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 05/12/14
 * Time: 19:24
 */

namespace Cpyree\SaparManagerBundle\Service;


use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Cpyree\TagBundle\Lib\Id3;

class ManagerService {

    private $tempDir = null;

    private $confFile = null;

    private $conf = array(
        'config'    => array(
            'workspace' =>  null,
            'set'       => array(),
        )
    );
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container){
        $this->container = $container;
    }

    /**
     * @param $name
     * @return object
     */
    private function get($name){
        return $this->container->get($name);
    }

    private $cleanDirPatterns = array(
        '^(?P<artist>va)_?\s?-\s?_?(?P<album>[\w\s\(\)]*)-(\([\w\s]*\)-)?(\d-?\w{2}-|[a-z]{2,5}-(FR-)?)?(?P<year>\d{4})?', //Various
        '^(?!va_?-)(?P<artist>[a-z0-9\_\(\)]*)-(?P<album>[a-z0-9\_\(\)]*)-(([a-z0-9\_\(\)]*)-)?(([a-z0-9\_\(\)]*)-)?(?P<year>\d{4})'
    );
    /**
     * @return null
     */
    public function getConfFile()
    {
        return $this->confFile;
    }

    /**
     * @param null $confFile
     */
    public function setConfFile($confFile)
    {
        $f = new Filesystem();
        if(!$f->exists($confFile)){
            throw new Exception(sprintf('SaparManager: file not exist (%s)', $confFile));
        }
        //if(\php_check_syntax($confFile) == false){
        //    throw new Exception(sprintf('SaparMAnager: check php syntax into file %q', $confFile));
        //}
        $this->confFile = $confFile;
        $this->loadConf();
    }

    public function loadConf(){
        if($this->getConfFile() == null) return false;
        include $this->getConfFile();
        $this->conf = $conf;
    }
    /**
     * return temporary directory
     * @return string
     */
    public function getTempDir()
    {
        return $this->tempDir;
    }

    /**
     * Set temporary directorie
     * @param null $tempDir
     */
    public function setTempDir($tempDir)
    {
        if(!is_dir($tempDir)){
            throw new Exception(sprintf('SaparManager: Directory not exist (%s)', $tempDir));
        }
        $this->tempDir = $tempDir;
    }

    /**
     * Get all dir in Temporary directory
     * @return Finder
     */
    public function listTemp(){
        if($this->tempDir == null){
            throw new Exception("You must set à tempDir");
        }

        $fs = new Finder();
        $dirs = $fs->directories()->depth(0)->in($this->getTempDir());
        return $dirs;
    }

    /**
     * Check if $dir is already dispatched
     * @param $dir
     * @return categorie|false
     */
    public function dirIsAlreadyDispatched($dir){
        $find = false;
        foreach($this->conf as $cat => $part){
            if(in_array($dir, (array)$part)){
                $find = $cat;
                break;
            }
        }
        return $find;
    }
    /**
     *
     * @return array
     */
    public function listAllTempArray(){
        $finder = $this->listTemp();
        $return = array();
        foreach ($finder as /** @var SplFileInfo $item */ $item) {
            if(!$this->validDir($item->getRelativePathname())) continue;
            $return[] = $this->getDirInfo($item);
        }
        return $return;
    }

    /**
     *
     * @return array
     */
    public function listProcessedTempArray($processed = false){
        $finder = $this->listTemp();
        $return = array();
        foreach ($finder as /** @var SplFileInfo $item */ $item) {
            if($this->dirIsAlreadyDispatched($item->getRelativePathname()) != $processed) continue;
            if(!$this->validDir($item->getRelativePathname())) continue;
            $return[] = $this->getDirInfo($item);
        }
        return $return;
    }
    public function getCleanName($folder){

    }

    /**
     * @param $folderId
     * @return array
     */
    public function getDirDetail($folderId){
        $fs = new Finder();
        $workSpace = $this->getTempDir() . DIRECTORY_SEPARATOR . base64_decode($folderId);
        $files = $fs->files()->name('/\.mp3$/i')->depth(0)->in($workSpace);
        $return = array();
        foreach ($files as /** @var SplFileInfo $item */ $item) {
            $tag = new Id3($workSpace. DIRECTORY_SEPARATOR . $item->getRelativePathname());
            $tag->read();
            $return['files'][] = array(
                'relativePathName'  =>  $item->getRelativePathname(),
                'relativePath'      =>  $workSpace. DIRECTORY_SEPARATOR . $item->getRelativePathname(),
                'id'                =>  base64_encode($workSpace. DIRECTORY_SEPARATOR . $item->getRelativePathname()),
                'artist'            =>  $tag->get('artist'),
                'title'             =>  $tag->get('title'),
                'track_number'      =>  $tag->get('track_number'),
            );
        }
        $return['coverThumb']        =  $this->getCoverThumb(basename($workSpace));
        return $return;
    }

    public function add($cat,$dir){
        $this->conf[$cat][] = $dir;
        $this->conf[$cat] = array_unique($this->conf[$cat]);
        asort($this->conf[$cat]);
    }
    public function saveConf(){
        file_put_contents($this->confFile.'.back',file_get_contents($this->getConfFile()));
        file_put_contents($this->confFile,"<?php\n \$conf = ".var_export($this->conf, true).";");
    }

    private function getNames($folder)
    {
        $folder = str_replace('_',' ',$folder);
        $data = array(
            'artist'    => null,
            'album'     => null,
            'fullName'  => $folder
        );

        foreach($this->cleanDirPatterns as $regex){
            if(preg_match("/$regex/i", $folder, $matches)){
                $data['artist'] = $matches['artist'];
                $data['album'] = $matches['album'];
                $data['fullName'] = $matches['artist'] . ' - ' . $matches['album'];
            }
        }
        return $data;
    }

    /**
     * @param $getRelativePathname
     * @return null|string
     */
    private function getCoverThumb($getRelativePathname)
    {
        $bundleImagePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Resources/public/images/';
        $imageName = md5($getRelativePathname).'.jpg';
        $imagePathFile = $bundleImagePath . $imageName;
        if(file_exists($imagePathFile)) return $imageName;
        //die($this->get('kernel')->getRootDir());
        $filePath = $this->getTempDir() . DIRECTORY_SEPARATOR . $getRelativePathname;
        $newFs = new Finder();
        foreach($newFs->files()->name('*.jpg')->depth(0)->in($filePath) as $picture){
            //die($picture->getPathName(). ' to ' . $imagePathFile);


            // Calcul des nouvelles dimensions
            list($width, $height) = getimagesize($picture->getPathName());
            $newwidth = 100;
            $newheight = 100;

            // Chargement
            $thumb = imagecreatetruecolor($newwidth, $newheight);
            $source = imagecreatefromjpeg($picture->getPathName());

            // Redimensionnement
            imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

            imagejpeg($thumb, $imagePathFile, 100);
            //copy($picture->getPathName(), $imagePathFile);
            return $imageName;
        }
        return null;
    }

    private function validDir($getRelativePathname)
    {
        $filePath = $this->getTempDir() . DIRECTORY_SEPARATOR . $getRelativePathname;
        $newFs = new Finder();
        return !($newFs->files()->name('*.mp3')->depth(0)->in($filePath)->count() === 0);
    }

    /**
     * Return detail of dir
     * @param $item \SplFileInfo
     * @return array
     */
    private function getDirInfo($item)
    {
        return array_merge(array(
            'relativePathName'  =>  $item->getRelativePathname(),
            'coverThumb'        =>  $this->getCoverThumb($item->getRelativePathname()),
            'relativePath'      =>  $item->getRelativePath(),
            'id'                =>  base64_encode($item->getRelativePathname())
            ),
            $this->getNames($item->getRelativePathname())
        );
    }

} 