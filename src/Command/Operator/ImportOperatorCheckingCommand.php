<?php

namespace App\Command\Operator;

use App\Command\AbstractBaseCommand;
use App\Entity\Operator\OperatorChecking;
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
     * @param InputInterface  $input
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

        // Set counters
        $beginTimestamp = new \DateTime();
        $rowsRead = 0;
        $newRecords = 0;
        $errors = 0;

        // Import CSV rows
        while (false != ($row = $this->readRow($fr))) {
            $begin = \DateTime::createFromFormat('Y-m-d', $this->readColumn(3, $row));
            $end = \DateTime::createFromFormat('Y-m-d', $this->readColumn(4, $row));
            $type = $this->em->getRepository('App:Operator\OperatorCheckingType')->findOneBy(['name' => $this->readColumn(5, $row)]);
            $operator = $this->em->getRepository('App:Operator\Operator')->findOneBy(['taxIdentificationNumber' => $this->readColumn(6, $row)]);
            $output->writeln('#'.$rowsRead.' · ID_'.$this->readColumn(0, $row).' · '.$this->readColumn(6, $row).' · '.$this->readColumn(3, $row).' · '.$this->readColumn(4, $row).' · '.$this->readColumn(5, $row));
            if ($operator && $type && $begin && $end) {
                $operatorChecking = $this->em->getRepository('App:Operator\OperatorChecking')->findOneBy([
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
        $endTimestamp = new \DateTime();
        $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp, $errors, $input->getOption('dry-run'));
    }
}
