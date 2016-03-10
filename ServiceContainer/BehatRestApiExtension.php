<?php

namespace Ulff\BehatRestApiExtension\ServiceContainer;

use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BehatRestApiExtension implements ExtensionInterface
{
    public function getConfigKey()
    {
        return 'behat_rest_api_context';
    }

    public function initialize(ExtensionManager $extensionManager)
    {
    }

    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('behat_rest_api_context')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')->defaultValue('Ulff\\BehatRestApiExtension\\Context\\RestApiContext')->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    public function load(ContainerBuilder $container, array $config)
    {
    }

    public function process(ContainerBuilder $container)
    {
    }
}
