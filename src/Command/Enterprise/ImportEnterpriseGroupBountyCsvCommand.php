<?php

namespace App\Command\Enterprise;

use App\Command\AbstractBaseCommand;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Enterprise\EnterpriseGroupBounty;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportEnterpriseGroupBountyCsvCommand.
 *
 * @category Command
 *
 * @author   David Romaní <david@flux.cat>
 */
class ImportEnterpriseGroupBountyCsvCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:enterprise:group:bounty');
        $this->setDescription('Import enterprise group bountys from CSV file');
        $this->addArgument('filename', InputArgument::REQUIRED, 'CSV file to import');
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'don\'t persist changes into database');
    }

    /**
     * Execute.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Welcome & Initialization & File validations
        $fr = $this->initialValidation($input, $output);

        // Set counters
        $beginTimestamp = new DateTimeImmutable();
        $rowsRead = 0;
        $newRecords = 0;
        $errors = 0;

        // Import CSV rows
        while (false != ($row = $this->readRow($fr))) {
            $name = $this->readColumn(2, $row);
            $output->writeln('#'.$rowsRead.' · ID_'.$this->readColumn(0, $row).' · '.$this->readColumn(21, $row).' · '.$name);
            /** @var Enterprise $enterprise */
            $enterprise = $this->rm->getEnterpriseRepository()->findOneBy(['taxIdentificationNumber' => $this->readColumn(21, $row)]);
            if ($enterprise) {
                /** @var EnterpriseGroupBounty $groupBounty */
                $groupBounty = $this->rm->getEnterpriseGroupBountyRepository()->findOneBy([
                    'group' => $name,
                    'enterprise' => $enterprise,
                ]);
                if (!$groupBounty) {
                    // new record
                    $groupBounty = new EnterpriseGroupBounty();
                    ++$newRecords;
                }
                $groupBounty
                    ->setEnterprise($enterprise)
                    ->setGroup($name)
                    ->setNormalHour($this->readColumn(3, $row))
                    ->setExtraNormalHour($this->readColumn(4, $row))
                    ->setExtraExtraHour($this->readColumn(5, $row))
                    ->setRoadNormalHour($this->readColumn(6, $row))
                    ->setRoadExtraHour($this->readColumn(7, $row))
                    ->setAwaitingHour($this->readColumn(8, $row))
                    ->setNegativeHour($this->readColumn(9, $row))
                    ->setLunch($this->readColumn(10, $row))
                    ->setDinner($this->readColumn(11, $row))
                    ->setOverNight($this->readColumn(12, $row))
                    ->setExtraNight($this->readColumn(13, $row))
                    ->setDiet($this->readColumn(14, $row))
                    ->setInternationalLunch($this->readColumn(15, $row))
                    ->setInternationalDinner($this->readColumn(16, $row))
                    ->setTruckOutput($this->readColumn(18, $row))
                    ->setCarOutput($this->readColumn(19, $row))
                    ->setTransferHour($this->readColumn(20, $row));
                $this->em->persist($groupBounty);
                if (0 == $rowsRead % self::CSV_BATCH_WINDOW && !$input->getOption('dry-run')) {
                    $this->em->flush();
                }
            } else {
                $output->writeln('<error>Error at row number #'.$rowsRead.'</error>');
                ++$errors;
            }
            ++$rowsRead;
        }
        if (!$input->getOption('dry-run')) {
            $this->em->flush();
        }

        // Print totals
        $endTimestamp = new DateTimeImmutable();
        $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp, $errors, $input->getOption('dry-run'));
    }
}
