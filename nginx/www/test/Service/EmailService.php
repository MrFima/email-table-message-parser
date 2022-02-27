<?php

namespace Service;

use PhpMimeMailParser\Parser;

class EmailService
{
    private const HTML_HEADER = '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head>';
    private const TARGET_CELL_CONTENT = 'yes/no';

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
        $emailDom->loadHTML($html);

        $tableBody = $emailDom->getElementsByTagName('tbody');
        //TODO: $targetColumnNumber название содержит неточность
        $targetColumnNumber = null;
        $targetCellNumber = null;
        $targetContent = 'У';

        /** @var DOMNode $tableRow */
        foreach ($tableBody as $tableRow) {
            /** @var DOMNode $tableCells */
            foreach ($tableRow->childNodes as $columnNumber => $tableCells) {
                foreach ($tableCells->childNodes as $cellNumber => $tableCell) {
                    if (strtolower(trim($tableCell->nodeValue)) === TARGET_CELL_CONTENT) {
                        $targetCellNumber = $cellNumber;
                        $targetColumnNumber = $columnNumber;

                    }
                }
                if ($targetColumnNumber) {
                    break;
                }
            }

            $tableRow->childNodes[$targetColumnNumber + 1]->childNodes[$targetCellNumber]->childNodes[1]->nodeValue = $targetContent;
        }

    }

}