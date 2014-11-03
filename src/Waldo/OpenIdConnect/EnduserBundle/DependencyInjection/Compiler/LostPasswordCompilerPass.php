<?php

namespace Waldo\OpenIdConnect\EnduserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * LostPasswordCompilerPass
 * 
 * Compiler for service tag with enduser.lost password,
 * to extend the original behavior of lost password procedure
 */
class LostPasswordCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('oicp.lostpassword')) {
            return;
        }

        $definition = $container->getDefinition(
                'oicp.lostpassword'
        );

        $taggedServices = $container->findTaggedServiceIds(
                'enduser.lostpassword'
        );

        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                    'addLostPasswordManager', array(new Reference($id))
            );
        }
    }

}
