<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Services;

use Waldo\OpenIdConnect\ProviderBundle\Entity\Token;
use Waldo\OpenIdConnect\ProviderBundle\Entity\IdToken;
use Waldo\OpenIdConnect\ProviderBundle\Services\AbstractTokenHelper;
/**
 * IdTokenHelper
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class IdTokenHelper extends AbstractTokenHelper
{

    public function makeIdToken(Token $token)
    {
        $idToken = new IdToken();
        
        $expire = new \DateTime('now');
        $expire->modify("+300 seconds");
        
        $iat = new \DateTime('now');
        
        $idToken->setIss($this->options['issuer'])
                ->setSub($this->genererateSub($token->getAccount()->getUsername()))
                ->setAud($token->getClient()->getClientId())
                ->setExp($expire->getTimestamp())
                ->setIat($iat->getTimestamp())
                ->setAuth_time($token->getAccount()->getLastLoginAt()->getTimestamp())
                ->setNonce($token->getNonce())
                ;
        
        //TODO add function for sign and encrypt idToken
        
        $idTokenEncode = json_encode($idToken->__toArray());
        
        return $idTokenEncode;
    }
    
    public function genererateSub($username)
    {
        return hash("sha256", $this->options['issuer'] . "#" . $username);
    }
   
}
