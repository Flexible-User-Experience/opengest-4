<?php

namespace App\Command\Setting;

use App\Command\AbstractBaseCommand;
use App\Entity\Setting\TimeRange;
use DateTime;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateTimeRangesCommand.
 *
 * @category Command
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 */
class CreateTimeRangesCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:create:time:ranges');
        $this->setDescription('Create default time ranges');
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
        $newTimeRanges = [
          ['Madrugada', 2, DateTime::createFromFormat('H:i:s', '00:00:00'), DateTime::createFromFormat('H:i:s', '06:00:00')],
          ['Mañana normal', 1, DateTime::createFromFormat('H:i:s', '06:00:00'), DateTime::createFromFormat('H:i:s', '08:00:00')],
          ['Mañana', 0, DateTime::createFromFormat('H:i:s', '08:00:00'), DateTime::createFromFormat('H:i:s', '13:00:00')],
          ['Mediodia', 1, DateTime::createFromFormat('H:i:s', '13:00:00'), DateTime::createFromFormat('H:i:s', '15:00:00')],
          ['Tarde', 0, DateTime::createFromFormat('H:i:s', '15:00:00'), DateTime::createFromFormat('H:i:s', '18:00:00')],
          ['Tarde normal', 1, DateTime::createFromFormat('H:i:s', '18:00:00'), DateTime::createFromFormat('H:i:s', '22:00:00')],
          ['Noche', 2, DateTime::createFromFormat('H:i:s', '22:00:00'), DateTime::createFromFormat('H:i:s', '23:59:59')],
        ];
        foreach ($newTimeRanges as $newTimeRange) {
            $timeRange = $this->rm->getTimeRangeRepository()->findOneBy([
               'description' => $newTimeRange[0],
            ]);
            if (!$timeRange) {
                //new Record
                $timeRange = new TimeRange();
                $timeRange->setDescription($newTimeRange[0]);
                $timeRange->setType($newTimeRange[1]);
                $timeRange->setStart($newTimeRange[2]);
                $timeRange->setFinish($newTimeRange[3]);
                $this->em->persist($timeRange);
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
