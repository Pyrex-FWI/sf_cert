<?php

namespace Cpyree\TagBundle\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Cpyree\TagBundle\Entity\MediaFile;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class SearchMediaFilesCommand extends TagBase
{

    public $second_in_microSec = 1000000;
    public $bulkSize = 200;
    public $path;
    public $context;
    public $printCsv;
    public $finderCount;
    public $mediaFileEntity = 'CpyreeTagBundle:Mediafile';
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tag:update:mediafile')
            ->setAliases(array('tag:mediafile:update'))
            ->setDescription('This commad update media_file database with files '
                    . 'Each physical file will be write in file for a mass post insertion or '
                    . 'a direction insert into batabase')
            ->addArgument('mode', InputArgument::REQUIRED, '"file" for bulk import (fast) or "db" for classic insert (slow)')
            ->addArgument("context", InputArgument::REQUIRED, "Set is 'dbs' or 'sapar'")
            ->addArgument('path', InputArgument::REQUIRED, 'Path to scan /your/audio/path or C://mymusic')
            
            ->addBulkOption()
            ->addEmOption()
            ->addSkipWriteOption()
            ->addSkipInsertOption()
            ->addOption('file', null, InputOption::VALUE_REQUIRED, 'file to write bulk data')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command greets somebody or everybody:

<info>php %command.full_name%</info>

The optional argument specifies who to greet:

<info>php %command.full_name%</info> Fabien
                                      
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
 
        $fs = new Filesystem();
        
        if($fs->exists($this->path) === false){
            $this->output->writeln ("<bg=red;options=bold>" . $this->path . " is not accessible!</bg=red;options=bold>");
            return;
        }
       
        
        //$this->output->writeln("Scan ".$this->path);
        $finder = new Finder();
        $finder
                ->files()
                ->in($this->path)
                ->name("*.mp3"); //@todo place this in config
        $this->finderCount = count($finder);
        
 
        $this->progress = $this->getHelperSet()->get('progress');
        
        $this->progress->start($this->output, $this->finderCount);
        switch ($this->input->getArgument("mode")) {
            case "db":
                    $this->dbOutput($finder);
                break;
            case "file":
                $this->fileOutput($finder);
                break;
            default:
                $this->output->writeln("<error>Your mode is not identified (".$this->input->getArgument("mode").")</error>");
                return;
        }        
       
    }
    
    
    public function init(){
        parent::init();
        $this->path = $this->input->getArgument("path");
        
        if(intval($this->input->getOption("bulk-size")) > 0){
            $this->bulkSize = intval($this->input->getOption("bulk-size"));
        }
        
        
        if($this->input->getOption("em")){
            $this->em = $this->container->get('doctrine')->getManager($this->input->getOption('em'));
        }
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
    }
    /**
     * 
     * @param type $finder 
     */
    public function fileOutput(Finder $finder){
        $fileOutput = new Filesystem();
        if(strlen($this->input->getOption("file")) == ""){
            $this->output->writeln("<error>You must specify a --file</error>");
            return;
        }
        if(is_file($this->input->getOption("file"))){
            $fileOutput->remove($this->input->getOption("file"));
        }
        
        $row = 0;
        $lines = array();
        foreach ($finder as $file){
            $this->progress->advance();
            switch ($this->input->getArgument("context")) {
                case "dbs":
                        $context = new \Cpyree\TagBundle\Entity\Context(1);
                        break;
                case "sapar":
                        $context = new \Cpyree\TagBundle\Entity\Context(2);
                        break;
                default:
                    $context = new \Cpyree\TagBundle\Entity\Context(0);
                    break;
            }
            $audioFile = new MediaFile();
            $audioFile->setFilepath($file->getRealPath());
            $audioFile->setHash($file->getRealPath());
            
            $audioFileRepository = $this->em->getRepository($this->mediaFileEntity);               
            
            //if(1 || is_null($audioFileRepository->findOneByHash($audioFile->getHash()))){
            if(1){
                $audioFile->setExist(1);
                $audioFile->setTagPass(0);
                $audioFile->setCreated(new \DateTime('now'));
                $lines[] = $audioFile->getBulkLine($context);
                $row++;
            }
            if($row % $this->bulkSize ==0 || $row == $this->finderCount){
                if($row > $this->bulkSize-1 && $row != $this->finderCount) $lines[] = "";
                file_put_contents($this->input->getOption("file"), implode("\n", $lines), FILE_APPEND);
                $lines = array();
            }
            
        }
        $this->progress->finish();
        $this->output->writeln("<succes>File \"" . $this->input->getOption("file") . "\" Write Ok</succes>");
        
        $command = $this->getApplication()->find('tag:bulk:insert');

        $arguments = array(
            'command'       => 'tag:bulk:insert',
            'input-file'    => $this->input->getOption("file"),
            'entity'        => 'CpyreeTagBundle:MediaFile',
            '--em'           => $this->input->getOption("em")
        );

        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $this->output);
        return $returnCode;
        
        
    }
    
    public function dbOutput(Finder $finder) {
        $saveCount = 0;
        
        foreach ($finder as $file){
            $this->progress->advance();
            
            $audioFile = new AudioFile();
            $audioFile->setFilepath($file->getRealPath());
            $audioFile->setHash($file->getRealPath());
    
            $audioFileRepository = $this->getMediaFileRepo();               
            
            if(is_null($audioFileRepository->findOneByHash($audioFile->getHash()))){
                $audioFile->setExist(1);
                $audioFile->setTagPass(0);
                $audioFile->setCreated(new \DateTime('now'));
                $this->em->persist($audioFile);
                $saveCount++;
            }
            
            if($saveCount % $this->bulkSize ==0){
                $this->em->flush();
                $this->em->clear();
                $saveCount = 0;
            }
        }
        $this->progress->finish();
        $this->em->flush();
        $this->em->clear();        
    }

   
}
