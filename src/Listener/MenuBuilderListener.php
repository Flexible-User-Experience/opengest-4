<?php

namespace App\Listener;

use Knp\Menu\Util\MenuManipulator;
use Sonata\AdminBundle\Event\ConfigureMenuEvent;

final class MenuBuilderListener
{
    public function addMenuItems(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $child = $menu->getChild('Ventas');
        $child->addChild('Facturar albaranes', [
                'label' => 'Facturar albaranes',
                'route' => 'admin_app_sale_saledeliverynote_generateInvoicesScreen',
            ])->setExtras([
                'translation_domain' => 'admin',
                'roles' => ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'],
            ]);
        $manipulator = new MenuManipulator();
        $manipulator->moveChildToPosition($child, $child->getChild('Facturar albaranes'), 3);
    }
}
