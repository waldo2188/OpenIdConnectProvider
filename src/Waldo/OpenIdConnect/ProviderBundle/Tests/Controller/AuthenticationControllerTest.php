<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\Controller;

use Waldo\OpenIdConnect\ProviderBundle\Tests\WebTestCase;

/**
 * Description of AuthenticationControllerTest
 *
 * @group AuthenticationController
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AuthenticationControllerTest extends WebTestCase
{

    public function testShouldFailLogin()
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertEquals(1, $crawler->filter('h2:contains("Sign in with your OIC account")')->count());

        $form = $crawler->selectButton('signin')->form();

        $form['_username'] = 'foo';
        $form['_password'] = 'bar';

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertEquals(1, $crawler->filter('div[class="alert alert-danger"]:contains("Bad credentials")')->count());
    }
    
    public function testShouldSuccessLogin()
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertEquals(1, $crawler->filter('h2:contains("Sign in with your OIC account")')->count());

        $form = $crawler->selectButton('signin')->form();

        $form['_username'] = 'user';
        $form['_password'] = 'user';

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertEquals(1, $crawler->filter('ul[class="nav navbar-nav"] li a:contains("Dear user")')->count());
    }
    
    public function testShouldSuccessLoginAndReuseToken()
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertEquals(1, $crawler->filter('h2:contains("Sign in with your OIC account")')->count());

        $form = $crawler->selectButton('signin')->form();

        $form['_username'] = 'user';
        $form['_password'] = 'user';

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $crawler = $this->client->request('GET', '/');
        
        $this->assertEquals(1, $crawler->filter('span[class="user-name"]:contains("user")')->count());
        $this->assertEquals(1, $crawler->filter('span[class="user-email"]:contains("user@exemple.com")')->count());
        
        
        $form = $crawler->selectButton('signin')->form();

        $form['_password'] = 'plop';

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        
        $this->assertEquals(1, $crawler->filter('div[class="alert alert-danger"]:contains("Bad credentials")')->count());
        $this->assertEquals(1, $crawler->filter('span[class="user-name"]:contains("user")')->count());
        $this->assertEquals(1, $crawler->filter('span[class="user-email"]:contains("user@exemple.com")')->count());
    }
    
    public function testShouldFailedAtLoginCauseUserIsDisabled()
    {
        $user = $this->entityManager->getRepository("WaldoOpenIdConnectModelBundle:Account")->findOneByUsername('user');
        $user->setEnabled(false);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/');

        $this->assertEquals(1, $crawler->filter('h2:contains("Sign in with your OIC account")')->count());

        $form = $crawler->selectButton('signin')->form();

        $form['_username'] = 'user';
        $form['_password'] = 'user';

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertEquals(1, $crawler->filter('div[class="alert alert-danger"]:contains("User account is disabled.")')->count());
        
        restoreDatabase();
    }
}
