<?php

namespace App\Command\Operator;

use App\Command\AbstractBaseCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use App\Entity\Operator\OperatorCheckingType;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportOperatorCheckingTypeCommand.
 *
 * @category Command
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class ImportOperatorCheckingTypeCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:operator:checking:type');
        $this->setDescription('Import operator checking type from CSV file');
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
        while (false !== ($row = $this->readRow($fr))) {
            $output->writeln('#'.$rowsRead.' · ID_'.$this->readColumn(0, $row).' · '.$this->readColumn(1, $row).' · '.$this->readColumn(2, $row));
            $operatorCheckingType = $this->em->getRepository('App:Operator\OperatorCheckingType')->findOneBy(['name' => $this->readColumn(1, $row)]);
            // new record
            if (!$operatorCheckingType) {
                $operatorCheckingType = new OperatorCheckingType();
                ++$newRecords;
            }
            $operatorCheckingType
                ->setName($this->readColumn(1, $row))
                ->setDescription($this->readColumn(2, $row))
            ;
            $this->em->persist($operatorCheckingType);
            if (0 == $rowsRead % self::CSV_BATCH_WINDOW && !$input->getOption('dry-run')) {
                $this->em->flush();
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
