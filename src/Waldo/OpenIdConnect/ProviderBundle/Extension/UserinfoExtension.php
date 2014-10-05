<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Extension;

use Waldo\OpenIdConnect\ModelBundle\Entity\Token;
use Waldo\OpenIdConnect\ProviderBundle\Extension\UserinfoExtensionInterface;

/**
 * Description of UserinfoExtention
 */
class UserinfoExtension
{
    private $extensions;
    
    public function __construct()
    {
        $this->extensions = array();
    }

    public function addExtension(UserinfoExtensionInterface $extension)
    {
        $this->extensions[] = $extension;
    }
    
    public function run(Token $token)
    {
        $claims = array();
        
        foreach($this->extensions as $extension) {
            $claims = array_merge($claims, $extension->handle($token));
        }
        
        return $claims;
    }
    
}   
