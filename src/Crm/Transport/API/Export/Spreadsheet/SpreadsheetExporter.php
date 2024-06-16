<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Spreadsheet;

use App\Crm\Transport\API\Export\Spreadsheet\CellFormatter\ArrayFormatter;
use App\Crm\Transport\API\Export\Spreadsheet\CellFormatter\BooleanFormatter;
use App\Crm\Transport\API\Export\Spreadsheet\CellFormatter\CellFormatterInterface;
use App\Crm\Transport\API\Export\Spreadsheet\CellFormatter\DateFormatter;
use App\Crm\Transport\API\Export\Spreadsheet\CellFormatter\DateTimeFormatter;
use App\Crm\Transport\API\Export\Spreadsheet\CellFormatter\DurationFormatter;
use App\Crm\Transport\API\Export\Spreadsheet\CellFormatter\StringFormatter;
use App\Crm\Transport\API\Export\Spreadsheet\CellFormatter\TimeFormatter;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_key_exists;
use function call_user_func;

/**
 * @internal
 */
class SpreadsheetExporter
{
    /**
     * @var CellFormatterInterface[]
     */
    private array $formatter = [];

    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
        $this->registerCellFormatter('datetime', new DateTimeFormatter());
        $this->registerCellFormatter('date', new DateFormatter());
        $this->registerCellFormatter('time', new TimeFormatter());
        $this->registerCellFormatter('duration', new DurationFormatter());
        $this->registerCellFormatter('boolean', new BooleanFormatter());
        $this->registerCellFormatter('array', new ArrayFormatter());
        $this->registerCellFormatter('string', new StringFormatter());
    }

    public function registerCellFormatter(string $type, CellFormatterInterface $formatter): void
    {
        $this->formatter[$type] = $formatter;
    }

    /**
     * @param ColumnDefinition[] $columns
     * @throws Exception
     */
    public function export(array $columns, array $entries): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set default row height to automatic, so we can specify wrap text columns later on
        // without bloating the output file as we would need to store stylesheet info for every cell.
        // LibreOffice is still not considering this flag, @see https://github.com/PHPOffice/PHPExcel/issues/588
        // with no solution implemented so nothing we can do about it there.
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        $recordsHeaderColumn = 1;
        $recordsHeaderRow = 1;

        foreach ($columns as $settings) {
            $sheet->setCellValue(CellAddress::fromColumnAndRow($recordsHeaderColumn++, $recordsHeaderRow), $this->translator->trans($settings->getLabel(), [], $settings->getTranslationDomain()));
        }

        $entryHeaderRow = $recordsHeaderRow + 1;

        foreach ($entries as $entry) {
            $entryHeaderColumn = 1;

            foreach ($columns as $settings) {
                $value = call_user_func($settings->getAccessor(), $entry);

                if (!array_key_exists($settings->getType(), $this->formatter)) {
                    $sheet->setCellValue(CellAddress::fromColumnAndRow($entryHeaderColumn, $entryHeaderRow), $value);
                } else {
                    $formatter = $this->formatter[$settings->getType()];
                    $formatter->setFormattedValue($sheet, $entryHeaderColumn, $entryHeaderRow, $value);
                }

                $entryHeaderColumn++;
            }

            $entryHeaderRow++;
        }

        return $spreadsheet;
    }
}
