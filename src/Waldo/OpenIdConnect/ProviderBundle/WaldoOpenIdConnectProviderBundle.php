<?php

namespace Waldo\OpenIdConnect\ProviderBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Waldo\OpenIdConnect\ProviderBundle\DependencyInjection\WaldoOpenIdConnectProviderExtension;
use Waldo\OpenIdConnect\ProviderBundle\DependencyInjection\Compiler\UserinfoCompilerPass;

class WaldoOpenIdConnectProviderBundle extends Bundle
{

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new UserinfoCompilerPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        // return the right extension instead of "auto-registering" it. Now the
        // alias can be waldo_oic_p instead of waldo_open_id_connect_provider..
        if (null === $this->extension) {
            return new WaldoOpenIdConnectProviderExtension();
        }
        return $this->extension;
    }

}
