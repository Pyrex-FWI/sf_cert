<?php

namespace Cpyree\TagBundle\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument; 
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\ArrayInput;
use Cpyree\TagBundle\Entity\Cover;
use Cpyree\TagBundle\Entity\MediaFile;
use Cpyree\TagBundle\Lib\Id3;

class CoverCommand extends TagBase
{
    
    public $bulkSize = 50;
   
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cover')
            //->setAliases(array('tag:tag:update'))
            ->setDescription('This commad insert into a table from a file')
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

        
        $tagDataRepo = $this->getTagDataRepo();
        $this->progress = $this->getHelperSet()->get('progress');
        //$this->progress->start($this->output, $this->audioFilesToRead);
        
        while($batch = $tagDataRepo->withoutCover($this->bulkSize)){
            foreach($batch as $tagData){
                /* @var $tagData \Cpyree\TagBundle\Entity\TagData */
                //$this->progress->advance();
                if($tagData->getMediaFile()->exist()){
                    $tagDataRepo->persistCover($this->getApplication()->getKernel()->getWebDir() . "/TagBundle/Cover/", $tagData);
                    continue;
                }
            }
            $this->em->clear();
            gc_collect_cycles();
        }
        //$this->progress->finish();
    }  
        
    

}
