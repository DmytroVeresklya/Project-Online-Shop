<?php

namespace App\Service;

use App\Entity\Subscriber;
use App\Exception\SubscriberAlreadyExistException;
use App\Model\SubscribeRequest;
use App\Repository\SubscriberRepository;

class SubscribeService
{
    public function __construct(private readonly SubscriberRepository $subscriberRepository)
    {
    }

    public function subscribe(SubscribeRequest $subscribeRequest): void
    {
        if ($this->subscriberRepository->existByEmail($subscribeRequest->getEmail())) {
            throw new SubscriberAlreadyExistException();
        }

        $subscriber = new Subscriber();
        $subscriber->setEmail($subscribeRequest->getEmail());

        $this->subscriberRepository->save($subscriber, true);
    }
}
