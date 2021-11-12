<?php

namespace App\Manager;

use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleMaintenance;
use App\Repository\Operator\OperatorWorkRegisterRepository;
use App\Repository\Vehicle\VehicleMaintenanceRepository;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class VehicleMaintenanceManager.
 *
 * @category Manager
 **/
class VehicleMaintenanceManager
{
    private VehicleMaintenanceRepository $vehicleMaintenanceRepository;

    private OperatorWorkRegisterRepository $operatorWorkRegisterRepository;

    /**
     * Methods.
     */
    public function __construct(VehicleMaintenanceRepository $vehicleMaintenanceRepository, OperatorWorkRegisterRepository $operatorWorkRegisterRepository)
    {
        $this->vehicleMaintenanceRepository = $vehicleMaintenanceRepository;
        $this->operatorWorkRegisterRepository = $operatorWorkRegisterRepository;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function checkIfMaintenanceNeedsCheck(VehicleMaintenance $vehicleMaintenance): bool
    {
        $vehicle = $vehicleMaintenance->getVehicle();
        $maxKm = $vehicleMaintenance->getVehicleMaintenanceTask()->getKm();
        $currentMileage = $vehicle->getMileage();
        if ($maxKm && $currentMileage) {
            $maintenanceKm = $vehicleMaintenance->getKm();
            $kmSinceLastMaintenance = $currentMileage - $maintenanceKm;
            if ($kmSinceLastMaintenance >= $maxKm) {
                return true;
            }
        }
        $maxHours = $vehicleMaintenance->getVehicleMaintenanceTask()->getHours();
        if ($maxHours) {
            $date = $vehicleMaintenance->getDate();
            $hours = $this->numberOfHoursFromDate($vehicle, $date);
            if ($hours >= $maxHours) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws NonUniqueResultException
     */
    private function numberOfHoursFromDate(Vehicle $vehicle, DateTime $date): ?int
    {
        $saleDeliveryNotes = $vehicle->getSaleDeliveryNotes();
        if ($saleDeliveryNotes->first()) {
            $hoursFromDate = $this->operatorWorkRegisterRepository->getHoursFromperatorWorkRegistersWithHoursFromDeliveryNotesAndDate($saleDeliveryNotes, $date)['hours'];
        } else {
            $hoursFromDate = 0;
        }

        return $hoursFromDate;
    }
}
