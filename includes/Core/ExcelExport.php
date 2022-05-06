<?php
/**
 * TODO I think this is out of date and needs fixing still for box spout v3 - Mark
 */

declare(strict_types=1);
namespace WpDigitalDriveCompetitions\Core;
require_once dirname(__DIR__, 2) . '/extlibs/vendor/autoload.php';
use Box\Spout\Common\Entity\Cell;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterFactory;
class ExcelExport
{
    /**
     * Export an excel file
     */
    public static function excelExport($fileName, $header, $data, $style = [])
    {

        // Type casting
        foreach ($data as $key => $res) {
            foreach ($res as $skey => $sres) {
                if (is_numeric($sres)) {
                    $data[$key][$skey] = (float) $sres;
                }
            }
        }

        $rows = [];

        foreach ($data as $dataRow) {

            $cells = [];

            foreach ($dataRow as $dataCell) {
                $cells[] = new Cell($dataCell);
            }

            $rows[] = new Row($cells, null);
        }

        $writer = WriterFactory::createFromType(Type::XLSX); // for XLSX files
        // $writer->setShouldUseCellAutosizing(true);

        // $writer->openToFile($filePath); // write data to a file or to a PHP stream
        $writer->openToBrowser($fileName . '.xlsx'); // stream data directly to the browser

        $body_color = $style['body_color'] ?? 'FFFFFF';
        $body_font_color = $style['body_font_color'] ?? '000000';

        $head_color = $style['head_color'] ?? 'FFFFFF';
        $head_font_color = $style['head_font_color'] ?? '000000';

        $brand_text = $style['brand_text'] ?? 'Export';

        $bodystyle = (new StyleBuilder())
            ->setFontBold()
            ->setFontSize(15)
            ->setFontColor($body_font_color)
            ->setBackgroundColor($body_color)
            // ->setShouldWrapText()

            ->build();

        $headstyle = (new StyleBuilder())
            ->setFontBold()
            ->setFontSize(25)
            ->setFontColor($head_font_color)
            ->setBackgroundColor($head_color)
            // ->setShouldWrapText()
            ->build();

        // ARGB color codes
        $writer->addRows([WriterEntityFactory::createRowFromArray([$brand_text], $headstyle)]);
        $writer->addRows([WriterEntityFactory::createRowFromArray([''], $headstyle)]);
        $writer->addRows([WriterEntityFactory::createRowFromArray($header, $bodystyle)]);

        $writer->addRows($rows); // add multiple rows at a time

        $writer->close();
    }
}
