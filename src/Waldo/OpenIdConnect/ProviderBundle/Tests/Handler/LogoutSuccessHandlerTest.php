<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\Handler;

use Waldo\OpenIdConnect\ProviderBundle\Handler\LogoutSuccessHandler;
use Symfony\Component\HttpFoundation\Request;

/**
 * LogoutSuccessHandlerTest
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class LogoutSuccessHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $httpUtils;

    public function testShouldRedirect()
    {
        $session = $this->getMock("Symfony\Component\HttpFoundation\Session\Session");
        $session->expects($this->once())
                ->method("remove")
                ->with($this->equalTo("oic.login.auth.user"));
        
        
        $request = new Request();
        $request->setSession($session);
        
        $logoutSuccessHandler = $this->getLogoutSuccessHandler();
        
        $this->httpUtils->expects($this->once())
                ->method("createRedirectResponse")
                ->with($this->equalTo($request), $this->equalTo("/"))
                ->will($this->returnValue("ok"));
        
        $res = $logoutSuccessHandler->onLogoutSuccess($request);
        
        $this->assertEquals("ok", $res);
    }
    
    public function testShouldRedirectToAnotherPath()
    {
        $session = $this->getMock("Symfony\Component\HttpFoundation\Session\Session");
        $session->expects($this->once())
                ->method("remove")
                ->with($this->equalTo("oic.login.auth.user"));
        
        
        $request = new Request();
        $request->setSession($session);
        
        $this->httpUtils = $this->getMock("Symfony\Component\Security\Http\HttpUtils");
     
        $this->httpUtils->expects($this->once())
                ->method("createRedirectResponse")
                ->with($this->equalTo($request), $this->equalTo("anotherPath"))
                ->will($this->returnValue("ok"));
        
        $logoutSuccessHandler = new LogoutSuccessHandler($this->httpUtils, 'anotherPath');
                
        $res = $logoutSuccessHandler->onLogoutSuccess($request);
        
        $this->assertEquals("ok", $res);
    }
    
    private function getLogoutSuccessHandler()
    {
        $this->httpUtils = $this->getMock("Symfony\Component\Security\Http\HttpUtils");
     
        return new LogoutSuccessHandler($this->httpUtils);
    }
    
}
