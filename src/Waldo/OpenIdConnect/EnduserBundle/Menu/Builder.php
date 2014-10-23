<?php

namespace Waldo\OpenIdConnect\EnduserBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Builder
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class Builder
{
    /**
     * @var FactoryInterface 
     */
    private $factory;

    /**
     * @var SecurityContextInterface 
     */
    private $securityContext;
    
    /**
     * @var array
     */
    private $provider = array('self' => array(
        'index' => 'oicp_account_index',
        'edit_profile' => 'oicp_account_edit_profile',
        'change_password' => 'oicp_account_change_password',
        'application_access_list' => 'oicp_account_application_access_list',
    ));
    
    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, SecurityContextInterface $securityContext)
    {
        $this->factory = $factory;
        $this->securityContext = $securityContext;
    }
    
    public function mainMenu(Request $request)
    {
        $providerName = $this->securityContext->getToken()->getUser()->getProviderName();

        $menu = $this->factory->createItem('root');
        
        $menu->addChild('label.Profile', array('route' => $this->getRoute($providerName, 'index')));
        $menu->addChild('label.edit_your_profile', array('route' => $this->getRoute($providerName, 'edit_profile')));
        $menu->addChild('label.change_password', array('route' => $this->getRoute($providerName, 'change_password')));
        $menu->addChild('label.applications_access_list', array('route' => $this->getRoute($providerName, 'application_access_list')));
        
        return $menu;
    }
    
    public function addProvider(MenuExtender $provider)
    {
        $this->provider[$provider->getProviderName()] = $provider->getRoutesName();
    }
    
    private function getRoute($providerName, $route)
    {
        if(array_key_exists($providerName, $this->provider)) {
            if(array_key_exists($route, $this->provider[$providerName])) {
                return $this->provider[$providerName][$route];
            }
        }
        
        return $this->provider['self'][$route];
    }

}
