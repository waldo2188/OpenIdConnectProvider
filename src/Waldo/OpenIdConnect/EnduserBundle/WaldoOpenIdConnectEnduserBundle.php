<?php

namespace Waldo\OpenIdConnect\EnduserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Waldo\OpenIdConnect\EnduserBundle\DependencyInjection\Compiler\MenuCompilerPass;
use Waldo\OpenIdConnect\EnduserBundle\DependencyInjection\Compiler\LostPasswordCompilerPass;

class WaldoOpenIdConnectEnduserBundle extends Bundle
{
    
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MenuCompilerPass());
        $container->addCompilerPass(new LostPasswordCompilerPass());
    }
}
