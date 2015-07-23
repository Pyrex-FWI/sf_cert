<?php

namespace Cpyree\TagBundle\Command\Bulk;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Cpyree\TagBundle\Manager\TagBundleManager;
use Cpyree\TagBundle\Entity\TagData;
use Cpyree\TagBundle\Command\TagBase;


class ArtistCommand extends TagBase
{
   
    public $bulkSize = 1000;
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tag:bulk:artist')
            ->addOption('file', null, InputOption::VALUE_REQUIRED, 'file to write bulk data')
            ->addOption('skip-link', null, InputOption::VALUE_NONE, 'Skip link tagData<-->Artist')
            ->addBulkOption()
            ->addSkipWriteOption()
            ->addSkipInsertOption()
            ->setDescription(''
                    . 'This commad Extract artists information from each tagData and create mass import file for artist '
                    . 'Final file is import in artist table via <lod data into file>')
            ->setHelp(<<<EOF
The <info>%command.name%</info> Update tagData:

<info>php %command.full_name%</info>

php app/console tag:insertbulk /DBS/audio_files.txt CpyreeTagBundle:AudioFile
                    
<info>php %command.full_name%</info> --force-all
EOF
            );
    }


    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->progress = $this->getHelperSet()->get('progress');
        
        if($this->input->getOption("skip-write") === false){
            $this->wrtieBulkFile();
        }
        
        if($this->input->getOption("skip-insert") === false){
            $this->insertFromFile();
        }
        
        if($this->input->getOption("skip-link") === false){
            $this->linkTagDataWithArtist();
        }


        $this->endExecute();
        $this->printExecuteTime();
        $this->printMemoryUsage();
        

        return;        
    }
    
    public function linkTagDataWithArtist(){
        
        $this->output->writeln("<info>link artist to tagdata</info>");
        
        $starTimeRef = new \DateTime('now');
        $totalToProcess = $this->em->getRepository('CpyreeTagBundle:TagData')->findTagaDataWithArtitCount($starTimeRef);
        $this->progress->start($this->output, $totalToProcess);
        $currentRow = 0;

        $tDs = $this->getTagDataRepo()->findTagaDataWithArtit($starTimeRef, $this->bulkSize,'td.id'); 
        $iterrable = $tDs->iterate();
        foreach($iterrable as $row ){
            /* @var $td TagData */
            $td = $row[0];
            $currentRow++;
            $this->progress->advance();
            $names = $td->explodeArtistName();
            $this->linkartist($td, $names);
            $this->em->persist($td);
            if($currentRow % $this->bulkSize == 0 || $currentRow == $totalToProcess){
                $this->em->flush();
                $this->em->clear();                               
            }  
        }
        
        $this->progress->finish();
                
    }
    
    public function insertFromFile(){
 
       $this->output->writeln("<info>Insert artist From file job</info>");
        
       $command = $this->getApplication()->find('tag:bulk:insertFile');

        $arguments = array(
            'command'       => 'tag:bulk:insertFile',
            'input-file'    => $this->input->getOption("file"),
            'entity'        => 'CpyreeTagBundle:Artist',
        );

        $input = new ArrayInput($arguments);
        $this->output->writeln("<comment>Call 'tag:insertbulk'...</comment>");
        $returnCode = $command->run($input, $this->output);
                
    }
    
    public function wrtieBulkFile(){
        
        $this->output->writeln("<info>Write job</info>");
        
        $starTimeRef = new \DateTime('now');
        $totalToProcess = $this->em->getRepository('CpyreeTagBundle:TagData')->findTagaDataWithArtitCount($starTimeRef);
        
        $this->progress->start($this->output, $totalToProcess);
        $offset = 0;
        $currentRow = 0;
        $lines = array();  
        
        if(is_file($this->input->getOption("file"))){
            unlink($this->input->getOption("file"));
        }
        
        $tDs = $this->getTagDataRepo()->findTagaDataWithArtit($starTimeRef, $this->bulkSize,'td.id'); 
        $iterrable = $tDs->iterate();
        foreach($iterrable as $row ){
            /* @var $td TagData */
            $td = $row[0];
            $names = $td->explodeArtistName();
            $this->addLineIntoBulkFile($lines, $names);
            $currentRow++;    
            $this->executeBulkWriteFile($currentRow, $totalToProcess, $lines);
            $td->setBulkLastPass($starTimeRef);
            $this->em->persist($td);
            if($currentRow % $this->bulkSize == 0 || $currentRow == $totalToProcess){
                $this->em->flush();
                $this->em->clear();                               
            }             
            
            $this->progress->advance();
       
            gc_collect_cycles();
        }
       $this->executeBulkRemoveDuplicateReoderd();
       
       $this->progress->finish();
       
       $this->output->writeln("<succes>File \"" . $this->input->getOption("file") . "\" Write Ok</succes>");        
    }
    
    /**
     * 
     * @param type TagData
     */
    public function linkartist(TagData &$td, $names){
        foreach($names as $name){
            $artist = $this->em->getRepository("CpyreeTagBundle:Artist")->findOneByName($name);
            if(get_class($artist) == 'Cpyree\TagBundle\Entity\Artist' ){
                $td->addArtist($artist);
            }
        }
    }
    
    /**
     * Add a line into $lines for each name in $names 
     * @param type $lines
     * @param type $names
     */
    private function addLineIntoBulkFile(&$lines = array(), $names = array()){
        foreach ($names as $name){
            $artist = new \Cpyree\TagBundle\Entity\Artist();
            $lines[] = $artist->setName($name)->getBulkLine();
            unset($artist);
         }        
    }
    
    /**
     * Write bulk File according to bulkSize or end of processing
     * @param type $currentRow
     * @param type $totalToProcess
     * @param type $lines
     */
    private function executeBulkWriteFile($currentRow, $totalToProcess, array &$lines){
       if($currentRow % $this->bulkSize == 0 || $currentRow == $totalToProcess){
           $this->progress->clear();
            if($currentRow > $this->bulkSize-1 && $currentRow != $totalToProcess){ $lines[] = "";}
            file_put_contents($this->input->getOption("file"), implode("\n", $lines), FILE_APPEND);
            $lines = array();      
        }         
    }
    
    private function executeBulkRemoveDuplicateReoderd(){
        $rows = explode(PHP_EOL, file_get_contents($this->input->getOption("file")));
        $rows = array_filter(array_unique($rows));
        
        sort($rows);
        file_put_contents($this->input->getOption("file"), implode(PHP_EOL, $rows));
    }

}
