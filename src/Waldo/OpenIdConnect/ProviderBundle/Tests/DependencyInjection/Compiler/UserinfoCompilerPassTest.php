<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\DependencyInjection;

use Waldo\OpenIdConnect\ProviderBundle\DependencyInjection\Compiler\UserinfoCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Description of UserinfoCompilerPassTest
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class UserinfoCompilerPassTest extends \PHPUnit_Framework_TestCase
{

    public function testProcessNoExtension()
    {
        $userinfoCompilerPass = new UserinfoCompilerPass();

        $container = new ContainerBuilder();

        $this->assertNull($userinfoCompilerPass->process($container));
    }

    public function testProcessWithExtension()
    {
        $container = new ContainerBuilder();

        $container
                ->register('waldo_oic_p.extension.userinfo')
                ->setArguments(array(new Reference('bar')));
//                ->setTags(array("waldo_oic_p.extension.userinfo"));
        ;
        $container
                ->register('foo')
                ->setArguments(array(new Reference('bar')))
                ->addTag("waldo_oic_p.extension.userinfo");
        ;

        $userinfoCompilerPass = new UserinfoCompilerPass();
        $userinfoCompilerPass->process($container);

        $this->assertCount(1, $container->getDefinition("waldo_oic_p.extension.userinfo")->getMethodCalls());
        
        $methods = $container->getDefinition("waldo_oic_p.extension.userinfo")->getMethodCalls();
        $this->assertEquals('addExtension', $methods[0][0]);
        $this->assertEquals('foo', \PHPUnit_Framework_Assert::readAttribute($methods[0][1][0], 'id'));
    }

}
