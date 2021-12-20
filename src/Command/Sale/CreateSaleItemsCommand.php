<?php

namespace App\Command\Sale;

use App\Command\AbstractBaseCommand;
use App\Entity\Sale\SaleItem;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateSaleItemsCommand.
 *
 * @category Command
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 */
class CreateSaleItemsCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:create:sale:items');
        $this->setDescription('Create default sale items');
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
        // Set counters
        $beginTimestamp = new DateTimeImmutable();
        $rowsRead = 0;
        $newRecords = 0;
        $errors = 0;
        $newSaleItems = [
          ['Horas', 1],
          ['Horas Festivos', 1],
          ['Horas Desplazamiento', 1],
          ['Km Delplazamiento', 1],
          ['Presupuesto', 1],
          ['Salida', 1],
          ['Desbloqueo', 1],
        ];
        foreach ($newSaleItems as $newSaleItem) {
            $saleItem = $this->rm->getSaleItemRepository()->findOneBy([
               'description' => $newSaleItem[0],
            ]);
            if (!$saleItem) {
                //new Record
                $saleItem = new SaleItem();
                $saleItem->setDescription($newSaleItem[0]);
                $saleItem->setType($newSaleItem[1]);
                $this->em->persist($saleItem);
                $this->em->flush();
            }
            ++$rowsRead;
            ++$newRecords;
        }
        if (!$input->getOption('dry-run')) {
            $this->em->flush();
        }

        // Print totals
        $endTimestamp = new DateTimeImmutable();

        return $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp, $errors, $input->getOption('dry-run'));
    }
}
