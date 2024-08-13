<?php

namespace App\Command\Payslip;

use App\Command\AbstractBaseCommand;
use App\Entity\Payslip\PayslipLineConcept;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreatePayslipLineConceptsCommand.
 *
 * @category Command
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 */
class CreatePayslipLineConceptsCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure(): void
    {
        $this->setName('app:create:payslip:line:concepts');
        $this->setDescription('Create default payslip line concepts');
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
        // Set counters
        $beginTimestamp = new DateTimeImmutable();
        $rowsRead = 0;
        $newRecords = 0;
        $errors = 0;
        $newPayslipLineConcepts = [
          'Salario base',
          'Extra',
          'Extra marzo',
          'Extra navidad',
          'Plus producción',
          'Plus convenio',
          'Plus transporte',
        ];
        foreach ($newPayslipLineConcepts as $newPayslipLineConcept) {
            $payslipLineConcept = $this->rm->getPayslipLineConceptRepository()->findOneBy([
               'description' => $newPayslipLineConcept,
            ]);
            if (!$payslipLineConcept) {
                //new Record
                $payslipLineConcept = new PayslipLineConcept();
                $payslipLineConcept->setDescription($newPayslipLineConcept);
                $this->em->persist($payslipLineConcept);
                $this->em->flush();
                ++$newRecords;
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
