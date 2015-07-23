<?php

namespace Cpyree\TagBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument; 
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;


class TagCommand extends TagBase
{
   
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tag')
            //->setAliases(array('tag:tag:update'))
            ->setDescription('This commad insert into a table from a file')
            ->addArgument('mode', InputArgument::REQUIRED, '"file" for bulk import (fast) or "db" for classic insert (slow)')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to scan /your/audio/path or C://mymusic')
            ->addArgument("context", InputArgument::REQUIRED, "context 'dbs' or 'sapar'")
            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'Entity manager to use')
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

        $this->mediaFileJob();
        
        $this->tagJob();
        
        $this->coverJob();
        
        $this->artistJob();
        
        $this->endExecute();
        $this->printExecuteTime();
        $this->printMemoryUsage();              
    }
    
    public function mediaFileJob(){
        $commandSearch = $this->getApplication()->find('tag:update:mediafile');
        
        $arguments = array(
            'command'       => 'tag:update:mediafle',
            'mode'          => 'file',
            'path'    => $this->input->getArgument("path"),
            'context'    => $this->input->getArgument("context"),
            '--file'      => $this->input->getArgument("path") . DIRECTORY_SEPARATOR . "audio_file.txt"
        );
        if($this->input->getOption("em")){
            $arguments['--em'] = $this->input->getOption("em");
        }

        $input = new ArrayInput($arguments);
 
        $returnCode = $commandSearch->run($input, $this->output);    
                
    }
    
    public function coverJob(){
        $commandSearch = $this->getApplication()->find('cover');

        $arguments = array(
            'command'       => 'cover',
            'context'    => $this->input->getArgument("context")
        );

        $input = new ArrayInput($arguments);
        $returnCode = $commandSearch->run($input, $this->output);   
                
    }
    
    public function tagJob(){

        $commandSearch = $this->getApplication()->find('tag:update:tag');

        $arguments = array(
            'command'       => 'tag:update:tag',
            'mode'          => 'file',         
            '--file'      => $this->input->getArgument("path") . DIRECTORY_SEPARATOR . "tag_data.txt"
        );
        if($this->input->getOption("em")){
            $arguments['--em'] = $this->input->getOption("em");
        }
        
        $input = new ArrayInput($arguments);
        $returnCode = $commandSearch->run($input, $this->output);    
                
    }
    
    
    public function artistJob(){
        

        $commandSearch = $this->getApplication()->find('tag:bulk:artist');

        $arguments = array(
            'command'       => 'tag:bulk:artist',
            '--file'      => $this->input->getArgument("path") . DIRECTORY_SEPARATOR . "tag_data.txt"
        );

        $input = new ArrayInput($arguments);
        $returnCode = $commandSearch->run($input, $this->output);            
    
    }

}
