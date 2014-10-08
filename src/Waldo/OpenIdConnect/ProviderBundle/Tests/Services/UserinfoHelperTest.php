<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\Services;

use Waldo\OpenIdConnect\ProviderBundle\Services\UserinfoHelper;
use Waldo\OpenIdConnect\ModelBundle\Entity\Token;
use Waldo\OpenIdConnect\ModelBundle\Entity\Account;
use Waldo\OpenIdConnect\ModelBundle\Entity\Client;

/**
 * UserinfoHelperTest
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class UserinfoHelperTest extends \PHPUnit_Framework_TestCase
{
   
    public function testShouldMakeUserinfoAndSign()
    {
        $jwkProvider = $this->getMockBuilder("Waldo\OpenIdConnect\ProviderBundle\Provider\JWKProvider")
                ->disableOriginalConstructor()
                ->getMock();
        
        $jwkProvider->expects($this->once())
                ->method("getPrivateKey")
                ->will($this->returnValue(file_get_contents( __DIR__ . "/../mocks/private_key") ));
        
        $userinfoHelper = new UserinfoHelper(array("issuer" => "anIssuer"), $jwkProvider);
        
        
                
        $userinfoSigned = $userinfoHelper->makeUserinfo($this->getToken());

        $expected = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiI3YTFlOWRiNWE0NjI5Y2Y2ODY3ZWI1OGY1MGRkZmM1ZGY3OWQxOTkyNjcyZDAyOGJlZDIwNTNjMDJlNWNjMzM3IiwiZmFtaWx5X25hbWUiOiJhY2NvdW50IEZhbWlseSBuYW1lIiwiYmlydGhkYXRlIjotMjgwMTk4ODAwfQ.aQDqqso_sWiagoW_YV5kdtG2jLVuztt9B56r0lBK2IatYO81rh7EUMVQmEvdNn1l-fGYLY3akD8XVx6VDPBuH-dpHfVgpbn0N5RYEY2z2QGnkTr7sLkvWdaRE_66UCV0Z99u-03-PJiyp0VDEIdSR_YZlSw7-ccz7zivnOgWXvdpLHvTmCMsOpCNDeCe-u9ZvX8C3OJR5sG44T2-8elUqBTUNkZ6g6ggogU1fem5OAAxQbZ9VnMATv6kkDF2vW3gk0Pk3_Lq3uu_kF3qre9VpN2rDOe7PiAU3PkZ8QBxIKvehF3WLKeve-5auk9ZhqEEvmjnknME9okkpMG-ZUecWA";

        $this->assertEquals($expected, $userinfoSigned);
            
    }
    
    public function testShouldMakeUserinfo()
    {
        $jwkProvider = $this->getMockBuilder("Waldo\OpenIdConnect\ProviderBundle\Provider\JWKProvider")
                ->disableOriginalConstructor()
                ->getMock();
        
        $jwkProvider->expects($this->never())
                ->method("getPrivateKey");
        
        $userinfoExention = $this->getMock("Waldo\OpenIdConnect\ProviderBundle\Extension\UserinfoExtension");
        $userinfoExention->expects($this->once())
                ->method('run')
                ->with($this->callback(function($v) {
                    return $v instanceof \Waldo\OpenIdConnect\ModelBundle\Entity\Token;
                    
                }))
                ->will($this->returnValue(array('roles' => array('role1', 'role2'))));
        
        $userinfoHelper = new UserinfoHelper(array("issuer" => "anIssuer"), $jwkProvider);
        $userinfoHelper->setUserinfoExtension($userinfoExention);
        
        $token = $this->getToken();
        $token->getClient()->setUserinfoSignedResponseAlg(null);
                
        $userinfo = $userinfoHelper->makeUserinfo($token);
        
        $birthdate = new \DateTime();
        $birthdate->setTimestamp(-280198800);



        $expected = array(
            "sub" => "7a1e9db5a4629cf6867eb58f50ddfc5df79d1992672d028bed2053c02e5cc337",
            "family_name" => "account Family name",
            "birthdate" => $birthdate->getTimestamp(),
            'roles' => array('role1', 'role2')
        );
        $this->assertEquals($expected, $userinfo);            
    }
   
    
    private function getToken()
    {
        $birthdate = new \DateTime();
        $birthdate->setTimestamp(-280198800);
        
        $account = new Account();
        $account->setUsername("account username")
                ->setFamilyName("account Family name")
                ->setBirthdate($birthdate)
                ;
        
        $client = new Client();
        $client->setUserinfoSignedResponseAlg('RS256');
        
        $token = new Token();
        $token->setAccount($account)
                ->setClient($client)
                ->setScope(array('openid', 'profile'));
        
        return $token;
    }
}
