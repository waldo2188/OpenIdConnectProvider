<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Services;

use Waldo\OpenIdConnect\ModelBundle\Entity\Token;
use Waldo\OpenIdConnect\ProviderBundle\Provider\JWKProvider;

/**
 * AbstractTokenHelper
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AbstractTokenHelper
{
    
    protected $options;
    
    /**
     * @var JWKProvider
     */
    protected $jWKProvider;
    
    
    public function __construct($option, JWKProvider $jWKProvider)
    {
        $this->options = $option;
        $this->jWKProvider = $jWKProvider;
    }
    
    public function genererateSub($username)
    {
        return hash("sha256", $this->options['issuer'] . "#" . $username);
    }

    /**
     * 
     * @param Token $token
     * @param type $alg
     * @param type $content
     * @return string
     */
    protected function sign(Token $token, $alg, $content)
    {   
        $key = null;
                
        if(substr($alg, 0, 2) == 'HS') {
            $key = $token->getClient()->getClientSecret();
        } elseif(substr($alg, 0, 2) == 'RS') {
            $key = $this->jWKProvider->getPrivateKey();
        }
        
        $jwt = new \JOSE_JWT($content);
        $jws = $jwt->sign($key, $alg);
        
        return $jws->toString();
    }
}
