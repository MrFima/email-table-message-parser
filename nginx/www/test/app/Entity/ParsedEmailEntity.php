<?php

namespace App\Entity;

/**
 * Сущность распаршенного письма, содержащее Dom письма и смены
 */
class ParsedEmailEntity
{
    /** @var \DOMDocument */
    private \DOMDocument $emailDom;
    /** @var ShiftEntity[] */
    private array $shifts;

    /**
     * @param \DOMDocument $emailDom
     * @param ShiftEntity[] $shifts
     */
    public function __construct(
        \DOMDocument $emailDom,
        array        $shifts = []
    )
    {
        $this->emailDom = $emailDom;
        $this->shifts = $shifts;
    }

    /**
     * @param ShiftEntity $shift
     */
    public function addShift(ShiftEntity $shift): void
    {
        $this->shifts[] = $shift;
    }

    /**
     * @return ShiftEntity[]
     */
    public function getShifts(): array
    {
        return $this->shifts;
    }

    /**
     * @return \DOMComment
     */
    public function getEmailDom(): \DOMDocument
    {
        return $this->emailDom;
    }

}