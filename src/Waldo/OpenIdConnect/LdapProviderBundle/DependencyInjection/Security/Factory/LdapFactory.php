<?php

namespace Waldo\OpenIdConnect\LdapProviderBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use  Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class LdapFactory extends AbstractFactory
{
    public function getPosition()
    {
        return 'form';
    }

    public function getKey()
    {
        return 'waldo_oic_ldap';
    }


    protected function getListenerId()
    {
        return 'waldo_oic_ldap.security.authentication.listener';
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {       
        $dao = 'security.authentication.provider.dao.'.$id;
        $container
            ->setDefinition($dao, new DefinitionDecorator('security.authentication.provider.dao'))
            ->replaceArgument(0, new Reference($userProviderId))
            ->replaceArgument(2, $id)
        ;
        

        $provider = 'waldo_oic_ldap.security.authentication.provider.'.$id;
        $container
            ->setDefinition($provider, new DefinitionDecorator('waldo_oic_ldap.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProviderId))
            ->replaceArgument(1, new Reference($dao))
            ->replaceArgument(2, $id)
            ;

        return $provider;
    }

    protected function createlistener($container, $id, $config, $userProvider)
    {
        $listenerId = parent::createListener($container, $id, $config, $userProvider);

        return $listenerId;
    }

//    protected function createEntryPoint($container, $id, $config, $defaultEntryPoint)
//    {
//        $entryPointId = 'waldo_oic_ldap.security.authentication.form_entry_point.'.$id;
//        $container
//            ->setDefinition($entryPointId, new DefinitionDecorator('waldo_oic_ldap.security.authentication.form_entry_point'))
//            ->addArgument(new Reference('security.http_utils'))
//            ->addArgument($config['login_path'])
//            ->addArgument($config['use_forward'])
//            ;
//
//        return $entryPointId;
//    }
}
