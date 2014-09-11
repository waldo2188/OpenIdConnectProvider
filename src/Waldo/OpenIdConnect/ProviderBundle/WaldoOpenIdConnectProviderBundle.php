<?php

namespace Waldo\OpenIdConnect\ProviderBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Waldo\OpenIdConnect\ProviderBundle\DependencyInjection\WaldoOpenIdConnectProviderExtension;

class WaldoOpenIdConnectProviderBundle extends Bundle
{
    
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
