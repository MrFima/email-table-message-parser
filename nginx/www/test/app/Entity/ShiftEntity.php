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
    /** @var bool */
    private bool $isConfirmed;
    /** @var int */
    private int $rowNumberInEmailTable;

    /**
     * @return int
     */
    public function getShift(): int
    {
        return $this->shift;
    }

    /**
     * @return Carbon
     */
    public function getDateFrom(): Carbon
    {
        return $this->dateFrom;
    }

    /**
     * @return Carbon
     */
    public function getDateTo(): Carbon
    {
        return $this->dateTo;
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }


    /**
     * @param int $shift
     */
    public function setShift(int $shift): void
    {
        $this->shift = $shift;
    }

    /**
     * @param Carbon $dateFrom
     */
    public function setDateFrom(Carbon $dateFrom): void
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * @param Carbon $dateTo
     */
    public function setDateTo(Carbon $dateTo): void
    {
        $this->dateTo = $dateTo;
    }

    /**
     * @return void
     */
    public function confirm(): void
    {
        $this->isConfirmed = true;
    }

    /**
     * @return void
     */
    public function decline(): void
    {
        $this->isConfirmed = false;
    }

    /**
     * @return int
     */
    public function getRowNumberInEmailTable(): int {
        return $this->rowNumberInEmailTable;
    }

    /**
     * Номер строки, которая содержит текущую смену
     * @param int $rowNumber
     * @return void
     */
    public function setRowNumberInEmailTable(int $rowNumber): void {
        $this->rowNumberInEmailTable = $rowNumber;
    }

}