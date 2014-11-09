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
        $menu->addChild('Account', array('label' => "label.Account"))
                ->setLabelAttribute('icon', 'fa fa-fw fa-users');
        $menu['Account']->addChild('Enduser', array('route' => 'oicp_admin_account_index', 'label' => "label.Enduser"));
        $menu['Account']['Enduser']->addChild('Profile', array('route' => 'oicp_admin_account_profile', 'label' => "label.Profile"))
                        ->setDisplay(false);
        
            $menu->addChild('Client', array('label' => "label.Client"))
                ->setLabelAttribute('icon', 'fa fa-fw fa-desktop');
        $menu['Client']->addChild('Manage', array('route' => 'oicp_admin_client_index', 'label' => "label.Manage"));
        $menu['Client']['Manage']->addChild('Client record', array('route' => 'oicp_admin_client_record', 'label' => "label.Client_record"))
                ->setDisplay(false);
        $menu['Client']->addChild('New', array('route' => 'oicp_admin_client_new', 'label' => "label.New"));
        $menu['Client']['Manage']->addChild('Edit', array('route' => 'oicp_admin_client_edit', 'label' => "label.Edit"))
                ->setDisplay(false);
        return $menu;
    }

}
