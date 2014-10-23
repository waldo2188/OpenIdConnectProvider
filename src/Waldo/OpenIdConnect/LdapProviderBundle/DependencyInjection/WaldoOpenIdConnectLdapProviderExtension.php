<?php

namespace Waldo\OpenIdConnect\LdapProviderBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class WaldoOpenIdConnectLdapProviderExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('enduser_menu.xml');
        
        $container->setParameter('waldo_oic_ldap.ldap_connection.params', $config);
        $container->setParameter('waldo_oic_ldap.model.user_class', $config["user_class"]);
    }
    
        
    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'waldo_oic_ldap';
    }
}
