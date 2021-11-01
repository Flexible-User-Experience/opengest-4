<?php

namespace App\Controller\Admin\Vehicle;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleConsumption;
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
            $uploadedFile = $form->get('csvFile')->getData();
            $records = [];
            $file = fopen($uploadedFile, 'r');
            while (($line = fgetcsv($file)) !== false) {
                $records[] = $line;
            }
            fclose($file);
            $em = $this->getDoctrine()->getManager();
            $vcr = $this->getDoctrine()->getRepository(VehicleConsumption::class);
            $vr = $this->getDoctrine()->getRepository(Vehicle::class);
            foreach ($records as $record) {
                $consumptionCode = $record[13];
                $consumption = $vcr->findOneBy(['supplyCode' => $consumptionCode]);
                if (!$consumption) {
                    /** @var Vehicle $vehicle */
                    $vehicle = $vr->findOneBy(['vehicleRegistrationNumber' => $record[20]]);
                    if ($vehicle) {
                        $consumption = new VehicleConsumption();
                        $consumption->setVehicle($vehicle);
                        $consumption->setSupplyCode($consumptionCode);
                        $date = \DateTime::createFromFormat('d/m/Y', $record[14]);
                        $time = \DateTime::createFromFormat('H:i', $record[15]);
                        $consumption->setSupplyDate($date);
                        $consumption->setSupplyTime($time);
                        $amount = floatval(str_replace(',', '.', str_replace('.', '', $record[21])));
                        $consumption->setAmount($amount);
                        // As we do not know the quantiy, we fill quantity and price unit
                        $consumption->setQuantity(1);
                        $consumption->setPriceUnit($amount);
                        $em->persist($consumption);
                        $em->flush();
                    } else {
                        $this->addFlash(
                            'warning',
                            'El vehículo con matrícula '.$record[20].' no está registrado en el sistema. Por favor, 
                            vuelve a realizar la importación una vez registrado.'
                        );
                    }
                } else {
                    $this->addFlash(
                        'warning',
                        'El consumo con código '.$consumptionCode.' ya ha sido importado. No se ha vuelto a importar'
                    );
                }
            }
            $this->addFlash(
                'notice',
                'Fichero importado correctamente'
            );

            return new RedirectResponse($this->generateUrl('admin_app_vehicle_vehicleconsumption_list'));

        }

        return $this->renderWithExtraParams(
            'admin/vehicle-consumption/uploadCsv.html.twig',
            ['uploadCsvForm' => $form->createView()]
        );
    }
}
