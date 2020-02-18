<?php

namespace App\Command\Web;

use App\Command\AbstractBaseCommand;
use App\Entity\Web\Service;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportServiceCsvCommand.
 */
class ImportServiceCsvCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:service');
        $this->setDescription('Import service from CSV file');
        $this->addArgument('filename', InputArgument::REQUIRED, 'CSV file to import');
    }

    /**
     * Execute.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws InvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Welcome & Initialization & File validations
        $fr = $this->initialValidation($input, $output);

        // Import CSV rows
        $beginTimestamp = new \DateTime();
        $rowsRead = 0;
        $newRecords = 0;
        while (false !== ($row = $this->readRow($fr))) {
            $service = $this->em->getRepository('App:Web\Service')->findOneBy(['slug' => $this->readColumn(26, $row)]);
            // new service
            if (!$service) {
                $service = new Service();
                ++$newRecords;
            }
            // update service
            $service
                ->setName($this->readColumn(8, $row))
                ->setMainImage('1.jpg')
            ;
            $this->em->persist($service);
            ++$rowsRead;
        }

        $this->em->flush();
        $endTimestamp = new \DateTime();
        // Print totals
        $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp);
    }
}
