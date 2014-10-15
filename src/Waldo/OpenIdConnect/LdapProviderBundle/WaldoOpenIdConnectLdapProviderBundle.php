<?php

namespace Waldo\OpenIdConnect\LdapProviderBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Waldo\OpenIdConnect\LdapProviderBundle\DependencyInjection\WaldoOpenIdConnectLdapProviderExtension;
use Waldo\OpenIdConnect\LdapProviderBundle\DependencyInjection\Security\Factory\LdapFactory;

class WaldoOpenIdConnectLdapProviderBundle extends Bundle
{

    /**
     * Build the bundle.
     *
     * This is used to register the security listener to support Symfony 2.1.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new LdapFactory);
    }
    
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();
        if (!function_exists('ldap_connect')) {
            throw new \Exception("module php-ldap isn't install");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        // return the right extension instead of "auto-registering" it. Now the
        // alias can be waldo_oic_p instead of waldo_open_id_connect_provider..
        if (null === $this->extension) {
            return new WaldoOpenIdConnectLdapProviderExtension();
        }
        return $this->extension;
    }

}
