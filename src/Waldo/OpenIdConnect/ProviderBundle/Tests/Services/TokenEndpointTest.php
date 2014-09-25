<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\Services;

use Waldo\OpenIdConnect\ProviderBundle\Services\TokenEndpoint;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Waldo\OpenIdConnect\ModelBundle\Entity\Token;
use Waldo\OpenIdConnect\ModelBundle\Entity\Client;

/**
 * TokenEndpointTest
 * @group TokenEndpoint
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class TokenEndpointTest extends \PHPUnit_Framework_TestCase
{

    private $token;
    private $securityToken;
    private $repo;
    private $em;
    private $idTokenHelper;
    private $securityContext;

    private $redirectURI = "aRedirectURI";
    
    protected function tearDown()
    {
        parent::tearDown();
        $this->token = null;
        $this->repo = null;
        $this->em = null;
        $this->idTokenHelper = null;
        $this->securityContext = null;
        $this->securityToken = null;
    }

    /**
     * @dataProvider dataproviderShoulReturnAJsonResponse
     */
    public function testShoulReturnAJsonResponse($grantType, $findBy, $code)
    {
        $this->prepareValue(10, $findBy);
        
        $this->mockIdTokenHelper()->expects($this->once())
                ->method('makeIdToken')
                ->will($this->returnValue(array("avalue")));

        $tokenEndpoint = $this->getTokenEndpoint();

        $request = new Request();
        $request->request->add(
                array("redirect_uri" => $this->redirectURI,
                    "grant_type" => $grantType,
                    $code => "azerttyuiop"));



        $response = $tokenEndpoint->handle($request);
        
        $this->assertInstanceOf("Symfony\Component\HttpFoundation\Response", $response);
        $this->assertEquals("application/json", $response->headers->get("content-type"));
        $this->assertEquals(200, $response->getStatusCode());
        
        $decodeContent = json_decode($response->getContent());

        $this->assertNotNull($decodeContent->access_token);
        $this->assertNotNull($decodeContent->refresh_token);
        $this->assertEquals($decodeContent->token_type, "Bearer");
        $this->assertEquals($decodeContent->id_token[0], "avalue");
    }

    public function dataproviderShoulReturnAJsonResponse()
    {
        return array(
            array("grantType" => "authorization_code", "findBy" => "getClientTokenByCode", "code" => 'code'),
            array("grantType" => "refresh_token", "findBy" => "getClientTokenByRefreshToken", "code" => 'refresh_token')
        );
    }
    
    public function testSouldFailAtRetrieveToken()
    {
        $this->mockSecurityContext();
        $this->securityContext->expects($this->once())
                ->method("getToken")
                ->will($this->returnValue(null));
        
        $tokenEndpoint = $this->getTokenEndpoint();
        
        $request = new Request();

        $response = $tokenEndpoint->handle($request);
        
        $this->assertInstanceOf("Symfony\Component\HttpFoundation\Response", $response);
        $this->assertEquals("application/json", $response->headers->get("content-type"));
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('{"error":"invalid_request","error_description":"invalid client"}', $response->getContent());
    }
    
    public function testSouldFailAtIsAuthenticated()
    {
        $this->mockSecurityToken();
        $this->securityToken->expects($this->once())
                ->method("isAuthenticated")
                ->will($this->returnValue(false));
        $this->mockSecurityContext();
        $this->securityContext->expects($this->exactly(2))
                ->method("getToken")
                ->will($this->returnValue($this->securityToken));
        
        $tokenEndpoint = $this->getTokenEndpoint();
        
        $request = new Request();

        $response = $tokenEndpoint->handle($request);
        
        $this->assertInstanceOf("Symfony\Component\HttpFoundation\Response", $response);
        $this->assertEquals("application/json", $response->headers->get("content-type"));
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('{"error":"invalid_request","error_description":"invalid client"}', $response->getContent());
    }
    
    public function testSouldFailAtIsAuthenticated2()
    {
        $this->mockSecurityToken();
        $this->securityToken->expects($this->once())
                ->method("isAuthenticated")
                ->will($this->returnValue(true));
        $this->mockSecurityContext();
        $this->securityContext->expects($this->exactly(2))
                ->method("getToken")
                ->will($this->returnValue($this->securityToken));
        $this->securityContext->expects($this->once())
                ->method("isGranted")
                ->with($this->equalTo(AuthenticatedVoter::IS_AUTHENTICATED_FULLY))
                ->will($this->returnValue(false));
        
        $tokenEndpoint = $this->getTokenEndpoint();
        
        $request = new Request();

        $response = $tokenEndpoint->handle($request);
        
        $this->assertInstanceOf("Symfony\Component\HttpFoundation\Response", $response);
        $this->assertEquals("application/json", $response->headers->get("content-type"));
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('{"error":"invalid_request","error_description":"invalid client"}', $response->getContent());
    }
    
    public function testShouldFailedChekForRedirectUri()
    {
        $this->prepareValue(0);
        
        $tokenEndpoint = $this->getTokenEndpoint();
        
        $request = new Request();

        $response = $tokenEndpoint->handle($request);
        
        $this->assertInstanceOf("Symfony\Component\HttpFoundation\Response", $response);
        $this->assertEquals("application/json", $response->headers->get("content-type"));
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('{"error":"invalid_request","error_description":"no such redirect_uri"}', $response->getContent());
    }
    
    public function testShouldFailedForRedirectUri()
    {
        $this->prepareValue(0);
        
        $tokenEndpoint = $this->getTokenEndpoint();
        
        $request = new Request();
        $request->request->add(array("redirect_uri" => "plop"));
        $response = $tokenEndpoint->handle($request);
        
        $this->assertInstanceOf("Symfony\Component\HttpFoundation\Response", $response);
        $this->assertEquals("application/json", $response->headers->get("content-type"));
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('{"error":"invalid_request","error_description":"no matching redirect_uri"}', $response->getContent());
    }
    
    public function testShouldFailedWhithNoGrantType()
    {
        $this->prepareValue(0);
        
        $tokenEndpoint = $this->getTokenEndpoint();
        
        $request = new Request();
        $request->request->add(array("redirect_uri" => $this->redirectURI));
        $response = $tokenEndpoint->handle($request);
        
        $this->assertInstanceOf("Symfony\Component\HttpFoundation\Response", $response);
        $this->assertEquals("application/json", $response->headers->get("content-type"));
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('{"error":"invalid_request","error_description":"no such grant_type"}', $response->getContent());
    }
    
    public function testShouldFailedWhithWrongGrantType()
    {
        $this->prepareValue(0);
        
        $tokenEndpoint = $this->getTokenEndpoint();
        
        $request = new Request();
        $request->request->add(array("redirect_uri" => $this->redirectURI,
                    "grant_type" => "authorization_nope"));
        $response = $tokenEndpoint->handle($request);
        
        $this->assertInstanceOf("Symfony\Component\HttpFoundation\Response", $response);
        $this->assertEquals("application/json", $response->headers->get("content-type"));
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('{"error":"unsupported_grant_type","error_description":"authorization_nope is not supported"}', $response->getContent());
    }
    
    public function testShouldFailedWhithNoCode()
    {
        $this->prepareValue(0);
        
        $tokenEndpoint = $this->getTokenEndpoint();
        
        $request = new Request();
        $request->request->add(array("redirect_uri" => $this->redirectURI,
                    "grant_type" => "authorization_code"));
        $response = $tokenEndpoint->handle($request);
        
        $this->assertInstanceOf("Symfony\Component\HttpFoundation\Response", $response);
        $this->assertEquals("application/json", $response->headers->get("content-type"));
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('{"error":"invalid_request","error_description":"no such code or refresh_token"}', $response->getContent());
    }
    
    public function testShouldFailedWhithNoToken()
    {
        $this->prepareValue(0);
        
        $repo = $this->mockEntityRepository();

        $repo->expects($this->once())
                ->method("getClientTokenByCode")
                ->with($this->equalTo($this->getClient()), $this->equalTo('azerttyuiop'))
                ->will($this->returnValue(null));
        
        $this->mockEm();
        $this->em->expects($this->once())
                ->method("getRepository")
                ->will($this->returnValue($repo));
        
        $tokenEndpoint = $this->getTokenEndpoint();
        
        $request = new Request();
        $request->request->add(array("redirect_uri" => $this->redirectURI,
                    "grant_type" => "authorization_code",
                    "code" => "azerttyuiop"));
        $response = $tokenEndpoint->handle($request);
        
        $this->assertInstanceOf("Symfony\Component\HttpFoundation\Response", $response);
        $this->assertEquals("application/json", $response->headers->get("content-type"));
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('{"error":"invalid_authorization_code","error_description":"no such code"}', $response->getContent());
    }
    
    public function testShouldFailedWhithWrongToken()
    {
        $token = $this->getToken();
        $token->setRedirectUri("plop");
        
        $this->prepareValue(0);
        
        $repo = $this->mockEntityRepository();

        $repo->expects($this->once())
                ->method("getClientTokenByCode")
                ->with($this->equalTo($this->getClient()), $this->equalTo('azerttyuiop'))
                ->will($this->returnValue($token));
        
        $this->mockEm();
        $this->em->expects($this->once())
                ->method("getRepository")
                ->will($this->returnValue($repo));
        
        $tokenEndpoint = $this->getTokenEndpoint();
        
        $request = new Request();
        $request->request->add(array("redirect_uri" => $this->redirectURI,
                    "grant_type" => "authorization_code",
                    "code" => "azerttyuiop"));
        $response = $tokenEndpoint->handle($request);
        
        $this->assertInstanceOf("Symfony\Component\HttpFoundation\Response", $response);
        $this->assertEquals("application/json", $response->headers->get("content-type"));
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('{"error":"invalid_request","error_description":"no matching request_uri"}', $response->getContent());
    }
    

    

    private function getTokenEndpoint()
    {
        $this->mockSecurityContext();
        $this->mockEm();
        $this->mockIdTokenHelper();
        
        return new TokenEndpoint($this->securityContext, $this->em, $this->idTokenHelper);
    }

    private function prepareValue($level = 10, $grantType = 'getClientTokenByCode')
    {
        $this->mockSecurityToken();
        $this->mockSecurityContext();
        
        $this->securityContext->expects($this->exactly(3))
                ->method("getToken")
                ->will($this->returnValue($this->securityToken));
        $this->securityContext->expects($this->once())
                ->method("isGranted")
                ->with($this->equalTo(AuthenticatedVoter::IS_AUTHENTICATED_FULLY))
                ->will($this->returnValue(true));

            $this->securityToken->expects($this->once())
                    ->method("isAuthenticated")
                    ->will($this->returnValue(true));
            $this->securityToken->expects($this->once())
                    ->method("getUser")
                    ->will($this->returnValue($this->getClient()));

        if ($level >= 1) {
            $repo = $this->mockEntityRepository();

            $repo->expects($this->once())
                    ->method($grantType)
                    ->with($this->equalTo($this->getClient()), $this->equalTo('azerttyuiop'))
                    ->will($this->returnValue($this->getToken()));

            $repo->expects($this->exactly(2))
                    ->method("findOneBy")
                    ->will($this->returnValue(null));

            $this->mockEm();
            $this->em->expects($this->once())
                    ->method("getRepository")
                    ->will($this->returnValue($repo));
            $this->em->expects($this->once())
                    ->method("persist");
            $this->em->expects($this->once())
                    ->method("flush");
        }
    }

    private function getClient()
    {
        $client = new Client();
        $client->setRedirectUris(array($this->redirectURI));
        return $client;
    }
    
    private function getToken()
    {
        $token = new Token();
        $token->setRedirectUri($this->redirectURI);
        return $token;
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
    
    private function mockIdTokenHelper()
    {
        return $this->idTokenHelper = ($this->idTokenHelper === null)
                ? $this->getMockBuilder("Waldo\OpenIdConnect\ProviderBundle\Services\IdTokenHelper")
                ->disableOriginalConstructor()->getMock()
                : $this->idTokenHelper;
        
    }
    private function mockEntityRepository()
    {
        return $this->getMockBuilder("Waldo\OpenIdConnect\ModelBundle\EntityRepository\TokenRepository")
                ->disableOriginalConstructor()
                ->disableOriginalClone()
                ->setMethods(array("getClientTokenByCode", "getClientTokenByRefreshToken", "findOneBy"))
                ->getMock()
                ;
    }

}
