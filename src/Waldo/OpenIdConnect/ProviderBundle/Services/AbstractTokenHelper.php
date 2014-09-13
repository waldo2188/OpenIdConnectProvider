<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Services;

/**
 * AbstractTokenHelper
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AbstractTokenHelper
{
    
    protected $options;
    
    public function __construct($option)
    {
        $this->options = $option;
    }
    
    public function genererateSub($username)
    {
        return hash("sha256", $this->options['issuer'] . "#" . $username);
    }
   
}
