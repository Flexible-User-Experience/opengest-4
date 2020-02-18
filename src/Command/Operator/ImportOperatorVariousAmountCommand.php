<?php

namespace App\Command\Operator;

use App\Command\AbstractBaseCommand;
use App\Entity\Operator\OperatorVariousAmount;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportOperatorVariousAmountCommand.
 *
 * @category Command
 *
 * @author   David Romaní <david@flux.cat>
 */
class ImportOperatorVariousAmountCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:operator:various:amount');
        $this->setDescription('Import operator various amounts from CSV file');
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

        // Import CSV rows
        $beginTimestamp = new \DateTime();
        $rowsRead = 0;
        $newRecords = 0;
        $errors = 0;

        // Import CSV rows
        while (false != ($row = $this->readRow($fr))) {
            $operator = $this->em->getRepository('App:Operator\Operator')->findOneBy(['taxIdentificationNumber' => $this->readColumn(6, $row)]);
            $date = \DateTime::createFromFormat('Y-m-d', $this->readColumn(2, $row));
            $description = $this->readColumn(3, $row);
            $output->writeln($this->readColumn(1, $row).' · '.$this->readColumn(2, $row).' · '.$this->readColumn(3, $row));
            if ($operator && $date && $description) {
                $variousAmount = $this->em->getRepository('App:Operator\OperatorVariousAmount')->findOneBy([
                    'operator' => $operator,
                    'date' => $date,
                    'description' => $description,
                ]);
                if (!$variousAmount) {
                    // new record
                    $variousAmount = new OperatorVariousAmount();
                    ++$newRecords;
                }
                $variousAmount
                    ->setOperator($operator)
                    ->setDate($date)
                    ->setDescription($description)
                    ->setPriceUnit($this->readColumn(4, $row))
                    ->setUnits($this->readColumn(5, $row))
                ;
                $this->em->persist($variousAmount);
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
        $endTimestamp = new \DateTime();
        $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp, $errors, $input->getOption('dry-run'));
    }
}
