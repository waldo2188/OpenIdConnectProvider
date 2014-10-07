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
    
    public function testShouldLoginAndFailToGetToken()
    {
        $postParameters = array(
            'grant_type' => 'authorization_code',
            'code' => "plop",
            'redirect_uri' => 'http://localhost/OIC-RP/web/app_dev.php/login_check'
        );

        $this->client->request('POST', '/token', $postParameters, array(), array(
            'PHP_AUTH_USER' => 'my_client_id',
            'PHP_AUTH_PW'   => 'my_client_secret'
        ));

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('{"error":"invalid_authorization_code","error_description":"no such code"}',
                $this->client->getResponse()->getContent());       
    }
    
    public function testShouldLoginAndGetToken()
    {
        $postParameters = array(
            'grant_type' => 'authorization_code',
            'code' => "code_token",
            'redirect_uri' => 'http://localhost/OIC-RP/web/app_dev.php/login_check'
        );
        
        $user = $this->entityManager->getRepository("WaldoOpenIdConnectModelBundle:Account")->findOneByUsername('user');
        $client = $this->entityManager->getRepository("WaldoOpenIdConnectModelBundle:client")->findOneByClientId('my_client_id');
        
        $token = new \Waldo\OpenIdConnect\ModelBundle\Entity\Token();
        $token->setAccount($user)
                ->setClient($client)
                ->setCodeToken("code_token")
                ->setNonce("code_nonce")
                ->setRedirectUri($postParameters['redirect_uri'])
                ->setIssuedAt(new \DateTime('now'));
        
        $this->entityManager->persist($token);
        $this->entityManager->flush();

        $this->client->request('POST', '/token', $postParameters, array(), array(
            'PHP_AUTH_USER' => 'my_client_id',
            'PHP_AUTH_PW'   => 'my_client_secret'
        ));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        
        $this->assertEquals('no-store, private', $this->client->getResponse()->headers->get('cache-control'));
        $this->assertEquals('no-cache', $this->client->getResponse()->headers->get('pragma'));
        $this->assertEquals('application/json', $this->client->getResponse()->headers->get('content-type'));
        
        $this->assertContains('{"access_token":"', $this->client->getResponse()->getContent());
        $this->assertContains(',\u0022nonce\u0022:\u0022code_nonce\u0022', $this->client->getResponse()->getContent());
    }
    
}
