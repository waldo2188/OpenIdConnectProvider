<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\Services;

use Waldo\OpenIdConnect\ProviderBundle\Services\AuthorizationEndpoint;
use Symfony\Component\HttpFoundation\Request;

/**
 * AuthorizationEndpointTest
 *
 * @group AuthorizationEndpoint
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AuthorizationEndpointTest extends \PHPUnit_Framework_TestCase
{

    private $authenticationRequestValidator;
    private $authenticationCodeFlow;
    
    protected function tearDown()
    {
        parent::tearDown();
        $this->authenticationCodeFlow = null;
        $this->authenticationRequestValidator = null;
    }

    
    public function testHandleRequestShouldReturnRespons()
    {
        $authEndpoint = $this->getAuthorizationEndpoint();
        
        $this->authenticationRequestValidator
                ->expects($this->once())
                ->method("validate")
                /* @var $o Waldo\OpenIdConnect\ModelBundle\Entity\Request\Authentication; */
                ->with($this->callback(function($o) {
                    $test = true;
                    $test &= $o instanceof \Waldo\OpenIdConnect\ModelBundle\Entity\Request\Authentication;
                    $test &= count($o->getScope()) == 3;
                    $test &= in_array('openid', $o->getScope());
                    $test &= in_array('scope1', $o->getScope());
                    $test &= in_array('scope2', $o->getScope());
                    $test &= $o->getClientId() == "one_clientId";
                    $test &= $o->getRedirectUri() == "one_redirectUri";
                    $test &= $o->getState() == "one_state";
                    $test &= $o->getResponseMode() == "one_responseMode";
                    $test &= $o->getNonce() == "one_nonce";
                    $test &= $o->getPrompt() == "one_prompt";
                    $test &= $o->getMaxAge() == "36000";
                    $test &= $o->getUiLocales() == "one_uiLocales";
                    $test &= $o->getIdTokenHint() == "one_idTokenHint";
                    $test &= $o->getLoginHint() == "one_loginHint";
                    
                    
                    return $test;
                }))
                ->will($this->returnValue(true));
        
        $this->authenticationCodeFlow
                ->expects($this->once())
                ->method("handle")
                ->with($this->callback(function($o){
                    return $o instanceof \Waldo\OpenIdConnect\ModelBundle\Entity\Request\Authentication;
                }))
                ->will($this->returnValue("good"));
                
        $request = new Request();
        $request->query->add(array(
            "scope" => "openid scope1 scope2",
            "response_type" => "code",
            'client_id' => 'one_clientId',
            'redirect_uri' => 'one_redirectUri',
            'state' => 'one_state',
            'response_mode' => 'one_responseMode',
            'nonce' => 'one_nonce',
            'display' => 'one_display',
            'prompt' => 'one_prompt',
            'max_age' => '36000',
            'ui_locales' => 'one_uiLocales',
            'id_token_hint' => 'one_idTokenHint',
            'login_hint' => 'one_loginHint'
        ));

        $result = $authEndpoint->handleRequest($request);
        
        $this->assertEquals("good", $result);
    }
    
    /**
     * @expectedException Waldo\OpenIdConnect\ProviderBundle\Exception\AuthenticationRequestException
     * @expectedExceptionMessage authentication flow is not yet implemented
     */
    public function testShouldFailForUnknowFlow()
    {
        $authEndpoint = $this->getAuthorizationEndpoint();
        $request = new Request();
        $request->query->add(array(
            "response_type" => "id_token"
        ));

        $authEndpoint->handleRequest($request);
    }
    
    /**
     * @expectedException Waldo\OpenIdConnect\ProviderBundle\Exception\AuthenticationRequestException
     * @expectedExceptionMessage authentication flow is not yet implemented
     */
    public function testShouldFailForUnknowFlow2()
    {
        $authEndpoint = $this->getAuthorizationEndpoint();
        $request = new Request();
        $request->query->add(array(
            "response_type" => "code id_token"
        ));

        $authEndpoint->handleRequest($request);
    }
    
    /**
     * @expectedException Waldo\OpenIdConnect\ProviderBundle\Exception\AuthenticationRequestException
     * @expectedExceptionMessage unknow authentication flow
     */
    public function testShouldFailForUnknowFlow3()
    {
        $authEndpoint = $this->getAuthorizationEndpoint();
        $request = new Request();
        $request->query->add(array(
            "response_type" => "dumb"
        ));

        $authEndpoint->handleRequest($request);
    }
    
    public function testShouldFailForUnknowFlowAndReturnRedirection()
    {
        $authEndpoint = $this->getAuthorizationEndpoint();
        $request = new Request();
        $request->query->add(array(
            "response_type" => "dumb",
            'redirect_uri' => 'one_redirectUri',
            'state' => 'one_state'
        ));

        $result = $authEndpoint->handleRequest($request);
        
        $this->assertInstanceOf("Symfony\Component\HttpFoundation\RedirectResponse", $result);
        
        $this->assertEquals(302, $result->getStatusCode());
        $this->assertEquals("http://localhostone_redirectUri?error=invalid_request&error_description=unknow%20authentication%20flow&state=one_state", $result->headers->get("location"));

    }


    private function getAuthorizationEndpoint()
    {
        return new AuthorizationEndpoint($this->mockAuthenticationRequestValidator(), $this->mockAuthenticationCodeFlow());
    }
    
    private function mockAuthenticationRequestValidator()
    {
        return $this->authenticationRequestValidator = ( $this->authenticationRequestValidator === null )
                ? $this->getMockBuilder("Waldo\OpenIdConnect\ProviderBundle\Constraints\AuthenticationRequestValidator")
                ->disableOriginalConstructor()->getMock()
                : $this->authenticationRequestValidator;
    }
    
    private function mockAuthenticationCodeFlow()
    {
        return $this->authenticationCodeFlow = ($this->authenticationCodeFlow === null) 
                ? $this->getMockBuilder("Waldo\OpenIdConnect\ProviderBundle\AuthenticationFlows\AuthenticationCodeFlow")
                ->disableOriginalConstructor()->getMock()
                : $this->authenticationCodeFlow;
    }
    
}
