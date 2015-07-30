<?php

namespace Redking\Bundle\UploadBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('redking_upload');

        $rootNode
            ->children()
                ->arrayNode('amazon_s3')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('key')->isRequired()->end()
                        ->scalarNode('secret')->isRequired()->end()
                        ->scalarNode('bucket')->isRequired()->end()
                        ->scalarNode('region')->defaultValue('eu-west-1')->isRequired()->end()
                        ->scalarNode('prefix_url')->end()
                    ->end()
                ->end()
            
                ->arrayNode('resizes')
                    ->useAttributeAsKey('suffix')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('width')->defaultNull()->end()
                            ->scalarNode('height')->defaultNull()->end()
                        ->end()
                    ->end()
                ->end()
            
            ->end()
        ;

        return $treeBuilder;
    }
}
