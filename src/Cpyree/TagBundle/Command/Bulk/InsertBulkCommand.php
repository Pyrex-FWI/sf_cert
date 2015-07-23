<?php

namespace Cpyree\TagBundle\Command\Bulk;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Cpyree\TagBundle\Entity\MediaFile;
use Cpyree\TagBundle\Entity\TagData;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;
use Cpyree\TagBundle\Command\TagBase;

class InsertBulkCommand extends TagBase
{
    /**
     *
     * @var Doctrine\
     */
    var $output;
    var $input;
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tag:bulk:insertFile')
            //->setAliases(array('tag:tag:update'))
            ->setDescription('This commad insert into a table from a file')
            ->addArgument("input-file", \Symfony\Component\Console\Input\InputArgument::REQUIRED, "Set your input file")
            ->addArgument('entity', InputArgument::REQUIRED, "YourNamspaceBundle:Entity")
            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'Entity manager to use')
            ->setHelp(<<<EOF
The <info>%command.name%</info> Update tagData:

<info>php %command.full_name%</info>

php app/console tag:insertbulk /DBS/audio_files.txt CpyreeTagBundle:AudioFile
                    
<info>php %command.full_name%</info> --force-all
EOF
            );
    }
    
    public function init(){
         
        parent::init();
        $this->container = $this->getApplication()->getKernel()->getContainer();
        
        $this->em = $this->container->get("doctrine")->getManager();
        
        if($this->input->getOption("em")){
            $this->em = $this->container->get('doctrine')->getManager($this->input->getOption('em'));
        }
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        
        
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->init();

        
        //Check repository
        $repository = $this->em->getRepository($this->input->getArgument("entity"));
        //Check input file
        $fs = new Filesystem();
        if(!$fs->exists($this->input->getArgument("input-file"))){
            //
            $this->output->writeln($this->input->getArgument("input-file") . " not exist.");
            return 1;
        }
        $countBefore = $repository->count();
        $cmd = $repository->getConsolBulkInsertCMD($this->input->getArgument("input-file"));
        $this->output->writeln("Importing..");
        exec($cmd, $out);
        $this->output->writeln($out);
        $this->output->writeln("Import is done.");
        $countAfter = $repository->count();
        $this->output->writeln("Before: ". $countBefore);
        $this->output->writeln("After: ". $countAfter);
        $this->output->writeln("Inserted: ". ($countAfter - $countBefore));
    }

}
