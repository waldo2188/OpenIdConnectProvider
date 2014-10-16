<?php

namespace Waldo\OpenIdConnect\ModelBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Waldo\OpenIdConnect\ModelBundle\DependencyInjection\Compiler\UniqueUsernameCompilerPass;

class WaldoOpenIdConnectModelBundle extends Bundle
{
    
     /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new UniqueUsernameCompilerPass());
    }

    
}
