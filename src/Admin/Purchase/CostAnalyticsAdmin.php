<?php

namespace App\Admin\Purchase;

use App\Admin\AbstractBaseAdmin;
use Sonata\AdminBundle\Route\RouteCollectionInterface;

/**
 * Class CostAnalyticsAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Jordi Sort <jordi.sort@mirmit.com>
 */
class CostAnalyticsAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Análisis de costes y márgenes';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'analisis-de-costes';

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
            ->remove('create')
            ->remove('list')
            ->remove('edit')
            ->remove('delete')
            ->remove('export')
            ->add('imputableCosts', 'costes-imputables/{?year}/{?vehicle}/{?operator}/{?costCenter}/{?download}')
            ->add('marginAnalysis', 'analisis-margenes/{?year}')
        ;
    }
}
