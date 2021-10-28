<?php

namespace App\Controller\Admin\Vehicle;

use App\Controller\Admin\BaseAdminController;
use App\Form\Type\UploadCsvFormType;
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
    public function uploadCsvAction(Request $request, $id = null)
    {
        $form = $this->createForm(UploadCsvFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($request);
            // Persist new contact message into DB
            //        $records = [];
//        $file = fopen($request->files->get('csvFile'), 'r');
//        while (($line = fgetcsv($file)) !== false) {
//            $records[] = $line;
//        }
//        fclose($file);
//        foreach ($records as $record) {
//        }
            // Set frontend flash message
            $this->addFlash(
                'notice',
                'Fichero importado correctamente'
            );
        }
        //formtype
        return $this->renderWithExtraParams(
            'admin/vehicle-consumption/uploadCsv.html.twig',
            ['uploadCsvForm' => $form->createView()]
        );
//
//        return new RedirectResponse($this->generateUrl('admin_app_vehicle_vehicleconsumption_list'));
    }
}
