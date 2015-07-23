<?php


namespace Cpyree\AudioDataBundle\Command;

use Cpyree\AudioDataBundle\Services\ItunesAbstractMusicInfoProvider;
use Cpyree\AudioDataBundle\Services\SpotifyAbstractMusicInfoProvider;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Hello World command for demo purposes.
 *
 * You could also extend from Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
 * to get access to the container via $this->getContainer().
 *
 * @author Tobias Schultze <http://tobion.de>
 */
class SearchCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('audiodata:search')
            ->setDescription('Audio data search')
            ->addArgument('string', InputArgument::REQUIRED, 'search string.')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command get music info from search keywords:

<info>php %command.full_name%</info>

The required argument specifies search keywords:

<info>php %command.full_name%</info> stromae
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	/** @var ItunesAbstractMusicInfoProvider $musicInfo */
    	$musicInfo = $this->getContainer()->get('cpyree_audio_data.itunes_music_info');
    	/** @var SpotifyAbstractMusicInfoProvider $musicInfoSpootify */
    	$musicInfoSpootify = $this->getContainer()->get('cpyree_audio_data.spotify_music_info');
    	//$r = $musicInfoSpootify->getInfo($input->getArgument('string'));
    	$r = $musicInfoSpootify->getInfo($input->getArgument('string'));

        $output->writeln($r . "");

    }
}
