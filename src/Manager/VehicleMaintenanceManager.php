<?php

namespace App\Manager;

use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleMaintenance;
use App\Repository\Operator\OperatorWorkRegisterRepository;
use App\Repository\Vehicle\VehicleMaintenanceRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
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

    private EntityManagerInterface $entityManager;

    /**
     * Methods.
     */
    public function __construct(
        VehicleMaintenanceRepository $vehicleMaintenanceRepository,
        OperatorWorkRegisterRepository $operatorWorkRegisterRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->vehicleMaintenanceRepository = $vehicleMaintenanceRepository;
        $this->operatorWorkRegisterRepository = $operatorWorkRegisterRepository;
        $this->entityManager = $entityManager;
    }

    public function checkVehicleMaintenance(): int
    {
        $needMaintenance = 0;
        /** @var VehicleMaintenance[] $vehicleMaintenances */
        $vehicleMaintenances = $this->vehicleMaintenanceRepository->findBy(
            ['enabled' => true,
                'needsCheck' => false, ]
        );
        foreach ($vehicleMaintenances as $vehicleMaintenance) {
            $needsCheck = $this->checkIfMaintenanceNeedsCheck($vehicleMaintenance);
            if ($needsCheck) {
                $vehicleMaintenance->setNeedsCheck(true);
                $this->entityManager->persist($vehicleMaintenance);
                ++$needMaintenance;
            }
        }
        $this->entityManager->flush();

        return $needMaintenance;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function checkIfMaintenanceNeedsCheck(VehicleMaintenance $vehicleMaintenance): bool
    {
        if ($this->remainingKm($vehicleMaintenance) < 0) {
            return true;
        }
        if ($this->remainingHours($vehicleMaintenance) < 0) {
            return true;
        }

        return false;
    }

    public function remainingKm(VehicleMaintenance $vehicleMaintenance)
    {
        $vehicle = $vehicleMaintenance->getVehicle();
        $maxKmBetweenRevisions = $vehicleMaintenance->getVehicleMaintenanceTask()->getKm();
        $currentMileage = $vehicle->getMileage();
        if ($maxKmBetweenRevisions && $currentMileage) {
            $maintenanceKm = $vehicleMaintenance->getKm();
            $kmSinceLastMaintenance = $currentMileage - $maintenanceKm;

            return $maxKmBetweenRevisions - $kmSinceLastMaintenance;
        }

        return false;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function remainingHours(VehicleMaintenance $vehicleMaintenance)
    {
        $vehicle = $vehicleMaintenance->getVehicle();
        $maxHoursBetweenRevisions = $vehicleMaintenance->getVehicleMaintenanceTask()->getHours();
        if ($maxHoursBetweenRevisions) {
            $date = $vehicleMaintenance->getDate();
            $hoursSinceLastMaintenance = $this->numberOfHoursFromDate($vehicle, $date);

            return $maxHoursBetweenRevisions - $hoursSinceLastMaintenance;
        }

        return false;
    }

    public function updateVehicleMileage(VehicleMaintenance $vehicleMaintenance): void
    {
        $km = $vehicleMaintenance->getKm();
        if ($km > 0) {
            $vehicle = $vehicleMaintenance->getVehicle();
            $vehicleKm = $vehicle->getMileage();
            if ($km > $vehicleKm) {
                $vehicle->setMileage($km);
                $this->entityManager->flush();
            }
        }
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
