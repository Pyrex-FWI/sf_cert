<?php

namespace Cpyree\TagBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;


class CleanCommand extends TagBase
{
   
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tag:clean')
            //->setAliases(array('tag:tag:update'))
            ->setDescription('This commad insert into a table from a file')
            //->addArgument('mode', InputArgument::REQUIRED, '"file" for bulk import (fast) or "db" for classic insert (slow)')
            //->addArgument('path', InputArgument::REQUIRED, 'Path to scan /your/audio/path or C://mymusic')
            //->addOption('em', null, InputOption::VALUE_OPTIONAL, 'Entity manager to use')
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
        $this->audioFileTagPass();
        
    }
    
    public function audioFileTagPass(){
        $audioFileRepo = $this->em->getRepository("CpyreeTagBundle:AudioFile");
        
        while($audioFiles = $audioFileRepo->findBy(array('tagPass'=>null), array('id'=>'ASC'), 100)){
            foreach($audioFiles as $audioFile){
                $this->output->writeln($audioFile->getId() ."-". $audioFile->getTagPass());
            }
        }
    }

}
