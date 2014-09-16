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
    public function testShouldFail($autentication, $codeError, $messageError)
    {
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
        
        return array(
            array($authentication, "invalid_request", "scope is missing"),
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
