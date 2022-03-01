<?php
require_once __DIR__.'/vendor/autoload.php';

use PhpMimeMailParser\Parser;

use App\Entity\RawEmailEntity;
use App\Service\EmailService;

const TARGET_CELL_CONTENT = 'yes/no';

$emailService = new EmailService();
$path = 'mail.txt';
$emailContent = file_get_contents($path);

$rawEmail = new RawEmailEntity($emailContent, new DateTimeZone('Europe/Moscow'));
try {
    $parsedEmail = $emailService->parse($rawEmail);
} catch (Throwable $exception) {
    var_dump($exception);
}
$parsedEmail->getShifts();

