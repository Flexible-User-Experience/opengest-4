<?php

namespace App\Command\Setting;

use App\Command\AbstractBaseCommand;
use App\Entity\Setting\SaleInvoiceSeries;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportSaleInvoiceSeriesCommand.
 *
 * @category Command
 *
 * @author   David Romaní <david@flux.cat>
 */
class ImportSaleInvoiceSeriesCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:setting:sale:invoice:series');
        $this->setDescription('Import setting sale invoice serires from CSV');
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
            $name = $this->lts->nameCleaner($this->readColumn(1, $row));
            $prefix = $this->lts->nameCleaner($this->readColumn(2, $row));
            $isDefault = '1' == $this->readColumn(3, $row) ? true : false;
            $output->writeln('#'.$rowsRead.' · ID_'.$this->readColumn(0, $row).' · '.$name.' · '.$prefix.' · '.$this->readColumn(3, $row));
            if ($name) {
                $saleInvoiceSeries = $this->em->getRepository('App:Setting\SaleInvoiceSeries')->findOneBy([
                    'name' => $name,
                ]);
                if (!$saleInvoiceSeries) {
                    // new record
                    $saleInvoiceSeries = new SaleInvoiceSeries();
                    ++$newRecords;
                }
                $saleInvoiceSeries
                    ->setName($name)
                    ->setPrefix($prefix)
                    ->setIsDefault($isDefault)
                ;
                $this->em->persist($saleInvoiceSeries);
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
