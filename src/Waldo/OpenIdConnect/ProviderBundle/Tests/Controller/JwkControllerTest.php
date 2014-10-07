<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\Controller;

use Waldo\OpenIdConnect\ProviderBundle\Tests\WebTestCase;

/**
 * Description of JwkControllerTest
 *
 * @group JwkController
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class JwkControllerTest extends WebTestCase
{

    public function testShouldFailLogin()
    {
        $this->client->request('GET', '/jwk/oicp.jwk');

        $this->assertEquals('no-store, private', $this->client->getResponse()->headers->get('cache-control'));
        $this->assertEquals('no-cache', $this->client->getResponse()->headers->get('pragma'));
        $this->assertEquals('application/jwk', $this->client->getResponse()->headers->get('content-type'));
     
    }
    
}
