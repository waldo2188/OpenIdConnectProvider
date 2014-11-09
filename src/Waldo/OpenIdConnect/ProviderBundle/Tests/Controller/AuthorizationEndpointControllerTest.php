<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\Controller;

use Waldo\OpenIdConnect\ProviderBundle\Tests\WebTestCase;

/**
 * Description of AuthorizationEndpointControllerTest
 *
 * @group AuthorizationEndpointController
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AuthorizationEndpointControllerTest extends WebTestCase
{

    public function setUp()
    {
        parent::setUp();
        restoreDatabase();
    }
    
    public function testShouldRedirectToTheOriginalApp()
    {
        $url = "/authorize/?client_id=my_client_id&display=page&max_age=50000&nonce=aNonceValue&redirect_uri=http%3A%2F%2Flocalhost%2FOIC-RP%2Fweb%2Fapp_dev.php%2Flogin_check&response_type=code&scope=openid%20profile%20email%20phone&state=aStateValue";
        
        $this->client->request('GET', $url);
        $crawler = $this->client->followRedirect();
        
        $this->assertEquals(1, $crawler->filter('h2:contains("My application name required you to be authenticated")')->count());

        $form = $crawler->selectButton('signin')->form();

        $form['_username'] = 'user@exemple.com';
        $form['_password'] = 'user@exemple.com';

        $this->client->submit($form);
        
        $crawler = $this->client->followRedirect();

        $this->assertEquals(1, $crawler->filter('h3:contains("Claimed informations")')->count());
        
        $form = $crawler->selectButton('scope_approval[accept]')->form();
        
        $crawler = $this->client->submit($form);
                
        $this->assertEquals(1, $crawler->filter('a:contains("http://localhost/OIC-RP/web/app_dev.php/login_check?")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("&nonce=aNonceValue&state=aStateValue")')->count());
    }

    public function testShouldRedirectToTheOriginalAppAfterCancelingScopeAcceptance()
    {
        $url = "/authorize/?client_id=my_client_id&display=page&max_age=50000&nonce=aNonceValue&redirect_uri=http%3A%2F%2Flocalhost%2FOIC-RP%2Fweb%2Fapp_dev.php%2Flogin_check&response_type=code&scope=openid%20profile%20email%20phone&state=aStateValue";
        
        $this->client->request('GET', $url);
        $crawler = $this->client->followRedirect();
        
        $this->assertEquals(1, $crawler->filter('h2:contains("My application name required you to be authenticated")')->count());

        $form = $crawler->selectButton('signin')->form();

        $form['_username'] = 'user@exemple.com';
        $form['_password'] = 'user@exemple.com';

        $this->client->submit($form);
        
        $crawler = $this->client->followRedirect();
        
        $this->assertEquals(1, $crawler->filter('h3:contains("Claimed informations")')->count());
        
        $form = $crawler->selectButton('scope_approval[cancel]')->form();
        
        $crawler = $this->client->submit($form);

        $this->assertEquals(1, $crawler->filter('a:contains("http://localhost/OIC-RP/web/app_dev.php/login_check?error=access_denied&error_description=scope%20denied%20by%20enduser&nonce=aNonceValue&state=aStateValue")')->count());
    }
    
}
