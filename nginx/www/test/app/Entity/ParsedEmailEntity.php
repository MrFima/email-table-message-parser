<?php

namespace App\Entity;

class ParsedEmailEntity
{
    /** @var \DOMComment */
    private \DOMComment $emailDom;
    /** @var ShiftEntity[] */
    private array $shifts;
}