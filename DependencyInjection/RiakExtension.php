<?php

namespace Kbrw\RiakBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class RiakExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config["clusters"] as $name => $clusterConfig) {
            $clusterConfig["name"] = $name;
            $definition = new Definition('Kbrw\RiakBundle\Model\Cluster\Cluster', array(
                $clusterConfig["name"],
                $clusterConfig["protocol"],
                $clusterConfig["domain"],
                $clusterConfig["port"],
                $clusterConfig["client_id"],
                $clusterConfig["max_parallel_calls"],
                $clusterConfig["buckets"],
                new Reference($clusterConfig["guzzle_client_provider_service"]),
                new Reference("event_dispatcher"),
                new Reference("kbrw.riak.bucket"),
                new Reference("kbrw.riak.kv"),
                new Reference("kbrw.riak.search"),
                new Reference("kbrw.riak.mapreduce")
            ));
            $container->setDefinition("riak.cluster." . $name, $definition);
        }
    }
}
