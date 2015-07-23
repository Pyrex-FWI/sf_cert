<?php

namespace Cpyree\SynologyBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CpyreeSynologyExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $this->setServer($config, $container);
    }

    /**
     * @param $config
     * @param ContainerBuilder $container
     */
    private function setServer($config, ContainerBuilder $container)
    {
        if(is_array($config['server'])){
            $server = &$config['server'];
            $container->setParameter('synology.server.host', $server['host']);
            $container->setParameter('synology.server.port', $server['port']);
            $container->setParameter('synology.server.scheme', $server['protocol']);
            $container->setParameter('synology.server.login', $server['login']);
            $container->setParameter('synology.server.password', $server['password']);
            $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('services.yml');
        }
    }
}
