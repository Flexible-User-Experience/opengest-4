<?php

namespace App\Listener;

use Doctrine\ORM\EntityManager;

class BeforeRequestListener
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function onKernelRequest(): void
    {
        $filter = $this->em
            ->getFilters()
            ->enable('operator_absence_filter');
    }
}
