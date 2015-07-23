<?php


namespace Cpyree\SynologyBundle\Command;

use Cpyree\AudioDataBundle\Services\ItunesAbstractMusicInfoProvider;
use Cpyree\AudioDataBundle\Services\SpotifyAbstractMusicInfoProvider;
use Cpyree\SynologyBundle\Services\SynologySession;
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
            ->setName('synology:search')
            ->setDescription('Search file on synology')
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
    	/** @var SynologySession $syno */
        $syno = $this->getContainer()->get('cpyree_synology.session');
        $r = $syno->login();
        print_r($r);
        $output->writeln($r);

    }
}
