<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\Controller;

use Waldo\OpenIdConnect\ProviderBundle\Tests\WebTestCase;

/**
 * Description of TokenEndpointControllerTest
 *
 * @group TokenEndpointController
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class TokenEndpointControllerTest extends WebTestCase
{

    public function testShouldRequiredLogin()
    {
        $this->client->request('GET', '/token');

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        
    }
    
    public function testShouldLogin()
    {
        $this->client->request('GET', '/token', array(), array(), array(
            'PHP_AUTH_USER' => 'my_client_id',
            'PHP_AUTH_PW'   => 'my_client_secret',
        ));

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('{"error":"invalid_request","error_description":"no such redirect_uri"}',
                $this->client->getResponse()->getContent());       
    }
    
//    public function testShouldLoginAndGetToken()
//    {
//        $postParameters = array(
//            'grant_type' => 'authorization_code',
//            'code' => $code,
//            'redirect_uri' => 'http://localhost/OIC-RP/web/app_dev.php/login_check'
//        );
//        
//        $headers = array(
//            'User-Agent: WaldoOICRelyingPartyhBundle',
//            'Content-Type: application/x-www-form-urlencoded',
//            'Content-Length: ' . strlen($postParametersQuery)
//        );
//        
//        $this->client->request('GET', '/token', array(), array(), array(
//            'PHP_AUTH_USER' => 'my_client_id',
//            'PHP_AUTH_PW'   => 'my_client_secret',
//        ));
//        
//        $this->client->s
//
//        
//        
//        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
//        $this->assertEquals('{"error":"invalid_request","error_description":"no such redirect_uri"}',
//                $this->client->getResponse()->getContent());       
//    }
    
}
