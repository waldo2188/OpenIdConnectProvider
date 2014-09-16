<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\Provider;
        
use Waldo\OpenIdConnect\ProviderBundle\Provider\JWKProvider;
use Symfony\Component\Filesystem\Filesystem;

/**
 * JWKProvider
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class JWKProviderTest extends \PHPUnit_Framework_TestCase
{
 
    private $cacheDir;
    private $option;
    
    protected function setUp()
    {
        parent::setUp();
        
        $this->cacheDir = sys_get_temp_dir() . "/waldo-oicp";
        $this->option = array("private_key" =>  __DIR__ . "/../mocks/private_key");
        $fs = new Filesystem;
        $fs->remove($this->cacheDir);
    }
    
    protected function tearDown()
    {
        parent::tearDown();
        
        $fs = new Filesystem;
        $fs->remove($this->cacheDir);
    }
    
    public function testShouldReturnPrivateKey()
    {
        $jwkProvider = $this->getJWKProvider();
        $pk = $jwkProvider->getPrivateKey();
        
        $expected ="-----BEGIN RSA PRIVATE KEY-----
MIIEpAIBAAKCAQEAyAv4CWejLjY5XmJZmuEIHKBJp5xzLSZ5K1fKUpUOF4C/kQ6X
4fQ/Jgx9JpwA7ZDtTlq4d0fJp9Ezte++xfCSf1G0WHxXgzFnhmnp34xWbRU/iexF
OSGR8Xj+dUl70reBHHtj+RH0fEAphkjVAsvFJgrkI461rDHhZ4sPcQVD1PW3CkER
FGX3ojlePn5RO9xKVZ4mTqvY6zQpn6SmLm1AOtypwyqOXFPvZ8pUu7vQflZ9LaAw
VvAMFbDjVdhC6o55uw0vUtBXuNpaFqYU58oqR7GUuYRGai+EW3Dh9GhH2qmr4mbM
1i4yYLumGKr+KGTDhIOsqgIz1A/EdvyM5PA+AQIDAQABAoIBAAbdakigVtg6IiPc
CiknecjJs11eIBG3lUbof1fwJ4ik8W3/6zRk1lnu6ciOZ/W/GHWs5VGqk///TN/8
dzaBt/VIj4DN247z/hJ9xfnW7gxWQ1TvV7zLKx/3P/w0Zlxn0bVxgo/EjvhIl2Q1
UaID7iMNEqEpv5BGMe8EtaXyQU6ohGJXsY7B3yaLhCl9rWLx0IP2zY+weYf43Vze
G+3sHUlicvZdOcdkrP7JHoOIXPidZJWlGgHMc99xMaSGOFc67UgdVfVQh8Eg9zhM
wv6IcPaPemVswJ7KJqHHF0ZZda75IFgTdRrYHe83trOKl7dhczoJ33/1uBW5FYvj
YlEA4yUCgYEA+OJyseNo+Okwvwiren47MuQu2Jp+z7IHKimXx5t3WlSkwKYd5Y7q
WNwE090TXSikIsI6L5Ox4drVR7PiU1rZwfxurXyXxILo2+3cEEvKkCEt0MGQoa6f
WUVrSC/qXQkcefH6vfdfFiYzdCFuVxVmNJA8pzLJfPRmJrtif1di/B8CgYEAzcQV
aX+YRHPACGMIH9yRVLU9gDAEX8u93QBpb1w0QJBO3Uvs4o6HBoAl4cb+5xnIElyi
8NaKlMkFmdfuUxwjwYd0uU+eyPn81gEtz9+QG5K3G2ZZLFq4pKmwVlS+HNqQzS2S
DmF1cE9J8LaMh+NlKZdh0tTDd06gQGWOMQX1gd8CgYEAgC1flG3iKg8uo7P7XGKW
amdKGRO4K48sUXKKUKqolUTydKUY0JkB6DGn6tT4nAnKPnx5KveNrRveq8bg0L0B
WP6AoaiBIRuqgVnG9D2UXRY5WkHWEu6z8m3mCo9iy/ddSxBsall0Bxw1c6GkCgVj
Vb7S/ekSC+Bym4/18k83E1ECgYAVH7QzCG5RWLC2K7SB0Nr6n0CZEFTioL/1GD26
uPSETExMW6cN+yPRfwZkj1FL2FG50NjJcLz5WUnB6XEGBzWEgKty+sGKUF+seKKD
XKgmrIEvxuoQq5+ZB/Kaaki6HZYP+kWsREUSKpGkrJjwul+ATZI1WrJWZTP+aG6Y
icwScQKBgQDgx4voeQr+LnT1913Q6vlolLUP3LwveQArotccIyAgJ8WdUqyCVYgm
gRlY5FHO3dbIeLC+y3gsw1GnOkd2UVA4ojQ/jvPPl/76fDncvn7aMlrXfmc0VHb0
YCMIUYXmT7g9c4z2YX7kT4MysTCO0Mtwhh+1uY9fidC98C7JAfs7Lg==
-----END RSA PRIVATE KEY-----
";
        
        $this->assertEquals($expected, $pk);
    }
    
    public function testShouldReturnNullAsPrivateKey()
    {
        $this->option = array('private_key' => null);
        
        $jwkProvider = $this->getJWKProvider();
        $pk = $jwkProvider->getPrivateKey();
        
        $this->assertNull($pk);
    }
    
    public function testShouldReturnResponse()
    {
        $jwkProvider = $this->getJWKProvider();
        $pk = $jwkProvider->getJwkResponse();
       

        $this->assertInstanceOf("Symfony\Component\HttpFoundation\Response", $pk);
        $this->assertEquals("application/jwk", $pk->headers->get("content-type"));
        
        $jwk = json_decode($pk->getContent());
        
        $this->assertEquals("yAv4CWejLjY5XmJZmuEIHKBJp5xzLSZ5K1fKUpUOF4C_kQ6X4fQ_Jgx9JpwA7ZDtTlq4d0fJp9Ezte--xfCSf1G0WHxXgzFnhmnp34xWbRU_iexFOSGR8Xj-dUl70reBHHtj-RH0fEAphkjVAsvFJgrkI461rDHhZ4sPcQVD1PW3CkERFGX3ojlePn5RO9xKVZ4mTqvY6zQpn6SmLm1AOtypwyqOXFPvZ8pUu7vQflZ9LaAwVvAMFbDjVdhC6o55uw0vUtBXuNpaFqYU58oqR7GUuYRGai-EW3Dh9GhH2qmr4mbM1i4yYLumGKr-KGTDhIOsqgIz1A_EdvyM5PA-AQ", $jwk->keys[0]->n);
        $this->assertEquals("AQAB", $jwk->keys[0]->e);        
    }
    
    public function testShouldReturn404Response()
    {
        $this->option = array('private_key' => null);
        
        $jwkProvider = $this->getJWKProvider();
        $pk = $jwkProvider->getJwkResponse();
       

        $this->assertInstanceOf("Symfony\Component\HttpFoundation\Response", $pk);
        $this->assertEquals(404, $pk->getStatusCode());
    }
    
    private function getJWKProvider()
    {
        return new JWKProvider($this->option, $this->cacheDir);
    }
    
    
}
