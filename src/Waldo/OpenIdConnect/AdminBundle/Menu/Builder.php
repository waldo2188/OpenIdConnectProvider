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
                ->setLabelAttribute('icon', 'fa fa-fw fa-group');
        $menu['Account']->addChild('Enduser', array('route' => 'oicp_admin_account_index'));
        
        return $menu;
    }

}
