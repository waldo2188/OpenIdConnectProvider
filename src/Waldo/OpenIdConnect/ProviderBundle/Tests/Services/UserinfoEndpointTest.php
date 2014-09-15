<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\Services;

use Waldo\OpenIdConnect\ProviderBundle\Services\UserinfoEndpoint;
use Symfony\Component\HttpFoundation\Request;
use Waldo\OpenIdConnect\ProviderBundle\Entity\Token;

/**
 * UserinfoEndpointTest
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class UserinfoEndpointTest extends \PHPUnit_Framework_TestCase
{
    private $token;
    private $repo;
    private $em;
    private $userinfoHelper;

    protected function tearDown()
    {
        parent::tearDown();
        $this->token = null;
        $this->repo = null;
        $this->em = null;
        $this->userinfoHelper = null;
    }

    public function testShoulReturnAJsonResponse()
    {
        $this->prepareValue(true,true,true);
        $this->userinfoHelper->expects($this->once())
                ->method('makeUserinfo')
                ->will($this->returnValue(array("avalue")));

        $userinforEndpoint = new UserinfoEndpoint($this->em, $this->userinfoHelper);

        $request = new Request();
        $request->headers->set("authorization", "Bearer azerttyuiop");


        $response = $userinforEndpoint->handle($request);

        $this->assertInstanceOf("Symfony\Component\HttpFoundation\Response", $response);
        $this->assertEquals("application/json", $response->headers->get("content-type"));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('["avalue"]', $response->getContent());
    }
    
    public function testShoulReturnAJwtResponse()
    {
        $this->prepareValue(true,true,true);
        
        $this->userinfoHelper->expects($this->once())
                ->method('makeUserinfo')
                ->will($this->returnValue("someJWTstring"));

        $userinforEndpoint = new UserinfoEndpoint($this->em, $this->userinfoHelper);

        $request = new Request();
        $request->headers->set("authorization", "Bearer azerttyuiop");

        $response = $userinforEndpoint->handle($request);
        
        $this->assertInstanceOf("Symfony\Component\HttpFoundation\Response", $response);
        $this->assertEquals("application/jwt", $response->headers->get("content-type"));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('someJWTstring', $response->getContent());
    }
    
    public function testShouldReturnNoSuchAuthorization()
    {
        $this->prepareValue();

        $userinforEndpoint = new UserinfoEndpoint($this->em, $this->userinfoHelper);

        $request = new Request();
        
        $response = $userinforEndpoint->handle($request);
        
        $this->assertInstanceOf("Symfony\Component\HttpFoundation\JsonResponse", $response);
        $this->assertEquals("application/json", $response->headers->get("content-type"));
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('{"error":"invalid_request","error_description":"no such authorization"}', $response->getContent());
    }
    
    public function testShouldReturnNoSuchBearerCode()
    {
        $this->prepareValue();

        $userinforEndpoint = new UserinfoEndpoint($this->em, $this->userinfoHelper);

        $request = new Request();
        $request->headers->set("authorization", "azerttyuiop");
        
        $response = $userinforEndpoint->handle($request);
        
        $this->assertInstanceOf("Symfony\Component\HttpFoundation\JsonResponse", $response);
        $this->assertEquals("application/json", $response->headers->get("content-type"));
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('{"error":"invalid_token","error_description":"no such bearer code"}', $response->getContent());
    }
    
    public function testShouldReturnInvalidBearerCode()
    {
        $this->prepareValue();

        $userinforEndpoint = new UserinfoEndpoint($this->em, $this->userinfoHelper);

        $request = new Request();
        $request->headers->set("authorization", "Bearer");
        
        $response = $userinforEndpoint->handle($request);
        
        $this->assertInstanceOf("Symfony\Component\HttpFoundation\JsonResponse", $response);
        $this->assertEquals("application/json", $response->headers->get("content-type"));
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('{"error":"invalid_token","error_description":"invalid bearer code"}', $response->getContent());
    }
    
    public function testShouldReturnInvalidBearerCodeFromBDD()
    {
        $this->prepareValue(false, true);

        $this->repo->expects($this->once())
                ->method("findOneByAccessToken")
                ->with($this->equalTo("azerttyuiop"))
                ->will($this->returnValue(null));

        $userinforEndpoint = new UserinfoEndpoint($this->em, $this->userinfoHelper);

        $request = new Request();
        $request->headers->set("authorization", "Bearer azerttyuiop");
        
        $response = $userinforEndpoint->handle($request);
        
        $this->assertInstanceOf("Symfony\Component\HttpFoundation\JsonResponse", $response);
        $this->assertEquals("application/json", $response->headers->get("content-type"));
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('{"error":"access_denied","error_description":"invalid bearer code"}', $response->getContent());
    }
    
    private function prepareValue($findOneByAccessToken = false, $getRepository = false, $persist = false)
    {
        $this->token = new Token();
        $this->token->setAccessToken("anAccessToken")
                ->setRefreshToken("aRefreshToken")
        ;

        $this->repo = $this->getMockBuilder("Doctrine\ORM\EntityRepository")
                ->disableOriginalConstructor()
                ->setMethods(array("findOneByAccessToken"))
                ->getMock();

        if($findOneByAccessToken) {
            $this->repo->expects($this->once())
                    ->method("findOneByAccessToken")
                    ->with($this->equalTo("azerttyuiop"))
                    ->will($this->returnValue($this->token));
        }
        $this->em = $this->getMockBuilder("Doctrine\ORM\EntityManager")
                ->disableOriginalConstructor()
                ->getMock();
        
        if($getRepository) {
            $this->em->expects($this->once())
                    ->method("getRepository")
                    ->will($this->returnValue($this->repo));
        }
        
        if($persist) {
            $this->em->expects($this->once())
                    ->method("persist")
                    ->with($this->callback(function($token) {
                                return $token->getAccessToken() === null && $token->getRefreshToken() !== null;
                            }))
            ;
        }

        $this->userinfoHelper = $this->getMockBuilder("Waldo\OpenIdConnect\ProviderBundle\Services\UserinfoHelper")
                ->disableOriginalConstructor()
                ->getMock();
    }
}
