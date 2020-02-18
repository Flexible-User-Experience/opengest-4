<?php

namespace App\Command\Web;

use App\Command\AbstractBaseCommand;
use App\Entity\Web\Work;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportWorkCsvCommand.
 */
class ImportWorkCsvCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:work');
        $this->setDescription('Import work from CSV file');
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
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Welcome & Initialization & File validations
        $fr = $this->initialValidation($input, $output);

        // Import CSV rows
        $beginTimestamp = new DateTimeImmutable();
        $rowsRead = 0;
        $newRecords = 0;
        while (false !== ($row = $this->readRow($fr))) {
            /** @var Work $work */
            $work = $this->rm->getWorkRepository()->findOneBy(['name' => $this->readColumn(8, $row)]);
            // new work
            if (!$work) {
                $work = new Work();
                ++$newRecords;
            }
            // update work
            $work
                ->setName($this->readColumn(8, $row))
                ->setDescription($this->readColumn(10, $row))
                ->setShortDescription($this->readColumn(9, $row))
            ;
            $image = $this->readColumn(2, $row);
            if (strlen($image) > 0) {
                $work->setMainImage($image);
            } else {
                $work->setMainImage('1.jpg');
            }
            $date = date_create_from_format('Y-m-d', $this->readColumn(3, $row));
            if ($date) {
                $work->setDate($date);
            }
            $createdAt = date_create_from_format('Y-m-d H:i:s', $this->readColumn(12, $row));
            if ($createdAt) {
                $work->setCreatedAt($createdAt);
            }

            $this->em->persist($work);
            ++$rowsRead;
        }

        $this->em->flush();
        $endTimestamp = new DateTimeImmutable();
        // Print totals
        $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp);
    }
}
