<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\Services;

use Waldo\OpenIdConnect\ProviderBundle\Services\IdTokenHelper;
use Waldo\OpenIdConnect\ModelBundle\Entity\Account;
use Waldo\OpenIdConnect\ModelBundle\Entity\Token;
use Waldo\OpenIdConnect\ModelBundle\Entity\Client;

/**
 * IdTokenHelperTest
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class IdTokenHelperTest extends \PHPUnit_Framework_TestCase
{
   
    public function testShouldMakeIdToken()
    {
        $jwkProvider = $this->getMockBuilder("Waldo\OpenIdConnect\ProviderBundle\Provider\JWKProvider")
                ->disableOriginalConstructor()
                ->getMock();
        
        $jwkProvider->expects($this->never())
                ->method("getPrivateKey");
        
        $idTokenHelper = new IdTokenHelper(array("issuer" => "anIssuer"), $jwkProvider);
        
        $token = $this->getToken();
        $token->getClient()->setIdTokenEncryptedResponseAlg(null);
                
        $idToken = $idTokenHelper->makeIdToken($token);
        
        
        $idTokenDecode = json_decode($idToken);

        $iat = new \DateTime();
        $iat->modify("-3 seconds");
        
        $this->assertEquals($idTokenDecode->iss, "anIssuer");
        $this->assertEquals($idTokenDecode->sub, '7a1e9db5a4629cf6867eb58f50ddfc5df79d1992672d028bed2053c02e5cc337');
        $this->assertEquals($idTokenDecode->aud, "a_client_id");
        $this->assertEquals($idTokenDecode->auth_time, (new \DateTime("2014-02-14"))->getTimestamp());
        $this->assertGreaterThan((new \DateTime())->getTimestamp(), $idTokenDecode->exp);
        $this->assertGreaterThanOrEqual($iat->getTimestamp(), $idTokenDecode->iat);
    }
    
    public function testShouldMakeIdTokenAndSign()
    {
        $jwkProvider = $this->getMockBuilder("Waldo\OpenIdConnect\ProviderBundle\Provider\JWKProvider")
                ->disableOriginalConstructor()
                ->getMock();
        
        $jwkProvider->expects($this->once())
                ->method("getPrivateKey")
                ->will($this->returnValue(file_get_contents( __DIR__ . "/../mocks/private_key") ));
        
        $idTokenHelper = new IdTokenHelper(array("issuer" => "anIssuer"), $jwkProvider);
        
        $token = $this->getToken();
                
        $userinfo = $idTokenHelper->makeIdToken($token);
        
        $jwt = \JOSE_JWT::decode($userinfo);

        $this->assertInstanceOf("\JOSE_JWT", $jwt);
      
        $jwkSetJsonObject = json_decode(file_get_contents( __DIR__ . "/../mocks/oic-p.jwk"));

        $jwkSet = new \JOSE_JWKSet();
        $jwkSet->setJwksFromJsonObject($jwkSetJsonObject);

        $jws = new \JOSE_JWS($jwt);
        try {

            $jwk = $jwkSet->filterJwk('use', \JOSE_JWK::JWK_USE_SIG);
            
            $jws->verify($jwk);

        } catch (\Exception $e) {

            $this->assertTrue(false, "fail at asserting that IdToken is properly signed");
        }
        
        $iat = new \DateTime();
        $iat->modify("-3 seconds");
        
        $this->assertEquals($jws->claims['iss'], "anIssuer");
        $this->assertEquals($jws->claims['sub'], '7a1e9db5a4629cf6867eb58f50ddfc5df79d1992672d028bed2053c02e5cc337');
        $this->assertEquals($jws->claims['aud'], "a_client_id");
        $this->assertEquals($jws->claims['auth_time'], (new \DateTime("2014-02-14"))->getTimestamp());
        $this->assertGreaterThan((new \DateTime())->getTimestamp(), $jws->claims['exp']);
        $this->assertGreaterThanOrEqual($iat->getTimestamp(), $jws->claims['iat']);
    }
   
    
    
    
    private function getToken()
    {
        $account = new Account();
        $account->setUsername("account username")
                ->setFamilyName("account Family name")
                ->setBirthdate(new \DateTime("1961-02-14"))
                ->setLastLoginAt(new \DateTime("2014-02-14"))
                ;
        
        $client = new Client();
        $client->setIdTokenEncryptedResponseAlg('RS256')
               ->setClientId("a_client_id");
                ;
        
        $token = new Token();
        $token->setAccount($account)
                ->setClient($client)
                ->setScope(array('openid', 'profile'));
        
        return $token;
    }
       
}
