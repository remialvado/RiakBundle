<?php

namespace Kbrw\RiakBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use Kbrw\RiakBundle\Model\Cluster\Cluster;

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
        $rootNode = $treeBuilder->root('riak');

        $rootNode
            ->children()
                ->arrayNode("clusters")
                    ->useAttributeAsKey('name')
                    ->prototype("array")
                        ->children()
                            ->scalarNode("protocol")->defaultValue(Cluster::DEFAULT_PROTOCOL)->end()
                            ->scalarNode("domain")->defaultValue(Cluster::DEFAULT_DOMAIN)->end()
                            ->scalarNode("port")->defaultValue(Cluster::DEFAULT_PORT)->end()
                            ->scalarNode("client_id")->isRequired()->end()
                            ->scalarNode("max_parallel_calls")->defaultValue(Cluster::DEFAULT_MAX_PARALLEL_CALLS)->end()
                            ->scalarNode("guzzle_client_provider_service")->defaultValue(Cluster::DEFAULT_GUZZLE_CLIENT_PROVIDER_SERVICE)->end()
                            ->arrayNode("buckets")
                                ->useAttributeAsKey('name')
                                ->prototype("array")
                                    ->children()
                                        ->scalarNode("fqcn")->defaultNull()->end()
                                        ->scalarNode("format")->defaultNull()->end()
                                        ->scalarNode("class")->defaultValue("\Kbrw\RiakBundle\Model\Bucket\Bucket")->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
