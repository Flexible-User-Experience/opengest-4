<?php

namespace App\Manager\Xls;

use App\Manager\RepositoriesManager;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Class MarginAnalysisXlsManager.
 *
 * @category Manager
 */
class MarginAnalysisXlsManager
{
    private RepositoriesManager $rm;

    /**
     * Methods.
     */
    public function __construct(RepositoriesManager $rm)
    {
        $this->rm = $rm;
    }

    public function outputXls($parameters)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet = $this->buildXls($spreadsheet, $parameters);
        $filename = 'marginAnalysis.xlsx';
        try {
            $writer = new Xlsx($spreadsheet);
            $writer->save($filename);
            $content = file_get_contents($filename);
        } catch (Exception $e) {
            exit($e->getMessage());
        }
        unlink($filename);

        return $content;
    }

    private function buildXls(Spreadsheet $spreadsheet, $parameters)
    {
        return $spreadsheet;
    }
}
