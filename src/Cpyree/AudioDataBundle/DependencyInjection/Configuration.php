<?php

namespace Cpyree\AudioDataBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 * http://sapar.myasustor.com/sf_cert/web/ddp/track/approve_view/1/
 * t.score?TrackExpertFilter%5Btitle%5D=&TrackExpertFilter%5BuidRange%5D=&TrackExpertFilter%5BtrackIdRange%5D=68644%3A73635&TrackExpertFilter%5BreleaseDateRange%5D=&TrackExpertFilter%5BapprobationType%5D=NOT_PROCESSED&TrackExpertFilter%5Bsearch%5D=&TrackExpertFilter%5BisApproval%5D=1
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cpyree_audio_data');

        $rootNode
            ->children()
                ->arrayNode('album_cover')
                ->useAttributeAsKey('name')
                ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('cache_path')->info("Path to store covers")->end()
                            ->append($this->addSource())
                            ->booleanNode('persist')->defaultFalse()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

    public function addSource(){
        $builder = new TreeBuilder();
        $node = $builder->root('sources');
        $node
            ->prototype('array')
                ->beforeNormalization()
                    ->ifTrue(function($v){
                        return ($v['type'] == 'repository') && !isset($v['entityname']);
                    })
                    ->thenInvalid("Entityname is required")
                    ->ifTrue(function($v){
                        return ($v['type'] == 'file') && !isset($v['path']);
                    })
                    ->thenInvalid("path is required for file type")
                ->end()
                ->children()
                    ->scalarNode('type')->info("repository or file")
                        ->validate()
                            ->ifNotInArray(array('repository', 'file'))
                            ->thenInvalid('Invalid type "%s" for source')
                       ->end()
                    ->end()
                    ->scalarNode('em')->info('Entity manager')->defaultValue('default')->end()
                    ->scalarNode('entityname')->info('Entity to use like VendorBundle:EntityName')->end()
                    ->arrayNode('methods')->info('Method to use to construct string search')->prototype('scalar')->end()->end()
                    ->scalarNode('path')->info('path to fecth files')->end()
                ->end()
            ->end()
        ->end();
        return $node;
    }
}
