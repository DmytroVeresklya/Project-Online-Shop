<?php

namespace App\EventListener;

use App\EntityDateTimeAwareInterface;
use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Exception;

class EntityDateTimeListener
{
    /**
     * @throws Exception
     */
    public function prePersist(EntityDateTimeAwareInterface $entity, LifecycleEventArgs $event): void
    {
        if (null == $entity->getCreatedAt()) {
            $entity->setCreatedAt(new DateTime());
        }
        $entity->setModifiedAt(new DateTime());
    }

    /**
     * @throws Exception
     */
    public function preUpdate(EntityDateTimeAwareInterface $entity, PreUpdateEventArgs $event): void
    {
        $entity->setModifiedAt(new DateTime());
    }
}
