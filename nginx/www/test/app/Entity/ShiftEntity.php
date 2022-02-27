<?php

namespace App\Entity;

use Carbon\Carbon;

/**
 * Сущность смены
 */
class ShiftEntity
{
    /** @var int Какая смена (обед, ужин) */
    private int $shift;
    /** @var Carbon Время начало смены */
    private Carbon $dateFrom;
    /** @var Carbon Время окончания смены */
    private Carbon $dateTo;
}