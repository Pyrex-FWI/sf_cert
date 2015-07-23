<?php


namespace Cpyree\AudioDataBundle\Command;

use Cpyree\AudioDataBundle\Services\ItunesAbstractMusicInfoProvider;
use Cpyree\AudioDataBundle\Services\SpotifyAbstractMusicInfoProvider;
use Cpyree\AudioDataBundle\Services\AlbumCover;
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
class AlbumCoverCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('album:cover')
            ->setDescription('Audio data search')
            ->addArgument('target', InputArgument::OPTIONAL, 'target configuration.')
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
    	/** @var AlbumCover $albumCover */
    	$albumCover = $this->getContainer()->get('cpyree_audio_data.album_cover.digitaldjpool');
        $albumCover->run();
    	/*$albumCover = $this->getContainer()->get('cpyree_audio_data.album_cover');
        $albumCover->setConf('dbs');*/
    }
}
