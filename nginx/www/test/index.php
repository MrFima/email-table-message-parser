<?php
require_once __DIR__.'/vendor/autoload.php';

use App\Service\EmailService;
use App\Service\EmailRenderService;

const EMAIL_PATH = 'mail.txt';

$emailService = new EmailService();
$emailRenderService = new EmailRenderService();

$rawEmail = $emailService->getRawEmail(EMAIL_PATH);

try {
    $parsedEmail = $emailService->parse($rawEmail);

    $emailRenderService->render($parsedEmail);
} catch (Throwable $exception) {
    var_dump($exception);
}
