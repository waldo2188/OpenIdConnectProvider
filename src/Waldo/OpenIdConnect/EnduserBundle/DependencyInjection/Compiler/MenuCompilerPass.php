<?php

namespace Waldo\OpenIdConnect\EnduserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Description of MenuCompilerPass
 *
 */
class MenuCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('oic_enduser.menu_builder')) {
            return;
        }

        $definition = $container->getDefinition(
                'oic_enduser.menu_builder'
        );

        $taggedServices = $container->findTaggedServiceIds(
                'oic_enduser.menu'
        );
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                    'addProvider', array(new Reference($id))
            );
        }
    }

}
