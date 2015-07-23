<?php

namespace Cpyree\DigitalDjPoolBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('cpyree_digital_dj_pool')
            ->children()->scalarNode('files_path')->isRequired()->end()->end()
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('console')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('pool_size')->defaultValue(200)->end()
                        ->scalarNode('ddp_folder')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('ddp_recycle_folder')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('stream')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('type')
                            ->defaultValue('distant')
                            ->cannotBeEmpty()
                            ->validate()
                                ->ifNotInArray(array('distant', 'local'))
                                ->thenInvalid('Invalid Stream type "%s"')
                            ->end()
                        ->end()
                        ->scalarNode('route')->end()
                    ->end()
                ->end()
                ->arrayNode('extract')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('max_size')
                            ->defaultValue('2147483648') //2GO
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->append($this->playlistConf())
            ->end();
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }

    public function playlistConf(){
        $builder = new TreeBuilder();
        $node = $builder->root('playlist');
        $node
            ->info('List of playlist you want generate')
            ->prototype('array')
                ->children()
                    ->arrayNode('criteria')->end()
                    //->scalarNode('')
                ->end()
            ->end()
        ->end();
        return $node;
    }
}
