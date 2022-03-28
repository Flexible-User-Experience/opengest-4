<?php

namespace App\Command\Operator;

use App\Command\AbstractBaseCommand;
use App\Entity\Operator\OperatorAbsenceType;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportOperatorAbsenceTypeCommand.
 *
 * @category Command
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class ImportOperatorAbsenceTypeCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:operator:absence:type');
        $this->setDescription('Import operator absence type from CSV file');
        $this->addArgument('filename', InputArgument::REQUIRED, 'CSV file to import');
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
        while (false !== ($row = $this->readRow($fr))) {
            $output->writeln('#'.$rowsRead.' · ID_'.$this->readColumn(0, $row).' · '.$this->readColumn(1, $row).' · '.$this->readColumn(2, $row));
            /** @var OperatorAbsenceType $operatorAbsenceType */
            $operatorAbsenceType = $this->rm->getOperatorAbsenceTypeRepository()->findOneBy(['name' => $this->readColumn(1, $row)]);
            if (!$operatorAbsenceType) {
                // new record
                $operatorAbsenceType = new OperatorAbsenceType();
                ++$newRecords;
            }
            $operatorAbsenceType
                ->setName($this->readColumn(1, $row))
                ->setDescription($this->readColumn(2, $row))
            ;
            $this->em->persist($operatorAbsenceType);
            if (0 == $rowsRead % self::CSV_BATCH_WINDOW && !$input->getOption('dry-run')) {
                $this->em->flush();
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
