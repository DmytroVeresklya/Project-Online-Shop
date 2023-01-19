<?php

namespace App;

use DateTime;

interface EntityDateTimeAwareInterface
{
    public function getCreatedAt(): ?DateTime;

    public function setCreatedAt(DateTime $createdAt): mixed;

    public function getModifiedAt(): DateTime;

    public function setModifiedAt(DateTime $modifiedAt): mixed;
}
