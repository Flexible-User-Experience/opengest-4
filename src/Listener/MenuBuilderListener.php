<?php

namespace App\Listener;

use Sonata\AdminBundle\Event\ConfigureMenuEvent;

final class MenuBuilderListener
{
    public function addMenuItems(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $salesMenu = $menu->getChild('Ventas');

        $child = $salesMenu->addChild('calendar', [
            'label' => 'Calendario',
            'route' => 'admin_app_sale_salerequest_calendar',
        ])->setExtras([
            'icon' => 'fa fa-calendar',
        ]);
    }
}
