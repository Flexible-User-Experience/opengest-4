<?php

namespace App\Command\Enterprise;

use App\Command\AbstractBaseCommand;
use App\Entity\Enterprise\CollectionDocumentType;
use App\Entity\Enterprise\Enterprise;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportCollectionDocumentTypeCsvCommand.
 *
 * @category Command
 *
 * @author   David Romaní <david@flux.cat>
 */
class ImportCollectionDocumentTypeCsvCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:enterprise:collection:document:type');
        $this->setDescription('Import enterprise collection document types from CSV file');
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
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Welcome & Initialization & File validations
        $fr = $this->initialValidation($input, $output);

        // Set counters
        $beginTimestamp = new DateTimeImmutable();
        $enterprises = $this->rm->getEnterpriseRepository()->findAll();
        $rowsRead = 0;
        $newRecords = 0;
        $errors = 0;

        // Import CSV rows
        while (false != ($row = $this->readRow($fr))) {
            $output->writeln('#'.$rowsRead.' · ID_'.$this->readColumn(0, $row).' · '.$this->readColumn(1, $row));
            /** @var Enterprise $enterprise */
            foreach ($enterprises as $enterprise) {
                /** @var CollectionDocumentType $searchedCollectionDocumentType */
                $searchedCollectionDocumentType = $this->rm->getCollectionDocumentTypeRepository()->findOneBy(['enterprise' => $enterprise, 'name' => $this->readColumn(1, $row)]);
                if (!$searchedCollectionDocumentType) {
                    ++$newRecords;
                    $collectionDocumentType = new CollectionDocumentType();
                    $collectionDocumentType
                        ->setName($this->readColumn(1, $row))
                        ->setEnterprise($enterprise)
                        ->setDescription($this->readColumn(2, $row))
                        ->setSitReference($this->readColumn(3, $row))
                    ;
                    $this->em->persist($collectionDocumentType);
                } else {
                    $searchedCollectionDocumentType
                        ->setDescription($this->readColumn(2, $row))
                        ->setSitReference($this->readColumn(3, $row))
                    ;
                }
                if (!$input->getOption('dry-run')) {
                    $this->em->flush();
                }
            }
            ++$rowsRead;
        }
        if (!$input->getOption('dry-run')) {
            $this->em->flush();
        }

        // Print totals
        $endTimestamp = new DateTimeImmutable();
        $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp, $errors, $input->getOption('dry-run'));
    }
}
