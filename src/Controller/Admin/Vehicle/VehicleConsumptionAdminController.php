<?php

namespace App\Controller\Admin\Vehicle;

use App\Controller\Admin\BaseAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
        //formtype
        return $this->renderWithExtraParams(
            'admin/vehicle-consumption/uploadCsv.html.twig'
        );
    }

    /**
     * @param int|null $id
     *
     * @return Response
     */
    public function uploadCsvAction(Request $request, $id = null)
    {
        //TODO check user has en enoght permissions
        $records = [];
        $file = fopen($request->files->get('csvFile'), 'r');
        while (($line = fgetcsv($file)) !== false) {
            $records[] = $line;
        }
        fclose($file);
        foreach ($records as $record) {
        }

        return new RedirectResponse($this->generateUrl('admin_app_vehicle_vehicleconsumption_list'));
    }
}
