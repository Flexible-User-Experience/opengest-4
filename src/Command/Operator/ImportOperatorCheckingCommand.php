<?php

namespace App\Command\Operator;

use App\Command\AbstractBaseCommand;
use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorChecking;
use App\Entity\Operator\OperatorCheckingType;
use DateTime;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportOperatorCheckingCommand.
 *
 * @category Command
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class ImportOperatorCheckingCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:operator:checking');
        $this->setDescription('Import operator checking from CSV file');
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
        while (false != ($row = $this->readRow($fr))) {
            $begin = DateTime::createFromFormat('Y-m-d', $this->readColumn(3, $row));
            $end = DateTime::createFromFormat('Y-m-d', $this->readColumn(4, $row));
            /** @var OperatorCheckingType $type */
            $type = $this->rm->getOperatorCheckingTypeRepository()->findOneBy(['name' => $this->readColumn(5, $row)]);
            /** @var Operator $operator */
            $operator = $this->rm->getOperatorRepository()->findOneBy(['taxIdentificationNumber' => $this->readColumn(6, $row)]);
            $output->writeln('#'.$rowsRead.' · ID_'.$this->readColumn(0, $row).' · '.$this->readColumn(6, $row).' · '.$this->readColumn(3, $row).' · '.$this->readColumn(4, $row).' · '.$this->readColumn(5, $row));
            if ($operator && $type && $begin && $end) {
                /** @var OperatorChecking $operatorChecking */
                $operatorChecking = $this->rm->getOperatorCheckingRepository()->findOneBy([
                    'type' => $type,
                    'operator' => $operator,
                ]);
                if (!$operatorChecking) {
                    // new record
                    $operatorChecking = new OperatorChecking();
                    ++$newRecords;
                }
                $operatorChecking
                    ->setOperator($operator)
                    ->setBegin($begin)
                    ->setEnd($end)
                    ->setType($type)
                ;
                $this->em->persist($operatorChecking);
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
