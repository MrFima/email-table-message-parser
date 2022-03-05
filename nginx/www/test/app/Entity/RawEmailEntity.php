<?php

namespace App\Entity;

use DateTimeZone;

/**
 * Сущность письма, которая хранит загруженное письмо как есть с таймзоной, которой соответствует дата и время смены
 */
class RawEmailEntity
{
    /** @var string */
    private string $rawEmailMessage;
    /** @var DateTimeZone */
    private DateTimeZone $timeZone;

    /**
     * @param DateTimeZone $timeZone
     * @param string $rawEmailMessage
     */
    public function __construct(
        string $rawEmailMessage,
        DateTimeZone $timeZone
    )
    {
        $this->rawEmailMessage = $rawEmailMessage;
        $this->timeZone = $timeZone;
    }

    /**
     * @return DateTimeZone
     */
    public function getTimeZone(): DateTimeZone
    {
        return $this->timeZone;
    }

    /**
     * @return string
     */
    public function getRawEmailMessage(): string
    {
        return $this->rawEmailMessage;
    }

}