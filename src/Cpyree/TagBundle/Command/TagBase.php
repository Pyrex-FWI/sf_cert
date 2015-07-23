<?php

namespace Cpyree\TagBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Cpyree\TagBundle\Manager\TagBundleManager;
use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Stopwatch\Stopwatch;

abstract class TagBase extends ContainerAwareCommand
{
    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    public $em;
    
    public $container;
    
    /**
     *
     * @var ProgressHelper
     */
    public $progress;
    /**
     *
     * @var OutputInterface
     */
    public $output;
    
    /**
     *
     * @var InputInterface
     */
    public $input;
    
    /**
     *
     * @var Stopwatch
     */
    public $stopwatch;
    
    
    public $bulkSize;
    /**
     * StopwatchEvent
     */
    public $executeEvent;
    
    public $styles = array('info','title','warning', 'success');
    
    public function init(){
        
        $this->container = $this->getApplication()->getKernel()->getContainer();
        
        $this->em = $this->container->get("doctrine")->getManager();

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        
        $this->styles['title'] = new OutputFormatterStyle("yellow", "black", array("bold"));
        
        $this->output->getFormatter()->setStyle('info', $this->styles['title']);
        
        $this->styles['success'] = new OutputFormatterStyle("black", "green", array("bold"));
        
        $this->output->getFormatter()->setStyle('succes', $this->styles['success']);
        
        if(in_array('bulk-size', $this->input->getOptions()) && $this->input->getOption('bulk-size')){
            $this->bulkSize = $this->input->getOption('bulk-size');
        }
        
        $this->stopwatch = new Stopwatch();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        gc_enable();
        
        $this->output = $output;
        $this->input = $input;
        
        $this->init();        
        $this->stopwatch->start('execute');
        $this->showName();
        
        
    }
    
    protected function showName(){
        $this->showTitle($this->getName());
    }
    
    protected function showTitle($title){
        $this->output->writeln("<info>$title</info>");
    }
    
    /**
     * 
     * @return TagBundleManager
     */
    public function getTagBundleManager(){
        
        return $this->getApplication()->getKernel()->getContainer()->get('cpyree_tag_bundle_manager');
    }
    
    
    public function endExecute(){
        $this->executeEvent = $this->stopwatch->stop('execute');;
    }
    
    public function getExecuteTime(){
        return $this->getDateDif($this->executeEvent->getStartTime(), $this->executeEvent->getDuration());
    }
    
    public function printExecuteTime(){
        $this->output->writeln("Execution Time: " . $this->getExecuteTime());
    }
    
    public function printMemoryUsage(){
        $this->output->writeln("Memory usage: " .$this->executeEvent->getMemory());
    }
    
    
    public function getDateDif($start, $end){
        $end = $end/1000;
        $s = new \DateTime();
        $s->setTimestamp($start);
        $e = new \DateTime();
        $e->setTimestamp($end);
        
        $diff = $s->diff($e);
        /** @var \DateInterval */
        return $diff->i . ":" . $diff->s;
        
    }

    public function addBulkOption($shortcut = null, $mode = InputOption::VALUE_REQUIRED, $description = 'Process by lot of specific size.'){
        $this->addOption('bulk-size', $shortcut, $mode, $description );
        return $this;
    }

    public function addEmOption($shortcut = null, $mode = InputOption::VALUE_REQUIRED, $description = 'Entity manager to use'){
        $this->addOption('em', $shortcut, $mode, $description);
        return $this;
    }

    public function addSkipWriteOption($shortcut = null, $mode = InputOption::VALUE_NONE, $description = 'Skip Write file, go to insert directly'){
        $this->addOption('skip-write', null, $mode, $description);
        return $this;
    }

    public function addSkipInsertOption($shortcut = null, $mode = InputOption::VALUE_NONE, $description = 'Skip insert from file'){
        $this->addOption('skip-insert', $shortcut, $mode, $description);
        return $this;
    }

    
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\DbsFileRepository
     */
    public function getDbsFileRepo(){
        return $this->em->getRepository("CpyreeTagBundle:DbsFile");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\DbsFileRepository
     */
    public function getSaparFileRepo(){
        return $this->em->getRepository("CpyreeTagBundle:SaparFile");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\TagDataRepository
     */
    public function getTagDataRepo(){
        return $this->em->getRepository("CpyreeTagBundle:TagData");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\MediaFileRepository
     */
    public function getMediaFileRepo(){
        return $this->em->getRepository("CpyreeTagBundle:MediaFile");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\CoverRepository
     */
    public function getCoverFileRepo(){
        return $this->em->getRepository("CpyreeTagBundle:Cover");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\ArtistRepository
     */
    public function getArtistRepo(){
        return $this->em->getRepository("CpyreeTagBundle:Artist");
    }    
}
