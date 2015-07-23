<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 24/01/15
 * Time: 17:03
 */


namespace Cpyree\AudioDataBundle\TagReader;

abstract class BinTagReader{

    protected $bin;

    protected $executionResult;

    protected $fileOrDir;

    public function __construct($bin, $fileOrDir = null){
        $this->bin = $bin;
        $this->setFileOrDir($fileOrDir);
    }

    /**
     * @return mixed
     */
    public function getBin()
    {
        return $this->bin;
    }

    /**
     * @param mixed $bin
     */
    public function setBin($bin)
    {
        $this->bin = $bin;
    }

    /**
     * @return string
     */
    abstract function getCommandLine();

    public function executeCmd(){
        $consoleOutput = array();
        $returnVar = null;
        exec($this->getCommandLine(), $consoleOutput, $returnVar);
        //$consoleOutput = shell_exec($this->getCommandLine());
        //print_r($consoleOutput);
        //print_r($returnVar);
    }

    /**
     * @return mixed
     */
    public function getFileOrDir()
    {
        return $this->fileOrDir;
    }

    /**
     * @param mixed $fileOrDir
     */
    public function setFileOrDir($fileOrDir)
    {
        $this->fileOrDir = $fileOrDir;
    }

}