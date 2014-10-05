<?php

namespace Waldo\OpenIdConnect\ProviderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Description of UserinfoCompilerPass
 *
 */
class UserinfoCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('waldo_oic_p.extension.userinfo')) {
            return;
        }

        $definition = $container->getDefinition(
                'waldo_oic_p.extension.userinfo'
        );

        $taggedServices = $container->findTaggedServiceIds(
                'waldo_oic_p.extension.userinfo'
        );
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                    'addExtension', array(new Reference($id))
            );
        }
    }

}
