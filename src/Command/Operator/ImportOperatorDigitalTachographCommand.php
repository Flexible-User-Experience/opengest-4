<?php

namespace App\Command\Operator;

use App\Command\AbstractBaseCommand;
use App\Entity\Operator\OperatorDigitalTachograph;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportOperatorDigitalTachographCommand.
 *
 * @category Command
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class ImportOperatorDigitalTachographCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:operator:digital:tachograph');
        $this->setDescription('Import operator digital tachographs from CSV file');
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
        $rowsRead = 1;
        $newRecords = 0;
        $errors = 0;

        // Import CSV rows
        while (false != ($row = $this->readRow($fr))) {
            $operator = $this->em->getRepository('App:Operator\Operator')->findOneBy(['taxIdentificationNumber' => $this->readColumn(4, $row)]);
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $this->readColumn(2, $row));
            $file = $this->readColumn(3, $row);
            $output->writeln('#'.$rowsRead.' · ID_'.$this->readColumn(0, $row).' · '.$this->readColumn(4, $row).' · '.$date->format('Y-m-d H:i:s').' · '.$file);
            if ($operator && $date && $file) {
                $digitalTachograph = $this->em->getRepository('App:Operator\OperatorDigitalTachograph')->findOneBy([
                    'operator' => $operator,
                    'createdAt' => $date,
                ]);
                if (!$digitalTachograph) {
                    // new record
                    $digitalTachograph = new OperatorDigitalTachograph();
                    ++$newRecords;
                }
                $digitalTachograph
                    ->setOperator($operator)
                    ->setCreatedAt($date)
                    ->setUploadedFileName($file)
                ;
                $this->em->persist($digitalTachograph);
                if (0 == $rowsRead % self::CSV_BATCH_WINDOW && !$input->getOption('dry-run')) {
                    $this->em->flush();
                }
            } else {
                $output->write('<error>#'.$rowsRead);
                if (!$operator) {
                    $output->write(' · no operator found');
                }
                if (!$date) {
                    $output->write(' · no date found');
                }
                if (!$file) {
                    $output->write(' · no file found');
                }
                $output->writeln('</error>');
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
