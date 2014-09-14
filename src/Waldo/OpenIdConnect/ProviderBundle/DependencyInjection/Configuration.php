<?php

namespace Waldo\OpenIdConnect\ProviderBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('waldo_oic_p');

        $rootNode
            ->children()
                ->scalarNode('base_url')->end()
                // issuer is the URL of the OpenId Connect Provider
                // This is needed for validate response of the OpenId Connect Provider
                ->scalarNode('issuer')->cannotBeEmpty()->end()
                ->scalarNode('private_key')->end()
        ;
                                    

        return $treeBuilder;
    }
}
