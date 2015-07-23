<?php

namespace Cpyree\DigitalDjPoolBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CpyreeDigitalDjPoolExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');
        $container->setParameter('ddp.files_path', $config['files_path']);
        $container->setParameter('ddp.console.pool_size', $config['console']['pool_size']);
        $container->setParameter('ddp.console.ddp_folder', $config['console']['ddp_folder']);
        $container->setParameter('ddp.console.ddp_recycle_folder', $config['console']['ddp_recycle_folder']);
        $container->setParameter('ddp.stream.type', $config['stream']['type']);
        $container->setParameter('ddp.stream.route', $config['stream']['route']);
        $container->setParameter('ddp.extract.max_size', $config['extract']['max_size']);
    }
}
