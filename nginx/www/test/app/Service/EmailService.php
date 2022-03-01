<?php

namespace App\Service;

use App\Entity\ShiftEntity;
use Carbon\Carbon;
use DOMDocument;
use DOMNode;
use PhpMimeMailParser\Parser;

use App\Entity\ParsedEmailEntity;
use App\Entity\RawEmailEntity;


class EmailService
{
    private const HTML_HEADER = '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head>';
    private const TARGET_CELL_CONTENT = 'yes/no';

    private const DATE_COLUMN = 2;
    private const START_TIME_SHIFT_COLUMN = 3;
    private const END_TIME_SHIFT_COLUMN = 4;
    private const SHIFT_COLUMN = 5;
    private const SERVERS_COLUMN = 6;

    /**
     * @param RawEmailEntity $rawEmail
     * @return ParsedEmailEntity
     */
    public function parse(RawEmailEntity $rawEmail): ParsedEmailEntity
    {

        $parser = new Parser();
        $parser->setText($rawEmail->getRawEmailMessage());
        $html = $parser->getMessageBody('html');

        if (!strrpos($html, '<head>')) {
            $html = self::HTML_HEADER . $html;
        }

        $emailDom = new DOMDocument();
        @$emailDom->loadHTML($html);
        echo $emailDom->saveHTML();
        $parsedEmail = new ParsedEmailEntity(
            $emailDom
        );

        $tableBody = $parsedEmail->getEmailDom()->getElementsByTagName('tbody');
        //TODO: $targetColumnNumber название содержит неточность
        $isShiftsTablesStart = false;
        $targetColumnNumber = null;
        $targetCellNumber = null;
        $targetContent = 'У';

        /**
         * @var int $rowNumber
         * @var DOMNode $rowCells
         */
        foreach ($tableBody[0]->childNodes as $rowNumber => $rowCells) {

            if ($isShiftsTablesStart) {
                $shift = new ShiftEntity();
                $dateStart = $rowCells->childNodes[self::DATE_COLUMN]->nodeValue
                    . $rowCells->childNodes[self::START_TIME_SHIFT_COLUMN]->nodeValue;
                $dateEnd = $rowCells->childNodes[self::DATE_COLUMN]->nodeValue
                    . $rowCells->childNodes[self::END_TIME_SHIFT_COLUMN]->nodeValue;
                $shift->setDateFrom(Carbon::parse($dateStart));
                $shift->setDateTo(Carbon::parse($dateEnd));
                $parsedEmail->addShift($shift);
            }

            foreach ($rowCells->childNodes as $columnNumber => $columnCell) {
                if (strtolower(trim($columnCell->nodeValue)) === self::TARGET_CELL_CONTENT) {
                    $isShiftsTablesStart = true;
                }
            }
        }
        return $parsedEmail;
    }

}