<?php

namespace App\Command\Operator;

use App\Command\AbstractBaseCommand;
use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorAbsence;
use App\Entity\Operator\OperatorAbsenceType;
use DateTime;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportOperatorAbsenceCommand.
 *
 * @category Command
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class ImportOperatorAbsenceCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:operator:absence');
        $this->setDescription('Import operator absence from CSV file');
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
    protected function execute(InputInterface $input, OutputInterface $output): int|null
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
            $begin = DateTime::createFromFormat('Y-m-d', $this->readColumn(3, $row));
            $end = DateTime::createFromFormat('Y-m-d', $this->readColumn(4, $row));
            /** @var OperatorAbsenceType $type */
            $type = $this->rm->getOperatorAbsenceTypeRepository()->findOneBy(['name' => $this->readColumn(5, $row)]);
            /** @var Operator $operator */
            $operator = $this->rm->getOperatorRepository()->findOneBy(['taxIdentificationNumber' => $this->readColumn(6, $row)]);
            $output->writeln('#'.$rowsRead.' · ID_'.$this->readColumn(0, $row).' · '.$this->readColumn(6, $row).' · '.$this->readColumn(3, $row).' · '.$this->readColumn(4, $row).' · '.$this->readColumn(5, $row));
            if ($operator && $type && $begin && $end) {
                /** @var OperatorAbsence $operatorAbsence */
                $operatorAbsence = $this->rm->getOperatorAbsenceRepository()->findOneBy([
                    'begin' => $begin,
                    'end' => $end,
                    'type' => $type,
                    'operator' => $operator,
                ]);
                if (!$operatorAbsence) {
                    // new record
                    $operatorAbsence = new OperatorAbsence();
                    ++$newRecords;
                }
                $operatorAbsence
                    ->setOperator($operator)
                    ->setBegin($begin)
                    ->setEnd($end)
                    ->setType($type)
                ;
                $this->em->persist($operatorAbsence);
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
