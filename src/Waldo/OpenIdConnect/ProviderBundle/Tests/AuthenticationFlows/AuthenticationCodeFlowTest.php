<?php

namespace Waldo\OpenIdConnect\ProviderBundle\AuthenticationFlows;

use Waldo\OpenIdConnect\ProviderBundle\AuthenticationFlows\AuthenticationCodeFlow;
use Waldo\OpenIdConnect\ProviderBundle\Exception\AuthenticationRequestException;
use Waldo\OpenIdConnect\ModelBundle\Entity\Request\Authentication;
use Waldo\OpenIdConnect\ModelBundle\Entity\Account;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * AuthenticationCodeFlowTest
 *
 * @group AuthenticationCodeFlow
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AuthenticationCodeFlowTest extends \PHPUnit_Framework_TestCase
{
 private $token;
    private $securityToken;
    private $repo;
    private $em;
    private $httpUtils;
    private $securityContext;
    private $session;
    
    protected function tearDown()
    {
        parent::tearDown();
        $this->token = null;
        $this->repo = null;
        $this->em = null;
        $this->httpUtils = null;
        $this->securityContext = null;
        $this->securityToken = null;
        $this->session = null;
    }
    
    
    public function testShouldHandleAuthentication()
    {
        $acf = $this->getAuthenticationCodeFlow();
        
        $this->prepareSecurityContextPass();
        $this->prepareSecurityTokenIssuedAt(new \DateTime('now'));
        

        $account = new Account();
        $account->setId(2);
        $this->securityToken->expects($this->once())
                    ->method("getUser")
                    ->will($this->returnValue($account));
        
        $repo = $this->getMockBuilder("Doctrine\ORM\EntityRepository")
                ->disableOriginalConstructor()->getMock();
        
        $repo->expects($this->exactly(1))
                    ->method("findOneBy")
                    ->will($this->returnValue(null));
        
        $this->em->expects($this->exactly(2))
                    ->method("getRepository")
                    ->will($this->returnValue($repo));
        
        $authentication = new Authentication();
        $authentication->setMaxAge(3600)
                ->setRedirectUri("/redirect_uri/")
                ->setNonce("aNonce")
                ->setState("aState")
                ;
        
        $result = $acf->handle($authentication);
        
        $this->assertNotFalse(stripos($result->headers->get('location'),'?code'));
        $this->assertNotFalse(stripos($result->headers->get('location'), '&nonce'));
        $this->assertNotFalse(stripos($result->headers->get('location'), '&state'));
        $this->assertEquals(302, $result->getStatusCode());
    }
    
    /**
     * 
     * @dataProvider dpNeedAuthentication
     */
    public function testShouldNeedAuthentication($authentication, $callbacks = null, $errorCode = null, $errorMessage = null)
    {
        $acf = $this->getAuthenticationCodeFlow();

        $this->httpUtils->expects($this->any())
                ->method('createRedirectResponse')
                ->will($this->returnValue(new RedirectResponse('url')));

        if($callbacks !== null) {
            if(!is_array($callbacks)) {
                $callbacks = array($callbacks);
            }
            foreach($callbacks as $callback) {
                $me = $this;
                $callback($me);
            }
        }
        
        try {
            $result = $acf->handle($authentication);

            $this->session->expects($this->any())
                    ->method('set')
                    ->with($this->equalTo("'oicp.authentication.flow.code'"), $this->equalTo($authentication))
                    ->will($this->returnValue(new RedirectResponse('url')));

            $this->assertEquals("url", $result->headers->get('location'));
            $this->assertEquals(302, $result->getStatusCode());
            
        } catch (AuthenticationRequestException $ex) {
            
            $this->assertEquals($errorCode, $ex->getError());
            $this->assertEquals($errorMessage, $ex->getMessage());
            
        }
    }
    
    public function dpNeedAuthentication()
    {
        $authentication = new Authentication();
        
        $callback = function($me) {            
            $me->securityContext->expects($me->any())
                    ->method('getToken')
                    ->will($me->returnValue(null));
        };
        
        $callback1 = function($me) {
            $me->securityContext->expects($me->any())
                    ->method('getToken')
                    ->will($me->returnValue($me->securityToken));
            $me->securityContext->expects($me->any())
                    ->method('isGranted')
                    ->with($me->equalTo(AuthenticatedVoter::IS_AUTHENTICATED_FULLY))
                    ->will($me->returnValue(true));

            $me->securityToken->expects($me->any())
                    ->method('isAuthenticated')
                    ->will($me->returnValue(false));
        };
        
        $callback2 = function($me) {
            $me->prepareSecurityContextPass();
            $me->prepareSecurityTokenIssuedAt(new \DateTime('now'));
            
        };
        $authentication2 = clone $authentication;
        $authentication2->setMaxAge(-12);
        
        $authentication3 = clone $authentication;
        $authentication3->setMaxAge(3600)
                ->setPrompt(Authentication::PROMPT_LOGIN);
        
        $authentication4 = clone $authentication3;
        $authentication4->setPrompt(Authentication::PROMPT_CONSENT);
        
        $authentication5 = clone $authentication2;
        $authentication5->setPrompt(Authentication::PROMPT_NONE);
        
        $authentication6 = clone $authentication;
        $authentication6->setPrompt(Authentication::PROMPT_SELECT_ACCOUNT);

        return array(
            array($authentication, $callback),
            array($authentication, $callback1),
            array($authentication2, $callback2),
            array($authentication3, $callback2),
            array($authentication4, $callback2),
            array($authentication5, $callback2, 'login_required', 'enduser need to login'),
            array($authentication6, $callback2, 'account_selection_required', 'enduser need to select an account'),
        );
    }
    
    
    public function testSouldReturnDataFromSession()
    {
        $acf = $this->getAuthenticationCodeFlow();
        
        $this->session->expects($this->once())
                    ->method('has')
                    ->with($this->equalTo("oicp.authentication.flow.code.client_id"))
                    ->will($this->returnValue(true));
        
        $this->session->expects($this->once())
                    ->method('get')
                    ->with($this->equalTo("oicp.authentication.flow.code.client_id"))
                    ->will($this->returnValue("ok"));
        
        $this->assertEquals('ok', $acf->getAuthentication("client_id"));
    }
    
    public function testShouldHandleCancel()
    {
        $acf = $this->getAuthenticationCodeFlow();
     
        $authentication = new Authentication();
        $authentication->setNonce("anonce")
                ;
        
        $result = $acf->handleCancel($authentication);
        

        $this->assertEquals(
                "http://localhost?error=access_denied&error_description=scope+denied+by+enduser&nonce=anonce?error=access_denied&error_description=scope%20denied%20by%20enduser&nonce=anonce",
                $result->headers->get('location'));
        $this->assertEquals(302, $result->getStatusCode());
    }
    
    private function prepareSecurityTokenIssuedAt($date)
    {
        $this->securityToken->expects($this->any())
                ->method("hasAttribute")
                ->with($this->equalTo("ioc.token.issuedAt"))
                ->will($this->returnValue(true));
        $this->securityToken->expects($this->any())
                ->method("getAttribute")
                ->with($this->equalTo("ioc.token.issuedAt"))
                ->will($this->returnValue($date));
    }
    
    private function prepareSecurityContextPass()
    {
        $this->securityContext->expects($this->any())
                ->method('getToken')
                ->will($this->returnValue($this->securityToken));
        $this->securityContext->expects($this->any())
                ->method('isGranted')
                ->with($this->equalTo(AuthenticatedVoter::IS_AUTHENTICATED_FULLY))
                ->will($this->returnValue(true));

        $this->securityToken->expects($this->any())
                ->method('isAuthenticated')
                ->will($this->returnValue(true));
    }

    private function getAuthenticationCodeFlow()
    {
        $this->mockSecurityToken();
        return new AuthenticationCodeFlow(
                $this->mockSecurityContext(), $this->mockSession(), $this->mockEm(), $this->mockHttpUtils()
        );
    }

    private function mockHttpUtils()
    {
        return $this->httpUtils = ($this->httpUtils === null)
                ? $this->getMock("Symfony\Component\Security\Http\HttpUtils")
                : $this->httpUtils;
    }
    
    private function mockSession()
    {
        return $this->session = ($this->session === null)
                ? $this->getMock("Symfony\Component\HttpFoundation\Session\Session")
                : $this->session;
    }
    
    private function mockSecurityContext()
    {
        return $this->securityContext = ($this->securityContext === null)
                ? $this->getMock("Symfony\Component\Security\Core\SecurityContextInterface")
                : $this->securityContext;
    }

    private function mockSecurityToken()
    {
        return $this->securityToken = ($this->securityToken === null)
                ? $this->getMock("Symfony\Component\Security\Core\Authentication\Token\TokenInterface")
                : $this->securityToken;
    }

    private function mockEm()
    {
        return $this->em = ($this->em === null)
                ? $this->getMockBuilder("Doctrine\ORM\EntityManager")->disableOriginalConstructor()->getMock()
                : $this->em;
    }
    
    private function mockEntityRepository()
    {
        return $this->getMockBuilder("Waldo\OpenIdConnect\ModelBundle\EntityRepository\TokenRepository")
                ->disableOriginalConstructor()
                ->disableOriginalClone()
                ->setMethods(array("findOneBy"))
                ->getMock()
                ;
    }

}
