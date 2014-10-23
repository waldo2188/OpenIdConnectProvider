<?php

namespace Waldo\OpenIdConnect\EnduserBundle\Menu;

/**
 * MenuExtender
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class MenuExtender
{
    /**
     * @var string
     */
    private $providerName;

    /**
     * @var array 
     */
    private $routesName ;
        
    public function __construct($providerName, $routesName)
    {
        $this->providerName = $providerName;
        $this->routesName = $routesName;
    }

    public function getProviderName()
    {
        return $this->providerName;
    }

    public function getRoutesName()
    {
        return $this->routesName;
    }
    
}
