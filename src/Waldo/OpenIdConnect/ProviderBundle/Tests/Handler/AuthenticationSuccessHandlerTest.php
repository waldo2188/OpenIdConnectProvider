<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\Handler;

use Waldo\OpenIdConnect\ProviderBundle\Handler\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Request;

/**
 * AuthenticationSuccessHandlerTest
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AuthenticationSuccessHandlerTest extends \PHPUnit_Framework_TestCase
{

    private $httpUtils;
    private $securityContext;
    private $token;

    public function testHasClientId()
    {
        $logoutSuccessHandler = $this->getAuthenticationSuccessHandler();

        $request = new Request();
        $request->query->set('client_id', 'my_client_id');

        $this->httpUtils->expects($this->once())
                ->method("createRedirectResponse")
                ->with($this->callback(function($o) {
                            return $o instanceof \Symfony\Component\HttpFoundation\Request &&
                                    $o->attributes->get("clientId") === 'my_client_id';
                        }), $this->equalTo("oicp_authentication_scope"))
                ->will($this->returnValue("ok"));

        $res = $logoutSuccessHandler->onAuthenticationSuccess($request, $this->token);

        $this->assertEquals("ok", $res);
    }

    public function testHasSession()
    {
        $logoutSuccessHandler = $this->getAuthenticationSuccessHandler();

        $request = new Request();
        $session = $this->getMock("Symfony\Component\HttpFoundation\Session\Session");
        $session->expects($this->once())
                ->method("has")
                ->with($this->equalTo("_security.securitytest.target_path"))
                ->will($this->returnValue(true));
        $session->expects($this->once())
                ->method("get")
                ->with($this->equalTo("_security.securitytest.target_path"))
                ->will($this->returnValue("session_value"));

        $request->setSession($session);

        $this->token->expects($this->once())
                ->method("getProviderKey")
                ->will($this->returnValue("securitytest"));

        $this->httpUtils->expects($this->once())
                ->method("createRedirectResponse")
                ->with($this->callback(function($o) {
                            return $o instanceof \Symfony\Component\HttpFoundation\Request;
                        }), $this->equalTo("session_value"))
                ->will($this->returnValue("ok"));

        $res = $logoutSuccessHandler->onAuthenticationSuccess($request, $this->token);

        $this->assertEquals("ok", $res);
    }
    
    public function testIsAdmin()
    {
        $logoutSuccessHandler = $this->getAuthenticationSuccessHandler();

        $this->securityContext->expects($this->once())
                ->method("isGranted")
                ->with($this->equalTo("ROLE_ADMIN"))
                ->will($this->returnValue(true));
        
        $request = new Request();
        $session = $this->getMock("Symfony\Component\HttpFoundation\Session\Session");
        $session->expects($this->once())
                ->method("has")
                ->with($this->equalTo("_security.securitytest.target_path"))
                ->will($this->returnValue(false));
        $request->setSession($session);
        
        $this->token->expects($this->once())
                ->method("getProviderKey")
                ->will($this->returnValue("securitytest"));

        $this->httpUtils->expects($this->once())
                ->method("createRedirectResponse")
                ->with($this->callback(function($o) {
                            return $o instanceof \Symfony\Component\HttpFoundation\Request;
                        }), $this->equalTo("oicp_admin_index"))
                ->will($this->returnValue("ok"));

        $res = $logoutSuccessHandler->onAuthenticationSuccess($request, $this->token);

        $this->assertEquals("ok", $res);
    }
    
    public function testIsSimpleUser()
    {
        $logoutSuccessHandler = $this->getAuthenticationSuccessHandler();

        $this->securityContext->expects($this->once())
                ->method("isGranted")
                ->with($this->equalTo("ROLE_ADMIN"))
                ->will($this->returnValue(false));
        
        $request = new Request();
        $session = $this->getMock("Symfony\Component\HttpFoundation\Session\Session");
        $session->expects($this->once())
                ->method("has")
                ->with($this->equalTo("_security.securitytest.target_path"))
                ->will($this->returnValue(false));
        $request->setSession($session);
        
        $this->token->expects($this->once())
                ->method("getProviderKey")
                ->will($this->returnValue("securitytest"));

        $this->httpUtils->expects($this->once())
                ->method("createRedirectResponse")
                ->with($this->callback(function($o) {
                            return $o instanceof \Symfony\Component\HttpFoundation\Request;
                        }), $this->equalTo("oicp_account_index"))
                ->will($this->returnValue("ok"));

        $res = $logoutSuccessHandler->onAuthenticationSuccess($request, $this->token);

        $this->assertEquals("ok", $res);
    }

    private function getAuthenticationSuccessHandler()
    {
        $this->securityContext = $this->getMock("Symfony\Component\Security\Core\SecurityContextInterface");
        $this->httpUtils = $this->getMock("Symfony\Component\Security\Http\HttpUtils");
        $this->token = $this->getMockBuilder("Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken")
                ->disableOriginalConstructor()
                ->getMock();

        return new AuthenticationSuccessHandler($this->securityContext, $this->httpUtils);
    }

}
