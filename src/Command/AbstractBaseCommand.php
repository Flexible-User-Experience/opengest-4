<?php

namespace App\Command;

use App\Manager\RepositoriesManager;
use App\Manager\VehicleMaintenanceManager;
use App\Service\NotificationService;
use App\Transformer\DatesTransformer;
use App\Transformer\LocationsTransformer;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class AbstractBaseCommand.
 */
abstract class AbstractBaseCommand extends Command
{
    public const CSV_DELIMITER = ',';
    public const CSV_ENCLOSURE = '"';
    public const CSV_ESCAPE = '\\';
    public const CSV_BATCH_WINDOW = 100;

    protected EntityManagerInterface $em;
    protected Filesystem $fss;
    protected RepositoriesManager $rm;
    protected VehicleMaintenanceManager $vmm;
    protected NotificationService $ns;
    protected LocationsTransformer $lts;
    protected DatesTransformer $dts;

    public function __construct(?string $commandName, EntityManagerInterface $em, Filesystem $fss, RepositoriesManager $rm, VehicleMaintenanceManager $vmm, NotificationService $ns, LocationsTransformer $lts, DatesTransformer $dts)
    {
        parent::__construct($commandName);
        $this->em = $em;
        $this->fss = $fss;
        $this->rm = $rm;
        $this->vmm = $vmm;
        $this->ns = $ns;
        $this->lts = $lts;
        $this->dts = $dts;
    }

    public function init(): self
    {
        ini_set('auto_detect_line_endings', true);

        return $this;
    }

    /**
     * Load column data (index) from searched array (row) if exists, else throws an exception.
     *
     * @param int   $index column index
     * @param array $row   data
     *
     * @return string|object|null
     *
     * @throws Exception
     */
    protected function readColumn($index, $row)
    {
        if (!array_key_exists($index, $row)) {
            throw new Exception('Column index '.$index.' doesn\'t exists');
        }

        return '\\N' !== $row[$index] ? $row[$index] : null;
    }

    /**
     * Read line (row) from CSV file.
     *
     * @param resource $fr file resource, a valid file pointer to a file successfully opened
     *
     * @return array
     */
    protected function readRow($fr)
    {
        return fgetcsv($fr, 0, self::CSV_DELIMITER, self::CSV_ENCLOSURE, self::CSV_ESCAPE);
    }

    /**
     * Get current timestamp string with format Y/m/d H:i:s.
     *
     * @return string
     *
     * @throws Exception
     */
    protected function getTimestampString()
    {
        $cm = new DateTimeImmutable();

        return $cm->format('Y/m/d H:i:s');
    }

    /**
     * Execute.
     *
     * @return resource
     *
     * @throws InvalidArgumentException
     */
    protected function initialValidation(InputInterface $input, OutputInterface $output)
    {
        // Welcome
        $output->writeln('<info>Welcome to "'.$this->getDescription().'" command.</info>');

        // Initializations
        $this->init();

        // File validations
        $output->writeln('<comment>loading data, please wait...</comment>');
        $filename = $input->getArgument('filename');
        if (!$this->fss->exists($filename)) {
            throw new InvalidArgumentException('The file '.$filename.' does not exists');
        }

        $fr = fopen($filename, 'r');
        if (!$fr) {
            throw new InvalidArgumentException('The file '.$filename.' exists but can not be readed');
        }

        return $fr;
    }

    /**
     * @param int  $rowsRead
     * @param int  $newRecords
     * @param int  $errors
     * @param bool $isDryRunModeEnabled
     */
    protected function printTotals(OutputInterface $output, $rowsRead, $newRecords, DateTimeInterface $beginTimestamp, DateTimeInterface $endTimestamp, $errors = 0, $isDryRunModeEnabled = false)
    {
        // Print totals
        if ($isDryRunModeEnabled) {
            $output->writeln('<comment>********************************************************</comment>');
            $output->writeln('<comment>* --dry-run mode enabled (nothing changes in database) *</comment>');
            $output->writeln('<comment>********************************************************</comment>');
        }
        $output->writeln('<comment>'.$rowsRead.' rows read.</comment>');
        $output->writeln('<comment>'.$newRecords.' new records.</comment>');
        $output->writeln('<comment>'.($rowsRead - $newRecords - $errors).' updated records.</comment>');
        if ($errors > 0) {
            $output->writeln('<comment>'.$errors.' errors found</comment>');
        }
        $output->writeln('<info>Total ellapsed time: '.$beginTimestamp->diff($endTimestamp)->format('%H:%I:%S').'</info>');
    }
}
