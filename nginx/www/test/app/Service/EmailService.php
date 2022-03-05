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

/**
 * Сервис для взаимодействия с письмо и изменения его контента
 */
class EmailService
{
    private const HTML_HEADER = '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head>';
    // Строка, по которой определяем начало таблицы
    private const START_SHIFT_TABLE_SIGLE = 'yes/no';
    // Символ, который устанавливаем в качестве согласия со сменой
    private const CONFIRM_SYMBOL = 'У';

    // Номер колонки в таблице смен с датой смены
    private const DATE_COLUMN = 2;
    // Номер колонки в таблице смен с временем началы смены
    private const START_TIME_SHIFT_COLUMN = 3;
    // Номер колонки в таблице смен с временем окончания смены
    private const END_TIME_SHIFT_COLUMN = 4;
    // Номер колонки в таблице смен с названием смены
    private const SHIFT_COLUMN = 5;
    // Номер колонки в таблице смен c количеством порций (столов?)
    private const SERVERS_COLUMN = 6;

    /**
     * Получение сущности письма, которая хранит в себе письмо, полученное из файла и таймзону
     * @param string $path
     * @param string $timeZone
     * @return RawEmailEntity
     */
    public function getRawEmail(string $path, string $timeZone = 'Europe/Moscow') {
        return new RawEmailEntity($this->getEmailContent($path), new DateTimeZone($timeZone));
    }

    /**
     * Обработка письма и создание сущностей смен, которые были найдены в письме при парсинге
     * Логика парсина смен следующая: если при парсинге строки в предполагаемой колонке даты ошибка - значит в искомой
     * ячейке нет даты, а значит строка не содежрит смену. Следовательно, парсинг будет окончен и метод вернет сущность
     * ParsedEmailEntity со всеми найденными сервисами
     * @param RawEmailEntity $rawEmail
     * @return ParsedEmailEntity
     */
    public function parse(RawEmailEntity $rawEmail): ParsedEmailEntity
    {

        $parser = new Parser();
        $parser->setText($rawEmail->getRawEmailMessage());
        $html = $parser->getMessageBody('html');
        // Есть вариант, что почтовый клиент создал письмо без хедера, поэтому, устанавливаем хедер с нунжным
        // <meta> тегом
        if (!strrpos($html, '<head>')) {
            $html = self::HTML_HEADER . $html;
        }

        $emailDom = new DOMDocument();
        @$emailDom->loadHTML($html);
        $parsedEmail = new ParsedEmailEntity(
            $emailDom
        );

        // Вытаскиваем тело таблицы по тегу, однако, если всё письмо таблица - придется варсить каждую строку и
        // искать смены по определённым правилам
        $tableBody = $parsedEmail->getEmailDom()->getElementsByTagName('tbody');
        $isShiftsTablesStart = false;

        /**
         * @var int $rowNumber
         * @var DOMNode $rowCells
         */
        foreach ($tableBody[0]->childNodes as $rowNumber => $rowCells) {
            // Если при парсинге строки будет ошибка - просто прекращаем парсинг письма, т.к. смен нет
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

            foreach ($rowCells->childNodes as $columnCell) {
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