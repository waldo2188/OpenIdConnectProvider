<?php

namespace Waldo\OpenIdConnect\ModelBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Description of UniqueUsernameCompilerPass
 *
 */
class UniqueUsernameCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('waldo_oic_model.constraint.account')) {
            return;
        }

        $definition = $container->getDefinition(
                'waldo_oic_model.constraint.account'
        );

        $taggedServices = $container->findTaggedServiceIds(
                'waldo_oic_model.checker.unique_username'
        );
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                    'addUniqueUsernameChecker', array(new Reference($id))
            );
        }
    }

}
