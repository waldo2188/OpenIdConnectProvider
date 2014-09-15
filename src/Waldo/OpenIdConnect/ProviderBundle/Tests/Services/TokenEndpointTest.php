<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\Services;

use Waldo\OpenIdConnect\ProviderBundle\Services\TokenEndpoint;
use Symfony\Component\HttpFoundation\Request;
use Waldo\OpenIdConnect\ProviderBundle\Entity\Token;
use Waldo\OpenIdConnect\ProviderBundle\Entity\Client;

//TODO need to finish

/**
 * TokenEndpointTest
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class TokenEndpointTest extends \PHPUnit_Framework_TestCase
{
    private $token;
    private $repo;
    private $em;
    private $idTokenHelper;
    private $securityContext;

    protected function tearDown()
    {
        parent::tearDown();
        $this->token = null;
        $this->repo = null;
        $this->em = null;
        $this->idTokenHelper = null;
        $this->securityContext = null;
    }

    public function testShoulReturnAJsonResponse()
    {
        $this->prepareValue(true,true,true);
        $this->idTokenHelper->expects($this->once())
                ->method('makeUserinfo')
                ->will($this->returnValue(array("avalue")));

        $userinforEndpoint = $this->getTokenEndpoint();

        $request = new Request();
        $request->headers->set("redirect_uri", "aRedirectURI");
        $request->headers->set("grant_type", "authorization_code");
        $request->headers->set("code", "azerttyuiop");


        $response = $userinforEndpoint->handle($request);

        print_r($response);
        echo "\n";exit;



//        $this->assertInstanceOf("Symfony\Component\HttpFoundation\Response", $response);
//        $this->assertEquals("application/json", $response->headers->get("content-type"));
//        $this->assertEquals(200, $response->getStatusCode());
//        $this->assertEquals('["avalue"]', $response->getContent());
    }
    
    private function getTokenEndpoint()
    {
        return new TokenEndpoint($this->securityContext, $this->em, $this->idTokenHelper);
    }
            
    
    private function prepareValue($findOneByAccessToken = false, $getRepository = false, $persist = false)
    {
        $client = new Client();
        $client->setRedirectUris(array("aRedirectURI"));
        
        $tokenInterface = $this->getMock("Symfony\Component\Security\Core\Authentication\Token\TokenInterface");
        $tokenInterface->expects($this->once())
                ->method("getUser")
                ->will($this->returnValue($client));
        
        $this->securityContext = $this->getMock("Symfony\Component\Security\Core\SecurityContextInterface");
       
        
        $this->securityContext->expects($this->exactly(2))
                ->method('getToken')
                ->will($this->returnValue($tokenInterface));
        
        
        $this->token = new Token();
        $this->token->setAccessToken("anAccessToken")
                ->setRefreshToken("aRefreshToken")
        ;

        $this->repo = $this->getMockBuilder("Doctrine\ORM\EntityRepository")
                ->disableOriginalConstructor()
                ->setMethods(array("getClientTokenByCode"))
                ->getMock();

        if($findOneByAccessToken) {
            $this->repo->expects($this->once())
                    ->method("getClientTokenByCode")
                    ->with($this->equalTo($client), $this->equalTo("azerttyuiop"))
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

        $this->idTokenHelper = $this->getMockBuilder("Waldo\OpenIdConnect\ProviderBundle\Services\IdTokenHelper")
                ->disableOriginalConstructor()
                ->getMock();
    }
}
