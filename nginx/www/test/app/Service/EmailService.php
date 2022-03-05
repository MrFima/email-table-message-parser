<?php

namespace App\Service;

use Carbon\Carbon;
use DateTimeZone;
use DOMDocument;
use DOMNode;
use PhpMimeMailParser\Parser;

use App\Entity\ParsedEmailEntity;
use App\Entity\RawEmailEntity;
use App\Entity\ShiftEntity;

class EmailService
{
    private const HTML_HEADER = '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head>';
    private const START_SHIFT_TABLE_SIGLE = 'yes/no';
    private const CONFIRM_SYMBOL = 'У';

    private const DATE_COLUMN = 2;
    private const START_TIME_SHIFT_COLUMN = 3;
    private const END_TIME_SHIFT_COLUMN = 4;
    private const SHIFT_COLUMN = 5;
    private const SERVERS_COLUMN = 6;

    /**
     * @param string $path
     * @param string $timeZone
     * @return RawEmailEntity
     */
    public function getRawEmail(string $path, string $timeZone = 'Europe/Moscow') {
        return new RawEmailEntity($this->getEmailContent($path), new DateTimeZone($timeZone));
    }

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
        $parsedEmail = new ParsedEmailEntity(
            $emailDom
        );

        $tableBody = $parsedEmail->getEmailDom()->getElementsByTagName('tbody');
        //TODO: $targetColumnNumber название содержит неточность
        $isShiftsTablesStart = false;

        /**
         * @var int $rowNumber
         * @var DOMNode $rowCells
         */
        foreach ($tableBody[0]->childNodes as $rowNumber => $rowCells) {

            try {
                if ($isShiftsTablesStart) {
                    $shift = new ShiftEntity();

                    $dateStart = $rowCells->childNodes[self::DATE_COLUMN]->nodeValue
                        . $rowCells->childNodes[self::START_TIME_SHIFT_COLUMN]->nodeValue;
                    $dateEnd = $rowCells->childNodes[self::DATE_COLUMN]->nodeValue
                        . $rowCells->childNodes[self::END_TIME_SHIFT_COLUMN]->nodeValue;
                    $shift->setDateFrom(Carbon::parse($dateStart));
                    $shift->setDateTo(Carbon::parse($dateEnd));
                    $shift->setRowNumberInEmailTable($rowNumber);

                    $parsedEmail->addShift($shift);
                }
            } catch (\Throwable $exception) {
                break;
            }

            foreach ($rowCells->childNodes as $columnNumber => $columnCell) {
                if (strtolower(trim($columnCell->nodeValue)) === self::START_SHIFT_TABLE_SIGLE) {
                    $isShiftsTablesStart = true;
                }
            }
        }
        return $parsedEmail;
    }

    /**
     * @param string $path
     * @return string
     */
    private function getEmailContent(string $path): string {
        return file_get_contents($path);
    }
}