<?php

namespace Waldo\OpenIdConnect\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Builder
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class Builder extends ContainerAware
{

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->addChild('Account')
                ->setLabelAttribute('icon', 'fa fa-fw fa-users');
        $menu['Account']->addChild('Enduser', array('route' => 'oicp_admin_account_index'));
        $menu['Account']['Enduser']->addChild('Profile', array('route' => 'oicp_admin_account_profile'))
                        ->setDisplay(false);
        
        $menu->addChild('Client')
                ->setLabelAttribute('icon', 'fa fa-fw fa-desktop');
        $menu['Client']->addChild('Manage', array('route' => 'oicp_admin_client_index'));
        $menu['Client']['Manage']->addChild('Client record', array('route' => 'oicp_admin_client_record'))
                ->setDisplay(false);
        $menu['Client']['Manage']->addChild('New', array('route' => 'oicp_admin_client_new'));
        $menu['Client']['Manage']->addChild('Edit', array('route' => 'oicp_admin_client_edit'))
                ->setDisplay(false);
        return $menu;
    }

}
