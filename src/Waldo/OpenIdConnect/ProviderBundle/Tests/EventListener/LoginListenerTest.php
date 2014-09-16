<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\EventListener;

use Waldo\OpenIdConnect\ProviderBundle\EventListener\LoginListener;
use Symfony\Component\Security\Http\SecurityEvents;
use Waldo\OpenIdConnect\ProviderBundle\Entity\Account;

/**
 * Description of LoginListener
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class LoginListenerTest extends \PHPUnit_Framework_TestCase
{

    private $em;

    protected function tearDown()
    {
        parent::tearDown();
        $this->em = null;
    }

    public function testGetSubscribedEvents()
    {
        $loginListener = $this->getLoginListener();
        
        $expected = array(
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin'
        );
        
        $this->assertEquals($expected, $loginListener->getSubscribedEvents());
    }

    private function getLoginListener()
    {
        return new LoginListener($this->mockEm());
    }

    private function mockEm()
    {
        return $this->em = ($this->em === null) 
                ? $this->getMockBuilder("Doctrine\ORM\EntityManager")->disableOriginalConstructor()->getMock() 
                : $this->em;
    }
    
    public function testOnSecurityInteractiveLogin()
    {
        $loginListener = $this->getLoginListener();
        
        $event = $this->getMockBuilder("Symfony\Component\Security\Http\Event\InteractiveLoginEvent")
                ->disableOriginalConstructor()->getMock();
        
        $authenticationToken = $this->getMock("Symfony\Component\Security\Core\Authentication\Token\TokenInterface");
        
        $event->expects($this->exactly(3))
                ->method("getAuthenticationToken")
                ->will($this->returnValue($authenticationToken));
        
        $authenticationToken->expects($this->once())
                ->method("isAuthenticated")
                ->will($this->returnValue(true));
        
        $authenticationToken->expects($this->once())
                ->method("setAttribute")
                ->with($this->equalTo("ioc.token.issuedAt"), $this->anything());
        
        $authenticationToken->expects($this->once())
                ->method("getUser")
                ->will($this->returnValue(new Account()));
                
        $this->em->expects($this->once())
                ->method("persist")
                ->with($this->callback(function($o)
                {
                    return $o->getLastLoginAt() !== null;
                }));
        $this->em->expects($this->once())
                ->method("flush");
        
        $loginListener->onSecurityInteractiveLogin($event);
    }


}
