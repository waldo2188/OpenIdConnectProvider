<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Services;

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

    protected function sign($alg, $content)
    {      
        $jwt = new \JOSE_JWT($content);
        $jws = $jwt->sign(
                $this->jWKProvider->getPrivateKey(),
                $alg
                );
        
        return $jws->toString();
    }
}
