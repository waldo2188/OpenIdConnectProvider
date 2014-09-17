<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\Constraints;

use Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication;
use Waldo\OpenIdConnect\ProviderBundle\Entity\Client;
use Waldo\OpenIdConnect\ProviderBundle\Exception\AuthenticationRequestException;
use Waldo\OpenIdConnect\ProviderBundle\Constraints\AuthenticationRequestValidator;

/**
 * AuthenticationRequestValidatorTest
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AuthenticationRequestValidatorTest extends \PHPUnit_Framework_TestCase
{
    
    private $em;

    protected function tearDown()
    {
        parent::tearDown();
        $this->em = null;
    }
    
    public function testShouldValidate()
    {
        $auth = $this->getAuthenticationRequestValidator();
        
        $authentication = new Authentication();
        
        $authentication
                ->setScope("openid")
                ->setResponseType("code")
                ->setClientId("clientId")
                ->setRedirectUri("redirectURI")
                ;
        
        $client = new Client();
        
        $client->setRedirectUris(array("redirectURI"));
        
        $repo = $this->getMockBuilder("stdClass")
                ->setMethods(array("findOneByClientId"))
                ->getMock();
        
        $repo->expects($this->once())
                ->method("findOneByClientId")
                ->with($this->equalTo("clientId"))
                ->will($this->returnValue($client));
        
        $this->em->expects($this->once())
                ->method("getRepository")
                ->will($this->returnValue($repo));
        
        $auth->validate($authentication);
    }
    
    /**
     * @dataProvider dpShouldFail
     */
    public function testShouldFail($autentication, $codeError, $messageError, $callback = null)
    {
        if($callback !== null) {
            $callback($this);
        }
        
        $auth = $this->getAuthenticationRequestValidator();
                
        try {
            $auth->validate($autentication);
        } catch(AuthenticationRequestException $e) {
            
            $this->assertEquals($codeError, $e->getError());
            $this->assertEquals($messageError, $e->getMessage());
        }
    }
    
    public function dpShouldFail($x)
    {
        $authentication = new Authentication();
        
        $authentication1 = new Authentication();
        $authentication1->setScope("plop");
        
        $authentication2 = clone $authentication1;
        $authentication2->setResponseType("code");
        
        $authentication3 = clone $authentication2;
        $authentication3->setClientId("client_id");
        
        $authentication4 = clone $authentication3;
        $authentication4->setRedirectUri("client_uri");
        
        $authentication5 = clone $authentication4;
        $authentication5->setScope("openid");
        
        $callback5 = function(&$me) {
            $repo = $me->getMockBuilder("stdClass")
                    ->setMethods(array("findOneByClientId"))
                    ->getMock();

            $repo->expects($me->once())
                    ->method("findOneByClientId")
                    ->with($me->equalTo("client_id"))
                    ->will($me->returnValue(null));

            $me->mockEm()->expects($me->once())
                    ->method("getRepository")
                    ->will($me->returnValue($repo));
        };
        
        $authentication6 = clone $authentication5;
        $callback6 = function(&$me) {
            $client = new Client();
            $client->setRedirectUris(array("redirectURI"));
            
            $repo = $me->getMockBuilder("stdClass")
                    ->setMethods(array("findOneByClientId"))
                    ->getMock();

            $repo->expects($me->once())
                    ->method("findOneByClientId")
                    ->with($me->equalTo("client_id"))
                    ->will($me->returnValue($client));

            $me->mockEm()->expects($me->once())
                    ->method("getRepository")
                    ->will($me->returnValue($repo));
        };
        
        $authentication7 = clone $authentication6;
        $authentication7->setRedirectUri("redirectURI")
                ->setResponseType(null);
        
        $authentication8 = clone $authentication7;
        $authentication8->setResponseType("id_token");

        return array(
            array($authentication, "invalid_request", "scope is missing"),
            array($authentication1, "invalid_request", "response_type is missing"),
            array($authentication2, "invalid_request", "client_id is missing"),
            array($authentication3, "invalid_request", "redirect_uri is missing"),
            array($authentication4, "invalid_scope", "openid scope is missing"),
            array($authentication5, "unauthorized_client", "client_id not found", $callback5),
            array($authentication6, "invalid_request", "no matching request_uri", $callback6),
            array($authentication7, "invalid_response_type", "Unknown response_type ", $callback6),
            array($authentication8, "invalid_request", "nonce is missing", $callback6),
        );
        
    }

    private function getAuthenticationRequestValidator()
    {
        return new AuthenticationRequestValidator($this->mockEm());
    }

    private function mockEm()
    {
        return $this->em = ($this->em === null) 
                ? $this->getMockBuilder("Doctrine\ORM\EntityManager")->disableOriginalConstructor()->getMock() 
                : $this->em;
    }
    
}
