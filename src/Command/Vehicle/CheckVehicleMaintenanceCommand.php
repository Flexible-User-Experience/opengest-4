<?php

namespace App\Command\Vehicle;

use App\Command\AbstractBaseCommand;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleMaintenance;
use DateTime;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckVehicleMaintenanceCommand.
 *
 * @category Command
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 */
class CheckVehicleMaintenanceCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:vehicle:check_maintenance');
        $this->setDescription('Check vehicle maintenances');
    }

    /**
     * Execute.
     *
     * @return int|void|null
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Welcome
        $output->writeln('<info>Welcome to "'.$this->getDescription().'" command.</info>');

        // Initializations
        $this->init();
        /** @var VehicleMaintenance[] $vehicleMaintenances */
        $vehicleMaintenances = $this->rm->getVehicleMaintenanceRepository()->findBy(['enabled' => true]);
        foreach ($vehicleMaintenances as $vehicleMaintenance) {
            $vehicle = $vehicleMaintenance->getVehicle();
            $maxKm = $vehicleMaintenance->getVehicleMaintenanceTask()->getKm();
            if ($maxKm) {
                $maintenanceKm = $vehicleMaintenance->getKm();
                $currentMileage = $vehicle->getMileage();
                $kmSinceLastMaintenance = $currentMileage - $maintenanceKm;
                if ($kmSinceLastMaintenance >= $maxKm) {
                    $vehicleMaintenance->setNeedsCheck(true);
                    $this->em->persist($vehicleMaintenance);

                    continue;
                }
            }
            $maxHours = $vehicleMaintenance->getVehicleMaintenanceTask()->getHours();
            if ($maxHours) {
                $date = $vehicleMaintenance->getDate();
                $hours = $this->numberOfHoursFromDate($vehicle, $date);
                if ($hours >= $maxHours) {
                    $vehicleMaintenance->setNeedsCheck(true);
                    $this->em->persist($vehicleMaintenance);
                }
            }
        }
        $this->em->flush();
    }

    private function numberOfHoursFromDate(Vehicle $vehicle, DateTime $date): int
    {
        $saleDeliveryNotes = $vehicle->getSaleDeliveryNotes();
        if ($saleDeliveryNotes->first()) {
            $operatorWorkRegisters = $this->rm->getOperatorWorkRegisterRepository()->getOperatorWorkRegistersFromDeliveryNotesAndDateQB($saleDeliveryNotes, $date);
            dd('hey');
        } else {
            return 0;
        }

        return 0;
    }
}
