<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\DependencyInjection;

use Waldo\OpenIdConnect\ProviderBundle\DependencyInjection\WaldoOpenIdConnectProviderExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Description of WaldoOpenIdConnectProviderExtensionTest
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class WaldoOpenIdConnectProviderExtensionTest extends \PHPUnit_Framework_TestCase
{

    public function testGetAlias()
    {
        $caldoOpenIdConnectProviderExtension = new WaldoOpenIdConnectProviderExtension();
        
        $this->assertEquals('waldo_oic_p', $caldoOpenIdConnectProviderExtension->getAlias());
    }
    
    public function testLoad()
    {
        $container = new ContainerBuilder();
        
        $caldoOpenIdConnectProviderExtension = new WaldoOpenIdConnectProviderExtension();
        $caldoOpenIdConnectProviderExtension->load(array(), $container);
        
    }

}
