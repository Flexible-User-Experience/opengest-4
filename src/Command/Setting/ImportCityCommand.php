<?php

namespace App\Command\Setting;

use App\Command\AbstractBaseCommand;
use App\Entity\Setting\City;
use App\Entity\Setting\Province;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportCityCommand.
 *
 * @category Command
 *
 * @author   David Romaní <david@flux.cat>
 */
class ImportCityCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:city');
        $this->setDescription('Import city from CSV file by index');
        $this->addArgument('filename', InputArgument::REQUIRED, 'CSV file to import');
        $this->addArgument('name', InputArgument::REQUIRED, 'city name column index');
        $this->addArgument('zip', InputArgument::REQUIRED, 'postal code column index');
        $this->addArgument('province', InputArgument::REQUIRED, 'province name column index');
        $this->addArgument('country', InputArgument::REQUIRED, 'country name column index');
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'don\'t persist changes into database');
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
        // Welcome & Initialization & File validations
        $fr = $this->initialValidation($input, $output);

        // Set counters
        $beginTimestamp = new DateTimeImmutable();
        $rowsRead = 0;
        $newRecords = 0;
        $errors = 0;

        // Import CSV rows
        while (false != ($row = $this->readRow($fr))) {
            $name = $this->lts->cityNameCleaner($this->readColumn($input->getArgument('name'), $row));
            $postalCode = $this->lts->postalCodeCleaner($this->readColumn($input->getArgument('zip'), $row));
            $provinceName = $this->lts->provinceNameCleaner($this->readColumn($input->getArgument('province'), $row));
            $countryName = $this->lts->countryNameCleaner($this->readColumn($input->getArgument('country'), $row));
            $output->writeln('#'.$rowsRead.' · ID_'.$this->readColumn(0, $row).' · '.$name.' · '.$postalCode.' · '.$provinceName.' · '.$countryName);
            $countryCode = $this->lts->countryToCode($countryName);
            /** @var Province $province */
            $province = $this->rm->getProvinceRepository()->findOneBy([
                'name' => $provinceName,
                'country' => $countryCode,
            ]);
            if ($province) {
                /** @var City $city */
                $city = $this->rm->getCityRepository()->findOneBy([
                    'postalCode' => $postalCode,
                    'name' => $name,
                ]);
                if (!$city) {
                    // new record
                    $city = new City();
                    ++$newRecords;
                }
                $city
                    ->setName($name)
                    ->setPostalCode($postalCode)
                    ->setProvince($province)
                ;
                $this->em->persist($city);
                if (0 == $rowsRead % self::CSV_BATCH_WINDOW && !$input->getOption('dry-run')) {
                    $this->em->flush();
                }
            } else {
                ++$errors;
                $output->writeln('<error>Error at row number #'.$rowsRead.'</error>');
            }
            ++$rowsRead;
        }
        if (!$input->getOption('dry-run')) {
            $this->em->flush();
        }

        // Print totals
        $endTimestamp = new DateTimeImmutable();

        return $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp, $errors, $input->getOption('dry-run'));
    }
}
