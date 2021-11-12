<?php

namespace App\Command\Vehicle;

use App\Command\AbstractBaseCommand;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleMaintenance;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
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
        $needMainenance = 0;
        /** @var VehicleMaintenance[] $vehicleMaintenances */
        $vehicleMaintenances = $this->rm->getVehicleMaintenanceRepository()->findBy(
            ['enabled' => true,
            'needsCheck' => false, ]
        );
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
                    ++$needMainenance;

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
                    ++$needMainenance;
                }
            }
        }
        $this->em->flush();
        $output->writeln('<info>'.$needMainenance.' vehicles need new maintenancecommand.</info>');
    }

    /**
     * @throws NonUniqueResultException
     */
    private function numberOfHoursFromDate(Vehicle $vehicle, DateTime $date): int
    {
        $saleDeliveryNotes = $vehicle->getSaleDeliveryNotes();
        if ($saleDeliveryNotes->first()) {
            $hoursFromDate = $this->rm->getOperatorWorkRegisterRepository()->getHoursFromperatorWorkRegistersWithHoursFromDeliveryNotesAndDate($saleDeliveryNotes, $date)['hours'];
        } else {
            $hoursFromDate = 0;
        }

        return $hoursFromDate;
    }
}