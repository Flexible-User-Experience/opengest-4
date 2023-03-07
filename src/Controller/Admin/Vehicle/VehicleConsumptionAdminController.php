<?php

namespace App\Controller\Admin\Vehicle;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleConsumption;
use App\Entity\Vehicle\VehicleFuel;
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
            $em = $this->em->getManager();
            $vcr = $em->getRepository(VehicleConsumption::class);
            $vr = $em->getRepository(Vehicle::class);
            $vf = $em->getRepository(VehicleFuel::class);
            foreach ($records as $record) {
                $consumptionCode = $record[13];
                $consumption = $vcr->findOneBy(['supplyCode' => $consumptionCode]);
                if (!$consumption) {
                    /** @var Vehicle $vehicle */
                    $vehicle = $vr->findOneBy(['vehicleRegistrationNumber' => $record[20]]);
                    if ($vehicle) {
                        /** @var VehicleFuel $vehicleFuel */
                        $vehicleFuel = $vf->findOneBy(['id' => 1]);
                        $consumption = new VehicleConsumption();
                        $consumption->setVehicle($vehicle);
                        $consumption->setSupplyCode($consumptionCode);
                        $consumption->setVehicleFuel($vehicleFuel);
                        $date = \DateTime::createFromFormat('d/m/Y', $record[14]);
                        $time = \DateTime::createFromFormat('H:i', $record[15]);
                        $consumption->setSupplyDate($date);
                        $consumption->setSupplyTime($time);
                        $quantity = floatval(str_replace(',', '.', str_replace('.', '', $record[21])));
                        $consumption->setQuantity($quantity);
                        $priceUnit = $vehicleFuel->getPriceUnit() ?? 0;
                        $consumption->setPriceUnit($priceUnit);
                        $consumption->setAmount($quantity * $vehicleFuel->getPriceUnit());
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
