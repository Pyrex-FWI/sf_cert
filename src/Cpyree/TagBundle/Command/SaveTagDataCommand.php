<?php

namespace Cpyree\TagBundle\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Cpyree\TagBundle\Lib\Id3;
use Symfony\Component\Filesystem\Filesystem;
use Cpyree\TagBundle\Entity\TagData;
use Symfony\Component\Console\Input\ArrayInput;

class SaveTagDataCommand extends TagBase
{

    var $second_in_microSec = 1000000;
    var $bulkSize = 50;
    var $audioFilesToRead;
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tag:update:tag')
            ->setAliases(array('tag:tag:update'))
            ->setDescription('This commad update audio_file database with files')
            ->addArgument("mode", \Symfony\Component\Console\Input\InputArgument::OPTIONAL, "Set your output: 'db' or 'file' ", "db")
            ->addOption('force-all', "fa", InputOption::VALUE_NONE, 'Force to overwrite existing tag audio_files.')
            ->addBulkOption()
            ->addEmOption()
            ->addOption("file","f" , InputOption::VALUE_REQUIRED, 'File wich will be created with output=file')
            ->setHelp(<<<EOF
The <info>%command.name%</info> Update tagData:

<info>php %command.full_name%</info>

Force update all audio_file:
<info>php %command.full_name%</info> --force-all
EOF
            );
    }
    
    public function init(){
        parent::init();   
        if(intval($this->input->getOption("bulk-size")) > 0){
            $this->bulkSize = intval($this->input->getOption("bulk-size"));
        }
        
        $this->container = $this->getApplication()->getKernel()->getContainer();
        
        $this->em = $this->container->get("doctrine")->getManager();
        
        if($this->input->getOption("em")){
            $this->em = $this->container->get('doctrine')->getManager($this->input->getOption('em'));
        }
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        
        if($this->input->getOption("force-all") == 1){
            $numUpdated = $this->em->createQuery('update CpyreeTagBundle:MediaFile af set af.tagPass = 0')->execute();
        }
        
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->init();

        gc_enable();
        
        $audioFileRepository = $this->em->getRepository("CpyreeTagBundle:MediaFile");
        $this->audioFilesToRead = $audioFileRepository->nbUntaged();
        
        if($this->audioFilesToRead == 0){
            $this->output->writeln("No audio file to update.");
            return;
        }else{
            $this->output->writeln($this->audioFilesToRead . " can be updated.");
        }
        
        $this->progress = $this->getHelperSet()->get('progress');
        switch ($this->input->getArgument("mode")) {
            case "db":
                    $this->doDatabaseJob();
                break;
            case "file":
                $this->doFileJob();
                break;
            default:
                $this->output->writeln("Your output is not identified (".$this->input->getArgument("output").")");
                return;
        }
        
    }
    
    public function doFileJob(){
        $fileOutput = new Filesystem();
        if(strlen($this->input->getOption("file")) == ""){
            $this->output->writeln("You must specify a -file");
            return;
        }
        if(is_file($this->input->getOption("file"))){
            $fileOutput->remove($this->input->getOption("file"));
        }
        $audioFileRepository = $this->getMediaFileRepo();
        $this->progress->start($this->output, $this->audioFilesToRead);
        $dataPutCount =0;
        $count = 0;
        while($batch = $audioFileRepository->getUntaged($this->bulkSize)){
            $lines = array();
            foreach($batch as $audioFile){
            /* @var $audioFile AudioFile */
                $this->progress->advance();
                
                $count++;
                
                $fs = new Filesystem();
                if($fs->exists(trim($audioFile->getFilepath())) === false){
                    $audioFile->setExist(0);
                    $this->output->writeln($audioFile->getFilepath());
                    $this->em->persist($audioFile);
                    continue;
                }
                
                $id3 = new Id3($audioFile->getFilepath());
                $tag = new TagData();
                $id3->read();
                
                $tag->setMediaFile($audioFile);
                
                $tag->buildFromId3($id3);
                $tag->setCreated(new \DateTime('now'));
                
                $lines[] = $tag->getBulkLine();
                //@todo do this after insert tag_data
                $audioFile->setTagPass(1);
                $this->em->persist($audioFile);
            }
            if($count==1) $lines[0] = chr(239) . chr(187) . chr(191) . $lines[0];
            if($count != $this->audioFilesToRead){
                $lines[] = "";
            }
            file_put_contents($this->input->getOption("file"), implode("\n", $lines), FILE_APPEND);
            $dataPutCount++;
            $this->em->flush();
            $this->em->clear();
            gc_collect_cycles();
        }
        $this->progress->finish();
        $this->output->writeln("\nFile \"" . $this->input->getOption("file") . "\" Write Ok");
        
        
        $command = $this->getApplication()->find('tag:bulk:insert');

        $arguments = array(
            'command'       => 'tag:bulk:insert',
            'input-file'    => $this->input->getOption("file"),
            'entity'        => 'CpyreeTagBundle:TagData',
            '--em'           => $this->input->getOption("em")
        );

        $input = new ArrayInput($arguments);
        $this->output->writeln("<comment>Call 'tag:insertbulk'...</comment>");
        $returnCode = $command->run($input, $this->output);
        return $returnCode;
    }
    
    public function doDatabaseJob() {
        $this->output->writeln("Database job");
        
        $audioFileRepository = $this->getMediaFileRepo();
        $this->progress->start($this->output, $this->audioFilesToRead);
        
        while($batch = $audioFileRepository->getUntaged($this->bulkSize)){
            foreach($batch as $audioFile){
                /* @var $audioFile AudioFile */
                $this->progress->advance();
                $fs = new Filesystem();
                if($fs->exists(trim($audioFile->getFilepath())) === false){
                    $audioFile->setExist(0);
                    $this->output->writeln($audioFile->getFilepath());
                    $this->em->persist($audioFile);
                    continue;
                }

                $id3 = new Id3($audioFile->getFilepath());
                $tag = new TagData();
                $id3->read();                
                $tag->setMediaFile($audioFile);
                $tag->buildFromId3($id3);

                $audioFile->setTagPass(1);
                $tag->setMediaFile($audioFile);
                $this->em->persist($audioFile);
                $this->em->persist($tag);
            }
            $this->em->flush();
            $this->em->clear();
            gc_collect_cycles();
        }
        $this->progress->finish();
    }

}
