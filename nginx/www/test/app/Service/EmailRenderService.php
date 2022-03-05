<?php

namespace App\Service;

use App\Entity\ParsedEmailEntity;

class EmailRenderService
{
    /**
     * @param ParsedEmailEntity $entity
     * @return void
     */
    public function render(ParsedEmailEntity $entity) {
        echo $entity->getEmailDom()->saveHTML();
    }
}