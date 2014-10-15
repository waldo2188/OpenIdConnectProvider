<?php

namespace Waldo\OpenIdConnect\LdapProviderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('waldo_oic_ldap');

        $rootNode
                ->children()
                    ->append($this->addClientNode())
                    ->append($this->addUserNode())
                    ->scalarNode('user_class')
                        ->defaultValue("Waldo\OpenIdConnect\ModelBundle\Entity\Account")
                    ->end()
                ->end()
        ;

        $this->addRoleNode($rootNode);

        return $treeBuilder;
    }

    private function addClientNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('client');

        $node
                ->isRequired()
                ->children()
                    ->scalarNode('host')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('port')->defaultValue(389)->end()
                    ->scalarNode('version')->end()
                    ->scalarNode('username')->end()
                    ->scalarNode('password')->end()
                    ->scalarNode('referrals_enabled')->end()
                    ->scalarNode('network_timeout')->end()
                    ->booleanNode('skip_roles')->defaultFalse()->end()
                ->end()
        ;

        return $node;
    }

    private function addUserNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('user');

        $node
                ->isRequired()
                ->children()
                    ->scalarNode('base_dn')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('filter')->end()
                    ->scalarNode('name_attribute')->defaultValue('uid')->end()
                    ->variableNode('attributes')->defaultValue(array())->end()
                ->end()
        ;

        return $node;
    }

    private function addRoleNode(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->fixXmlConfig('role')
            ->children()
                ->arrayNode('roles')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('base_dn')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('filter')->end()
                            ->scalarNode('name_attribute')->defaultValue('cn')->end()
                            ->scalarNode('user_attribute')->defaultValue('member')->end()
                            ->scalarNode('user_id')->defaultValue('dn')
                            ->validate()
                                ->ifNotInArray(array('dn', 'username'))
                                ->thenInvalid('Only dn or username')
                            ->end()
                        ->end()
                        ->arrayNode('roles')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
 
    }

}
