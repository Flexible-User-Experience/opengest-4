<?php

namespace App\Command\Sale;

use App\Command\AbstractBaseCommand;
use App\Entity\Sale\SaleServiceTariff;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SetActivityLineToSaleServiceTariffCommand.
 *
 * @category Command
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 */
class SetActivityLineToSaleServiceTariffCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:set:sale:tariff:activity_line');
        $this->setDescription('Set activity line for sale tariffs');
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

        $saleServiceTariffs = $this->rm->getSaleServiceTariffRepository()->findAll();
        $activityLineCamionPluma = $this->rm->getActivityLineRepository()->findOneBy(['name' => 'CAMION PLUMA']);
        $activityLineHidraulica = $this->rm->getActivityLineRepository()->findOneBy(['name' => 'HIDRAULICA']);
        $activityLineCarretera = $this->rm->getActivityLineRepository()->findOneBy(['name' => 'CARRETERA']);
        $activityLineTransporte = $this->rm->getActivityLineRepository()->findOneBy(['name' => 'TRANSPORTE']);
        $activityLinePlataformaAerea = $this->rm->getActivityLineRepository()->findOneBy(['name' => 'PLATAFORMA AEREA']);
        $activityLineMinigrua = $this->rm->getActivityLineRepository()->findOneBy(['name' => 'MINIGRUA']);
        /** @var SaleServiceTariff $saleServiceTariff */
        foreach ($saleServiceTariffs as $saleServiceTariff) {
            $description = $saleServiceTariff->getDescription();
            if (str_contains($description, 'CP')) {
                $saleServiceTariff->setActivityLine($activityLineCamionPluma);
            }
            if (
                str_contains($description, 'UNIC') ||
                str_contains($description, 'MINI')
            ) {
                $saleServiceTariff->setActivityLine($activityLineMinigrua);
            }
            if (
                str_contains($description, 'TRAN') ||
                str_contains($description, 'TRAILER') ||
                str_contains($description, 'TRAC') ||
                str_contains($description, 'PTC') ||
                str_contains($description, 'GON')
            ) {
                $saleServiceTariff->setActivityLine($activityLineTransporte);
            }
            if (str_contains($description, 'REM')) {
                $saleServiceTariff->setActivityLine($activityLineCarretera);
            }
            if (str_contains($description, 'AEREA')) {
                $saleServiceTariff->setActivityLine($activityLinePlataformaAerea);
            }
            if (is_numeric($description)) {
                $saleServiceTariff->setActivityLine($activityLineHidraulica);
            }
            $this->em->persist($saleServiceTariff);
            $this->em->flush();
            ++$newRecords;
        }

        // Print totals
        $endTimestamp = new DateTimeImmutable();

        return $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp, $errors, $input->getOption('dry-run'));
    }
}
