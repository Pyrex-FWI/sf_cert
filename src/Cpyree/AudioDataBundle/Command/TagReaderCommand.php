<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 25/01/15
 * Time: 14:36
 */

namespace Cpyree\AudioDataBundle\Command;


use Cpyree\AudioDataBundle\TagReader\id3infoBinReader;
use Cpyree\AudioDataBundle\TagReader\phpTagReader;
use Cpyree\TagBundle\Lib\Id3;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class TagReaderCommand extends ContainerAwareCommand{

    const PHP_READER = 1;
    const BIN_READER = 2;
    /**
     * @var Stopwatch
     */
    protected $stopWatch;

    protected function configure()
    {
        $this
            ->setName('id3:read')
            ->setDescription('Audio ID3 reader')
            ->addArgument('file', InputArgument::REQUIRED, 'File to read.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Start: " . date('H:i:s'));
        $output->writeln("Use at start: " . $this->getMemory());
        $this->stopWatch = new Stopwatch();
        $file = $input->getArgument('file');
        if(is_file($file)){
            $this->stopWatch->start('id3');
            for($i=0; $i< 1000; $i++) {
                $id3info = new id3infoBinReader("/opt/local/bin/id3v2 -l");
                $id3info->read($file);

                /*$id3 = new phpTagReader();
                $id3->read($file);*/

            }
            $output->writeln("Use at start: " . $this->getMemory());
            $event = $this->stopWatch->stop('id3');
            $output->writeln("Duration: " . $event->getDuration()/1000);
            $output->writeln("Stop: " . date('H:i:s'));
        }

    }
    public function convert($size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    public function getMemory(){
        return $this->convert(memory_get_usage(true));
    }
} 