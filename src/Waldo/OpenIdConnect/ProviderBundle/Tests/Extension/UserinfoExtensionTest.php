<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\Extension;

use Waldo\OpenIdConnect\ModelBundle\Entity\Token;
use Waldo\OpenIdConnect\ProviderBundle\Extension\UserinfoExtension;

/**
 * AuthenticationSuccessHandlerTest
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class UserinfoExtensionTest extends \PHPUnit_Framework_TestCase
{

    public function testRunExtentionReturnNull()
    {
        $token = new Token();
        
        $extension = $this->getMock("Waldo\OpenIdConnect\ProviderBundle\Extension\UserinfoExtensionInterface");
        $extension->expects($this->once())
                ->method("handle");
        
        $extensionRunner = new UserinfoExtension();
        $extensionRunner->addExtension($extension);
        
        $claims = $extensionRunner->run($token);
        
        $this->assertEquals(array(), $claims);        
    }

    public function testRunExtentionReturnArray()
    {
        $token = new Token();
        
        $expected = array('roles' => array('ROLE_1', 'ROLE_3'));
        
        $extension = $this->getMock("Waldo\OpenIdConnect\ProviderBundle\Extension\UserinfoExtensionInterface");
        $extension->expects($this->once())
                ->method("handle")
                ->with($this->equalTo($token))
                ->will($this->returnValue($expected));
        
        $extensionRunner = new UserinfoExtension();
        $extensionRunner->addExtension($extension);
        
        $claims = $extensionRunner->run($token);
        
        $this->assertEquals($expected, $claims);        
    }

    public function testRun2Extention()
    {
        $token = new Token();
        
        $expected = array('roles' => array('ROLE_1', 'ROLE_3'));
        
        $extension = $this->getMock("Waldo\OpenIdConnect\ProviderBundle\Extension\UserinfoExtensionInterface");
        $extension->expects($this->exactly(2))
                ->method("handle")
                ->with($this->equalTo($token))
                ->will($this->returnValue($expected));
        
        $extensionRunner = new UserinfoExtension();
        $extensionRunner->addExtension($extension);
        $extensionRunner->addExtension($extension);
        
        $claims = $extensionRunner->run($token);
     
        $this->assertEquals($expected, $claims);        
    }
    
}
