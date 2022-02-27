<?php
require_once __DIR__.'/vendor/autoload.php';

use PhpMimeMailParser\Parser;
use Service\EmailService;

const TARGET_CELL_CONTENT = 'yes/no';

try {
    $emailService = new EmailService();
} catch (Throwable $exception) {
    var_dump($exception);
}


$path = 'mail.txt';
//$emailContent = file_get_contents($path);

//$rawEmail = new RawEmailEntity($emailContent, new DateTimeZone('Europe/Moscow'));
//$parsedEmail = $emailService->parse($rawEmail);
exit;
try {
    $parser = new Parser();
    $parser->setText(file_get_contents($path));
    $html = $parser->getMessageBody('html');

    echo $html;

        if (!strrpos($html, '<head>')) {
        $html = '<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>' . $html;
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
                var_dump($targetColumnNumber, $targetCellNumber);
                break;
            }
        }

        $tableRow->childNodes[$targetColumnNumber + 1]->childNodes[$targetCellNumber]->childNodes[1]->nodeValue = $targetContent;
    }
    echo $emailDom->saveHTML();

} catch (Throwable $error) {
    var_dump($error);
}

