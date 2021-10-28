<?php

namespace App\Controller\Admin\Vehicle;

use App\Controller\Admin\BaseAdminController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VehicleDigitalTachographAdminController.
 */
class VehicleConsumptionAdminController extends BaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return Response
     */
    public function uploadCsvViewAction($id = null)
    {
        return $this->render(
            'admin/vehicle-consumption/uploadCsv.html.twig'
        );
    }
}
