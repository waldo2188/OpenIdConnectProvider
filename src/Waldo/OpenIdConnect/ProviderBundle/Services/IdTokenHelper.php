<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Services;

use Waldo\OpenIdConnect\ModelBundle\Entity\Token;
use Waldo\OpenIdConnect\ModelBundle\Entity\IdToken;
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
                ->setNonce($token->getNonce())
                ;
        
        if($token->getAccount()->getLastLoginAt() !== null) {
            $idToken->setAuth_time($token->getAccount()->getLastLoginAt()->getTimestamp());
        }
                
        //TODO add function for sign and encrypt idToken
        
        if($token->getClient()->getIdTokenSignedResponseAlg() !== null) {
            return $this->sign(
                    $token,
                    $token->getClient()->getIdTokenSignedResponseAlg(),
                    $idToken->__toArray()
                    );
        }
        
        $jwt = new \JOSE_JWT($idToken->__toArray());
        
        return $jwt->toString();
    }
        
    public function genererateSub($username)
    {
        return hash("sha256", $this->options['issuer'] . "#" . $username);
    }
       
}
